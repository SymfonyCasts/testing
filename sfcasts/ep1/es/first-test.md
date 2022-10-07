# Nuestra primera prueba

Ya tenemos esta clase `Dinosaur`... y es bastante sencilla. Pero cuando se trata de dinosaurios, los errores en nuestro código pueden ser, mmm, un poco dolorosos. ¡Así que vamos a añadir algunas pruebas básicas!

## Crear la clase de prueba

Mmmm... ¿dónde ponemos esta nueva prueba? Técnicamente podemos poner nuestras pruebas en cualquier lugar de nuestro proyecto, pero cuando instalamos `symfony/test-pack`, Flex creó un directorio `tests/` que, no es de extrañar, es el lugar recomendado para poner nuestras pruebas.

Recuerda que, en este tutorial, sólo estamos tratando con pruebas unitarias. Así que, dentro de `tests/`, crea un nuevo directorio llamado `Unit`. Y como nuestro `Dinosaur::class` vive en el espacio de nombres `Entity`, crea al mismo tiempo un directorio `Entity`dentro de él.

Toda esta organización es técnicamente opcional: puedes organizar el directorio `tests/`como quieras. Pero, poner todas nuestras pruebas unitarias en un directorio `Unit`es simplemente... agradable. Y la razón por la que hemos creado el directorio `Entity`es porque queremos que la estructura de archivos dentro de `Unit` refleje la estructura de nuestro directorio `src/`. Es una buena práctica que mantiene nuestras pruebas organizadas.

Por último, crea una nueva clase llamada `DinosaurTest`. ¡Utilizar ese sufijo `Test` tiene sentido: estamos probando `Dinosaur`, así que la llamamos `DinosaurTest`! Pero también es un requisito: PHPUnit -nuestra biblioteca de pruebas- lo requiere. También requiere que cada clase extienda `TestCase`:

[[[ code('a18a9e97ac') ]]]

Ahora vamos a escribir una prueba sencilla para asegurarnos de que todo funciona.

Dentro de nuestra clase `DinosaurTest`, añadamos `public function testIsWorks()`... ¡donde crearemos la prueba más emocionante! Si te gustan los tipos de retorno -¡a mí me gustan! - utiliza`void`... aunque eso es opcional

Dentro llama a `self::assertEquals(42, 42)`:

[[[ code('c65f8a62e5') ]]]

¡Eso es todo! No es una prueba muy interesante - si nuestro ordenador piensa que 42 no es igual a 42, tenemos problemas mayores - pero es suficiente.

## Ejecución de PHPUnit

¿Cómo ejecutamos la prueba? Ejecutando PHPUnit. En tu terminal, ejecuta:

```terminal
./vendor/bin/phpunit
```

Y... ¡impresionante! PHPUnit vio una prueba -para nuestro único método de prueba- y una aserción.

También podríamos decir `bin/phpunit` para ejecutar nuestras pruebas, que es básicamente un atajo para ejecutar `vendor/bin/phpunit`.

Pero, seguro que tienes curiosidad... ¿Qué es... una aserción?

Volviendo a `DinosaurTest`, la única aserción se refiere al método `assertEquals()`, que proviene de la clase `TestCase` de PHPUnit. Si el valor real -42- no coincide con el valor esperado, la prueba fallaría. PHPUnit tiene un montón de métodos de aserción más... y podemos verlos todos yendo a https://phpunit.readthedocs.io. Está lleno de cosas buenas, incluyendo una sección de "Aserciones". Y... ¡vaya! Míralas todas... Hablaremos de las aserciones más importantes a lo largo de la serie. Pero por ahora, ¡volvamos a la prueba!

## Convenciones de nomenclatura de las pruebas

Porque, tengo una pregunta: ¿cómo sabe PHPUnit que esto es una prueba? Cuando llamamos a`vendor/bin/phpunit`, PHPUnit hace tres cosas. Primero, busca su archivo de configuración, que es `phpunit.xml.dist`:

[[[ code('93d0379590') ]]]

Dentro, encuentra `testsuites`... y la parte `directory` dice:

[[[ code('2f4b344a62') ]]]

> Eh, PHPUnit: ¡busca pruebas dentro del directorio `tests/`!

En segundo lugar, encuentra ese directorio y busca recursivamente todas las clases que terminen con la palabra`Test`. En este caso, `DinosaurTest`. Por último, una vez que encuentra una clase de prueba, obtiene una lista de todos sus métodos públicos.

Entonces... ¿estoy diciendo que PHPUnit ejecutará cada método público como una prueba? Vamos a averiguarlo! Crea un nuevo `public function itWorksTheSame(): void`

[[[ code('67cfb1a4ef') ]]]

Dentro vamos a `self::assertSame()` que 42 es igual a 42. `assertSame()` es muy similar a `assertEquals()` y veremos la diferencia en un minuto.

[[[ code('31924251b4') ]]]

Ahora, vuelve a tu terminal y ejecutamos de nuevo estas pruebas:

```terminal-silent
./vendor/bin/phpunit
```

¿Eh? PHPUnit sigue diciendo que sólo hay una prueba y una aserción. Pero dentro de nuestra clase de prueba, tenemos dos pruebas y dos aserciones. El problema es que PHPUnit sólo ejecuta los métodos públicos que llevan como prefijo la palabra `test`. Podrías poner la anotación `@test` encima del método, pero eso no es muy habitual. Así que evitemos ser raros, y cambiemos esto por`testItWorksTheSame()`.

[[[ code('0d3b2cd0c0') ]]]

Ahora, cuando ejecutamos la prueba

```terminal-silent
./vendor/bin/phpunit
```

¡PHPUnit ve 2 pruebas y 2 aserciones! ¡Shweeeet!

## Fallos en las pruebas 😱

¿Qué aspecto tiene cuando falla una prueba? ¡Averigüémoslo! Cambia nuestro `42` esperado por una cadena dentro de `testItWorks()`... y haz lo mismo dentro de `testItWorksTheSame()`. Sí, uno de ellos no funciona.

[[[ code('729d779ffd') ]]]

Esta vez, cuando lo probemos:

```terminal-silent
./vendor/bin/phpunit
```

¡Oh, no! ¡Un fallo!

> `DinosaurTest::testItWorksTheSame()` falló al afirmar que `42` es idéntico a `42`.

Así que... `assertEquals()` pasó, pero `assertSame()` falló. Eso es porque`assertEquals()` es el equivalente a hacer un if 42 `==` 42: utilizando el doble signo de igualdad. Pero `assertSame()` equivale a 42 `===` 42: con tres signos iguales.

Y como la cadena 42 no es triplemente igual al entero 42, esa prueba falla y PHPUnit nos grita.

Bien, ¡ya tenemos nuestras primeras pruebas! Aunque... probar que la respuesta a la vida el universo y todo es igual a la respuesta a la vida el universo y todo... no es muy interesante. Así que lo siguiente: vamos a escribir pruebas reales para la clase `Dinosaur`.
