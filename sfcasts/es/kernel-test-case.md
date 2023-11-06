# KernelTestCase: Obtención de servicios

En nuestra aplicación, si quisiéramos utilizar `LockDownRepository` para hacer consultas reales, podríamos autocablear `LockDownRepository` en un controlador -o en algún otro lugar-, llamar a un método sobre él, y ¡boom! Todo funcionaría.

Ahora queremos hacer lo mismo en nuestra prueba: en lugar de crear el objeto manualmente, queremos pedirle a Symfony que nos proporcione el servicio real que está configurado para hablar con la base de datos real, para que pueda hacer su lógica real. ¡De verdad!

## Arrancar el núcleo

Para obtener un servicio dentro de una prueba, necesitamos arrancar Symfony y acceder a su contenedor de servicios: el objeto místico que contiene todos los servicios de nuestra aplicación.

Para ayudarnos con esto, Symfony nos proporciona una clase base llamada `KernelTestCase`. No hay nada particularmente especial en esta clase. Mantén pulsado "comando" o "control" para ver que amplía la normal `TestCase` de PHPUnit. Sólo añade métodos para arrancar y apagar el núcleo de Symfony -que es algo así como el corazón de Symfony- y para coger el contenedor.

[[[ code('929bcce07d') ]]]

## Obtención de servicios

En la parte superior de nuestro método de prueba, comienza con `self::bootKernel()`. Una vez que llames a esto, puedes imaginar que tienes una aplicación Symfony ejecutándose en segundo plano, esperando a que la utilices. Concretamente, esto significa que podemos coger cualquier servicio. Hazlo con `$lockDownRepository = self::getContainer()` (que es un método ayudante de`KernelTestCase`) `->get()`. A continuación, pasa el ID del servicio que, en nuestro caso, es el nombre de la clase: `LockDownRepository::class`.

Para ver si funciona, `dd($lockDownRepository)`.

[[[ code('fdf13682f0') ]]]

Por cierto, las pruebas unitarias y las de integración suelen tener el mismo aspecto: llamas a métodos de un objeto y ejecutas aserciones. Si resulta que tu prueba arranca el núcleo y coge un servicio real, le damos el nombre de "prueba de integración". Pero eso no es más que una forma elegante de decir: "Una prueba unitaria... salvo que utilizamos servicios reales".

Bien, ¡vamos a probarlo! En tu terminal, ejecuta:

```terminal
./vendor/bin/phpunit
```

También puedes ejecutar `./bin/phpunit`, que es un acceso directo configurado para Symfony. Pero yo seguiré ejecutando directamente `phpunit`.

Y... ¡sí! ¡Ahí está nuestro servicio! No parece gran cosa, pero este objeto perezoso es algo que vive en el servicio real.

## El contenedor especial del servicio de pruebas

Así que, ¡sencillo! `self::getContainer` nos da el contenedor del servicio... y luego llamamos a `get()` sobre él. Pero quiero señalar que acceder al contenedor de servicios y tomar un servicio de él no es algo que hagamos en el código de nuestra aplicación. Para la mayoría de los servicios, que son privados, ¡hacer esto ni siquiera funcionará! En su lugar, confiamos en la inyección de dependencias y el autocableado.

Pero en una prueba no hay inyección de dependencias ni autocableado. Así que tenemos que coger servicios como éste. Y la única razón por la que esto funciona es porque`self::getContainer()` nos proporciona un contenedor especial que sólo existe en el entorno`test`. Es especial porque te permite llamar a un método `get()`y pedir cualquier servicio que quieras por su ID... aunque ese servicio sea normalmente privado. Así que éste es un superpoder exclusivo del entorno `test`.

## Ejecutar el código y confirmarlo

Vale, ya que tenemos `LockDownRepository`, vamos a intentar ejecutar una prueba sencilla. Pero, hmm, no obtengo el autocompletado correcto. Ah, eso es porque mi editor no sabe qué devuelve el método `get()`. Para ayudarle, `assert()` que `$lockDownRepository`es un `instanceof LockDownRepository`. Esto no es una aserción PHPUnit: no hemos dicho `$this->assert`-algo. Esto es sólo una función PHP que lanzará una excepción si `$lockDownRepository` no es un `LockDownRepository`. Será... 
y este código nunca causará un problema... ¡pero ahora disfrutamos del encantador autocompletado!

[[[ code('d363521a6f') ]]]

Digamos `$this->assertFalse($lockDownRepository->isInLockDown())`.

[[[ code('4fb9dc8d3c') ]]]

La idea es que no hemos añadido ninguna fila a la base de datos... y por eso, no deberíamos estar en un bloqueo. Y como el método devuelve false ahora mismo... esta prueba debería pasar:

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡lo hace! Así que estamos utilizando el servicio real... pero todavía no está haciendo ninguna consulta. ¿Seguirá funcionando si hacemos una consulta? Vamos a averiguarlo.
