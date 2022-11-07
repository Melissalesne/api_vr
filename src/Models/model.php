<?php

namespace Models;

use Services\DatabaseService;

class Model
{

    public string $table;
    public string $pk;
    public array $schema;

    public function __construct(string $table, array $json)
    {


        $this->table = $table;
        $this->pk = "Id_$this->table";                          // TODO recupere le nom de la colonne de la table
        $this->schema = self::getSchema($table);
        if (!isset($json[$this->pk])) {                        // TODO si on ne recupere pas l'id dans le json

            $json[$this->pk] = $this->nextGuid();             // TODO on en genère un nouveau
        }
        foreach ($this->schema as $k => $v) {           
            if (isset($json[$k])) {                           // TODO  si "$k"  est defini dans le json
                $this->$k = $json[$k];                        // TODO  définis a "$k" la valeur de ton json récupéré au dessus,  on recupère la variable $k et on lui assigne la valeur du json
            } elseif (
                $this->schema[$k]['nullable'] == 1 &&        // TODO  $k dans le schema est nullable, il vaut 1
                $this->schema[$k]['default'] == ''
            ) {                                              // TODO et qu'il n'a pas de valeur par defaut
                $this->$k = null;                            // TODO  on Lui donne une valeur par defaut qui sera "null"
            } else {
                $this->$k = $this->schema[$k]['default'];        // TODO si $k n'a pas de valeur, on lui donne celle du schema par defaut
            }
        }
    }
    public function nextGuid(int $length = 16): string
    {
        $guid = microtime(true) * 10000;
        $guid = base_convert($guid, 10, 35);
        while (strlen($guid) < $length) {                             // TODO  strlen permet de mesurer la taille d'une chaine de caractères (espaces inclus)
            $guid .= base_convert(rand(0, 35), 10, 35);               // TODO  le random permet de choisir 1 caractere parmis 36 alatoires
        }

        return $guid;
    }

    public function data() : array
{
  $data = (array) clone $this;             // TODO  le clone copie le model(id, nom, etc...) recréé un json pour changer la destination (voir tp6 pour explication)
  foreach($data as $key => $value){         
    if(!isset($this->schema[$key])){
      unset($data[$key]);
    }
  }
  return $data;
}

    public static function getSchema(string $table): array
    {

        $schemaName = "Schemas\\" . ucfirst($table);



        return $schemaName::COLUMNS;
    }
}