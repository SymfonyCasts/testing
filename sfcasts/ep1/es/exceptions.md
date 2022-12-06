# Probando Excepciones Excepcionales

¿Recuerdas cuando veíamos esta excepción porque nuestra aplicación no entendía el estado de "hambre" de Maverick? Bueno, ya lo hemos arreglado, pero todavía tenemos que ocuparnos de un pequeño detalle. La próxima vez que GenLab nos lance una bola curva, como poner "Estado: Antsy" en un dino, `GithubService` debería lanzar una excepción clara que mencione la etiqueta.

## ¿Dónde podemos lanzar una excepción?

Para ello, vamos a hacer una pausa en TDD por un momento. En`getDinoStatusFromLabels()`, si una etiqueta tiene el prefijo "Estado:", lo cortamos, ponemos lo que queda en `$status`, y lo pasamos a `tryFrom()` para poder devolver un `HealthStatus`. Creo que éste sería un buen punto para lanzar una excepción si `tryFrom()` devuelve `null`.

Corta `HealthStatus::tryFrom($status)` del retorno y justo encima añade `$health =` y pega. Entonces `if (null === $health)` lo haremos `throw new \RuntimeException()` con el mensaje, `sprintf('%s is an unknown status label!')` pasando por `$status`. Abajo devuelve `$health`.

Pero, si el asunto no tiene una etiqueta de estado, todavía tenemos que devolver un`HealthStatus`. Así que arriba, sustituye `$status` por `$health = HealthStatus::HEALTHY`, porque a menos que GenLab añada una etiqueta de "Estado: Enfermo", todos nuestros dinos están sanos:

[[[ code('f5d532b43b') ]]]

## ¿Se lanza la excepción?

Ahora bien, normalmente escribimos pruebas para los valores de retorno. Pero también puedes escribir pruebas para verificar que se lanza la excepción correcta. Así que hagamos eso en `GithubServiceTest`. Hmm... Esta primera prueba tiene gran parte de la lógica que necesitaremos. Cópiala y pégala en la parte inferior. Cambia el nombre a `testExceptionThrownWithUnknownLabel` y elimina los argumentos. Dentro, quita la aserción dejando sólo la llamada a`$service->getHealthReport()`. Y en lugar de `$dinoName`, pasa a `Maverick`. 
Para `$mockResponse`, quita la margarita de `willReturn()` y cambia la etiqueta Mavericks de `Healthy` a `Drowsy`:

[[[ code('ebc5d64297') ]]]

Muy bien, vamos a intentarlo:

```terminal
./vendor/bin/phpunit
```

Y... Ouch! `GithubServiceTest` falló debido a una:

> Excepción de tiempo de ejecución: ¡Drowsy es una etiqueta de estado desconocida!

En realidad, esto es una buena noticia. Significa que `GithubService` está haciendo exactamente lo que queremos que haga. Pero, ¿cómo hacemos que esta prueba pase?

Justo antes de llamar a `getHealthReport()`, añade `$this->expectException()` pasando por `\RuntimeException::class`:

[[[ code('850d2f4772') ]]]

Vuelve a probar las pruebas:

```terminal-silent
./vendor/bin/phpunit
```

¡Impresionante salsa! ¡Estamos en verde!

## Evita las erratas en el mensaje de excepción

Pero... Si conseguimos estropear nuestro código por accidente, un `RuntimeException`podría venir de otro sitio. Para asegurarnos de que estamos comprobando la excepción correcta, di `$this->expectExceptionMessage('Drowsy is an unknown status label!')`:

[[[ code('4d4c4bdcac') ]]]

Luego vuelve a ejecutar nuestro corrector ortográfico:

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡AH! Hemos añadido otra aserción que está pasando y no tenemos ninguna errata en nuestro mensaje. ¡Guau!

## Prueba algo más que el mensaje de excepción

Junto con `expectExceptionMessage()`, PHPUnit tiene expectativas para el código de la excepción, el objeto, e incluso tiene la capacidad de pasar una regex para que coincida con el mensaje. Por cierto, todos estos métodos de `expect` son iguales a los de `assert`. 
La gran diferencia es que deben llamarse antes de la acción que estás probando y no después. Y al igual que las aserciones, si cambiamos el mensaje esperado de `Drowsy` a `Sleepy` y ejecutamos la prueba:

```terminal-silent
./vendor/bin/phpunit
```

Hmm... ¡Sí! Veremos que la prueba falla porque `Drowsy` no es `Sleepy`. Volvamos a cambiarlo en la prueba... ¡Y ahí lo tienes! ¡Las puertas de Dinotopia ya están abiertas y Bob es mucho más feliz ahora que nuestra aplicación se actualiza en tiempo real con GenLab! Para celebrarlo, hagamos nuestra vida un poco más fácil utilizando un toque de magia HttpClient para refactorizar nuestro test.
