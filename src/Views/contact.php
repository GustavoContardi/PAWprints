<h1>PAWprints</h1>
<h2>Pedidos, consultas y más</h2>  

<form method="post" action="/procesar-contacto">
    <fieldset>
        <legend>Formulario de Contacto</legend>

        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" placeholder="Valor" required>

        <label for="apellido">Apellido</label>
        <input type="text" name="apellido" id="apellido" placeholder="Valor" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Valor" required>
            
        <label for="mensaje">Mensaje</label>
        <textarea name="mensaje" id="mensaje" placeholder="Valor" required></textarea>
        <button type="submit">Enviar</button>
    </fieldset>
</form>
