# De acuerdo.

De acuerdo. Así que creo que la mejor manera de manejar, eh, para que nos demos cuenta si el laboratorio gen crea alguna etiqueta de estado nueva que aún no conocemos, como por ejemplo el estado de somnolencia, es lanzar una excepción en nuestro código.

Vamos a

Volver aquí a nuestra clase de servicio GitHub. Y vamos a tomarnos un descanso de TDD por un momento. Así que mirando nuestra función, llamamos a la API de GitHub. Recorremos la lista de asuntos. Y si el título de la cuestión contiene el nombre de nuestro dinosaurio, llamamos al estado de Dino GI desde el método de etiquetas,

Con permiso.

Y pasamos el array de etiquetas de esa incidencia a este método. Así que mirando aquí en nuestro método, tomamos ese array de etiquetas. Y si la etiqueta no empieza por estado, seguimos adelante. Si no, eliminamos el prefijo de estado y lo recortamos. Así que creo que este es el punto en el que deberíamos lanzar una excepción.

Vamos a

Crear un vamos a seguir adelante y vamos a copiar. Cortaremos el, eh, bloque de estado de salud aquí mismo de nuestro retorno y lo pegaremos justo debajo de nuestro filtro. De acuerdo. Y luego diremos si o no, diremos salud = estado de salud, prueba de nuestro estado y ahora nuestro bloque TRIR, o, o prueba de método. Uh, le pasamos una cadena e internamente este método intenta hacer coincidir nuestro valor, como sano, enfermo o, uh, hambriento con esta cadena. Y si lo hace nos devuelve o enumera, si no lo hace va a devolver. No. Así que lo que podemos hacer aquí es que si no = salud, lanzaremos una nueva excepción en tiempo de ejecución y le daremos a esta excepción un mensaje. Así que hagamos el sprint F y queremos que diga si la etiqueta es una etiqueta de estado desconocida, signo de exclamación, y ahora tenemos que pasar la etiqueta de GitHub. Y sigamos adelante y cerremos esa línea. Genial. Aquí abajo a la declaración de retorno, podemos seguir adelante y, y devolver la salud. Pero si el tema no tiene etiquetas o la matriz está vacía, todavía tenemos que devolver un estado de salud. Así que en lugar de utilizar la variable de estado, vamos a cambiar esto a salud = estado de salud saludable, porque recuerda, a menos que el laboratorio de ginebra nos diga que Diana estaba enferma, siempre consumimos que están sanos. Genial. Ahora que tenemos esto, vamos a escribir una prueba, a nuestra clase de prueba de servicio GI up, y tenemos que desplazarnos hacia abajo

Hmm.

Nuestro método de prueba existente aquí. Tiene mucho del mismo código que vamos a necesitar para, eh, probar y exceptuar. Así que vamos a copiar este método de prueba. Vamos a copiar este método de prueba y a desplazarnos hacia abajo hasta la parte inferior. Y lo pegaremos aquí debajo de nuestro proveedor de datos. Genial. Vamos a cambiar el nombre de esto a, uh, excepción de prueba, lanzada con etiqueta desconocida, y no vamos a utilizar un proveedor de datos. Así que podemos seguir adelante y eliminar ambos argumentos de nuestro método de prueba. Y ahora vamos a desplazarnos hasta nuestra aserción, que no vamos a necesitar. Así que vamos a eliminar esta aserción. Uh, todavía necesitamos llamar a obtener el informe de salud y <affirmative>, cambiaremos este nombre Dino por Maverick ya que no estamos usando un proveedor de datos, echemos un vistazo a nuestra respuesta. Y cada vez que llamamos al método a array, estamos devolviendo un array de asuntos de GitHub y sólo necesitamos uno. Así que vamos a eliminar a Daisy y nos quedaremos con Maverick. Y para esta, etiquetas, vamos a cambiar el estado saludable por el estado somnoliento. Genial. Ahora volvamos a nuestro terminal y vendamos la unidad PHP.</affirmative>

Impresionante prueba de servicio de GitHub, la excepción de prueba lanzada con una etiqueta desconocida tiene una excepción en tiempo de ejecución. Drowsy es una etiqueta de estado desconocida. Esto es genial. Nuestra aplicación lanzará una excepción. Ahora bien, si no reconoce una etiqueta de estado, pero ¿cómo nos aseguramos de que nuestra prueba pasa cuando estamos probando que sí lanza una excepción?

Hmm.

Vuelve a tu prueba y desplázate aquí abajo justo antes de llamar al método de obtención del informe de salud. Vamos a añadir una, esta excepción esperada, esta excepción esperada y el método de excepción esperada toma una cadena, que es una excepción. En este caso, es una excepción de tiempo de ejecución que estamos lanzando. Así que haremos la clase de excepción en tiempo de ejecución

Mover

Volvemos a nuestro proveedor de pruebas, bend PHP, unit trait. Tenemos 10 pruebas, 16 aserciones, y todas están pasando. Vamos a añadir en uno más a, eh, aserción para que sepamos nuestra, nuestra aplicación sólo se lanza en la excepción de una etiqueta de estado desconocido. Y no porque hayamos estropeado alguna otra parte de nuestro código y eso esté lanzando una excepción en tiempo de ejecución. Así que justo aquí abajo, podemos hacer esta excepción de espera, y en realidad podemos probar un mensaje, un código, un mensaje coincide o un objeto. Vamos a utilizar el mensaje. Y ahora nuestro mensaje en este caso es drowsy es una etiqueta de estado desconocida.

Ah,

Volvamos a un terminal. Vamos a ejecutar nuestra prueba. Una vez más con la unidad vendor bend PHP y genial. Tenemos 10 pruebas y 17 aserciones. No lo necesitamos aquí, pero podríamos comprobar, eh, el código de estado de la excepción si fuera importante, eh, utilizando expect, esperar código de excepción y pasando su código, igual que hicimos con el mensaje aquí. Una última cosa sobre la excepción, las aserciones, todas ellas excepto los métodos expect. Uh, en nuestra prueba, se tratan igual que las aserciones. Si esperamos que se lance una excepción o un mensaje y nunca se lanza, nuestras pruebas van a fallar. Ejecuta de nuevo nuestra prueba. <laugh>por supuesto que se levantan excepción de prueba de servicio, lanzada con unlabel es fall</laugh> ida. <laugh>Afirmando que el mensaje de excepción drowsy es una etiqueta de estado desconocida contiene sleepy es una etiqueta de estado desconocida. Más o menos sabíamos que eso iba a pasar. Sigamos adelante y lo arreglaremos rápidamente y deberíamos estar listos, cambiaremos eso a somnoliento, y estaremos listos para seguir adelante. Te mostraré un pequeño truco bastante ingenioso sobre cómo podemos limpiar algunos de estos mocks aquí con, eh, algunos de los poderes incorporados del cliente HTTP de Symfony.</laugh> 
