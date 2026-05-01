<h1>PAWprints</h1>
<h2>Pedidos, consultas y más</h2>  

<form method="post" action="/procesar-contacto">
    <fieldset>
        <legend>Formulario de Contacto</legend>

        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" placeholder="Tu nombre" required>

        <label for="apellido">Apellido</label>
        <input type="text" name="apellido" id="apellido" placeholder="Tu apellido" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="un-email@ejemplo.com" required>

        <label for="asunto">Asunto</label>
        <input type="text" name="asunto" id="asunto" placeholder="Asunto del mensaje" required>
            
        <label for="mensaje">Mensaje</label>
        <textarea name="mensaje" id="mensaje" placeholder="Escribe aqui tu mensaje" required></textarea>
        <button type="submit">Enviar</button>
    </fieldset>
</form>
