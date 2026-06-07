**6. ¿Qué implica un ataque XSS? ¿Cómo puede evitarse?**

Un ataque **XSS (Cross-Site Scripting)** consiste en la inyección de código JavaScript malicioso dentro de una página web para que sea ejecutado por el navegador de otros usuarios. Esto ocurre cuando la aplicación muestra datos proporcionados por usuarios sin realizar un tratamiento adecuado del contenido.

Mediante XSS, un atacante puede:

* Robar cookies o tokens de sesión.
* Suplantar la identidad de otros usuarios.
* Modificar el contenido visible de una página.
* Redirigir a sitios maliciosos.
* Ejecutar acciones en nombre del usuario autenticado.

Existen diferentes variantes de XSS, como:

* **Stored XSS:** el script malicioso se almacena en la aplicación y se ejecuta cada vez que otros usuarios visualizan el contenido.
* **Reflected XSS:** el script forma parte de una solicitud y se refleja inmediatamente en la respuesta.
* **DOM-based XSS:** la vulnerabilidad se produce por manipulación insegura del DOM mediante JavaScript.

Para prevenir ataques XSS se recomienda:

* Escapar o codificar adecuadamente la salida HTML antes de mostrar datos ingresados por usuarios.
* Validar y sanitizar la entrada de datos.
* Utilizar motores de plantillas que realicen escape automático.
* Implementar políticas **Content Security Policy (CSP)**.
* Evitar el uso de funciones peligrosas como `innerHTML` cuando no sea necesario.
* Marcar cookies sensibles con los atributos `HttpOnly` y `Secure`.

La defensa principal contra XSS consiste en tratar todo dato externo como potencialmente no confiable y asegurarse de que sea correctamente escapado antes de ser renderizado en el navegador.
