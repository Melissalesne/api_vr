<?php 

use Helpers\CustomeToken;


class AuthMiddleware{

public function __construct($req)
{
    $restrictedRoutes = (array)$_ENV['config']->restricted;

    $params = explode('/', $req);
    $this->id = array_pop($params);
    if(isset($restrictedRoutes[$req])){
        $this->condition = $restrictedRoutes[$req];
    }
    foreach ($restrictedRoutes as $k=>$v){
        $restricted = str_replace(":id", $this->id, $k);
        if($restricted == $req){
            $this->condition = $v;
            break;
        }
    }
}

public function verify(){
    if(isset($this->condition)){//? si les conditions existe
     $headers = apache_request_headers();
        if(isset($headers['Authorization'])){ //? si l'entête n'est pas vide 
            $token = $headers['Authorization'];//? on ajoute le token dans l'entête d'authaurization
        }
     
        if(isset($token) && !empty($token)){ // ? si le token existe et si il n'est pas vide 
            try{
                $tkn = CustomeToken::create($token); //? on utilise un try catch pour capturé les erreurs si jamais le token n'a pas été créer, dans ce cas le token sera égal à Null
            }catch(Exception $e){
                $tkn = null;
            }
            if (isset($tkn) && $tkn->isValid() //? si le token n'est pas vide et qu'il est valide 

              )
            {
                $compteRole = $tkn->decoded["compteRole"];
                $compteId = $tkn->decoded["compteId"];
                $id = $this->id;
                $test = false;
                eval("\$test=".$this->condition);
                if($test){
                    return true;
                }
            }
        }
        header('HTTP/1.0 401 Unauthorized');
        die;
    }

    return true;
}



}
