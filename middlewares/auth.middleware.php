<?php 

use Helpers\CustomeToken;


class AuthMiddleware{
/**
 //? le constructeur sert a initialiser le authMiddlewares 
 *
 * @param [type] $req
 */
public function __construct($req)
{
    $restrictedRoutes = (array)$_ENV['config']->restricted; // ?recupère les routes  et le  convertie en tableau 

    $params = explode('/', $req);
    $this->id = array_pop($params); 
    if(isset($restrictedRoutes[$req])){ // ? si la route existe 
        $this->condition = $restrictedRoutes[$req]; // ?  on récupère la condition associé a la route 
    }
    foreach ($restrictedRoutes as $k=>$v){ // ?  on fait un foreach sur le tableau 
        $restricted = str_replace(":id", $this->id, $k); // ?  on remplace le :id  par le id de la request 
        if($restricted == $req){ // ? si la route est == à la request 
            $this->condition = $v; // ?  on remplace la condition par la valeur 
            break; // ? on break
        }
    }
}

public function verify(){
    if(isset($this->condition)){//? si les conditions existe
     $headers = apache_request_headers();
        if(isset($headers['Authorization'])){ //? si l'entête n'est pas vide 
            $token = $headers['Authorization'];//? on récupère le token dans l'entête d'authaurization
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
                // ? on le décode et renvoie le résul du role et de l'id
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
