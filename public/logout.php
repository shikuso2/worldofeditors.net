<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	session_start();

	include "../include/env.php";
	include "../include/discord.php";

	discord_logout();
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WorldOfEditors - Logout</title>
	<meta http-equiv="refresh" content="5; url=/jugar.php" />
</head>
<body>
	Redireccionando a WorldOfEditors en 5 segundos!
</body>
</html>
