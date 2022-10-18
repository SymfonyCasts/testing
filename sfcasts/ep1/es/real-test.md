# Probar los métodos de la clase

Como recordatorio, la clase es actualmente bastante sencilla: pasamos algunos datos al constructor... y luego podemos leer esos datos mediante algunos métodos. En lugar de "esperar" que todo esto funcione, ¡vamos a asegurarnos de que nuestra clase `Dinosaur` está realmente libre de errores con algunas pruebas!

En `DinosaurTest`, elimina estas dos pruebas y sustitúyelas por`public function testCanGetAndSetData()`. Dentro... vamos a jugar literalmente con el objeto instanciándolo y probando algunos métodos.

Así, `$dino = new Dinosaur()`.... y pasa algunos datos. Para el nombre, eh - vamos a usar `Big Eaty`: es nuestro residente `Tyrannosaurus` que resulta tener `15` metros de longitud. Y Big Eaty vive actualmente en `Paddock A`. Ahora que tenemos nuestro objeto `Dinosaur`, podemos escribir algunas afirmaciones `self::assertSame()`que `Big Eaty` es idéntico a `$dino->getName()`, `assertSame()` que `Tyrannosaurus` es idéntico a `$dino->getGenus()`, `assertSame()` que `15` es idéntico a `getLength()`, y por último, pero no menos importante, `assertSame()` que Big Eaty sigue en `Paddock A` cuando llamamos a `getEnclosure()`... y no anda suelto por la isla.

¡Vamos a probarlo! Vuelve a tu terminal y ejecuta

```terminal
./vendor/bin/phpunit
```

## ¿Debo probar ese método?

Y... ¡SÍ! Tenemos una prueba con cuatro aserciones. Pero... volviendo a mirar nuestra clase`Dinosaur`, en realidad no estamos haciendo gran cosa aquí. Estamos requiriendo unos cuantos argumentos en nuestro constructor, estableciéndolos en propiedades, y exponiendo esas propiedades con métodos getter. Nada complejo en absoluto. Así que, aunque nuestro `DinosaurTest` es perfectamente aceptable, no es el más útil, porque las probabilidades de que estos métodos tengan un fallo son bajas. Y además, si hubiera un fallo, probablemente lo detectaríamos al probar otras partes de nuestra aplicación que los llaman.

La cuestión es que, aunque puedes hacer lo que quieras, probablemente ésta no sea una prueba que yo escribiría en un proyecto real. Mi regla general es: si un método asusta, merece una prueba. Y si no estás seguro, siempre es seguro añadir una prueba.

## El orden de los argumentos del método assert()

Por supuesto: el orden de los argumentos de los métodos assert es importante.

El primer argumento debe ser siempre el esperado -como`Big Eaty` - y el segundo debe ser el valor real que obtenemos -como `$dino->getName()`-. Esto no es un gran problema para las aserciones que estamos utilizando aquí... aunque si lo inviertes, el mensaje de error será confuso.

Es más importante para otras afirmaciones, como `assertGreaterThan()`... que podemos utilizar para comprobar que `$dino->getLength()` es mayor que `10`.

Cuando probamos esto:

```terminal-silent
./vendor/bin/phpunit
```

¡Si! Un fallo en `DinosaurTest`:

> Fallo al afirmar que 10 es mayor que 15.

¡Ups! Mirando hacia atrás en nuestro `DinosaurTest`, esta prueba falló porque pasamos primero el valor real en lugar de nuestro valor esperado.

## El mensaje de afirmación

Antes de limpiar esto, vamos a pasar un tercer argumento opcional:

> Se supone que Dino es mayor de 10 metros.

Para ver qué hace esto, vuelve a ejecutar las pruebas:

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡qué bien! La prueba sigue fallando, pero ahora también vemos el mensaje, que a veces puede ayudarnos a entender más rápidamente qué ha fallado y por qué. Todos los métodos de afirmación tienen este argumento "mensaje" y me gusta utilizarlo cuando una prueba compleja podría necesitar un poco más de explicación.

## Convenciones de nomenclatura

Quiero volver al nombre de nuestro primer método de prueba: `testCanGetAndSetData`. En el PHP estándar, intentamos crear métodos que sean descriptivos... pero no necesariamente superlargos... ya que tendremos que llamarlos en nuestro código. Buenos ejemplos son `getGenus()` y `getName()` en la clase `Dinosaur`. Pero cuando se trata de hacer pruebas, mantener las cosas cortas no es una ventaja.

Compruébalo: cambia el nombre de nuestro método de prueba a `testDinosaur()`... y vuelve a ejecutar nuestras pruebas.

```terminal-silent
vendor/bin/phpunit
```

PHPUnit nos dice que `DinosaurTest::testDinosaur()` falló al afirmar que 10 es mayor que 15. Vale... ¿pero qué estamos probando? El nombre del método - `testDinosaur()` - no nos dice nada... ¡sobre todo porque estamos dentro de una clase llamada `DinosaurTest`! Sí, lo entiendo: ¡estamos probando dinosaurios!

El nombre de cada método de prueba es tu oportunidad de describir exactamente lo que estás probando, e incluso a veces el porqué. Vuelve a cambiar el nombre de la prueba a `testCanGetAndSetData()`, que explica mucho mejor el propósito de esta prueba. Observa que casi se lee como una frase. ¡Es estupendo! Y algunas personas incluso van más allá incluyendo la palabra "eso", como `testItCanGetAndSetData()`. La cuestión es: sé descriptivo, no hay inconveniente en que los nombres de las pruebas sean largos.

## Salida descriptiva de Testdox

Permíteme mostrarte otro truco genial con PHPUnit. Vuelve al terminal y ejecuta de nuevo nuestras pruebas... pero esta vez pasa una bandera `--testdox`:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... ¡Wooah! La salida es diferente. Lo más importante es que ha convertido el nombre del método en una frase legible para los humanos... lo cual es menor, pero genial.

Por cierto, el ejecutable `phpunit` tiene muchas más opciones y argumentos disponibles. Ejecuta PHPUnit con la bandera `help` para verlos.

```terminal-silent
./vendor/bin/phpunit --help
```

Hablaremos de las más útiles a lo largo del tutorial.

Antes de continuar, tenemos que limpiar nuestra prueba. Elimina esta aserción `testGreaterThan()`... y vuelve a ejecutar nuestras pruebas:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... ¡SÍ! Todas nuestras pruebas pasan. A continuación, vamos a ponernos filosóficos y a echar un vistazo al Desarrollo Dirigido por Pruebas o, simplemente, a TDD.
