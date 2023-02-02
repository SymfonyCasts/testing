# La burla: Objetos simulados

Nuestras pruebas están pasando, los dinos se pasean, ¡y la vida es genial! Pero... pensemos en esto un segundo. En `GithubService`, cuando probamos `getHealthReport()`, podemos controlar el `$response` que nos devuelve `request()` mediante un stub. Eso está muy bien, pero también estaría bien asegurarse de que el servicio sólo llama a GitHub una vez y que utiliza el método HTTP correcto con la URL correcta. ¿Podemos hacerlo? Por supuesto

## Esperar que se llame a un método

En `GithubServiceTest` donde configuramos el `$mockHttpClient`, añadimos `->expects()`, y pasamos `self::once()`.

[[[ code('d4cd801092') ]]]

En el terminal, ejecuta nuestras pruebas...

```terminal
./vendor/bin/phpunit
```

## Esperar argumentos específicos

Y... ¡Impresionante! Acabamos de añadir una aserción a nuestro cliente simulado que requiere que el método`request` se llame exactamente una vez. Vayamos un paso más allá y añadamos `->with()` pasando `GET`... y luego pegaré la URL de la API de GitHub.

[[[ code('47a458cb01') ]]]

Vuelve a probar las pruebas...

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡Huh! Tenemos 2 fallos:

> Fallo al afirmar que dos cadenas son iguales

Hmm... ¡Ah Ha! Mis habilidades para copiar y pegar son un poco débiles. Me faltó `/issue` al final de la URL. Añade eso 

[[[ code('4d846bbca3') ]]]

Veamos si ese era el truco:

```terminal-silent
./vendor/bin/phpunit
```

Umm... ¡Sí! Estamos en verde todo el día. Pero lo mejor de todo es que las pruebas confirman que estamos utilizando la URL y el método HTTP correctos cuando llamamos a GitHub.

Pero... ¿Y si quisiéramos llamar a GitHub más de una vez? O... ¿queríamos afirmar que no se ha llamado en absoluto? PHPUnit nos tiene cubiertos. Hay un puñado de otros métodos que podemos llamar. Por ejemplo, cambia `once()` por `never()`.

Y observa lo que ocurre ahora:

```terminal-silent
./vendor/bin/phpunit
```

Hmm... Sí, tenemos dos fallos porque

> no se esperaba llamar a request().

¡Eso es realmente ingenioso! Vuelve a cambiar el `expects()` a `once()` y, para asegurarnos de que no hemos roto nada, vuelve a ejecutar las pruebas.

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡Impresionante!

## Aplicando cuidadosamente las aserciones

Podríamos llamar a `expects()` en nuestro `$mockResponse` para asegurarnos de que `toArray()`se llama exactamente una vez en nuestro servicio. Pero, ¿realmente nos importa? Si no se llama en absoluto, nuestra prueba fallaría sin duda. Y si se llama dos veces, ¡no pasa nada! Utilizar `->expects()` y `->with()` son formas estupendas de añadir afirmaciones adicionales... cuando las necesites. Pero no es necesario microgestionar cuántas veces se llama a algo o sus argumentos si eso no es tan importante.

## Utilizar GitHubService en nuestra aplicación

Ahora que `GithubService` está totalmente probado, ¡podemos celebrarlo utilizándolo para manejar nuestro panel de control! En `MainController::index()`, añade un argumento 
`GithubService $github` para autoconectar el nuevo servicio.

[[[ code('d3adeb62ba') ]]]

A continuación, justo debajo de la matriz `$dinos`, `foreach()` sobre `$dinos as $dino` y, dentro digamos de `$dino->setHealth()` pasando por `$github->getHealthReport($dino->getName())`.

[[[ code('27580fbe41') ]]]

Al navegador y actualiza...

Y... ¿Qué?

> `getDinoStatusFromLabels()` debe ser `HealthStatus`, `null` devuelto

¿Qué está pasando aquí? Por cierto, el hecho de que nuestra prueba unitaria pase pero nuestra página falle puede ocurrir a veces y, en un futuro tutorial, escribiremos una prueba funcional para asegurarnos de que esta página realmente se carga.

El error no es muy evidente, pero creo que uno de nuestros dinos tiene una etiqueta de estado que desconocemos. Volvamos a echar un vistazo a los problemas en GitHub y... ¡HA! "Dennis" vuelve a causar problemas. Al parecer, está un poco hambriento...

En nuestro enum `HealthStatus`, no tenemos un caso para las etiquetas de estado `Hungry`. Imagínate. ¿Es un dinosaurio hambriento que acepta visitas? No lo sé, supongo que depende de si le preguntas al visitante o al dino. En cualquier caso, `Hungry` no es un estado que esperemos. Así que, a continuación, vamos a lanzar una excepción clara si nos encontramos con un estado desconocido y a probar esa excepción.
