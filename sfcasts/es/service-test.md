# Probar un servicio

Si haces clic en este botón para finalizar el bloqueo... golpea una sentencia `die`. Creé un controlador... pero me dio pereza...

Para finalizar un bloqueo, tenemos que encontrar el bloqueo activo, cambiar su estado a finalizado y guardarlo en la base de datos. Muy fácil. Pero en lugar de poner esa lógica dentro de nuestro controlador, vamos a crear un servicio.

## Crear el servicio

Podríamos usar TDD, pero voy a crear la clase rápidamente, y luego haremos pruebas: será más fácil de entender.

Dentro de `src/Service/`, añade una nueva clase `LockdownHelper`. Pondré algo de lógica... porque es muy aburrido. Tenemos un método llamado`endCurrentLockDown()`, que llama a un método `findMostRecent()` en el repositorio, establece el estado en `ENDED` y lo vacía. Aquí arriba, autoconectamos `LockdownRepository`y `EntityManagerInterface`.

[[[ code('cc9cf735a8') ]]]

El método `findMostRecent()` aún no existe en el repositorio. Así que abre`LockDownRepository`... y hagamos algo de refactorización. Crea una nueva función pública llamada `findMostRecent()`, que devolverá un `Lockdown` anulable. Luego coge el código de abajo, pégalo, devuélvelo y llámalo: `$lockdown` es igual a`$this->findMostRecent()`.

[[[ code('6c51f5c855') ]]]

Y sí, podrías crear una prueba de integración para `findMostRecent()`, pero nos la saltaremos.

De vuelta en `LockDownHelper`... ¡esto es feliz! Antes de utilizar esta clase, ¡vamos a probarla!

## ¿Prueba unitaria? ¿O prueba de integración?

La primera pregunta es, ¿necesitamos una prueba unitaria o una prueba de integración? Y sinceramente, cualquiera de las dos estaría bien. Podríamos hacer una prueba unitaria, simular `LockdownRepository`, asegurarnos de que se llama a `findMostRecent()`, y de que establece el estado en `ENDED`y llama a `flush()` en el gestor de entidades. Así que sí, una prueba unitaria estaría bien: el mocking no es demasiado complicado... y probaría la lógica bastante bien.

O podemos escribir una prueba de integración, que se ejecutará un poco más despacio, pero será más realista. Por el bien de este tutorial, vamos a hacer una prueba de integración. Además, puedes hacer ambas cosas. Diablos, nada te impide arrancar el núcleo en un método de prueba... y utilizar mocks en otro método de prueba de la misma clase. Los mocks y el contenedor son dos herramientas diferentes que te ayudarán a realizar tu trabajo.

En el directorio `Integration/`, crea un nuevo directorio `Service/`... y luego una nueva clase PHP: `LockdownHelperTest`. Esta vez, ve directamente a extender `KernelTestCase`, y luego utiliza nuestros dos traits favoritos: `use ResetDatabaseTrait` y `Factories`. 

[[[ code('260ffd8d3d') ]]]

Como utilizaremos estos rasgos en todas las pruebas de integración, también puedes crear una clase base. En algún lugar dentro de `tests/`, podrías crear una clase abstracta`BaseKernelTestCase`, poner los rasgos allí, y luego hacer que todas tus pruebas de integración la extiendan.

Aquí abajo, vamos a montar nuestra prueba: `testEndCurrentLockdown()`. Y ya sabemos cómo empezar: `self::bootKernel()`.

[[[ code('e042a79100') ]]]

Pensemos. Si vamos a terminar un bloqueo... necesitamos un `LockDown`activo en la base de datos. Digamos que `$lockdown` es igual a `LockDownFactory::createOne()`... y que `status` es igual a `LockDownStatus::ACTIVE`.

[[[ code('1fa2c58b1d') ]]]

Como sabemos que nuestra base de datos empezará vacía, sabemos que éste será el elemento que devuelva nuestra consulta. Aquí abajo, coge el `$lockDownHelper` con`self::getContainer()->get(LockDownHelper::class)`... y utiliza el truco `assert()` para decirle a nuestro editor que se trata de un `instanceof` `LockDownHelper` .

[[[ code('19150f0967') ]]]

Con la parte "Organizar" de la prueba hecha, actuemos:`$lockDownHelper->endCurrentLockDown()`.

Con un poco de suerte, este registro debería haber cambiado su estado en la base de datos. Para probarlo, afirma que `LockDownStatus::ENDED` es igual a `$lockDown->getStatus()`.

[[[ code('7951c2f338') ]]]

## Actualización automática en acción

¡Qué buena pinta tiene esta prueba! Aunque hay un pequeño detalle que debo mencionar. Primero... voy a decir una mentira. Al comprobar `$lockDown->getStatus()`, en realidad sólo estamos comprobando que a este objeto `LockDown` le ha cambiado el estado el código... no estamos comprobando realmente si su nuevo valor se ha guardado en la base de datos. Para comprobar eso, podríamos hacer una nueva consulta a la base de datos, como a través de`LockDownFactory::repository()`... y luego encontrar la más reciente. Hablaremos más sobre el acceso directo al repositorio más adelante.

Ahora, la verdad. Deberías pensar críticamente sobre lo que estás probando o no, como acabamos de hacer. Sin embargo, como hemos creado la variable `$lockDown`a través de Foundry, está envuelta en un `Proxy`. Una de las principales características de un `Proxy` se llama "auto-refrescar". Cada vez que accedes a una propiedad o llamas a un método de tu entidad, entre bastidores, Foundry busca los datos más recientes en la base de datos y los establece. Por tanto, si no hubiéramos volcado el cambio de estado en la base de datos, la prueba habría fallado. De hecho, Foundry habría visto que teníamos cambios sin guardar en esa entidad, y nos habría gritado. Genial.

## ¿Servicios incorporados o eliminados?

Vale, ¡probemos esto! Ejecuta:

```terminal
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Y... ¿qué demonios? Dice

> El servicio o alias `LockDownHelper` ha sido eliminado o inlineado cuando
> se compiló el contenedor.

¿Qué significa esto? Vale, una cosa muy guay del contenedor de servicios de Symfony es que si un servicio no es utilizado por nada en tu aplicación, se elimina del contenedor... lo que hace que nuestra aplicación sea más esbelta.

En el código real de nuestra aplicación, como controladores, repositorios y servicios, nadie utiliza el servicio `LockDownHelper`. No lo estamos autocableando en ningún controlador o servicio. Y así, Symfony lo elimina del contenedor... lo que significa que no está ahí en la prueba.

La solución para esto es... ¡asegurarnos de que se utiliza en alguna parte! Es decir, si estamos escribiendo este código, seguro que teníamos la intención de... ya sabes, utilizarlo.

En la acción `endLockDown()`, autowire `LockDownHelper $lockDownHelper`... y ni siquiera voy a llamar a nada todavía. Con tenerlo aquí será suficiente.

[[[ code('9029f9e08b') ]]]

Y ahora:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

¡La prueba pasa! ¡Guau!

Vamos a utilizarlo: llama a `$lockDownHelper->endCurrentLockDown()`... y luego redirige de nuevo a la página de inicio.

[[[ code('2348905b21') ]]]

¡Vamos a probarlo! Actualiza, estamos en un bloqueo... "Fin del bloqueo"... ya no está. Todos los dinos han vuelto a sus corrales.

Siguiente: Voy a complicar las cosas introduciendo una situación que nos hará querer hacer pruebas unitarias y pruebas de integración `LockDownHelper`... al mismo tiempo. Eso nos llevará a algo que yo llamo "mocking parcial".
