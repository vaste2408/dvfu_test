<?php

class Router {
    private $urls;

    public function __construct (){
        // Разбираем url
        $url = (isset($_GET['q'])) ? $_GET['q'] : ''; //здесь всегда целиковая строка запроса
        $url = rtrim($url, '/');
        $this->urls = explode('/', $url);
    }

    public function run () {
        // Определяем контроллер
        $controller_name = $this->urls[0] . 'Api';
        if (class_exists($controller_name)) {
            $controller = new $controller_name();
            print_r($controller->run());
        }
        else
            echo "Controller $this->urls[0] not found";
    }
}