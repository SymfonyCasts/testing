# Mocking: Stubs

Echemos un vistazo rápido a `GithubService` para ver exactamente lo que hace. En primer lugar, el constructor requiere un objeto `HttpClientInterface` que utilizamos para llamar a GitHub. A cambio, obtenemos un `ResponseInterface` que contiene una matriz de incidencias del repositorio `dino-park`. A continuación, llamamos al método `toArray()` en la respuesta, e iteramos sobre cada incidencia para ver si el título contiene el`$dinosaurName`, de modo que podamos obtener su etiqueta de estado.

[[[ code('8232c3dd4b') ]]]

Para que nuestras pruebas pasen, tenemos que enseñar a nuestro falso `httpClient` que cuando llamemos al método `request()`, debe devolver un objeto `ResponseInterface` que contenga datos que nosotros controlamos. Así que... vamos a hacerlo.

## Enseñar al Mock qué debe devolver

Justo después de `$mockHttpClient`, di `$mockResponse = $this->createMock()` utilizando`ResponseInterface::class` para el nombre de la clase. Abajo en `$mockHttpClient`, llama,`->method('request')` que `willReturn($mockResponse)`. Esto le dice a nuestro cliente simulado que cada vez que llamemos al método `request()` de nuestro simulado, debe devolver este `$mockResponse`.

[[[ code('db50103536') ]]]

Ahora podríamos ejecutar nuestras pruebas, pero fallarían. Hemos enseñado a nuestro cliente simulado lo que debe devolver cuando llamemos al método `request()`. Pero ahora tenemos que enseñar a nuestro `$mockResponse` lo que debe hacer cuando llamemos al método `toArray()`. Así que justo encima, vamos a enseñarle al `$mockResponse` que cuando llamemos,`method('toArray')` y él `willReturn()` un array de incidencias. Porque eso es lo que devuelve GitHub cuando llamamos a la API.

[[[ code('c9bc8019ab') ]]]

Para cada incidencia, GitHub nos da el "título" de la incidencia y, entre otras cosas, una matriz de "etiquetas". Así que imitemos a GitHub y hagamos que esta matriz incluya una incidencia que tenga `'title' => 'Daisy'`.

Y, para la prueba, haremos como si se hubiera torcido el tobillo, así que añadiremos un conjunto de claves `labels` a un array, que incluya `'name' => 'Status: Sick'`.

Vamos a crear también un dino sano para poder afirmar que nuestro análisis sintáctico también lo comprueba correctamente. Copia esta edición y pégala a continuación. Cambia `Daisy` por `Maverick`y pon su etiqueta en `Status: Healthy`.

[[[ code('6ae7eb1f96') ]]]

¡Perfecto! Nuestras afirmaciones ya esperan que `Daisy` esté enfermo y `Maverick`sano. Así que, si nuestras pruebas pasan, significa que toda nuestra lógica de análisis de etiquetas es correcta.

Crucemos los dedos y probemos:

```terminal
./vendor/bin/phpunit
```

Y... ¡Genial! ¡Pasan! Y lo mejor de todo, ¡ya no estamos llamando a la API de GitHub cuando ejecutamos nuestras pruebas! Imagínate el pánico que causaríamos si tuviéramos que bloquear el parque porque nuestras pruebas fallan porque la api está desconectada... o simplemente porque alguien ha cambiado las etiquetas en GitHub, Ya... Yo tampoco quiero ese dolor de cabeza...

## ¿Stubs? ¿Mocks?

¿Recuerdas cuando hablábamos de los diferentes nombres de los mocks? Pues bien, tanto`mockResponse` como `mockHttpClient` se llaman ahora oficialmente stubs... Es una forma elegante de decir objetos falsos en los que, opcionalmente, tomamos el control de los valores que devuelven. Eso es exactamente lo que estamos haciendo con el método `willReturn()`. De nuevo, la terminología no es demasiado importante, pero ahí la tienes. Esto son stubs. Y sí, cada vez que enseño esto, tengo que buscar estos términos para recordar qué significan exactamente.

A continuación, vamos a convertir nuestros stubs en auténticos objetos simulados, probando también los datos pasados al objeto simulado.
