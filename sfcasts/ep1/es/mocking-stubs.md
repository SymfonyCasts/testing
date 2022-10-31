# Burlas: Stubs

Echemos un vistazo rápido a `GithubService` para ver qué hace exactamente. En primer lugar, el constructor requiere un objeto `HttpClientInterface` que utilizamos para llamar a GitHub. A cambio, obtenemos un `ResponseInterface` que tiene un array de issues para el repositorio `dino-park`. A continuación, llamamos al método `toArray()` en la respuesta, e iteramos sobre cada incidencia para ver si el título contiene el`$dinosaurName`, de modo que podamos obtener su etiqueta de estado.

[[[ code('8232c3dd4b') ]]]

Para que nuestras pruebas pasen, tenemos que enseñar a nuestro falso `httpClient` que cuando llamemos al método `request()`, debe devolver un objeto `ResponseInterface` que contenga datos que nosotros controlamos. Así que... vamos a hacerlo.

## Entrenar al simulacro sobre lo que debe devolver

Justo después de `$mockHttpClient`, di `$mockResponse = $this->createMock()` utilizando`ResponseInterface::class` para el nombre de la clase. Abajo en `$mockHttpClient`, llamamos,`->method('request')` que `willReturn($mockResponse)`. Esto le dice a nuestro cliente simulado que, oye, cada vez que llamemos al método `request()` en nuestro simulacro, tiene que devolver este `$mockResponse`.

[[[ code('db50103536') ]]]

¡¡¡¡¡¡¡¡¡!!!!!!!!! Este capítulo es lo suficientemente corto, podríamos? ejecutar las pruebas !!!!!!!!! Podríamos ejecutar nuestras pruebas ahora, pero fallarían. Hemos enseñado a nuestro cliente simulado lo que debe devolver cuando llamamos al método `request()`. Pero ahora tenemos que enseñar a nuestro `$mockResponse` lo que debe hacer cuando llamemos al método `toArray()`. Así que, justo encima, vamos a enseñarle al `$mockResponse` que cuando llamemos,`method('toArray')` y él `willReturn()` un array de temas. Porque eso es lo que GitHub devuelve cuando llamamos a la API.

[[[ code('c9bc8019ab') ]]]

Para cada incidencia, GitHub nos da el "título" de la incidencia y, entre otras cosas, un array de "etiquetas". Así que imitemos a GitHub y hagamos que esta matriz incluya una incidencia que tenga `'title' => 'Daisy'`. !!!!!!!!!! Vuelve a los temas de Github para "ver" qué dinos podemos utilizar.....!!!!!!!!!!!!!!!!!!! Y, para la prueba, fingiremos que se ha torcido el tobillo, así que añade un conjunto de claves `labels` a un array, que incluya `'name' => 'Status: Sick'`

¡¡¡¡¡¡¡¡¡¡!!!!!!!!!! Vuelve a los temas de Github para "rodear" a Maverick!!!!!!!!!!!!!!!!!!! Creemos también un dino sano para poder afirmar que nuestro análisis sintáctico también lo comprueba correctamente. Copia este tema y pégalo a continuación. Cambia `Daisy` por `Maverick`y pon su etiqueta en `Status: Healthy`.

[[[ code('6ae7eb1f96') ]]]

Perfecto Nuestras afirmaciones ya esperan que `Daisy` esté enfermo y que `Maverick`esté sano. Así que, si nuestras pruebas pasan, significa que toda nuestra lógica de análisis de etiquetas es correcta.

Cruzando los dedos, vamos a probarlo:

```terminal
./vendor/bin/phpunit
```

Y... ¡Impresionante! ¡Pasan las pruebas! Y lo mejor de todo, ¡ya no llamamos a la API de GitHub cuando ejecutamos nuestras pruebas! Imagina el pánico que causaríamos si tuviéramos que bloquear el parque porque nuestras pruebas fallaran porque la api estuviera desconectada... o simplemente porque alguien cambiara las etiquetas en GitHub, Ya... Yo tampoco quiero ese dolor de cabeza...

## ¿Stubs? ¿Mocks?

¿Recuerdas cuando hablábamos de los diferentes nombres de los mocks? Pues bien, tanto`mockResponse` como `mockHttpClient` se llaman ahora oficialmente stubs... Es una forma elegante de decir objetos falsos en los que opcionalmente tomamos el control de los valores que devuelven. Eso es exactamente lo que estamos haciendo con el método `willReturn()`. De nuevo, la terminología no es demasiado importante, pero ahí tienes. Estos son stubs. Y sí, cada vez que enseño esto, tengo que buscar estos términos para recordar qué significan exactamente.

A continuación, vamos a convertir nuestros "stubs" en auténticos objetos simulados, probando también los datos que se pasan al simulacro.
