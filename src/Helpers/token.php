<?php namespace Helpers;

use Exception;

class Token {
  
  private static $prefix = "$2y$08$"; // ? le prefix du token 
  private static $defaultValidity = 60 * 60 * 1; // ? le temps de la validité du token
  
  private function __construct(){
    $args = func_get_args(); // ? Retourne les arguments de la fonction is_array args[0] 
    
    if(empty($args)){ // ? si les arguments sont vide
      throw new Exception('one argument required'); // ? j'envoie une exception 
    } else if (is_array($args[0])){ // ? si l'arguement a l'index 0 est un tableau
      $this->encode($args[0]); // ? on encode 
    } else if (is_string($args[0])){ // ? si l'argument à l'index 0 est une chaine de caractère 
      $this->decode($args[0]); // ? on décode 
    } else {
      throw new Exception('argument must be a string or an array'); // ? sinon on renvoie une exception 
    }
  }
  
  public array $decoded;
  public string $encoded;
  
  public static function create($entry): Token {
    return new Token($entry); // ? retourne et crée un new token

  }
  
  private function encode(array $decoded = []): void {
    $decoded['createdAt'] = isset($decoded['createdAt']) ? $decoded['createdAt'] : time();
    $decoded['usableAt'] = isset($decoded['usableAt']) ? $decoded['usableAt'] : time();
    $decoded['validity'] = isset($decoded['validity']) ? $decoded['validity'] : self::$defaultValidity;
    
    $decoded['expireAt'] = isset($decoded['expireAt']) ? $decoded['expireAt'] : 
      ($decoded['usableAt'] + $decoded['validity']);
    
    $this->decoded = $decoded;
    
    $payload = base64_encode(json_encode($this->decoded));
    $signature = ltrim(password_hash($payload, PASSWORD_BCRYPT, ['cost'=> 8]), self::$prefix);
    
    $this->encoded = "$payload." . base64_encode($signature);
  }
  
  private function decode(string $encoded): void {
    $this->encoded = $encoded;
    
    $args = explode('.', $encoded);
    
    $signature = self::$prefix . base64_decode($args[1]);
    
    $verified = password_verify($args[0], $signature);
    
    if($verified){
      $this->decoded = json_decode(base64_decode($args[0]), true);
    }
  }
  
  public function isValid(bool $withDate = true): bool {
    if(!isset($this->decoded)){
      return false;
    }
    
    if($withDate){
      $time = time();
      $decoded = $this->decoded;
      
      return $decoded['usableAt'] < $time && $decoded['expireAt'] > $time;
    }
    
    return true;
  }
  
}