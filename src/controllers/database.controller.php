<?php namespace Controllers;

use ArrayAccess;
use Services\DatabaseService;
use Helpers\HttpRequest;


class DatabaseController {

  private string $table;
  private string $pk;
  private ?string $id;
  private array $body;
  private string $action;

  public function __construct( HttpRequest $request) { // ? constructeur definit une classe 
    
    $this->table = $request->route[0]; // ? récupére la première chaine de caractère fragmenté par explode
    $this->pk = "Id_" . $this->table; // ?  récupère le nom de la colonne de la pk (colonne = Id_)
    $this->id = isset($request-> route[1]) ?$request-> route[1] : null; // ? si la route 1 exite on récupère la route 1,  sinon c'est NULL 

    //?  le JSON est propre au JS donc on le decode sous forme de tableau associatif
    $request_body = file_get_contents('php://input'); // ? php//input récupère le body 
    $this->body = json_decode($request_body, true) ?: []; // ? si gauche = Null alors on récupère un tableau (assoc)

    $this->action = $request->method;
  }
  
public function execute() : ?array
{
 $result = $this->{$this->action}();
  return $result

  
;}
  
private function get() :?array
{

  $dbs = new DatabaseService($this->table); //? instancie  un new DatabaseService qui renvoie la table  de l'instance en cour 
  $data = $dbs->selectWhere("$this->pk = ?", [$this->id]); //? execute une requete sql qui va selectionner avec une condition 
  return $data; //? retourne les données 
}

private function put() : array
{
  $dbs = new DatabaseService($this->table);
  $rows = $dbs->insertOrUpdate($this->body); //? insert ou met à jour une ligne dans la BDD
  return $rows;
}
public function patch(): ?array {     //? le patch fait fait un soft deleted
  $dbs = new DatabaseService($this->table);
  $rows = $dbs->softDelete($this->body); //? supprime une ligne dans la BDD  (supprime pas l'entité dans la bdd lors de la suppression par un utilisateur)
  
  return $rows;
}

public function delete(): ?array {   //?fait un hard delete
  $dbs = new DatabaseService($this->table);
  $rows = $dbs->hardDelete($this->body); //? supprimer définitivement une ligne dans la BDD 
  
  return $rows;
}
}

?>