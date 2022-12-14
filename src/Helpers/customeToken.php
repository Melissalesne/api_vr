<?php namespace Helpers;

use Exception;

class CustomeToken {

    private static $prefix = "$2y$08$"; //bcrypt (salt = 8) // ? le préfixe du token 
    private static $defaultValidity = 60 * 60 * 24; //24H
    private static $remarkableKey = "|"; // ? créer le caractère remarquable 
    private static $separator = null;
    private function __construct()
    {
        

        $args = func_get_args(); // ? retourne les arguments de la fonction 
        if(empty($args)){ // ? si les arguments son vide
            throw new Exception("one argument required"); // ? return une Exception avec un msg
        }
        elseif(is_array($args[0])){ // ? si l'argument a l'index 0 est un tableau 
            $this->encode($args[0]); 
        }
        elseif(is_string($args[0])){ 
            $this->decode($args[0]); 
        }
        else{
            throw new Exception("argument must be a string or an array"); 
        }
      
    }

    public array $decoded; //? stocke le tableau de données
    public string $encoded; //? stocke le token

private function setValidity($decoded):void 
 {
    $timeStamp = time(); // ? on y récupére le  timeStamp 

 // ? on  va  vérifie si dans le tableau associatif il y a des données sinon on  la stocke 
 
    if(!isset($decoded['createdAt']))// ? si les entrées ne sont pas fournies
    {
        $decoded['createdAt'] = $timeStamp; // ? on  stocke le timeStamp dans createdAt
    }; 

    if(!isset($decoded['usableAt']))
    {
        $decoded['usableAt'] = $timeStamp;
    }; 

    if(!isset($decoded['validity']))
    {
        $decoded['validity'] = self::$defaultValidity;
    };

    if(!isset($decoded['expireAt']))
    {
        $decoded['expireAt'] = $timeStamp + self::$defaultValidity;
    };

    $this->decoded = $decoded; // ? on va   stoke le new array associatif 
 }

/**
 //? elle stocke un token dans la variable encoded
 *
 * @param array $decoded
 * @return void
 */
private function encode(array $decoded = []) : void
{
    $this->setValidity($decoded);

    $decodedStringify = json_encode($this->decoded); // ? me renvoie une chaine de caractère au format json 

    $payload = base64_encode($decodedStringify); // ? je l'encode en base64
   
    // $signature = password_hash($payload.$_ENV , PASSWORD_BCRYPT, ['cost' => 8]); 
    $secret_key = $_ENV['config']->secret_key->secret; // ? je récupére la secret key 
    $signature = password_hash($payload. self::$separator . $secret_key, PASSWORD_BCRYPT, ['cost' => 8]); // ? créer la signature 
    
    $token = "$payload".self::$remarkableKey."$signature"; // ? on concaténe le payload, $remarkableKey et la signature

    $this->encoded = base64_encode($token); // ? on stocke le token dans encoded 

}

/**
//? Décode le token pour obtenir le tableau de données initial
*
*/

private function decode(string $encoded) : void
{
    $token = base64_decode($encoded);
    $encodedSplit = explode(self::$remarkableKey, $token); // ? split la chaine  à l'endroit ou il y a le caractère remarquable
    $signature = $encodedSplit[1]; 
    $payload = $encodedSplit[0]; 
    $secret_key = $_ENV['config']->secret_key->secret;
    if(password_verify($payload. self::$separator . $secret_key, $signature)){ // ? vérifie si le paylod n'a pas été modifié par l'utilisateur (si le mdp entré par le user correspond bien au mdp hasché en bdd)

        $decoded = base64_decode($payload);
        $decoded = json_decode($decoded,true); // ? decode et retourne en tableau associatif
    }

    $this->decoded = $decoded ?? null; // ? on stocke la version decoded ds l'instance 
}

/**
//? Vérifie la validité du token encodé ($this->decoded not null)
* si $withDate vaut true vérifie également les dates expireAt et usableAt
*/
public function isValid(bool $withDate = true) : bool
{
    if(!isset($this->decoded)){
        return false;
    }
    if($withDate && (isset($this->decoded['expireAt']) && $this->decoded['expireAt'] < time())){  // ? si il est expiré il est plus valide 
        return false;
    }
    if($withDate && isset($this->decoded['usableAt']) && $this->decoded['usableAt'] > time()){ // ? si nest pas utilisable 
        return false;
    }
    return true;
}



    public static function create($entry) : CustomeToken
    {

        return new CustomeToken($entry); 

    }

}