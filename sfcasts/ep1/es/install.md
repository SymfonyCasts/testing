# Instalación de PHPUnit

¡Hola a todos! Bienvenidos a PHPUnit: ¡pruebas con un bocado! El tutorial en el que descubrimos, para nuestro horror, que otro parque temático de dinosaurios ha construido sus sistemas... sin ninguna prueba. No importa si los raptores pueden o no abrir puertas... si las vallas nunca se encienden.

Nuestro parque se llama Dinotopia. Y, para ayudar a manejar a nuestros amigos prehistóricos, hemos escrito una sencilla aplicación que nos muestra qué dinos están donde y... cómo se sienten. Como verás, ¡es genial! Excepto por la falta total de pruebas.

## Configuración de la aplicación

De todos modos, para aprender lo máximo posible sobre las pruebas y garantizar que no se escape nada mortal de tu aplicación, deberías codificar conmigo. Después de hacer clic en "Descargar" en esta página, descomprime el archivo y entra en el directorio `start/` para encontrar el código que ves aquí. Echa un vistazo a `README.md` para conocer todos los detalles de la configuración.

El último paso será abrir un terminal y ejecutar

```terminal
symfony serve -d
```

para iniciar un servidor web local en `127.0.0.1` puerto `8000`.

¡Genial! Ve a tu navegador, abre una pestaña, ve a `localhost:8000`... ¡y sí! ¡Nuestra aplicación Dinotopia Status!

## La aplicación: Dinotopia Status

Esta sencilla aplicación tiene el nombre de cada dino, el género, el tamaño y el recinto en el que se encuentra actualmente. Aquí abajo, también tenemos un enlace al repositorio súper secreto de GenLab `dino-park` en GitHub. OoooO. Aquí es donde los ingenieros publican regularmente actualizaciones para que Bob, nuestro guardabosques residente, sepa qué dinos se sienten bien, necesitan su medicina o se han escapado. Espera, ¿qué? Afortunadamente, GitHub no se desconecta cuando eso ocurre.

¡Y ahí es donde entramos nosotros! Ya hemos construido la primera versión de la aplicación Dinotopia Status. Mirando el código que hay detrás, es bastante sencillo: un controlador

[[[ code('8077dc53c3') ]]]

una clase `Dinosaur`...

[[[ code('922f8aa370') ]]]

y exactamente cero pruebas. Nuestro trabajo consiste en arreglar eso. También vamos a añadir una función en la que leamos el estado de cada dino desde GitHub y lo rendericemos. Eso ayudará a Bob a evitar entrar en el recinto de Big Eaty -nuestro T-Rex residente- cuando su estado sea "Hambriento". Esos accidentes implican un montón de papeleo. Y gracias a nuestras pruebas, enviaremos esa función sin errores. ¡De nada, Bob!

Si eres nuevo en esto de las pruebas, puede ser intimidante. Hay pruebas unitarias, pruebas funcionales, pruebas de integración, pruebas de aceptación, pruebas matemáticas La lista es casi interminable. Hablaremos de todas ellas -excepto de las pruebas matemáticas- a lo largo de esta serie. En este tutorial, vamos a centrarnos en las pruebas unitarias: pruebas que cubren una parte específica del código, como una función o un método.

Ah, y por cierto, las pruebas también son súper divertidas. ¡Es la automatización! Así que abróchate el cinturón.

## Instalación de PHPUnit

¿Cuál es el primer paso para escribir pruebas? Instalar la herramienta de pruebas estándar de PHP: PHPUnit. Ve a tu terminal y ejecuta:

```terminal
composer require --dev symfony/test-pack
```

Este `test-pack` es un "paquete" de Symfony que instalará PHPUnit -que es todo lo que necesitamos ahora- así como algunas otras bibliotecas que serán útiles más adelante.

Cuando termine, ejecuta:

```terminal
git status
```

¡Genial! Además de instalar los paquetes, parece que algunas recetas de Symfony Flex han modificado y creado algunos otros archivos. Ignóralos por ahora. Hablaremos de cada uno en algún momento de esta serie cuando sean relevantes.

Bien, ¡estamos listos para escribir nuestra primera prueba! Hagámoslo a continuación.
