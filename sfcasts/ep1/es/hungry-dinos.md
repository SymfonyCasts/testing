# Filtrar los dinos hambrientos

En lugar de ver nuestros dinos en el tablero, vemos un `TypeError` para`GithubService`:

> El valor devuelto debe ser del tipo `HealthStatus`, `null` devuelto

Eso no hace un gran trabajo para decirnos cuál es realmente el problema. Gracias al seguimiento de la pila, parece que está siendo causado por una etiqueta `Status: Hungry`. ¡Sí! En GitHub, parece que Dennis vuelve a tener hambre después de terminar su rutina diaria de ejercicios.

## Nuestro Enum también tiene hambre

Mirando `HealthStatus`, no tenemos un caso de dinos hambrientos:

[[[ code('f206435c2b') ]]]

Así que añade `case HUNGRY` que devuelve `Hungry`... y luego refresca el tablero.

[[[ code('6155f75ab1') ]]]

Y... ¡Ya! No hay más errores...

Pero, espera... Dice que `Dennis` no acepta visitas. No está enfermo, sólo tiene hambre. GenLab dijo que sólo los dinos enfermos no deberían estar en exposición. Además, ¿quién no quiere ver lo que le pasa a la cabra?

## Prueba Los dinos hambrientos pueden tener visitas

En `DinosaurTest`, tenemos que afirmar que los dinos hambrientos pueden recibir visitas. Hmm... Creo que podríamos utilizar `testIsNotAcceptingVisitorsIfSick()` para esto. Sí, eso es lo que haremos. A continuación, añade un `healthStatusProvider()` que devuelva`\Generator` y para el primer conjunto de datos `yield 'Sick dino is not accepting visitors'`. 
En el array di `HealthStatus::SICK`, y `false`. A continuación, `yield 'Hungry dino is accepting visitors'` con `[HealthStatus::HUNGRY, true]`:

[[[ code('6ad9401fac') ]]]

Arriba, añade la anotación `@dataProvider` para que podamos utilizar `healthStatusProvider()`. Ya que estamos aquí, cambia el nombre del método a `testIsAcceptingVisitorsBasedOnHealthStatus` y añade los argumentos `HealthStatus $healthStatus` y `bool $expectedVisitorStatus`:

[[[ code('62a155f67d') ]]]

Dentro pon la salud con `$healthStatus` y luego sustituye `assertFalse()` por`assertSame($expectedStatus)` es idéntico a `$dino->isAcceptingVisitors()`:

[[[ code('2180c8cad5') ]]]

¡Uf, eso ha sido mucho trabajo!

## Pruebas de filtrado

Veamos si ha funcionado.. Ejecuta:

```terminal
./vendor/bin/phpunit --filter testIsAcceptingVisitorsBasedOnHealthStatus
```

¿Ves lo que he hecho? Para centrarnos sólo en esta prueba, podemos añadir el conjunto `--filter` al nombre completo o parcial de una clase de prueba, de un método o de cualquier otra cosa. Esto resulta muy útil cuando tienes un conjunto de pruebas grande y sólo quieres ejecutar una o unas pocas pruebas.

En cualquier caso, el dino hambriento no acepta a los visitantes y falla:

> Fallo al afirmar que falso es verdadero.

Mirando `Dinosaur::isAcceptingVisitors()`, para tener en cuenta a los dinos hambrientos, tenemos que devolver `$this->health` no es igual a `HealthStatus::SICK`:

[[[ code('326a577dd2') ]]]

Veamos qué ocurre cuando lo ejecutamos:

```terminal
./vendor/bin/phpunit --filter "Hungry dino is accepting visitors"
```

Y... ¡boom! Nuestra prueba del dino hambriento pasa ahora, ¡ja! Sí, también podemos utilizar claves de proveedores de datos con la bandera `filter`. Pero para asegurarnos de que no impedimos que los dinos sanos tengan visitas, ejecuta

```terminal
./vendor/bin/phpunit
```

Um... ¡Sí! Todos los puntos y ningún error. ¡Genial! No hemos destrozado el parque. Echa un vistazo al tablero de control, actualízalo y ¡ya! Dennis puede volver a comer con los clientes del parque. Aunque creo que deberíamos ser proactivos y lanzar una excepción más clara por si alguna vez vemos alguna etiqueta de estado futura que no conozcamos. Hagamos eso a continuación.