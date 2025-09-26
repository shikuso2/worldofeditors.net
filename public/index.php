<?php

header("Cache-Control: public, max-age=5, stale-while-revalidate=5");

// Redirect to foroactivo if required.
$request_uri = $_SERVER['REQUEST_URI'];

// Check if the URI matches the /t{number}- pattern
if (preg_match('/^\/t(\d+)-(.+)$/', $request_uri, $matches)) {
    // Extract the values from the pattern
    $id = $matches[1];
    $slug = $matches[2];

    // Build the redirection URL
    $redirect_url = "https://worldofeditors.foroactivo.com/t{$id}-{$slug}";

    // Redirect with a 301 status code
    header("Location: $redirect_url", true, 301);
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>
        <?php if (isset($page_title)) { ?>
            <?php echo $page_title . " - World of Editors" ?>
        <?php } else { ?>
            World of Editors
        <?php } ?>
    </title>
    <meta name="description" content="Comunidad Latina de Warcraft 3 donde podras encontrar recursos y tutoriales para aprender a crear tus propios mapas usando el editor de mundos, World Edit.">
    
    <link rel="shortcut icon" href="./img/favicon.png" type="image/x-icon">

    <style>
        @font-face {
            font-family: "friz";
            src: url("./resources/friz.woff2") format("woff2");
            font-style: normal;
            font-weight: normal;
            font-display: swap;
        }

        body {
            font-family: friz;
            background-image: url("./img/backlow.webp");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            font-size: 18px;
        }

        .container {
            display: grid;
            grid-template-columns: 420px 1180px;
            margin: 50px auto;
            max-width: 1600px;
            gap: 50px;
        }

        .chain {
            background-image: url("./img/chain.webp");
            background-repeat: no-repeat;
            width: 112px;
            height: 233px;
            position: absolute;
            top: -230px;
        }

        nav {
            text-align: center;
            padding: 25px;
            border: 35px solid;
            border-image: url("./img/marco.webp");
            border-image-slice: 79;
            border-image-repeat: round;
            background: rgb(30 26 25 / 60%);
            border-radius: 21px;
            background-image: url("./img/bcmad2.webp");
            background-position: center;
            background-repeat: repeat;
            position: relative;
            text-transform: uppercase;
            font-size: 16px;
        }

        nav > a {
            display: block;
            background-image: url("./img/btn.webp");
            background-repeat: round;
            border-radius: 5px;
            color: gold;
            text-decoration: none;
            padding: 10px;
            margin-top: 15px;
            margin-bottom: 15px;
            border: 1px solid #361515;
            box-shadow: 0 0 5px black;
            transition: all 300ms;
        }

        nav > a:hover {
            color: white;
        }

        nav > p {
            color: white;
        }

        main {
            padding: 25px;
            border: 35px solid;
            border-image: url("./img/marco.webp");
            border-image-slice: 79;
            border-image-repeat: round;
            background: rgb(12 15 26 / 70%);
            border-radius: 21px;
            position: relative;
            color: antiquewhite;
        }

        main a {
            color: white;
        }

        h1 {
            text-transform: uppercase;
            color: gold;
        }

        h2 {
            color: white;
        }
    </style>

    <script>
        // Preload page when hovering a link.
        document.addEventListener("mouseover", (e) => {
            if (!e.target.href) return;
            if (e.target.target) return;
            fetch(e.target.href);
        });

        // Replace page's content when clicking a link preventing a full page reload.
        document.addEventListener("click", async (e) => {
            const link = a.target.href;

            if (!link) return;
            if (e.target.target) return;

            // Don't push the same url multiple times in the history.
            if (new URL(link).origin !== location.origin) return;

            e.preventDefault();

            const result = await fetch(link);
            const content = await result.text();

            history.pushState({}, "", e.target.href);
            document.body.innerHTML = content;
        });

        // Handle history (back)
        window.addEventListener("popstate", async (e) => {
            const result = await fetch(window.location.href);
            const content = await result.text();

            document.body.innerHTML = content;
        });
    </script>
</head>
<body>
    <div class="container">
        <div>
            <nav>
                <div aria-hidden="true" class="chain" style="left: 0;"></div>
                <div aria-hidden="true" class="chain" style="right: 0;"></div>

                <header>
                    <a href="/" style="display: block;">
                        <img width="250" height="152" src="./img/logo-menu.webp" alt="Logo de World of Editors">
                    </a>
                </header>

                <a id="menu-jugar" href="/jugar.php">Jugar</a>
                <a id="menu-como-jugar" href="/como-jugar.php">Como Jugar</a>
                <a id="menu-descargas" href="/descargas.php">Descargas</a>
                <!-- <a href="/mapas.php">Mapas</a> -->
                <a id="menu-canal" href="https://www.youtube.com/@WorldOfEditorsOficial/videos" target="_blank" rel="noopener">Canal</a>
                <p>
                    World of Editors<br />
                    <span style="font-size: 12px; color: gray;">Original design by Shikuso</span>
                </p>
            </nav>
        </div>

        <?php if (isset($content)) { ?>
        <main>
            <?php echo $content ?>
        </main>
        <?php } ?>
    </div>

</body>
</html>
