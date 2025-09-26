<?php

function discord_token_url()
{
	return 'https://discord.com/api/oauth2/token';
}

function discord_api_base_url()
{
	return 'https://discord.com/api/users/@me';
}

function discord_revoke_url()
{
	return 'https://discord.com/api/oauth2/token/revoke';
}

function discord_start_login_redirect_url()
{
	if (is_prod())
		return 'https://worldofeditors.net/complete_discord_login.php';
	return 'http://localhost/complete_discord_login.php';
}

function discord_complete_login_redirect_url()
{
	if (is_prod())
		return 'https://worldofeditors.net';
	return 'http://localhost';
}

function discord_api_request($url, $post = false, $headers = array())
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$response = curl_exec($ch);

	if ($post)
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

	$headers[] = 'Accept: application/json';

	if (discord_session('access_token'))
		$headers[] = 'Authorization: Bearer ' . discord_session('access_token');

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$response = curl_exec($ch);
	return json_decode($response);
}

// The flow starts from our page redirecting to discord.
// From discord, it will redirect back to us again with a code we can exchange
// for a session. With that session, we can get the user's data back.
function discord_login_url()
{
	$params = array(
		'client_id' => getenv("OAUTH2_CLIENT_ID"),
		'redirect_uri' => discord_start_login_redirect_url(),
		'response_type' => 'code',
		'scope' => 'identify email'
	);

	return 'https://discord.com/api/oauth2/authorize' . '?' . http_build_query($params);
}

function discord_complete_login()
{
	// Exchange the auth code for a token
	$token = discord_api_request(discord_token_url(), array(
		"grant_type" => "authorization_code",
		'client_id' => getenv("OAUTH2_CLIENT_ID"),
		'client_secret' => getenv("OAUTH2_CLIENT_SECRET"),
		'redirect_uri' => discord_start_login_redirect_url(),
		'code' => discord_get('code')
	));

	$_SESSION['access_token'] = $token->access_token;
}

function discord_get_user()
{
	static $user = null;

	if (!$user)
		$user = discord_api_request(discord_api_base_url());

	return $user;
}

function discord_is_logged_in()
{
	return isset(discord_get_user()->id);
}

function discord_logout()
{
	$data = array(
		'token' => discord_session('access_token'),
		'token_type_hint' => 'access_token',
		'client_id' => getenv("OAUTH2_CLIENT_ID"),
		'client_secret' => getenv("OAUTH2_CLIENT_SECRET"),
	);

	$ch = curl_init(discord_revoke_url());

    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
        CURLOPT_POSTFIELDS => http_build_query($data),
    ));

    curl_exec($ch);
	unset($_SESSION['access_token']);
}

function discord_get($key, $default = NULL)
{
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function discord_session($key, $default=NULL)
{
  return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}
