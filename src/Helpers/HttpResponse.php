<?php namespace Helpers;

class HttpResponse
{
   
    //TODO Cette méthode fixe le status code de la réponse HTTP
    
   public static function send(array $data, int $status = 200): void
   {
      if ($status <= 300) {      //TODO Si le status est <= 300
         self::exit($status);   //TODO Elle appelle la methode exit
      }
      echo json_encode($data); //TODO Ecrit les datas au format json
      die;                    //TODO Arrête l'exécution du script
   }


   //TODOint Cette méthode fixe le status code de la réponse HTTP (>=300)
    
   public static function exit(int $status = 404): void
   {

      header("HTTP/1.0" . $status);
      die; 
   }
}