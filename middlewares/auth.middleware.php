<?php 

use \CustomeToken;


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
     
        if(isset($_ENV['vinyle_remenber'])){
            $token = $_ENV['vinyle_remenber'];
        }
     
        if(isset($token) && !empty($token)){
            try{
                $tkn = CustomeToken::create($token);
            }catch(Exception $e){
                $payload = null;
            }
            if (isset($payload) &&
                $decoded->usableAt === "vr-api" &&
                $decoded->validity < time() &&
                $decoded->expireAt> time())
            {
                $userRole = $payload->userRole;
                $userId = $payload->userId;
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
