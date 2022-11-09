<?php
namespace Models;
use Services\ DatabaseService;

class ModelList{

public string $table;
public string $pk;
public array $items;                                       // ? liste des instances de la classe Model
public function __construct (string $table , array $list)
{
    $this->table = $table;
    $this->pk = "Id_$this->table";          
    $this->items = [];                                     // ?$this->items est une variable sous forme d'un tableau
foreach($list as $json){
    $model= new Model($this->table, $json);                // ?  dans chaque json qui est dans liste, on créé un new model, et on ajoute dans notre liste items
array_push($this->items, $model);                         // ?  on reprend la variable pour y pusher de nouvelles données
}

}
public static function getSchema ( $table ) : array
{
    $schemaName = "Schemas\\" . ucfirst($table);
    return $schemaName::COLUMNS;
}
/**
// ?  Même principe que pour Model mais sur une liste ($this->items)
*/
public function data () : array                     // ? on a un tableau de models ($this->items)
{
  $data=[];                                         // ? on recupère un tableau de tableau
    foreach($this->items as $itemModel){            // ?  pour chaque itemModel qu'il y a dans items
        array_push($data, $itemModel->data());      // ?  le premier élément de la () est ce dans quoi on ajoute le deuxième
    }                                             // ? le ->data() transforme le parametre precedent en [] parceque c'est un model avec une fonction data()
  return $data;
}
/**
// ?  Renvoie la liste des id contenus dans $this->items
*/
public function idList ( $key = null ) : array
{
if (!isset($key)){                          // ? si la key n'est pas isset, on lui donne pk(valeur par defaut)
$key=$this->pk ;
}
$tabItem=[];                                // ? on créé un tableau vide
foreach($this->items as $item){             // ?  pour chaque items, on les nomme item
    if(isset($item->$key))                  // ? isset: si il est assigné,,,  (je recupère la valeur de key pour verifier si il est set dans item)
    {
     array_push($tabItem, $item->$key);     // ?  on ajoute au tableau tabItem la valeur trouvée dans item->$key
    }
}
return $tabItem;
}
/**
// ?  Renvoie l'instance contenue dans $this->items correspondant à $id
*/
public function findById($id): ?Model {
    foreach($this->items as $model){                              // ?  pour chaque model dans items
      if(isset($model[$model->pk]) && $model[$model->pk] == $id){ // ?  si le model contient bien la varible $model->pk ET QUE la valeur dans $model->Id_xxx et bien égal à l'id renseigné lors de l'appelle de la fonction ALORS 
        return $model;                                           // ?  On renvoie le model trouvé
      }
    }
    
    return null; // ?  Rien n'a été trouvé dans la boucle for, donc on renvoie null
  }
}