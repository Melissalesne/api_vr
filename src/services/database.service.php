<?php

namespace Services;

use PDO;
use PDOException;

class DatabaseService
{
    public ?string $table;
    public string $pk;
    public function __construct(?string $table = null)
    {
        $this->table = $table;
        $this->pk = "Id_" . $this->table;
    }
    private static ?PDO $connection = null;
    private function connect(): PDO
    {
        if (self::$connection == null) {
            $dbConfig = $_ENV['db'];
            $host = $dbConfig["host"];
            $port = $dbConfig["port"];
            $dbName = $dbConfig["dbName"];
            $dsn = "mysql:host=$host;port=$port;dbname=$dbName";
            $user = $dbConfig["user"];
            $pass = $dbConfig["pass"];
            try {
                $dbConnection = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    )
                );
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données :
                $e -> getMessage ()");
            }
            self::$connection = $dbConnection;
        }
        return self::$connection;
    }
    public function query(string $sql, array $params = []): object
    {
        $statement = $this->connect()->prepare($sql);
        $result = $statement->execute($params);
        return (object)['result' => $result, 'statement' => $statement];
    }
    
     // TODO  Retourne la liste des tables en base de données sous forme de tableau
     
    public static function getTables(): array
    {
        $dbs = new DatabaseService(null);
        $query_resp = $dbs->query("SELECT table_name FROM information_schema.tables
                                     WHERE table_schema = ?", ['vinyle_remenber']);
        $rows = $query_resp->statement->fetchAll(PDO::FETCH_COLUMN);
        $bp=true;
        return $rows;
    }


    public function selectWhere(string $where = "1", array $bind = []) : array
    {
        $sql = "SELECT * FROM $this->table WHERE $where;";
        $resp = $this->query($sql, $bind);
        $rows = $resp->statement->fetchAll(PDO::FETCH_CLASS);   // TODO FETCH_CLASS donne un objet
        return $rows; // TODO  retourne les lignes des tables 
    }

    public function insertOrUpdate(array $body): ?array {
        $modelList = new ModelList($this->table, $body['items']); // TODO pour créer un nouveau modèlelist avec le tableau d'items récupéré on récupére un array associatf
        
        $idList = $modelList->idList();  //TODO  on récupère tous les ids de la liste        
        $where = "$this->pk IN ("; // TODO  on récupère la primary key
        foreach($idList as $id){
            $where .= "?, "; // TODO  créer autant de ? que d'id 
        }
        $where = substr($where, 0, -2) . ")"; // TODO 
        
        $resp = $this->selectWhere($where, $idList, PDO::FETCH_ASSOC); // TODO tu récupère une condition avec  en parametre  le $where, (id doit être dans  $idList)
        
        foreach($body['items'] as $data){ // TODO pour chaque elements ds body[items] on le nomme data (la valeur qui va nous renvoyer)
           $exist = false; // TODO false par defaut  les data n'existe pas 
          foreach($resp as &$arr){ // TODO  pr chaque reponses qui sont dans RESP [] on le nomme arr &array veut dire que si on modifie arr, cela change dans resp ??
            if(!isset($arr[$this->pk]) || !isset($data[$this->pk])){ // TODO si la pk de $arr n'existe pas ou la pk de data n'existe pas alors on passe a l'autre this (on passe au suivant )
              continue;
            }
            
            if($arr[$this->pk] === $data[$this->pk]){ // TODO la pk est strictement = a la pk de data 
              $exist = true; // todo  exist devient true
              foreach($data as $k => $v){ // TODO pour chaque $k(key) et $v(value) dans data
                $arr[$k] = $v; // todo  tu remplace les clé de $arr par les $v du body (JSON) ??
              }
              break; // TODO  on break on sort de la boucle 
            }
          }
          if(!$exist){ // TODO si sa n'existe pas 
            array_push($resp, $data); // TODO ajoute  les  données  au tableau si elle n'existe pas 
          }
        }
        
        $modelList = new ModelList($this->table, $resp); // TODO  instance de modellist qui ajoute les resultat de resp  a la table de l'instance en cour 
        
        $columns = ""; // initialise des valeurs par defauts 
        $values = "";
        $duplicateUpdate = "";
        $valuesToBind = [];
        
        foreach($modelList->data() as $data){ // TODO on va charcher les datas de modellist  et on retourne en tableau associatif 
            $values .= "("; // TODO  on va ouvrir une parenthese 
            
            if(empty($columns)){ // TODO  si la valeur colum est vide 
                $columns .= "("; // TODO ON OUVRE UNE PARENTHSE 
                foreach(array_keys($data) as $key){ // TODO  on boucle pour récuperer les nom de ttes les proprités 
                    $columns .= "$key, "; // todo  on ajoute a columns la key que l'on a récupérer
                    $duplicateUpdate .= "$key=VALUES($key), "; // TODO  on ajoute duplicaUpdate les valeurs de columns 
                }
                $columns = substr($columns, 0, -2) . "), "; // TODO on retire la , et l'espace , et on ajoute une "("
                $duplicateUpdate = substr($duplicateUpdate, 0, -2); // TODO on refait pareil sans la parenthése 
            }
            
            foreach($data as $k => $v){ // TODO  pour chaque clé valeur contenu dans data 
                $values .= "?, "; // TODO  on va ajouter une chaine de caractère 
                array_push($valuesToBind, $v); // TODO on ajoute le tableau et ses valeurs (les ? vont être remplaceer par les valeurs du contenu de $valuesToBind)
            }
            $values = substr($values, 0, -2) . "), "; // TODO  
        }
        
        $columns = substr($columns, 0, -2); 
        $values = substr($values, 0, -2);
        
        $sql = "INSERT INTO $this->table $columns VALUES $values ON DUPLICATE KEY UPDATE $duplicateUpdate;";  // si id produit existe deja on update 
        
        $this->query($sql, $valuesToBind); // TODO la requette en cour va prendre en parzmetre la phrase du dessu + le tableau $valuesToBind
        
        return $modelList->data(); // todo  return un tableau associatif de modellist (en JSON)
      }

    public function getSchema(){
        
        $schemas = [];
        $sql = "SHOW FULL COLUMNS FROM $this->table";
        $query_resp_column = $this->query($sql);
        $schemas = $query_resp_column->statement->fetchAll(PDO::FETCH_ASSOC);    // TODO FETCH_ASSOC donne une liste[]

        return $schemas;
    }
}