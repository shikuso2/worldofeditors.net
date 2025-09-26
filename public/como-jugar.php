<?php 

$page_title = "Como jugar";

ob_start(); 

?>

<h1>Como jugar</h1>

<p>
    <a href="/descargas.php">Descargar los archivos necesarios.</a>
</p>

<p>
    Necesitaremos el Warcraft en la version 1.27b, el PVPGN para lograr la coneccion y el BattlenetEditor para agregar los servidores.
</p>

<p>
    Instalar el Warcraft III, si lo descargaste desde los vinculos de esta web, solo deberas extraer la carpeta "Warcraft III 1.27b".
</p>

<p>
    Extraer el contenido del archivo "W3l 1.4.2" y el archivo "BNetEditor" en la carpeta raiz del juego.
</p>

<p>
    Agregar nuestro servidor, para esto usaremos el "BNetEditor", con los datos correspondientes:
</p>

<ul>
    <li>
        IP: <code>worldofeditors.net</code>
    </li>
    <li>
        Zone: <code>0</code>
    </li>
</ul>

<p>
    Dentro del juego en el apartado de "PUERTA DE BATTLENET" selecciona el servidor de WorldofEditors, al hacer click en Battle net ya podras entrar. Solo queda iniciar sesion o en todo caso registrar una cuenta nueva.
</p>

<p>
    Para crear una partida dirigete a la parte <a href="/jugar.php">Jugar</a> de la pagina. Ahi podras escoger un mapa alojado en nuestro servidor o puedes subir cualquier mapa que desees.
</p>

<p>
    Una vez creada la partida, dirigete a tu juego e ingresa a la sala.
</p>

<?php
    $content = ob_get_clean();
    include "index.php";
?>