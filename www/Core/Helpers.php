<?php

namespace App\Core;

class Helpers{

	public static function cleanFirstname($firstname){
		return ucwords(mb_strtolower(trim($firstname)));
	}

	public static function getCurrentTimestamp(){
        $dateNow = new \DateTime('now');
        return $dateNow->format("Y-m-d H:i:s");
    }

    public static function dd($data) {
        echo "<pre>";
        var_dump($data);die;
        echo "</pre>";
    }

    public static function sanitizeString($url) {
        return htmlspecialchars(strip_tags($url));
    }

    public static function redirect($url, $statusCode = 0) {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }

    public static function setFlashMessage($key, $msg) {
        if(gettype($msg) == 'array'){
            foreach ($msg as $item) {
                $_SESSION['flash'][$key][] = $item;
            }
        } else {
            $_SESSION['flash'][$key] = $msg;
        }
    }

    /* URL Helpers */

    public static function urlBase()
    {
        return  $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . '/';
    }

    public static function urlJS(string $url) {
        return self::urlBase() . "src/js/" . $url . ".js";
    }

    public static function callRoute(string $name, array $params = []): string {
        foreach (Router::$routes as $office => $routes) {
            foreach ($routes as $routeName => $routeData) {
                if ($name == $routeName) {
                    if(array_key_exists('requirements', $routeData)) {
                        foreach ($routeData['requirements'] as $paramName => $regex) {
                            $routeData['path'] = str_replace('{' . $paramName . '}', $params[$paramName], $routeData['path']);
                        }
                    }
                    return $routeData['path'];
                }
            }
        }
        die($name.': route name not found');
    }
}