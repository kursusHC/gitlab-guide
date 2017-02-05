<?php

/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */

class Projector extends Controller {

	public function __construct()
	{

		$currentController = get_class($this);

		// Check if user is logged in before anything else
		if ( $currentController != 'Login' && $currentController != 'Oauth') {

			if (!isset($_SESSION) && APP_ENV != 'debug') {
				header( 'Location: /login' );
			}
		}
	}

}