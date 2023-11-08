# Siembra de datos de fábrica

Tengo que confesarte algo: ¡He estado haciéndonos trabajar demasiado!

Para sembrar la base de datos, instanciamos la entidad, cogemos el Gestor de Entidades y, a continuación, la persistimos y la vaciamos. Esto no tiene nada de malo, pero Foundry está a punto de hacernos la vida mucho más fácil.

## Generar la Fábrica

En tu terminal, ejecuta:

```terminal
php bin/console make:factory
```

Este comando viene de Foundry. Seleccionaré generar todas las fábricas.

La idea es que crees una fábrica para cada entidad para la que quieras crear datos ficticios, ya sea en una prueba o para tus fixtures normales. Sólo necesitamos`LockDownFactory`, pero eso está bien.

Gira y mira `src/Factory/LockDownFactory.php`. No voy a hablar demasiado de estas clases de fábrica: ya las tratamos en nuestro tutorial de Doctrine. Pero esta clase facilitará la creación de objetos `LockDown`, incluso estableciendo`createdAt` en un `DateTime` aleatorio, `reason` en un texto aleatorio y `status`en uno de los estados válidos, por defecto.

[[[ code('6d9a2e967b') ]]]

## Utilizar la Fábrica en una prueba

Utilizar esto en una prueba es una delicia. Digamos `LockDownFactory::createOne()`. Aquí, podemos pasar una matriz de cualquier campo que queramos establecer explícitamente. Lo único que nos importa es que este `LockDown` tenga un estado `ACTIVE`. Por tanto, establece`status` en `LockDownStatus::ACTIVE`.

[[[ code('c6e87cb50e') ]]]

Ya está No necesitamos crear este `LockDown` y no necesitamos el Gestor de Entidades. Esa única llamada se encarga de todo.

Observa, cuando ejecutemos la prueba:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

¡Pasa! Me encanta.

## Objetos proxy Foundry

Por cierto, el método `LockDownRepository` devuelve el nuevo objeto `LockDown`... lo que a menudo puede ser útil. Pero en realidad está envuelto en un objeto proxy especial. Así que si ejecutamos la prueba ahora, puedes ver que es un proxy... y que el `LockDown` se esconde dentro.

¿Por qué hace eso Foundry? Bueno, si vas a buscar su documentación, tienen toda una sección sobre el uso de esta biblioteca dentro de las pruebas. Un punto habla del proxy de objetos. El proxy te permite llamar a todos los métodos normales de tu entidad más varios métodos adicionales, como `->save()`, `->remove()` o incluso `->repository()` para obtener otro objeto proxy que envuelve al repositorio.

Así que parece y actúa como tu objeto normal, pero con algunos métodos adicionales. Eso no es importante para nosotros ahora, sólo quería que lo tuvieras en cuenta. Si necesitas el objeto entidad real, puedes llamar a `->object()` para obtenerlo.

[[[ code('a31edddd11') ]]]

## Añadir más objetos

De todos modos, ahora que añadir datos es tan sencillo, podemos hacer rápidamente que nuestra prueba sea más robusta. Para ver si podemos engañar a mi consulta, llama a `createMany()`... para crear 5 objetos `LockDown`con `LockDownStatus::ENDED`.

Para asegurarnos de que nuestra consulta sólo mira el `LockDown` más reciente , para el activo, establece su `createdAt` en `-1 day`. Y para los `ENDED`, establécelos en algo más antiguo.

[[[ code('665593bdae') ]]]

Veamos si nuestra consulta es lo suficientemente robusta como para seguir comportándose correctamente.

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

¡Lo es!

Pero... en realidad... la gestión tiene algunas reglas extra complicadas en torno a un bloqueo. Copia esta prueba, pégala y cámbiale el nombre a`testIsInLockdownReturnsFalseIfTheMostRecentIsNotActive`.

[[[ code('50bd43d597') ]]]

Para explicar la extraña regla de la dirección, permíteme modificar los datos. Haz que el primer `LockDown`sea`ENDED`... luego el siguiente, más antiguo de 5 estados `ACTIVE`. Por último, `assertFalse()` al final.

[[[ code('8c948d8bfd') ]]]

Eso... puede parecer confuso... y en cierto modo lo es. Según la dirección, al determinar si estamos en bloqueo, SÓLO debemos mirar el estado `LockDown`MÁS reciente. Si hay bloqueos activos más antiguos... esos, aparentemente, no importan.

No es sorprendente que, cuando probamos las pruebas:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

Ésta falla. Pero, mira el lado bueno: ¡esa prueba fue superrápida de escribir! Y ahora podemos entrar en `LockDownRepository` para arreglar las cosas. Avanzaré por algunos cambios que recuperan el `LockDown` más reciente, independientemente de su estado.

Si no encontramos ningún bloqueo, devolveré false. Si no, añadiré un `assert()`para ayudar a mi editor... y devolveré true si el estado no es igual a`LockDownStatus::ENDED`.

[[[ code('80177d2a3a') ]]]

Y ahora

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

¡Estamos en verde!

## Utilizar la función LockDown

Llevamos tanto tiempo viviendo en nuestro terminal que creo que deberíamos celebrarlo utilizándolo en nuestro sitio. En los accesorios, he añadido un `LockDown` activo por defecto.

Dirígete a `MainController`... y autoconecta `LockdownRepository $lockdownRepository`. A continuación, lanza una nueva variable en la plantilla llamada `isLockedDown` ajustada a`$lockdownRepository->isInLockdown()`.

[[[ code('2514f65635') ]]]

Por último, en la plantilla - `templates/main/index.html.twig` - ya tengo una plantilla`_lockdownAlert.html.twig`. Si, `isLockedDown`, incluye eso.

[[[ code('8115705ee9') ]]]

Momento de la verdad. Refresca. Ejecuta: ¡Sálvese quien pueda! ¡Estamos en bloqueo!

Siguiente: necesitamos una forma de desactivar el bloqueo. Porque, si hago clic en esto,... ¡no hace nada! Para ayudarnos con esta nueva tarea, vamos a utilizar una prueba de integración en una clase diferente: en uno de nuestros servicios normales.
