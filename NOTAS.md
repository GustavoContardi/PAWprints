# Contexto del Proyecto — Trabajo Práctico Nº 4: Programación Frontend

## Descripción General

Aplicación web de gestión y catálogo de libros, trabajo práctico académico de la carrera **Licenciatura en Sistemas de Información**. El TP N°4 agrega comportamiento del lado del cliente (JavaScript) a un sitio con ciclo petición/respuesta ya funcionando desde prácticas anteriores.

---

## Consigna 1 — Carrusel de Imágenes

- Convierte un contenedor HTML con imágenes en un carrusel interactivo.
- Solo recibe como parámetro el contenedor de las imágenes.
- Funciona tanto dentro del contenedor como ocupando todo el viewport, redimensionando las imágenes.

| Feature | Detalle |
|---|---|
| Transiciones | Mínimo 3 efectos distintos |
| Barra de progreso | Muestra % de descarga de imágenes; al llegar al 100% arranca la animación |
| Navegación por thumbs | Miniaturas clicables |
| Navegación por botones | Flechas anterior/siguiente |
| Navegación por swipe | Touch events para mobile |
| Navegación por teclado | Teclas `←` y `→` |
| Diseño responsivo | Adaptarse a mobile, tablet y desktop |

---

## Consigna 2 — Validación de Formulario

Formulario de carga de libros nuevos.

- Validar en tiempo real, no solo al hacer submit.
- Mensajes de error inline (debajo del campo), nunca con `alert()`.
- Al hacer submit con errores: mostrar todos, hacer scroll al primero, no enviar.
- Uniformar la experiencia cross-browser con UI propia, sin depender de la UI nativa del browser.
- Mostrar indicador visual cuando el campo es válido.

---

## Consigna 3 — Drag & Drop de Imágenes

Componente que complementa el input `file` del formulario de libros para arrastrar imágenes desde el sistema de archivos.

- Zona de drop con estados visuales: idle, drag-over, success, error.
- Aceptar solo archivos de imagen.
- Mostrar preview de la imagen antes de enviar.
- Permitir también clic en la zona para abrir el selector de archivos (fallback).
- Validar tipo y tamaño máximo con error inline.
- Compatible con mobile (solo tap/clic).

---

## Consigna 4 — Filtros, Orden y Paginación

El catálogo carga todos los datos desde el backend de una vez. Las funcionalidades son 100% client-side.

**Ordenamiento:** por título, precio, fecha de publicación y autor, en forma ascendente y descendente.

**Filtro por rango de precio:** actualización en tiempo real.

**Paginación:** paginado tradicional y/o scroll infinito, con selector de cantidad de ítems por página.

**Usabilidad mobile:**
- Los controles de filtro deben colapsar en mobile, no ocupar espacio permanente.
- Botones de paginación con área de tap suficiente.
- Mostrar cantidad de resultados encontrados tras filtrar.

---

## Consigna 5 — Historial de Búsquedas con LocalStorage

Recordar las últimas 5 búsquedas del usuario usando la Web Storage API (`localStorage`).

- Al buscar, guardar el término. Si ya existe, moverlo al tope sin duplicar.
- Si hay más de 5, eliminar la más antigua.
- Mostrar el historial como sugerencias al hacer foco en el input de búsqueda.
- Permitir borrar ítems individuales o todo el historial.
- El historial persiste entre sesiones.

---

## Convenciones del Proyecto

- camelCase para variables/funciones, PascalCase para clases, kebab-case para IDs y clases CSS.
- JSDoc en funciones públicas. Comentarios inline en lógica no obvia.
- Cada consigna en su propio archivo JS.
- JS vanilla (ES6+); `const` y `let`, nunca `var`.
- Atributos `aria-label` y `role` donde corresponda.
- CSS mobile-first con variables CSS para valores reutilizables.

---

## Glosario

| Término | Significado en este proyecto |
|---|---|
| `carrusel` | Componente de la Consigna 1 |
| `formulario de libros` | Formulario de carga, Consignas 2 y 3 |
| `catálogo` | Listado de libros con filtros, Consigna 4 |
| `buscador` | Input de búsqueda con historial, Consigna 5 |
| `backend` | Servidor existente de TPs anteriores |