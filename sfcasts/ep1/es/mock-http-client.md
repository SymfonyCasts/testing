# Simular el cliente Http de Symfony

Tener la capacidad de simular objetos en las pruebas es súper impresionante, y un poco raro y complejo al mismo tiempo. Si tenemos objetos sencillos, como `Dinosaur`, deberíamos evitar las líneas de código adicionales y limitarnos a instanciar un `Dinosaur` real para la prueba. Después de todo, es bastante fácil controlar el comportamiento de `Dinosaur`simplemente llamando a sus métodos reales. No es necesario el simulacro.

Pero, para objetos más complejos, como `HttpClient`, utilizar el cliente real... puede ser un dolor de cabeza. La regla general es utilizar simulacros para objetos complejos como los servicios... pero para los objetos simples, como los modelos o las entidades, basta con utilizar el objeto real.

Si volvemos a ver el cliente HTTP de Symfony, podemos simular tanto el cliente como la respuesta para controlar su comportamiento. Pero, como la necesidad de hacer este tipo de cosas es tan común, el Cliente HTTP de Symfony viene con algunas clases especiales que pueden hacer el mocking por nosotros. Viene con dos clases reales hechas específicamente para las pruebas: `MockHttpClient` y `MockResponse`. Utilizar el sistema de burlas de PHPUnit está bien, pero éstas existen para hacernos la vida aún más fácil.

Compruébalo: en lugar de crear un simulacro para `$mockResponse`, instala un`MockResponse()` pasando por `json_encode()` con un array para imitar la respuesta de la API de GitHub. Coge la edición de Maverick que aparece a continuación y pégala en el array. Como`MockResponse` ya está configurado, elimina los bits de `$mockResponse` que aparecen a continuación.

[[[ code('fe13320af2') ]]]

Para el cliente, elimina `$mockHttpClient` y abajo, instala un nuevo`MockHttpClient()` pasando en su lugar `$mockResponse`. Como no vamos a hacer nada con `$mockLogger`, corta `createMock()`, elimina la variable y pégala como argumento a `GithubService()`.

[[[ code('288873b40c') ]]]

Vaya, ¡esto ya tiene mejor pinta! Pero, veamos qué ocurre cuando ejecutamos las pruebas:

```terminal
./vendor/bin/phpunit
```

Y... ¡Woah! ¡Todas las pruebas se superan!

Pero, el recuento de aserciones bajó a "16" porque `MockHttpClient` y `MockResponse`no realizan realmente ninguna aserción, como cuántas veces se llama a un método.

Entonces... ¿cuál es el beneficio real de utilizar estas clases simuladas? ¿Por qué no crear las nuestras a través de PHPUnit? Ja... Echa un vistazo a este diff de `GithubService`. Hemos eliminado 11 líneas de código utilizando los mocks "incorporados" en una sola prueba. Imagina cuántas líneas de código podríamos eliminar usándolas en todas nuestras pruebas. Hm... Creo que eso es exactamente lo que haremos a continuación.
