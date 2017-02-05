<?php

/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
class Login extends Projector {
	/**
	 * The default controller method.
	 *
	 * @return void
	 */


	public function index() {

		// if logged, redirect to homepage :
		if (isset($_SESSION['access_token'])) {
			header( 'Location: /' );
		}

		// If not, display login form :
		else {
			$tokenModel = $this->model( 'token' );
			$authUrl = $tokenModel->getOAuthUrl();
			$this->view( 'login', ['class' => 'login', 'url' => $authUrl]);
		}

	}

	public function logout() {

		// Unset all of the session variables.
		$_SESSION = array();
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]);
		}
		// Finally, destroy the session.
		session_destroy();
		header( 'Location: /login' );

	}


	public function oauth() {

		// Check reponse from Gitlab
		$tokenModel = $this->model( 'token' );
		$token = $tokenModel->getOAuthToken();

		if ($token) {
			$this->index();
		}
		else {
			$this->logout();
		}

	}

}
