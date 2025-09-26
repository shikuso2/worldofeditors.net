<?php

session_start();

include "../include/env.php";
include "../include/discord.php";

discord_complete_login();

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WorldOfEditors</title>
	<meta http-equiv="refresh" content="5; url=/jugar.php" />
</head>
<body>
	Redireccionando a WorldOfEditors en 5 segundos!
</body>
</html>
