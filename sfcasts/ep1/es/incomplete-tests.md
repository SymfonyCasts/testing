# Pruebas incompletas y dinos bailarines

Bob nos acaba de decir que necesita mostrar qué dinos están aceptando comida en nuestra aplicación... Quiero decir que aceptan visitas. GenLab tiene unos protocolos estrictos: los visitantes del parque pueden visitar a los dinos sanos... pero si están enfermos, no se admiten visitas. Para ayudar a mostrar esto, necesitamos almacenar el estado de salud de cada dino y tener una forma fácil de averiguar si esto significa que aceptan visitas o no...

## Hagamos una prueba...

Empecemos por añadir un método - `isAcceptingVisitors()` a `Dinosaur`. Pero, lo haremos a la manera de TDD escribiendo primero el test. En `DinosaurTest` añade`public function testIsAcceptingVisitorsByDefault()`. Dentro, `$dino = new Dinosaur()`y llamémosle `Dennis`.

Si simplemente instanciamos un `Dinosaur` y no hacemos nada más, la política de GenLab establece que está bien visitar ese Dinosaurio. Así que `assertTrue()` que Dennis`isAcceptingVisitors()`.

Debajo de esta prueba, añade otra llamada `testIsNotAcceptingVisitorsIfSick()`. Y por ahora, seamos perezosos y digamos simplemente `$this->markTestIncomplete()`.

Bien, probemos las pruebas:

```terminal
./vendor/bin/phpunit --testdox
```

Y... ¡no hay sorpresa! Nuestra primera prueba nueva falla:

> Llamada a un método indefinido.

Pero nuestra siguiente prueba tiene este extraño círculo `∅` porque hemos marcado la prueba como incompleta. A veces utilizo esto cuando sé que tengo que escribir una prueba... pero aún no estoy preparado para hacerlo. PHPUnit también tiene un método `markSkipped()` que puede utilizarse para omitir pruebas bajo ciertas condiciones, como si una prueba debe ejecutarse en PHP 8.1.

## ¿Aceptan visitas?

En cualquier caso, volvamos a la codificación... En nuestra clase `Dinosaur`, añadimos un método`isAcceptingVisitors()` que devuelve un `bool`, y dentro devolveremos `true`.

Veamos qué ocurre ahora al ejecutar nuestras pruebas...

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... Sí! `Is accepting visitors by default`... ¡ahora pasa! Todavía tenemos una prueba incompleta como recordatorio, pero no está provocando que todo nuestro conjunto de pruebas falle.

## Dinos enfermos: ¡no te acerques!

Terminemos ahora. Si echamos un vistazo a las cuestiones en GitHub, GenLab está utilizando etiquetas para identificar la "salud" de cada dino: "Enfermo" frente a "Sano". Muy pronto, vamos a leer estas etiquetas y a utilizarlas en nuestra aplicación. Para prepararnos para ello, necesitamos una forma de almacenar la salud actual de cada `Dinosaur`.

Dentro de la prueba, elimina `markAsIncomplete()` y crea un `$dino` llamado`Bumpy`... es un triceratops. Ahora llama a `$dino->setHealth('Sick')` y luego a `assertFalse()`que es un Bumpy `isAcceptingVisitors()`. Está malhumorado cuando está enfermo.

Pero, no nos sorprende, PHPStorm nos dice

> Método setHealth() no encontrado dentro de Dinosaurio

Así que... dejemos de ejecutar la prueba y vayamos directamente a `Dinosaur` para añadir un método `setHealth()` que acepte un argumento `string $health`... y devuelva `void`. Dentro, digamos `$this->health = $health`... y luego arriba, añade una propiedad `private string $health` que por defecto sea `Healthy`.

¡Genial! Ahora sólo tenemos que actualizar `isAcceptingVisitors()` para que devuelva`$this->health === $healthy` en lugar de `true`.

Cruza los dedos para que nuestras pruebas pasen ahora...

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... ¡Misión cumplida!

## Los Enums son geniales para las etiquetas de salud

Ahora que las pruebas están pasando, estoy pensando que deberíamos refactorizar el método `setHealth()` para que sólo permita`Sick` o `Healthy`... y no algo como `Dancing`... Dentro de `src/`, crea un nuevo directorio `Enum/`y luego una nueva clase: `HealthStatus`. Para la plantilla, selecciona `Enum` y haz clic en `OK`. Necesitamos que `HealthStatus` esté respaldado por un `: string`... Y nuestro primer `case HEALTHY` devolverá `Healthy`, luego `case SICK` devolverá`Sick`.

En la propiedad `Dinosaur::$health`, por defecto, `HealthStatus::HEALTHY`. Y cambia el tipo de propiedad a `HealthStatus`. Abajo en `isAcceptingVisitors()`, devuelve true si `$this->health === HealthStatus::HEALTHY`. Abajo en `setHealth()`, cambia el tipo de argumento de `string` a `HealthStatus`.

Lo último que hay que hacer es utilizar `HealthStatus::SICK` en nuestra prueba.

¡A ver si rompemos algo!

```terminal-silent
./vendor/bin/phpunit --testdox
```

Y... ¡Ya! No hemos roto nada... Sólo estoy un poco sorprendido.

## Mostrar qué exposiciones están abiertas

Para cumplir los deseos de Bob, abre la plantilla `main/index.html.twig` y añade un título `Accepting Visitors` a la tabla. En el bucle dino, crea un nuevo `<td>` y llama a `dino.acceptingVisitors`. Mostraremos`Yes` si es verdadero o `No` si obtenemos falso.

En el navegador, actualiza la página de estado... Y... ¡WooHoo! Todos nuestros dinos están aceptando visitas... ¡porque no hemos puesto ninguno como "enfermo" en nuestro código!

Pero... Ya sabemos, por haber mirado antes en GitHub, que algunos de nuestros dinos están enfermos. Lo siguiente: vamos a utilizar la API de GitHub para leer las etiquetas de nuestro repositorio de GitHub y establecer la salud real de cada `Dinosaur` para que nuestro panel se actualice en tiempo real.
