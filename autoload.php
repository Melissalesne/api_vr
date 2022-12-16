<?php

class AutoLoader {
  
  public static function register() { 
    spl_autoload_register(function ($class) {
   
      $pattern = ['/Controller\b/', '/Service\b/', '/Config\b/'];
      $replace = ['.controller', '.service', '.config'];
      
      $file = 'src/'.preg_replace($pattern, $replace, $class) . '.php'; //? l'interpréteur va désormais rechercher des fichiers portant le même nom que la classe
      
      if(file_exists($file)){      //? avant de charger les fichiers dans l'autoload, on vérifie leurs existance 
         require_once $file;
      }
      $toolsPath = lcfirst($class).".php";    
      if(file_exists($toolsPath)){
        require_once $toolsPath;
      }
      
     
    });
  }
  
}

AutoLoader::register(); //? on charge la methode statique 

?>