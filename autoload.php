<?php

class AutoLoader {
  
  public static function register() { 
    spl_autoload_register(function ($class) {
   
      $pattern = ['/Controller\b/', '/Service\b/', '/Config\b/'];
      $replace = ['.controller', '.service', '.config'];
      
      $file = 'src/'.preg_replace($pattern, $replace, $class) . '.php'; 
      
      if(file_exists($file)){     
        return require_once $file;
      }
      $toolsPath = lcfirst($class).".php";    
      if(file_exists($toolsPath)){
        return require_once $toolsPath;
      }
      
      return false;
    });
  }
  
}

AutoLoader::register(); 

?>