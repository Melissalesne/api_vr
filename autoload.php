<?php

class AutoLoader {
  
  public static function register() { //TODO Méthode static: qui permet de l'activer sans instancier la classe
    spl_autoload_register(function ($class) {
      // echo "class: $class ";
      $pattern = ['/Controller\b/', '/Service\b/', '/Config\b/'];
      $replace = ['.controller', '.service', '.config'];
      
      $file = 'src/'.preg_replace($pattern, $replace, $class) . '.php';//TODO L'interpréteur va désormais rechercher des fichiers portant le même nom que la classe.
      
      if(file_exists($file)){ //TODO Avant de charger les fichiers dans l'autoload, on vérifie leur existence
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

AutoLoader::register(); //TODO Charge la méthode static 

?>