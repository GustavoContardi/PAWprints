# Prácticas Frontend

## Introducción
En este punto de las prácticas, disponemos de un sitio que tiene un ciclo de vida petición / respuesta completa, pero que carece de comportamiento del lado del cliente. La programación en Frontend nos permite mejorar la experiencia de usuario, implementar flujos de trabajos que se ajusten a lo que el usuario demanda de aplicaciones modernas y otorgar comportamiento sofisticado a nuestra solución.
En este TP se abordará la creación de componentes (reutilizables) del sitio con comportamiento (carrusel, filtros, drag&drop), como también interactuar con contenido del servidor para que nuestros formularios dependan de estados que pueden cambiar en el tiempo.

Consignas
1. Implementar una librería que permita convertir un listado de imágenes en slides (diapositivas) de imágenes de tipo "carousel". Debe cumplir con las reglas del Diseño Responsivo y deberá contar con diferentes efectos de transición en el pasaje de imágenes (3 como mínimo). Además, se debe mostrar una barra de progreso que muestre el avance (porcentual) de la descarga de las imágenes de tal manera que cuando llegue al 100% empiece a animarse la muestra de imágenes. Las diapositivas deben poder pasarse por thumbs, por botones, por swipe y presionando las teclas de las flechas.
Solo recibirá como parámetro el contenedor de las imágenes y debe permitir trabajar dentro del contenedor y ocupando todo el viewport redimensionando las imágenes para tal fin.
Pueden utilizar el sitio https://swiffyslider.com/examples/ para sacar algunas ideas interesantes.

2. Se pide implementar todas las validaciones y devoluciones en línea que correspondan al formulario de carga de “libros nuevos”, logrando uniformar la experiencia de usuario en todos los navegadores.  
	
Generar un componente que permita cargar, vía Drag and Drop, archivos de imagen de las tapas de los libros.

3. Implementar un componente que agregue funcionalidades de filtros a las búsquedas de libros que permitan ordenar los datos por varios criterios (en forma ascendente y descendente), buscar por rango de precio y que agregue la capacidad de paginar el contenido según la cantidad de elementos que desea ver el usuario (Paginado tradicional y / o scroll infinito). En esta primera etapa se cargarán todos los datos sin procesar desde el backend (la cantidad esperada es manejable de esta manera), y las funcionalidades serán 100% implementadas en JS. 

Nota: Prestar principal atención a los detalles de usabilidad en la versión Mobile.

4. Continuando el ejercicio de la búsqueda en el catálogo de libros de la práctica anterior, agregue la posibilidad de recordar las últimas 5 búsquedas realizadas por el usuario. Para ello, utilice la API de Local Storage. Si la cantidad de consultas es mayor a 5, debe eliminarse la más antigua.
