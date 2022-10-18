# TDD Parte 2: Terminar y refactorizar

Antes de pasar al último paso de TDD, creo que tenemos que añadir un par de pruebas más de descripción del tamaño de los dinosaurios medianos y pequeños 

## Unas cuantas pruebas más

En nuestro `DinosaurTest::class` copia nuestro método`testDino10MetersOrGreaterIsLarge` y lo renombra a`testDinoBetween5And9MetersIsMedium()`. Dentro, cambia el`length` de nuestro `$dino` de `10` a `5`, utiliza `Medium` para el valor esperado, y actualiza también el mensaje a `Medium`. Por último, pega de nuevo el método para nuestra prueba del dinosaurio pequeño, utilizando el nombre `testDinoUnder5MetersIsSmall()`. Establece la longitud en `4`, afirma que `Small` es idéntico a `getSizeDescription()` y actualiza también el mensaje.

De nuevo en nuestro terminal, ejecuta las pruebas de nuevo:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... ¡fallan! Pero no porque nuestro método devuelva un resultado erróneo. Están fallando debido a un error de tipo en `getSizeDescription()`:

> El valor devuelto debe ser de tipo cadena y no se devuelve ninguna.

¿Recuerdas que antes ejecutamos nuestra prueba del dinosaurio grande antes de escribir el método y no vimos nuestro mensaje "se supone que esto es un dinosaurio grande"? Pues aquí tampoco lo vemos... Eso es porque PHP lanzó un error... y por eso el mensaje `getSizeDescription()` explota antes de que PHPUnit pueda ejecutar el método`assertSame()`. No es un gran problema y aún podemos utilizar el seguimiento de la pila para ver exactamente dónde han ido mal las cosas.

Muy bien, volvemos a la clase `Dinosaur`. Vamos a arreglar estas pruebas añadiendo`if ($this->length)` es menor que `5`, `return 'Small'`. Y`if ($this->length)` es menor que `10`, `return 'Medium'`

Volvemos a nuestro terminal y ejecutamos de nuevo la prueba:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... bien, bien, bien... están pasando.

## Paso 4: Refactorización

Así que pasemos al último paso de TDD... ¡y uno muy divertido! Refactorizar nuestro código.

Mirando nuestro método `getSizeDescription()`, creo que podemos limpiarlo un poco. Y la gran noticia es que, como hemos cubierto nuestro método con pruebas, si estropeamos algo durante la refactorización, ¡las pruebas nos lo dirán! También significa que antes no teníamos que preocuparnos de escribir un código perfecto, sólo teníamos que hacer que nuestras pruebas pasaran. Ahora podemos mejorar las cosas...

Cambiemos esta condición intermedia a `if ($this->length)` es mayor o igual que `5`, devuelve `Medium`. Podemos eliminar por completo esta última condición y devolver simplemente `Small`.

¡Eso me gusta! Para ver si hemos metido la pata, vuelve al terminal y ejecuta de nuevo nuestras pruebas.

```terminal-silent
./vendor/bin/phpunit --tesdox
```

Y... ¡lo hemos conseguido! Eso es TDD: escribir la prueba, ver que la prueba falla, escribir código sencillo para ver que la prueba pasa, y luego refactorizar nuestro código. Aclarar y repetir.

TDD es interesante porque, al escribir primero nuestra prueba, nos obliga a pensar exactamente en cómo debe funcionar una función... En lugar de escribir código a ciegas y ver qué sale. También nos ayuda a centrarnos en lo que tenemos que codificar... Sin hacer las cosas demasiado rebuscadas. Sí, yo también soy culpable de eso... Consigue que tus pruebas pasen, luego refactoriza... No hace falta nada más. 

## Utilizar la descripción del tamaño en nuestro controlador

Y ahora que tenemos nuestro nuevo y elegante método -construido mediante los poderes de TDD- ¡celebremos su uso en el sitio!

Cierra nuestro terminal y ve a nuestra plantilla: `templates/main/index.html.twig`. En lugar de mostrar los dinos con `dino.length`, cambia esto por`dino.sizeDescription`. Guárdalo, vuelve a nuestro navegador y... actualiza. Impresionante. Tenemos grande, mediano y pequeño para el tamaño del dinosaurio en lugar de un número. ¡No hay manera de que Bob vuelva a meterse accidentalmente en el recinto del T-Rex!

Acabamos de utilizar TDD para hacer nuestra aplicación un poco más amigable para los humanos. A continuación, utilizaremos algunos de los principios de TDD que hemos aprendido aquí para limpiar nuestras pruebas con los proveedores de datos de PHPUnit