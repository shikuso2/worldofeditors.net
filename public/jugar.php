<?php 

session_start();

$page_title = "Jugar";

ob_start();

include "../include/js.php";
include "../include/request.php";
include "../include/discord.php";
include "../include/db.php";
include "../include/map_info.php";
include "../include/env.php";
include "../include/create_game.php";

if (isset($_POST["submit"])) {
    create_game(
        post_value("name"),
        post_value("owner"),
        post_value("uploaded_map", post_value("map_name"))
    );
}

?>

<style>
    #jugar .jugar-form {
        display: grid;
        grid-template-columns: 700px 300px;
        gap: 20px;
    }

    #jugar input, select {
        background-color: black;
        border: 1px solid gray;
        padding: 10px;
        outline: none;
        display: block;
        color: white;
        font-size: 16px;
        border-radius: 5px;
        /* 5 left padding + 5 padding right + borders */
        width: calc(100% - 22px);
        font-family: friz;
    }

    #jugar select {
        width: 100%;
    }

    #jugar option {
        padding: 10px;
        border-radius: 2px;
    }

    #jugar label {
        display: block;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    #jugar .jugar-form button[type="submit"] {
        display: block;
        background-image: url("./img/btn.webp");
        background-repeat: round;
        border-radius: 5px;
        color: gold;
        text-decoration: none;
        padding: 10px;
        padding-left: 50px;
        padding-right: 50px;
        margin-top: 15px;
        margin-bottom: 15px;
        border: 1px solid #361515;
        box-shadow: 0 0 5px black;
        transition: all 300ms;
        font-size: 16px;
        font-family: friz;
        text-transform: uppercase;
        position: absolute;
        bottom: -30px;
        left: 75px;
        cursor: pointer;
    }

    #jugar .jugar-form button[type="button"] {
        display: block;
        background-color: transparent;
        border: 0;
        cursor: pointer;
        color: gray;
        font-size: 16px;
        font-family: friz;
        width: 650px;
        text-align: left;
        white-space: nowrap;
    }

    #jugar .jugar-form button:hover {
        color: white;
    }

    #jugar .jugar-form button:disabled {
        filter: grayscale(100%);
    }

    #jugar .jugar-form img {
        border: 1px solid gray; 
        border-radius: 5px;
        box-shadow: 0px 0px 0px 1px black;
    }

    #selected_map_nombre, #selected_map_autor {
        font-weight: normal;
        display: block;
        width: 300px;
        overflow: hidden;
        text-decoration: none;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<div id="jugar">

<?php
if (discord_is_logged_in()) {
?>
    Bienvenido <?php echo discord_get_user()->username; ?>!
    <form action="/logout.php" method="POST">
        <button type="submit" style="background-color: transparent; border: 0; color: white; font-size: 16px; padding: 0; margin: 0; cursor: pointer;">Salir</button>
    </form>
    <?php
}
?>

<?php
if (!discord_is_logged_in()) {
?>
    <a href="<?php echo discord_login_url(); ?>">Ingresar con Discord</a>
<?php
}
?>

<h1>Jugar</h1>

<?php
    $game_created = isset($_GET["success"]);
    $game_message = isset($_GET["message"]) ? htmlspecialchars($_GET["message"]) : "???";
?>

<?php if ($game_created) { ?>
    <div style="margin-bottom: 50px;"> 
        <p id="partida_creada_detalles"><?php echo $game_message ?></p>
    </div>
<?php } ?>

<form
    class="jugar-form"
    method="post"
    x-data="{
        maps: [], 
        selected_map: { name: '', author: '', description: '', map_file_name: '', }, 
        map_preview: '',
        map_term: '',
        is_uploading_map: false,
        uploading_progress: 0,

        form: {
            name: '',
            owner: '',
            map_name: '',
            uploaded_map: '',
        },
    }"
    x-effect="
        const result = await fetch('maps.php?nombre=' + encodeURIComponent(map_term) + '&tipo=ALL&orden=false');
        maps = await result.json();
    "
