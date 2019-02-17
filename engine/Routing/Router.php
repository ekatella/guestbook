<?php

namespace guestbook\engine\Routing;


use guestbook\engine\Core\View;
use guestbook\engine\Handlers\AccessChecker;
use guestbook\engine\Handlers\JSON;

/**
 * Very very simplified router
 */
class Router
{

	// TODO: Registry pattern for configs
	const ROUTES_FILE = SITE_CONFIG_DIR . '/routes.php';

	private static $instance;

	private $routes;


	private function __construct()
	{

		$this->routes = include self::ROUTES_FILE;

	}

	/**
	 * @return Router
	 */
	public static function getInstance() : self
	{
		return self::$instance = self::$instance ?? new self();
	}

	/**
	 * @return array
	 */
	private function findRoute() : array
	{

		$result = [];

		//get data from REQUEST
		$request_uri = $this->getUri();
		$request_method = $this->getMethod();

		//take routes of correct http-method
		if (!empty($request_method)
			&& !empty($this->routes[$request_method])
		) {

			foreach ($this->routes[$request_method] as $pattern => $route_data) {

				// match Reqeust uri with route patterns
				if (preg_match('#^' . $pattern . '$#', $request_uri)) {

					$route_data['args'] = preg_replace('#^' . $pattern . '$#', $route_data['args'], $request_uri);

					$route_data['args'] = !empty($route_data['args']) ? explode('/', $route_data['args']) : [];

					$result = $route_data;

					break;
				}

			}

		} else {

			http_response_code(400);

		}

		return $result;

	}


	/**
	 * run matched Controller
	 */
	public function run()
	{

		$matched_route = $this->findRoute();

		if (!empty($matched_route)) {

			$class_name = str_replace('/', '\\', $matched_route['controller']) . 'Controller';

			if (class_exists($class_name) && method_exists($class_name, $matched_route['action'])) {

				$access_checker = new AccessChecker($matched_route['action'], $class_name);

				$errors = $access_checker->check();

				if (!empty($errors)) {

					$this->sendErrors($errors);

				} else {

					$controller = new $class_name($matched_route);

					//TODO: It must be implemented  Request and Response Classes to parse, prepare and manage data

					//call function of controller with parameters
					call_user_func_array([$controller, $matched_route['action']], $matched_route['args']);

				}

			} else {

				$this->setError404();

			}

		} else {

			$this->setError404();

		}

	}

	/**
	 * TODO: implement normal Responses
	 */
	private function setError404()
	{

		http_response_code(404);
		View::getInstance()->display('404.tpl');

	}

	private function sendErrors($errors)
	{

		$json = new JSON();
		$json->errors = $errors;
		$json->send();

	}


	/**
	 * TODO: Request logic - no place here
	 * @return string
	 */
	private function getUri() : string
	{

		return !empty($_SERVER['REQUEST_URI']) ? trim(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '/') : '';

	}

	/**
	 * TODO: Request logic - no place here
	 * @return string
	 */
	private function getMethod() : string
	{
		return $_SERVER['REQUEST_METHOD'] ??  '';

	}


}