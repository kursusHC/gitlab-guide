<?php

/**
 * The application class.
 *
 * Handles the request for each call to the application
 * and calls the chosen controller and method after splitting the URL.
 *
 */

use Whoops\Run as WhoopsRun;
use Whoops\Handler\PrettyPageHandler as WhoopsPrettyPageHandler;

class App
{

    /**
     * Stores the controller from the split URL
     *
     * @var string
     */
    protected $controller = 'repos';

    /**
     * Stores the method from the split URL
     * @var string
     */
    protected $method = 'index';

    /**
     * Stores the parameters from the split URL
     * @var array
     */
    protected $params = [];

    public function __construct()
    {


        // Get broken up URL
        $url = $this->parseUrl();

        // Error Handler Init
        if (APP_ENV == 'debug') {
            $this->initWhoopsErrorHandler();
        }

        // Does the requested controller exist?
        // If so, set it and unset from URL array
        if (file_exists('../app/controllers/' . ucfirst($url[0]) . '.php')) {
            $this->controller = $url[0];
            unset($url[0]);
        }

        require_once '../app/controllers/' . ucfirst($this->controller) . '.php';

        $this->controller = new $this->controller();

        // Has a second parameter been passed?
        // If so, it might be the requested method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];

                unset($url[1]);
            }
        }

        // Set parameters to either the array values or an empty array
        $this->params = $url ? array_values($url) : [];

        // Call the chosen method on the chosen controller, passing
        // in the parameters array (or empty array if above was false)
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Send any Exceptions or PHP errors to the Whoops! Error Handler
     *
     * @return $this
     */
    public function initWhoopsErrorHandler()
    {
        $whoops = new WhoopsRun();
        $handler = new WhoopsPrettyPageHandler();

        $whoops->pushHandler($handler)->register();

        return $this;
    }


    /**
     * Parse the URL for the current request. Effectivly splits it, stores the controller
     * and the method for that controller.
     *
     * @return void
     */
    public function parseUrl()
    {

        if (isset($_GET['url'])) {
            // Explode a trimmed and sanitized URL by /
            return $url = explode('/', filter_var(trim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
    }
}
