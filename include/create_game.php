<?php 

function create_game($name, $owner, $map_name)
{
    if (!$name || !$owner || !$map_name)
        return;

    if (!discord_is_logged_in()) {
        $query_params = http_build_query([
            'success' => 'false',
            'message' => 'Necesitas estar logueado con Discord para crear partidas.',
        ]);
        header("Location: /jugar.php?$query_params");
        return;
    }

    $user = discord_get_user();

    if (!$user->verified) {
        $query_params = http_build_query([
            'success' => 'false',
            'message' => 'Tu cuenta de Discord necesita estar verificada para crear partidas.',
        ]);
        header("Location: /jugar.php?$query_params");
        return;
    }

    $in_cd = find_one(
        "
        select *
        from games
        where
        user = :user and
        created_at > datetime('now', '-180 seconds') and
        not exists (select 1 from vips where vips.user = games.user)
        limit 1;
        ",
        ["user" => $user->email]
    );

    if ($in_cd) {
        $query_params = http_build_query([
            'success' => 'false',
            'message' => 'Necesitas esperar un poco para crear otra partida.',
        ]);

        header("Location: /jugar.php?$query_params");

        return;
    }

    insert("games", [
        "user" => $user->email,
        "map" => $map_name,
    ]);

    $banned = find_one(
        "select * from bans where user = :user limit 1;",
        ["user" => $user->email]
    );

    $safe_owner = htmlspecialchars($owner, ENT_QUOTES, 'UTF-8');

    if ($banned) {
        $query_params = http_build_query([
            'success' => 'true',
            'message' => 'La partida ha sido creada. El jugador ' . $safe_owner . ' puede iniciar la partida con el comando !start',
        ]);
        header("Location: /jugar.php?$query_params");
        return;
    }

    $bot_request =
        "\nbot_map = " . $map_name .
        "\nbot_owner = " . strtolower($owner) .
        "\nbot_game = " . $name . "\n";
    file_put_contents(__DIR__ . "/../public/pending/pending" . time(), $bot_request);

    if (is_prod())
        discord_notification($map_name, $name, $name, $owner);

    $query_params = http_build_query([
        'success' => 'true',
        'message' => 'La partida ha sido creada. El jugador ' . $safe_owner . ' puede iniciar la partida con el comando !start',
    ]);
    header("Location: /jugar.php?$query_params");
}

function discord_notification($map_file, $name, $game_name, $user)
{
    $map = find_one(
        "
        select * from maps
        where maps.map_file_name = :map_file
        limit 1
        ",
        ["map_file" => $map_file]
    );

    $hook     = getenv("DISCORD_HOOK");
    $hook_key = getenv("DISCORD_HOOK_KEY");

    $url = "https://discord.com/api/webhooks/" . $hook . "/" . $hook_key;

    $hook_object = json_encode([
        "content" => "Partida creada <@&854822908874326026> !",
        "embeds" => [
            [
                "title" => strip_tags($name),
                "description" => strip_tags($descripcion),
                "url" => "https://worldofeditors.net/maps/" . str_replace(" ", "%20", $map_file),
                "color" => 1422025,
                "author" => [
                    "name" => strip_tags($game_name)
                ],
                "image" => [
                    "url" => "https://worldofeditors.net/storage/" . $map["thumbnail_path"]
                ]
            ]
        ],
        "username" => $user,
        "avatar_url" => "https://media.discordapp.net/attachments/479286377519775744/1277729272248926208/fba9d541e865b191d7e4c56907bfa311.webp?ex=66ce399d&is=66cce81d&hm=726aa6944415c10e99c8f7a0e2e922bf6af7415b7c2573d945cc5495f06c78ac&=&format=webp",
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $hook_object,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ],
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
}

