<?php

class Token {

	public function getOAuthUrl() {

		require_once PARAMS;

		// Mitigates cross site request forgery attacks.
		if (!isset($_SESSION['state'])) {
			$_SESSION['state'] = rand( 0, 999999999 );
		}

		// URL parameters used to request an authorization token
		$queryParams = array(
			'client_id' => $clientId,
			'redirect_uri' => $localUrl . $redirectUriPath,
			'response_type' => 'code',
			'state' => $_SESSION['state'] );

		// Autohrisation query URL
		return $authorizeUrl . '?' . http_build_query( $queryParams );
	}

	public function getOAuthToken() {

		require_once PARAMS;


		// Get the authorization code from the URL as well as the cross site forgery mitigation value.

		$code = $_GET['code'];
		$state = $_GET['state'];

		// Verify the 'state' value is the same random value we created
		// when initiating the authorization request.
		if ( ( ! is_numeric( $state ) ) || ( $state != $_SESSION['state'] ) ) {
			return false;
		}

		// These params will be added to the URL used in
		// HTTP POST below to request an access token
		$accessTokenExchangeParams = array(
			'client_id' => $clientId,
			'client_secret' => $clientSecret,
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $localUrl . $redirectUriPath,
			);

		// Create an HTTP client and execute an HTTP POST request to
		// exchange the authorization token for an access token and
		// refresh token.

		$api = new RestClient( ['base_url' => $gitlabServer] );
		$result = $api->post( $accessTokenExchangeUrl, $accessTokenExchangeParams );
		$responseJson = $result->response;


		// The response formatted in json
		$responseArray = json_decode( $responseJson, TRUE );

		// If the response contains an access_token element, it was successful.
		// If not, an error occurred and the description will be displayed below
		if ( isset( $responseArray['access_token'] ) ) {
			$accessToken = $responseArray['access_token'];
			$refreshToken = $responseArray['refresh_token'];

			$_SESSION['access_token'] = $accessToken;
			$_SESSION['refresh_token'] = $refreshToken;
			return true;
		}
		return false;

	}

}
