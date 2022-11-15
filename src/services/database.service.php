<?php namespace Services;


use Models\ModelList;
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
        if (self::$connection == null) { // TODO connexion à la BDD
            $dbConfig = $_ENV['db'];
            $host = $dbConfig["host"]; //? hôte de connexion à la BDD 
            $port = $dbConfig["port"]; //?  Le numéro de port où le serveur de base de données est en train d'écouter.
            $dbName = $dbConfig["dbName"]; //? nom de la BDD 
            $dsn = "mysql:host=$host;port=$port;dbname=$dbName"; //? connexion à MSQL 
            $user = $dbConfig["user"]; //? nom de l'utilisateur "root"
            $pass = $dbConfig["pass"]; //? le MDP il n'y en a pas sur windows
            try {
                $dbConnection = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //? Lance une exeception
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", // ?définit le jeu de caractères
                    )
                );
            } catch (PDOException $e) { //? stop la connexion à la BDD et envoie un msg d'erreur
                die("Erreur de connexion à la base de données :    
                $e -> getMessage ()");
            }
            self::$connection = $dbConnection;
        }
        return self::$connection; //? retrun la connexion
    }
    public function query(string $sql, array $params = []): object //? création d'une requete préparée pour éviter les injection SQL
    {
        $statement = $this->connect()->prepare($sql); // ? prépare la requete
        $result = $statement->execute($params); // ? execute les paramètre du []
        return (object)['result' => $result, 'statement' => $statement]; //? retourne le résultat de la requete
    }
    /**
     //? Retourne la liste des tables en base de données sous forme de tableau
     */
    public static function getTables(): array
    {
        $dbs = new DatabaseService(null);
        $query_resp = $dbs->query("SELECT table_name FROM information_schema.tables
                                     WHERE table_schema = ?", ['vinyle_remenber']);
        $rows = $query_resp->statement->fetchAll(PDO::FETCH_COLUMN);

        return $rows;
    }


      // ? selectwhere:  selectionne en bdd avec une condition 
    public function selectWhere(string $where = "1", array $bind = []): array 
    {
        $sql = "SELECT * FROM $this->table WHERE $where;"; //? on récupère toutes les lignes de la table de l'instance en cours avec la condition $where
        $resp = $this->query($sql, $bind); //? 
        $rows = $resp->statement->fetchAll(PDO::FETCH_CLASS);   //? statement permet de faire plusieurs fois la meme requete / optimise / récupére tte les lignes sous forme de tableau associatif(récupére les classes)
        return $rows;
    }

    public function getSchema()
    {

        $schemas = [];
        $sql = "SHOW FULL COLUMNS FROM $this->table";
        $query_resp_column = $this->query($sql);
        $schemas = $query_resp_column->statement->fetchAll(PDO::FETCH_ASSOC);    //?FETCH_ASSOC donne une liste[]

        return $schemas;
    }

    public function insertOrUpdate(array $body): ?array
    {
        $modelList = new ModelList($this->table, $body['items']);       //?je créer un nouveau modelListe avec le tableau d'items recupéré dans la table

        $idList = $modelList->idList(); //?je  récupère tous les id de la liste, 

        $where = "$this->pk IN (";  //? je  recupère la Primary Key
        foreach ($idList as $id){ //? pr chaque id dans la list 
            $where .= "?, "; // ? je  créer une chaine de caractére
        }
        $where = substr($where, 0, -2) . ")";   //? j'enléve les 2 derniers char et j'ajoute une parenthèse 

        $resp = $this->selectWhere($where, $idList, PDO::FETCH_ASSOC); //?je  recupères une condition avec en parametre le $where, (id doit etre dans idlist)

        foreach ($body['items'] as $data) {      //?pour chaque elements dqns body items, on le nomme data(valeur renvoyée)
            $exist = false;
            foreach ($resp as &$arr) { //? chaque reponses de [resp], on le nomme "arr", "&" veut dire que si on modifie arr, sa changera sa valeur dans le resp
                if (!isset($arr[$this->pk]) || !isset($data[$this->pk])) { //?si le pk de $arr n'existe pas, ou le pk de data n'existe pas
                    continue; // ? on passe a l'autre instance 
                }

                if ($arr[$this->pk] === $data[$this->pk]) {  //? si la pk est === pk de data existe 
                    $exist = true;
                    foreach ($data as $k => $v) {       //? pour chaque $k(clé) et $v(valeurs)  dans data
                        $arr[$k] = $v;     // ?je remplace les clé de $arr par les valeurs du body 
                    }
                    break;
                }
            }
            if (!$exist) {
                array_push($resp, $data); //? si ça n'existe pas dans le resp, on ajoute au tableau
            }
        }
        $modelList = new ModelList($this->table, $resp); //?je crée une nouvelle instance de  modellist et j'ajoute  la response dans la  table de l'instance en cours
        $columns = "";
        $values = "";
        $duplicateUpdate = "";      //?j' initialise les données
        $valuesToBind = [];

        foreach ($modelList->data() as $data) {       //?je  recupere les datas dans modellist et le  retourne en tableau associatif
            $values .= "(";

            if (empty($columns)) {
                $columns .= "(";        //?si la valeur columns est vide, j'ajoute une "("
                foreach (array_keys($data) as $key) {     //?je boucle pour recuperer les noms de toutes les propriétées
                    $columns .= "$key, ";  //? j'ajoute a columns la clé que l'on récupére dans $data
                    $duplicateUpdate .= "$key=VALUES($key), ";  //?j'ajoute a $duplicateUpdate les valeurs de columns
                }
                $columns = substr($columns, 0, -2) . "), "; //?je retire la "," et l'espace, et j'ajoute une "("
                $duplicateUpdate = substr($duplicateUpdate, 0, -2);
            }

            foreach ($data as $k => $v) { //?pour chaque valeur de clé dans data 
                $values .= "?, ";       //?j' ajoute ",? " a $values
                array_push($valuesToBind, $v);      //? je push dans un tableau la valueToBind et la $value
            }
            $values = substr($values, 0, -2) . "), "; //?je retire la "," et l'espace, et j'ajoute une "("
        }

        $values = substr($values, 0, -2); // ? on retire les 2 dernier char 


        $sql = "INSERT INTO $this->table $columns VALUES $values ON DUPLICATE KEY UPDATE $duplicateUpdate;"; // ? on insert les valeurs dans la table  et j'update 

        $this->query($sql, $valuesToBind);
        //?la requete en cours va prendre en parametre le $sql plus le [] $valuesToBind
        return $modelList->data(); //?return  un tableau associatif 
    }

    /**
* permet la suppression (is_deleted = 1) d'une ou plusieurs lignes
* renvoie les lignes deleted sous forme de tableau
* si la mise à jour s'est bien passé (sinon null)
*/
public function softDelete(array $body): ?array {
    $modelList = new ModelList($this->table, $body['items']);  //?on créer un nouveau modelListe avec le tableau d'items recupéré dans la table

    $idList = $modelList->idList(); //?on récupère tous les id de la liste, 
    $where = "";

    $prefix = ""; // ? création du préfixe
    foreach($idList as $id){     //? pour chaque Id dans idList, 
      $where .= "$prefix?"; //? on écrit préfix ?
      $prefix = ', '; // ? on remplace "" par ", " pour à la prochaine boucle ecrire ", ?" dans where
    }

    $sql = "UPDATE $this->table SET is_deleted=? WHERE $this->pk IN ($where);";       //? on met a jour les lignes ou la condition (where) est remplie. 

    $this->query($sql, [1, ...$idList]);   //? on execute notre requete en ajoutant 1 au début car il représente le ? de is_deleted=?
    $sql = "SELECT * FROM $this->table WHERE $this->pk IN ($where);";   //? on defini $sql pour qu'il sélectionne la PK dans le $where (on récupére les lignes mise à jour juste avant)

    $resp = $this->query($sql, $idList);  //? on definit la variable "$resp" ce que nous retourne le query
    if($resp->result){   
      $rows = $resp->statement->fetchAll(PDO::FETCH_ASSOC);
      return $rows;
    }

    return null;
  }
  public function hardDelete(array $body): ?array {
    $modelList = new ModelList($this->table, $body['items']);  //?on créer un nouveau modelListe avec le tableau d'items recupéré dans la table
    
    $idList = $modelList->idList(); //?on récupère tous les id de la liste, 
    $where = "";
    
    foreach($idList as $id){  //? pour chaque Id dans  idList, 
      $where .= '?, '; //? on créer une chaine de characte de x "?, " (x = taille de la liste)
    }
    
    $where = substr($where, 0, -2);
    
    $sql = "DELETE FROM $this->table WHERE $this->pk IN ($where);"; // ? on supprime les lignes ou la condition (where) est rempli 
    
    $this->query($sql, $idList);  // ? on definit la variable "$resp" ce que nous retourne le query
    
    $sql = "SELECT * FROM $this->table WHERE $this->pk IN ($where);";  //? on defini $sql pour qu'il sélectionne la PK dans le $where
    
    $resp = $this->query($sql, $idList);
    if($resp->result){
      $rows = $resp->statement->fetchAll(PDO::FETCH_ASSOC);
      return $rows;
    }
    
    return null;
  }
}