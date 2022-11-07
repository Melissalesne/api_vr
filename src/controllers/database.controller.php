<?php namespace Controllers; 

use Helpers\HttpRequest;
use Services\DatabaseService;

class DatabaseController {

    private string $table;
    private string $pk;
    private ?string $id;
    private array $body;
    private string $action;

    public function __construct( HttpRequest $request) {
    
        $this->table = $request->route[0];
        $this->pk = "Id_" . $this->table;
        $this->id = isset($request-> route[1]) ?$request-> route[1] : null;
    
        $request_body = file_get_contents('php://input');
        $this->body = json_decode($request_body, true) ?: [];
    
        $this->action = $request->method;
      }

    // TODO  Retourne le résultat de la méthode ($action) exécutée

    public function execute() : ?array
{

    $result = self::get();
    return $result;
   
    return $this->{$this->action}();
  
}
  
private function get() :?array
{

  $dbs = new DatabaseService($this->table);
  $data = $dbs->selectWhere(is_null($this->id) ?: "$this->pk = ?", [$this->id]);
  return $data;
}


public function put(): ?array { // TODO methode PUT
  $dbs = new DatabaseService($this->table);
  $rows = $dbs->insertOrUpdate($this->body); // TODO methode insertOrUpdate
  
  return $rows;
}

}
?>