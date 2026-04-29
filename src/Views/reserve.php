<h1>PAWprints</h1>
<h2>Datos de reserva</h2>  

<form method="post" action="/procesar-reserva">
    <fieldset>
        <legend>Información de pedido</legend>

        <label for="nombre">Nombre entero</label>
        <input type="text" name="nombre" id="nombre" placeholder="eg. Juan Perez" required>

        <label for="tel">Telefono</label>
        <input type="text" name="telefono" id="tel" placeholder="eg. +54 11 1234 5678" required>

        <label for="email">Correo electrónico</label>
        <input type="email" name="email" id="email" placeholder="eg. juanperez@yahoo.com" required>
        
        <label for="busqueda">Libro a reservar</label>
        <input type="search" list="libros" id="busqueda" name="libro" placeholder="Titulo del libro" required>
        <datalist id="libros">
            <option value="El Principito">
            <option value="Cien años de soledad">
            <option value="1984">
            <option value="Don Quijote">
            <option value="Contardi">
            <option value="Zander">
            <option value="Romero">
        </datalist>
        
        <span class="reserva-copias-label">
            <label for="copias">Cantidad de copias</label>
            <output for="copias" id="copias-valor">0-100</output>
        </span>
        <input type="range" id="copias" name="copias" min="0" max="100" value="0" step="1"
                oninput="document.getElementById('copias-valor').value = this.value">

        <button type="submit">Guardar reserva</button>
    </fieldset>
</form>