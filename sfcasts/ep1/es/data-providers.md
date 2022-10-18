# Proveedores de datos

Tratamos nuestro código fuente como un ciudadano de primera clase. Eso significa, entre otras cosas, que evitamos la duplicación. ¿Por qué no hacer lo mismo con nuestras pruebas? Nuestras tres pruebas para el tamaño son... repetitivas. Prueban lo mismo sólo que con una entrada ligeramente diferente y una afirmación distinta. ¿Hay alguna forma de mejorar esto? Absolutamente: gracias a los Proveedores de Datos de PHPUnit.

## Refactorizar nuestras pruebas

Desplázate al final de `DinosaurTest` y añade`public function sizeDescriptionProvider()`. Dentro, `yield` un array con `[10, 'Large']`, luego `yield [5, 'Medium']`, y finalmente `yield [4, 'Small']`. Yield no es más que una forma elegante de devolver arrays utilizando la función Generador incorporada de PHP. Como verás en un minuto, estos valores -como `10` y `large` - se convertirán en argumentos de nuestra prueba.

Muy bien, arriba en nuestro método de prueba, añade un argumento `int $length` y luego`string $expectedSize`. Ahora, en lugar de que la longitud de Big Eaty sea `10`, utiliza`$length`. Y para nuestra afirmación, utiliza `$expectedSize` en lugar de `Large`. Ya no necesitamos las pruebas mediana y pequeña, así que podemos eliminarlas.

De acuerdo Vuelve a tu terminal y ejecuta nuestras pruebas:

```terminal
./vendor/bin/phpunit --testdox
```

Uh oh... ¡Nuestra prueba está fallando porque! Dice:

> ArgumentCountError - Se han proporcionado muy pocos argumentos. se han pasado 0 y se esperan exactamente 2.

## Dile a nuestra prueba que utilice el proveedor de datos

Oops, nunca le dijimos a nuestro método de prueba que utilizara el proveedor de datos. Vuelve a nuestro test y añade un DocBlock con `@dataProvider sizeDescriptionProvider`. Cuando se publique PHPUnit 10, podremos utilizar un elegante atributo `#[DataProvider]` en lugar de esta anotación.

¡Vuelve al terminal! Ejecuta de nuevo las pruebas:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... ¡Sí! ¡Nuestras pruebas pasan!

## Claves de mensajes en lugar de argumentos

En la salida, vemos que cada prueba se ejecutó con los conjuntos de datos 0, 1 y 2. Son las matrices del proveedor de datos. Podemos arreglar esto un poco... porque no va a ser muy útil después si PHPUnit nos dice que el conjunto de datos `2` ha fallado. ¿Cuál es ese?

Vuelve a nuestra prueba y, aquí abajo, después de la primera sentencia `yield`, añade la clave de mensaje `'10 Meter Large Dino' =>`. Copia y pega esto para nuestro dino mediano con `5`en lugar de `10` y esto tiene que ser `Medium`. Haz lo mismo para nuestro dino pequeño con `4` y `Small`.

De vuelta a nuestro terminal, veamos ahora nuestras pruebas:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... ¡Genial los frijoles! Ahora tenemos

> El dino de 10 metros o más es grande con Dino grande de 10 metros

Esto tiene mucho mejor aspecto que ver el conjunto de datos 0... aunque tenemos que arreglar una cosa más. El nombre del método de prueba ya no tiene sentido. Cámbialo por `testDinoHasCorrectSizeDescriptionFromLength()`.

Y, mirando nuestra afirmación, el argumento del mensaje ya no es muy útil... así que eliminémoslo.
# ¡Tipos de retorno por todas partes!

Por último, aunque no es necesario... Podemos utilizar `array` o`\Generator` como tipo de retorno del proveedor de datos. Optemos por`\Generator`; al fin y al cabo, puede que algún día los necesitemos para las vallas del parque...

Para asegurarnos de que esto no rompe nada, prueba las pruebas una vez más:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Ummm... ¡Impresionante! ¡Cheques verdes por todas partes!

Y ahí lo tienes, con un poco de cariño, nuestras pruebas están ahora bien ordenadas... A continuación, vamos a averiguar cómo podemos obtener el estado de salud de nuestro Dino desde GitHub y utilizarlo en nuestra aplicación...
