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
    if(isset($this->condition)){
     $headers = apache_request_headers();
        if(isset($headers['Authorization'])){
            $token = $headers['Authorization'];
        }
     
        if(isset($token) && !empty($token)){
            try{
                $tkn = CustomeToken::create($token);
            }catch(Exception $e){
                $tkn = null;
            }
            if (isset($tkn) && $tkn->isValid()

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
