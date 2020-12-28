<?php defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request 
{
	public static function process_uri($uri, $routes = NULL)
	{
		if(preg_match('#^([a-z][a-z])(\-([a-z][a-z]))?/#i', $uri, $lang)){
			$uri = substr($uri, strlen($lang[0]));
			$lang = substr($lang[0], 0, -1);
		} else if(preg_match('#^([a-z][a-z])(\-([a-z][a-z]))?$#i', $uri, $lang)){
			$uri = '';
			$lang = $lang[0];
		} else {
			$lang = false;
		}
		// Load routes
		$routes = (empty($routes)) ? Route::all() : $routes;
		$params = NULL;

		foreach ($routes as $name => $route)
		{
			// We found something suitable
			if ($params = $route->matches($uri))
			{
				if($lang){
					$params['localisation_lang'] = $lang;
				}
				return array(
					'params' => $params,
					'route' => $route,
				);
			}
		}

		return NULL;
	}
}
