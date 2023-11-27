# El ayudante de pruebas del repositorio

¡Muy bien, equipo! ¡Hemos cubierto todas las partes principales de las pruebas de integración! ¡Woo! Y es deliciosamente sencillo: sólo una estrategia para coger los servicios reales de un contenedor y probarlos, lo que... en última instancia nos da una prueba más realista.

Los inconvenientes de las pruebas de integración son que se ejecutan más lentamente que las pruebas unitarias, y a menudo son más complejas... porque tenemos que pensar en cosas como limpiar y sembrar la base de datos. Y a veces, no queremos que ocurran cosas reales (como llamadas a la API). En este caso, podemos utilizar un poco de Mocking para evitarlo. La gran conclusión es, como en todo, utilizar la herramienta adecuada -pruebas unitarias o pruebas de integración- para el trabajo adecuado. Eso es situacional y está bien utilizar ambas.

A medida que nos acercamos a la línea de meta, vamos a sumergirnos en las pruebas de algunas de las partes más complicadas de nuestro sistema: por ejemplo, si se enviaron correos electrónicos o mensajes de Messenger. Para ello, tenemos que dar a Bob un nuevo superpoder: la capacidad de bloquear el parque. Una vez activado, nuestra aplicación enviará un correo electrónico al personal del parque, básicamente diciendo:

> ¡Alerta! ¡Dinosaurios sueltos!

## Crear el comando

Dirígete a `LockDownHelper`. Aquí abajo, crea un nuevo método. Lo llamaremos para bloquear el parque, así que ¿qué te parece `public function dinoEscaped()`. Dale un tipo de retorno `void` y pon aquí algunos comentarios `TODO` que indiquen lo que tenemos que hacer: guardar un `LockDown` en la base de datos y enviar un correo electrónico.

[[[ code('770eb2609b') ]]]

Para llamar a este código y activar el bloqueo, vamos a crear un nuevo comando de consola. En el terminal, ejecuta:

```terminal
php bin/console make:command
```

Llámalo `app:lockdown:start`.

¡Bastante sencillo! Eso ha creado una única clase en `src/Command/LockdownStartCommand.php`. En su interior, autoconecta un `private LockDownHelper $lockDownHelper` y asegúrate de llamar al constructor `parent`.

[[[ code('aa391d50f1') ]]]

Aquí abajo, borra casi toda esta lógica... y sustitúyela por`$this->lockDownHelper->dinoEscaped()` y`$io->caution('Lockdown started!!!!!!!!!!')`.

[[[ code('6eb47e2496') ]]]

Peligroso. Este método aún no hace nada, pero ya podemos ir probando el comando. Copia su nombre... y ejecútalo:

```terminal
php bin/console app:lockdown:start
```

¡Me encanta!

## Crear la prueba

Antes de ensuciarnos las manos con el nuevo método, vamos a escribir una prueba. Pero antes, hagamos ese truco en el que añadimos un `private function` para ayudarnos a obtener el servicio que estamos probando: `private function getLockDownHelper()` que devolverá un`LockDownHelper`. Dentro, copia el código de arriba... y devuélvelo. Luego, simplifica el código de aquí arriba a sólo `$this->getLockDownHelper()->endCurrentLockDown()`.

[[[ code('139f58c090') ]]]

Muy bien, ahora crea el nuevo método de prueba:`public function testDinoEscapedPersistsLockDown()`. Empieza como siempre: arrancando el kernel. Luego llama al método con`$this->getLockDownHelper()->dinoEscaped()`.

[[[ code('391176b6bb') ]]]

¡Genial! No es interesante, pero haz la prueba de todos modos:

```terminal
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

No falla, pero... es arriesgado porque no hemos realizado ninguna aserción.

## Aserciones de la base de datos a través del repositorio

Lo que queremos afirmar es que se ha insertado una fila en la base de datos. Para ello, podríamos coger el gestor de entidades o nuestro servicio de repositorio, hacer una consulta y realizar algunas aserciones utilizándolo. Sin embargo, Foundry viene con un buen truco para esto.

Después de llamar al método, digamos `LockDownFactory`. Normalmente, llamaríamos a cosas como`create` o `createMany`, pero éste también tiene un método llamado `repository`. Éste devuelve un objeto de Foundry que envuelve el repositorio -de forma muy parecida a como Foundry envuelve nuestras entidades en un objeto `Proxy`. Esto significa que podemos llamar a métodos reales del repositorio, como `findMostRecent()` o `isInLockDown()`. Pero también tiene cosas extra, como `assert()`. Digamos `->assert()->count(1)` para asegurarnos de que hay un registro en esta tabla. Podríamos ir más allá y obtener ese registro para asegurarnos de que su estado es "activo", pero me lo saltaré.

[[[ code('8ee63f0e19') ]]]

Ejecuta ahora la prueba.

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Debería fallar y... falla.

Voy a pegar un código que cree el `LockDown` y lo guarde. Fácil y aburrido código.

[[[ code('1eedf0832c') ]]]

Ejecuta la prueba ahora... ¡pasa!

Siguiente paso: enviemos el correo electrónico y comprobemos que se ha enviado. Lo haremos con algunas herramientas básicas de Symfony y también con otra librería de zenstruck.