>
    <div>
        <label>
            Nombre de la partida
            <input id="name" name="name" x-model="form.name" placeholder="Nombre de la partida" />
        </label>

        <label>
            Usuario
            <input id="owner" name="owner" x-model="form.owner" placeholder="El nombre del usuario que esta creando la partida" />
        </label>

        <label>
            Subi un mapa
            <input
                id="uploaded_map"
                name="uploaded_map"
                type="file"
                :disabled="is_uploading_map"
                x-model="form.uploaded_map"
                x-on:change="
                const files = event.target.files || [];
                const file = files[0];
                if (!file)
                    return;

                is_uploading_map = true;
                uploading_progress = 0;
                
                const chunk_size = 1 * 1024 * 1024;
                // const chunk_size = 1024;
                const total_chunks = Math.ceil(file.size / chunk_size);
                
                for (let current_chunk = 0; current_chunk < total_chunks; current_chunk++) {
                    const start = current_chunk * chunk_size;
                    const end = Math.min(start + chunk_size, file.size);
                    const chunk = file.slice(start, end);

                    const form_data = new FormData();
                    form_data.append('file_chunk', chunk);
                    form_data.append('file_name', file.name);
                    form_data.append('chunk', current_chunk);
                    form_data.append('total_chunks', total_chunks);

                    await fetch('./upload.php', {
                        method: 'POST',
                        body: form_data,
                    });

                    uploading_progress = (current_chunk + 1) * 100 / total_chunks;
                }

                is_uploading_map = false;
                uploading_progress = 0;
                "
            />
        </label>

        <div>
            <div 
                style="
                background-color: #9b9b9b;
                height: 2px;
                border: 1px solid black;
                border-radius: 2px;
                "
            > 
                <div
                    :style="
                    `transition: width 1s; 
                    width: ${uploading_progress}%; 
                    height: 2px; 
                    background-color: gold;`
                    "
                    x-show="is_uploading_map"
                >
                </div>
            </div>
        </div>
        
        <label>
            O busca uno de nuestros mapas alojados:
            <input id="map_term" x-model.debounce="map_term" placeholder="Islas eco..." />
        </label>
        <input type="hidden" name="map_name" id="map_name" x-model="form.map_name" />
        <div style="display: flex; flex-direction: column; gap: 10px; border-radius: 2px; background-color: black; border: 1px solid gray; padding: 5px; padding-top: 10px; padding-bottom: 10px; height: 250px; overflow-x: hidden; overflow-y: auto;">
            <template x-for="map in maps">
                <button
                    type="button"
                    x-bind:id="'mapa-' + map.name"
                    x-on:click="
                        form.map_name = map.map_file_name;
                        selected_map = map;
                    "
                    x-html="map.name"
                >
                </button>
            </template>
        </div>
    </div>
    
    <div style="text-align: center;">
        <div style="position: relative; margin-bottom: 50px;">
            <img
                id="map_preview"
                width="302px" 
                src="./img/minmap.png"
                alt="Vista previa del mapa seleccionado"
                x-on:error="event.target.src = './img/minmap.png'"
                x-bind:src="map_preview"
                x-effect="
                    // Set map preview when selected map gets updated
                    map_preview = selected_map.thumbnail_path ? './storage/' + selected_map.thumbnail_path : './img/minmap.png';
                "
            >
            <button 
                :disabled="!(form.name && form.owner && (form.map_name || form.uploaded_map)) || is_uploading_map"
                type="submit"
                id="submit"
                name="submit"
            >
                Crear
            </button>
        </div>

        <h2 id="selected_map_nombre" style="font-weight: normal;" x-html="selected_map.name"></h2>
        <h3 id="selected_map_autor" style="font-weight: normal;" x-html="selected_map.author"></h3>

        <p id="selected_map_desc" style="text-align: left; font-size: 16px;" x-html="selected_map.description"></p>
    </div>
</form>

</div>

<?php
    $content = ob_get_clean();
    include "index.php";
?>
