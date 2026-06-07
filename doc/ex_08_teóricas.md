**8. Implementar las funcionalidades necesarias para que cada página tenga la microdata que corresponda.**

**a. ¿Toda la microdata es estática?**

No. La microdata puede ser **estática o dinámica**, según el contenido que describe.

* **Estática:** corresponde a información que cambia poco o nada, por ejemplo el nombre de la empresa, datos institucionales, una sucursal fija, información de contacto o redes sociales.

  En **PAWprints**, la microdata estática se implementa en:
  - `HomeController.php`: datos de `BookStore` (nombre "PAWprints libros S.A.", dirección en Humberto Primo 666, Luján, teléfono, redes sociales)
  - `PagesController.php` (métodos `about` y `contact`): misma información de `BookStore` combinada con `AboutPage` y `ContactPage`

* **Dinámica:** corresponde a datos generados o recuperados desde backend, APIs o bases de datos, como productos, libros, precios, stock, reseñas, eventos o resultados de búsqueda.

  En **PAWprints**, la microdata dinámica se genera en:
  - `BookController.php` (método `show`): microdata `Book` con datos recuperados de la base de datos (título, autor, precio, stock, descripción, imagen, categoría)
  - `CatalogueController.php` (método `index`): microdata `ItemList` con elementos `Book` generados desde la consulta a la base de datos
  - `PagesController.php` (método `special`): microdata `ItemList` para la página "Indispensables" con libros novedades, ofertas y recomendados

En aplicaciones reales suele coexistir ambos tipos: parte del marcado semántico está hardcodeado y otra parte se genera automáticamente junto con el contenido de la página.

---

**b. ¿Cómo decidimos en qué página es importante la microdata de ciertos objetos?**

La decisión depende del **contenido principal y propósito de cada página**. La microdata debe describir aquello que constituye la entidad central del contexto de la página.

En **PAWprints**, las decisiones de microdata por página son:

* **Home (`home.twig` / `HomeController.php`):** La entidad central es la librería como negocio. Se usa `BookStore` con datos institucionales estáticos (nombre, dirección, teléfono, redes sociales). Esto ayuda a los motores de búsqueda a identificar el negocio y su ubicación física.

* **About (`about.twig` / `PagesController::about`):** Combina `AboutPage` (para identificar el tipo de página) con `BookStore` (datos del negocio). Esto proporciona contexto semántico sobre qué es la página y quién es la organización detrás.

* **Contact (`contact.twig` / `PagesController::contact`):** Similar a About, combina `ContactPage` con `BookStore`. La información de contacto es el foco principal, por lo que la microdata de la organización con sus datos de contacto es relevante.

* **Catálogo (`catalogue.twig` / `CatalogueController::index`):** El foco es mostrar una colección de libros. Se usa `ItemList` con múltiples elementos `Book`, cada uno con sus datos básicos (título, autor, precio, URL). Esto permite a los motores de búsqueda entender que es un listado estructurado de libros.

* **Detalle de libro (`libro.twig` / `BookController::show`):** La entidad central es un libro específico. Se usa `Book` con información completa del libro (título, autor, descripción, precio, stock, imagen, categoría) incluyendo `Offer` para el precio y disponibilidad. Esta es la página más importante para SEO de productos individuales.

* **Indispensables/Special (`special.twig` / `PagesController::special`):** Muestra colecciones curadas (novedades, ofertas, recomendados). Se usa `ItemList` con elementos `Book`, similar al catálogo pero enfocado en selecciones destacadas.

* **Reserva (`reserve.twig` / `ReserveController::show`):** Actualmente no implementa microdata específica. Podría beneficiarse de `Book` (el libro que se reserva) o `ReservationAction` si se quisiera modelar la acción de reserva.

No existe una regla universal de “poner toda la microdata en todas las páginas”. Se busca representar semánticamente **lo relevante para esa vista específica**, evitando redundancia o marcado irrelevante.
