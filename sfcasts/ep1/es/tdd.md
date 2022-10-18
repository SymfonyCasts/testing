# TDD - Desarrollo dirigido por pruebas

Muy bien. Así que uno de los problemas es que cuando Bob, nuestro guardabosques, ve el tamaño de los dinosaurios... no puede recordar si están en metros... o en centímetros... lo que supone una gran diferencia cuando se entra en una jaula.

Una forma mejor sería utilizar simplemente palabras como pequeño, mediano o grande. Así que... ¡hagamos eso!

## ¿Qué es el TDD?

Para añadir esta característica, vamos a utilizar una filosofía llamada Desarrollo Dirigido por Pruebas o TDD. TDD es básicamente una palabra de moda que describe un proceso de 4 pasos para escribir tus pruebas primero.

Paso 1: Escribe una prueba para la función. Paso 2: Ejecuta tu prueba y observa cómo falla... ¡ya que aún no hemos creado esa función! Paso 3: Escribe el menor código posible para que nuestra prueba pase. Y Paso 4: Ahora que pasa, refactoriza tu código si es necesario para hacerlo más impresionante

Así que, para obtener el texto Pequeño, Mediano o Grande, creo que deberíamos añadir un nuevo método`getSizeDescription()` a nuestra clase `Dinosaur`. Pero, recuerda, vamos a hacer esto a la manera de TDD, donde el paso 1 es escribir una prueba para ese método... aunque todavía no exista. Sí, ya sé que es raro... ¡pero es un poco genial!

## Paso 1: Escribir una prueba para la función

Añade `public function` y probemos primero que un dinosaurio de más de 10 metros es grande. Dentro, di `$dino = new Dinosaur()`, dale un nombre, vamos a utilizar de nuevo a Big Eaty, ya que es un tipo genial, y establece su longitud en 10.

Entonces, `assertSame()` que `Large` será idéntico a `$dino->getSizeDescription()`. Para nuestro mensaje de fallo, utilizaremos `This is supposed to be a Large Dinosaur`. Sí, estamos probando literalmente un método que aún no existe. Eso es TDD.

## Paso 2: Ejecuta la prueba y observa cómo falla

Bien, el paso 1 está hecho. El paso 2 es ejecutar nuestra prueba y comprobar que falla. Abre un terminal y ejecuta `./vendor/bin/phpunit`.

```terminal
./vendor/bin/phpunit
```

Y... ¡genial! 2 pruebas, 4 aserciones y 1 error. Nuestra nueva prueba ha fallado porque, por supuesto, ¡hemos llamado a un método indefinido! Ya sabíamos que esto pasaría. Hm... ¿Te has dado cuenta de que nuestro mensaje "se supone que esto es un gran dinosaurio" no aparece aquí? Te explicaré por qué en un momento.

## Paso 3: Escribir código sencillo para que pase

Es la hora del paso 3 de TDD: escribir código sencillo para hacer que esta prueba pase. Esta parte, tomada al pie de la letra, puede ser un poco divertida. Observa: en nuestra clase `Dinosaur` añade un nuevo `public function getSizeDescription()`que devolverá un `string`. Dentro... `return 'Large'`. Sí, ¡ya está! Vuelve a tu terminal y vuelve a ejecutar las pruebas.

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... Impresionante: ¡pasan! Bueno... por supuesto que la prueba ha pasado: ¡hemos codificado el resultado que queríamos! Pero, técnicamente, eso es lo que dice TDD: escribe la menor cantidad de código posible para que tu prueba pase. Si tu método es demasiado simple después de hacer esto, significa que te faltan más pruebas -como para dinosaurios pequeños o medianos- que te obligarían a mejorar el método. Lo veremos en un momento.

Pero somos un poco más realistas. Digamos:`if ($this->length >= 10) {`, luego `return 'Large'`. Ejecuta las pruebas una vez más para asegurarte de que siguen pasando:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... ¡sí! ¡Seguimos siendo aptos!

A continuación, vamos a terminar este método a la manera de TDD: escribiendo primero más pruebas para las características que faltan. Luego pasaremos al último paso -y el más divertido- de TDD: ¡la refactorización!
