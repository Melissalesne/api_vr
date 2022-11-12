<?php namespace Helpers;

class HttpResponse
{
   
    //?Cette méthode fixe le status code de la réponse HTTP
    
   public static function send(array $data, int $status = 200): void
   {
      if ($status >= 300) {      
         self::exit($status);   
      }
      echo json_encode($data); 
      die;                    
   }


   //?int Cette méthode fixe le status code de la réponse HTTP (>=300)
    
   public static function exit(int $status = 404): void
   {

      header("HTTP/1.0" . $status);
      die; 
   }
}