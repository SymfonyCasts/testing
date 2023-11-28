# Probando Messenger

Vamos a animar un poco más nuestro `LockDownHelper` Cuando creemos un bloqueo, en lugar de enviar el correo electrónico directamente, vamos a enviar un mensaje a Messenger y hacer que envíe el correo electrónico. Empieza por instalar Messenger:

```terminal
composer require symfony/messenger
```

¡Encantador! En `.env`, esto añade un `MESSENGER_TRANSPORT_DSN` que, por defecto, utiliza el tipo de transporte Doctrine. Aunque no importará qué tipo de transporte utilices: Doctrine, Redis, lo que sea. Como verás, en el entorno `test`, anularemos esto por completo.

[[[ code('d1ae629d66') ]]]

## Configurar el transporte del entorno de pruebas

Para facilitar las pruebas, vamos a necesitar también otro paquete de, lo has adivinado, ¡Zenstruck! 

```terminal
composer require zenstruck/messenger-test --dev
```

¡Genial! Esta biblioteca `messenger-test` añade un transporte especial de Messenger llamado `test`. Seguiremos utilizando Doctrine por defecto, pero ahora abre`config/packages/messenger.yaml`. Descomenta el transporte `async`, que utiliza`MESSENGER_TRANSPORT_DSN`. A continuación, en `when@test`, anulamos el transporte `async`y lo establecemos en el tipo `in-memory`. Ah, y tengo que eliminar un espacio de más. Perfecto

[[[ code('3927670ded') ]]]

El `in-memory` viene de Symfony y está bien para hacer pruebas. Cuando se utiliza, los mensajes no se envían realmente a un transporte, sino que se almacenan -en memoria- en un objeto durante la prueba... que luego puedes utilizar para afirmar que el mensaje está ahí.

¡Eso me gusta! Pero los paquetes `messenger-test` nos ofrecen algo aún mejor. Cambia esto por `test://`. Veremos lo que hace en un momento.

[[[ code('9d91d738e2') ]]]

## Comprobar que se han enviado los mensajes

Antes de despachar el mensaje dentro de nuestro código, dirígete a la prueba. Aquí queremos afirmar que hemos enviado un mensaje a Messenger. Y -sorpresa, sorpresa- vamos a utilizar otro rasgo. Se llama `InteractsWithMessenger`. Aquí abajo, justo antes de llamar al método, decimos `$this->transport()->queue()->assertEmpty()`.

[[[ code('69a16861d0') ]]]

Al igual que en la biblioteca de correo, hay muchas cosas diferentes sobre los mensajes que podemos comprobar. Estamos afirmando que la cola empieza vacía, lo cual no es realmente necesario, pero es una buena forma de empezar. Al final, también`assertCount()` que se ha enviado el mensaje `1`.

[[[ code('b5476a42c8') ]]]

¡Vamos a probar esto! Sigue ejecutando todas las pruebas de `LockDownHelper`:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Y... ¡falla con el mensaje exacto que queríamos!

> Se esperaba 1 mensaje, pero se han encontrado 0 mensajes.

## Creación y envío del mensaje

¡Genial! Genera un mensaje de Messenger con:

```terminal
./bin/console make:message
```

Llámalo `LockDownStartedNotification` y ponlo en el transporte `async`. 
¡Listo! Esto ha creado una clase de mensaje, una clase manejadora y también ha actualizado`messenger.yaml` para que esta clase se envíe al transporte `async`.

[[[ code('2f0929508b') ]]]

A continuación, entra en `LockDownHelper` para enviarlo. En la parte superior, añade un `private MessageBusInterface $messageBus`. Luego, en la parte inferior, pon`$this->messageBus->dispatch(new LockDownStartedNotification())`.

[[[ code('99743a4ad9') ]]]

El manejador de esta clase, si miramos en`src/MessageHandler/LockDownStartedNotification.php`, aún no hace nada. Pero esto debería bastar para que nuestra prueba pase.

[[[ code('40e6055780') ]]]

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Y... ¡huy! ¡Un gremlin se coló en mi código! Añadí el código dentro de `endCurrentLockDown()`en lugar de `dinoEscaped()`. Y por eso tenemos gente de pruebas. Cuando volvamos a intentarlo... ya lo tengo.

Movamos toda la lógica del correo fuera de esta clase. Copia el método privado, borra donde lo llamamos, el `MailerInterface`... e incluso las antiguas declaraciones `use`.

Abre el manejador, pega allí el método privado y dale a "Aceptar" para volver a añadir esas sentencias`use`. Luego di `$this->sendEmailAlert()`.

[[[ code('d8a629bb8c') ]]]

¡Genial! Todo debería seguir funcionando bien... excepto que la prueba falla:

> Se esperaba el envío de 1 mensaje, pero se enviaron 0 mensajes.

## Procesando mensajes en tu prueba

Hmmm. Si esto fuera producción, cuando enviemos este mensaje al transporte `async`, no enviará el correo electrónico inmediatamente. Se enviaría a una cola y se procesaría más tarde. Y, el transporte `test` que estamos utilizando funciona muy parecido a una verdadera cola. Recibe el mensaje, pero no lo procesa automáticamente, lo cual es genial. Esto significa que, en nuestra prueba, estamos despachando este mensaje... pero el correo electrónico nunca se envía porque sigue esperando a ser procesado.

Lo que hagas aquí depende de ti. Quizá te parezca bien saber simplemente que el mensaje se ha enviado.

O puede que quieras ser un poco más práctico y decir:

> ¡De ninguna manera! Quiero una prueba completa de que cuando se gestiona este mensaje, se envía
> un correo electrónico.

Podemos hacerlo diciéndole al transporte `test` que procese sus mensajes. Copia esas dos líneas de `mailer()` y bórralas. Aquí abajo, pon `$this->transport()->process()`.

[[[ code('27b01fd847') ]]]

Ya está Eso ejecutará el manejador para cualquier mensaje en su cola. Debajo, debería enviarse el correo electrónico.

Pruébalo:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Y... falla. ¡Otro error! ¿Por qué no se envió? Porque fui demasiado rápido con mi manejador: no existe la propiedad `$this->mailer`. De hecho, me sorprende que no hayamos obtenido un error mayor dentro de nuestra prueba.

Para solucionarlo, añade `public function __construct(private MailerInterface $mailer)`. ¡Así queda mejor! Y si lo intentamos de nuevo... pasa.

¡Y podemos acortar las cosas! En lugar de `assertCount(1)` y `->process()`, podemos decir `processOrFail()`. Este método se asegura de que hay al menos un mensaje que procesar, y luego lo procesa.

[[[ code('525e2430e1') ]]]

Comprueba dos veces la prueba:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

¡Lo tenemos!

¡Lo hicimos equipo! Nuestra aplicación Dinotopia es peligrosa y está bien probada, gracias a las pruebas unitarias y de integración. En el siguiente tutorial de esta serie, pasaremos al último tipo de pruebas: las pruebas funcionales, en las que controlas de hecho un navegador, navegas por las páginas y compruebas lo que hay en ellas. Es divertido y también puede utilizarse para comprobar el comportamiento de JavaScript.

Muy bien amigos, hasta la próxima.
