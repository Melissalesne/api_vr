<?php

namespace Helpers;

class HttpRequest
{
    public string $method;
    public array $route;
  
    private function __construct()
    {
       
        $this->method = $_SERVER['REQUEST_METHOD'];                         //? récupère la methode "method" et lui assigne la valeur stockée dans $_SERVER['REQUEST_METHOD']
        $this->route = explode("/", trim($_SERVER['REQUEST_URI'], "/"));   //? recupère la route "route" avec le $_SERVER['REQUEST_URI'] et trim supprime les "/" qui entourents la chaine de caractere
       
    }
    private static HttpRequest $instance;
    
    
   
    public static function instance(): HttpRequest
    {if(!isset(self::$instance)){            //? Si l'instance est null
        self::$instance = new HttpRequest(); //? creer une nouvelle instance de HttpRequest
    }
        //...
        return self::$instance;             //? return l'instance 
    }
}