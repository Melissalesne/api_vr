<?php namespace Helpers;

use Exception;

class CustomeToken {

    private static $prefix = "$2y$08$"; //bcrypt (salt = 8)
    private static $defaultValidity = 60 * 60 * 24; //24H
    private static $remarkableKey = "|"; // ? créer le caractère remarquable 
    private static $separator = null;
    private function __construct()
    {

        $args = func_get_args();
        if(empty($args)){
            throw new Exception("one argument required");
        }
        elseif(is_array($args[0])){
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

private function setValidity($decoded):void //? Je créer une fonction 
 {
    $timeStamp = time(); // ? je créer un timeStamp qui sera unique pour chaque valeurs 


    if(!isset($decoded['createdAt']))// ? si les entrées ne sont pas fournies
    {
        $decoded['createdAt'] = $timeStamp;
    }; 

    if(!isset($decoded['usableAt']))// ? si les entrées ne sont pas fournies
    {
        $decoded['usableAt'] = $timeStamp;
    }; 

    if(!isset($decoded['validity']))// ? si les entrées ne sont pas fournies
    {
        $decoded['validity'] = self::$defaultValidity;
    };

    if(!isset($decoded['expireAt']))// ? si les entrées ne sont pas fournies
    {
        $decoded['expireAt'] = $timeStamp + self::$defaultValidity;
    };

    $this->decoded = $decoded;
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

    $decodedStringify = json_encode($this->decoded); // ? me renvoie une chaine de caractère 

    $payload = base64_encode($decodedStringify); // ? je l'encode en base64
   
    // $signature = password_hash($payload.$_ENV , PASSWORD_BCRYPT, ['cost' => 8]); //? je génére une chaine via un algorithme 
    $secret_key = $_ENV['config']->secret_key->secret;
    $signature = password_hash($payload. self::$separator . $secret_key, PASSWORD_BCRYPT, ['cost' => 8]);
    
    $token = "$payload".self::$remarkableKey."$signature"; // ? on concaténe le payload, $remarkableKey et la signature

    $this->encoded = $token; // ? encode est stocké dans le token 

}

/**
//? Décode un token pour obtenir le tableau de données initial
*
*/
private function decode(string $encoded) : void
{
    $token = $encoded;
    $encodedSplit = explode(self::$remarkableKey, $token); 
    $signature = $encodedSplit[1]; //? supprime le caractère remarquable |
    $payload = $encodedSplit[0]; // ? split la chaine 
    $secret_key = $_ENV['config']->secret_key->secret;
    if(password_verify($payload. self::$separator . $secret_key, $signature)){ // ? on vérifie si le mdp est hasché

        $decoded = base64_decode($payload);
        $decoded = json_decode($decoded,true);
    }

    $this->decoded = $decoded ?? null; // ? le decoded retourne  la valeur $decoded et renvoie le tableau de données inital 
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
    if($withDate && (isset($this->decoded['expireAt']) && $this->decoded['expireAt'] < time())){
        return false;
    }
    if($withDate && isset($this->decoded['usableAt']) && $this->decoded['usableAt'] > time()){
        return false;
    }
    return true;
}



    public static function create($entry) : CustomeToken
    {

        return new CustomeToken($entry); // ? retourne et crée un new token

    }

}