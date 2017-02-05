<?php

// Environement : dev or prod
define('APP_ENV', '${APP_ENV}');

$localUrl = 'https://temporator.timmxware.fr';
$gitlabServer = 'https://preview.timmxware.fr';

$clientId = '${OAUTH_ID}';
$clientSecret = '${OAUTH_SECRET}';

$authorizeUrl = $gitlabServer.'/oauth/authorize';
$accessTokenExchangeUrl = "oauth/token";

$apiUrl = $gitlabServer.'/api/v3';
$redirectUriPath = "/login/oauth";
$getDataRedirectUriPath = "index.php";

?>