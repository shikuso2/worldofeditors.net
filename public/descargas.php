<?php 

$page_title = "Descargas";

ob_start(); 

?>

<h1>Descargas</h1>

<h2>Warcraft 3</h2>

<p>
    Warcraft III es un videojuego de estrategia en tiempo real creado por Blizzard Entertainment y es la tercera parte de la serie Warcraft. Además de continuar la historia del mundo épico medieval de Warcraft se distingue de sus predecesores por incorporar dos importantes cambios: el paso a los gráficos 3D y la aparición de dos nuevas razas.
</p>

<ul>
    <li><a href="https://drive.google.com/file/d/18_eRqRjodv_7CjxmFFlQ331_5RW784iO/view?usp=sharing">Warcraft III 1.26a ESP</a> (1.10GB)</li>
    <li><a href="https://drive.google.com/file/d/1RG4ZB6H_5CReIPX8bQGmBm1_SVhAtwHM/view?usp=sharing">Warcraft III 1.27b ESP</a> (1.36GB)</li>
</ul>

<h2>PVPGN</h2>

<p>
    PvPGN es un software de servidor multiplataforma gratuito y de código abierto que admite clientes de juegos Battle.net y Westwood Online.
    Warcraft 3 Loader aplica un parche a Warcraft III para permitir conexiones a servidores PvPGN.
</p>

<ul>
    <li><a href="./storage/w3l_1_4_2_by_Keres.zip">W3l 1.4.2</a> (21KB, password: pvpgn)</li>
</ul>

<h2>BATTLENET EDITOR</h2>

<p>
    Pequeña herramienta para agregar y editar las direcciones IP de los servidores de Battlenet o PvPGN para el juego Warcraft III: The Frozen Throne, StarCraft: Brood War y Diablo II.
</p>

<ul>
    <li><a href="https://www.mediafire.com/file/gatgkquggt2hg0d/BNetEditor.rar/file">BNetEditor</a> (13KB)</li>
</ul>

<?php
    $content = ob_get_clean();
    include "index.php";
?>
