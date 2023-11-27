# Prueba de correos electrónicos

Cuando entramos en bloqueo, necesitamos enviar un correo electrónico. Antes de escribir el código para hacerlo, añadamos una aserción para ello.

## Afirmar que se envía un correo electrónico

¿Cómo? Symfony nos cubre las espaldas: nos proporciona unos cuantos métodos relacionados con los correos electrónicos, como `$this->assertEmailCount()`. Podemos afirmar muchas cosas sobre los correos electrónicos, pero por simplicidad, nos ceñiremos a esta sencilla cuenta.

[[[ code('aed90b5090') ]]]

Ejecuta la prueba:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Fallo épico, porque... ni siquiera tenemos mailer instalado todavía. ¡Hagámoslo! Ejecuta:

```terminal
composer require symfony/mailer
```

Si te pregunta por la configuración de Docker, eso depende de ti, pero yo voy a decir`Yes permanently`. Hablaremos de lo que hizo eso en un minuto, pero no es superimportante.

De forma similar a una base de datos, necesitamos configurar nuestros parámetros de conexión a Mailer. Eso se hace en `.env` a través de `MAILER_DSN`. Descomenta esto. El transporte `null` es un gran valor por defecto. Significa que los correos electrónicos no se enviarán realmente en los entornos dev o test. Y luego puedes anularlo en tu entorno de producción para establecerlo en algo real.

[[[ code('7871a7b275') ]]]

Si quieres cambiar esto por otra cosa en el entorno `dev`, yo probablemente añadiría este transporte `null` a `.env.test`... porque está muy bien evitar el envío de correos electrónicos desde nuestras pruebas.

Muy bien, vuelve a tirar los dados de las pruebas:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

¡Mejor! Falla porque no hemos enviado ningún correo electrónico. ¡Hagámoslo!

## Enviar el correo electrónico

En `LockDownHelper`, autoconecta un servicio más:`private MailerInterface $mailer`. Luego, aquí abajo, como esto no es un tutorial de Mailer, llama a un nuevo método `sendEmailAlert()`... y lo pegaré. Pasa el ratón por encima de la clase `Email` y pulsa "alt" + "enter" para añadir la sentencia`Symfony\Component\Mime\Email` `use` .

[[[ code('8d1f812c8c') ]]]

¡Listo! Vuelve a la línea de comandos:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

¡Ya está! ¡La prueba ha pasado!

## Ver correos electrónicos a través de MailCatcher

Por cierto, esto no está relacionado con las pruebas, pero una de las cosas buenas de utilizar la integración con Docker es que, cuando instalamos Mailer, añadió este servicio `mailcatcher`.

[[[ code('08c1cf5ef6') ]]]

Ejecuta:

```terminal
docker compose down
```

Luego

```terminal
docker compose up -d
```

para iniciar el nuevo servicio. Ejecuta de nuevo la prueba. Sigue pasando. Sin embargo, como el servicio `mailcatcher` está en marcha y ejecutamos nuestras pruebas a través del binario Symfony, éste anuló la variable de entorno `MAILER_DSN` y la apuntó a MailCatcher. ¿Qué... es MailCatcher?

Para averiguarlo, ejecuta:

```terminal
symfony open:local:webmail
```

¡Genial! MailCatcher es un servicio de correo electrónico falso con una pequeña GUI web para ver los correos que ha enviado tu aplicación. Si enviáramos un correo electrónico a través de nuestra aplicación real, aparecería aquí.

Observa. Ejecuta:

```terminal
symfony console app:lockdown:start
```

¡Cierre! Y cuando compruebes MailCatcher... ¡ja! ¡Tenemos dos mensajes! ¡Qué guay!

## Utilizando zenstruck/mailer-test

De todas formas, antes de que dejemos de hablar de correos electrónicos, quiero mostrarte una herramienta más. Y es otra biblioteca de Zenstruck. Ejecuta:

```terminal
composer require zenstruck/mailer-test --dev
```

Symfony tiene herramientas integradas para probar correos electrónicos, y funcionan muy bien. Esta biblioteca de `mailer-test` nos da aún más herramientas, ¡y es fácil de usar!

Añade otro rasgo a nuestra prueba - `use InteractsWithMailer` - y luego, aquí abajo, en lugar de `assertEmailCount`, podemos decir `$this->mailer()->`... y entonces, woh, tenemos un montón de asertos diferentes a nuestra disposición. Di`->assertSentEmailCount(1)`, y debajo, `assertEmailSentTo()` con`staff@dinotopia.com` y la línea de asunto `PARK LOCKDOWN`. ¡Uy! Deja que corrija mi errata. Puedes ver que esto es el `expectedTo` y luego esto es un `callable` donde podríamos afirmar más cosas o simplemente pasar el asunto esperado.

[[[ code('f20cd362b9') ]]]

Esto es bastante sencillo, pero es una de las muchas cosas que podemos hacer con esta biblioteca. Consulta la documentación para enterarte de todo.

Ejecuta de nuevo la prueba:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

¡Todo bien! A continuación: hablemos de las pruebas con Messenger.
