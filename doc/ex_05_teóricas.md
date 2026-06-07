**5. ¿Qué es un ataque de inyección SQL? ¿Cómo puede evitarse?**

Un ataque de **inyección SQL (SQL Injection)** ocurre cuando un atacante logra introducir código SQL malicioso en los datos enviados por una aplicación web. Si la aplicación construye consultas SQL concatenando directamente la entrada del usuario, el atacante puede alterar la consulta original para acceder, modificar o eliminar información de la base de datos.

Por ejemplo, en un formulario de inicio de sesión, un atacante podría ingresar expresiones SQL especialmente diseñadas para evitar la autenticación o extraer datos confidenciales.

Las principales consecuencias de un ataque de inyección SQL son:

* Acceso no autorizado a información sensible.
* Modificación o eliminación de registros.
* Obtención de credenciales almacenadas.
* Compromiso total de la base de datos.

Para evitar este tipo de ataques se recomienda:

* Utilizar **consultas parametrizadas** (*prepared statements*) en lugar de concatenar cadenas SQL.
* Validar y sanitizar los datos recibidos desde formularios y parámetros.
* Aplicar el principio de **mínimos privilegios** a los usuarios de la base de datos.
* Utilizar ORM o frameworks que gestionen correctamente la construcción de consultas.
* Evitar mostrar mensajes de error detallados relacionados con la base de datos.

El uso de consultas parametrizadas es considerado la medida más importante, ya que separa los datos proporcionados por el usuario de la lógica de la consulta SQL.
