<?php

namespace Models;

use Services\DatabaseService;

class Model
{

    public string $table;
    public string $pk;
    public array $schema;

    public function __construct(string $table, array $json) // ? construit le schéma de chaque tables 
    {


        $this->table = $table;
        $this->pk = "Id_$this->table";                          // ? recupere le nom de  la table
        $this->schema = self::getSchema($table);
        if (!isset($json[$this->pk])) {                        //? si on ne recupere pas l'id dans le json

            $json[$this->pk] = $this->nextGuid();             // ? on en genère un nouveau
        }
        foreach ($this->schema as $k => $v) {           
            if (isset($json[$k])) {                           // ? si "$k"  est defini dans le json
                $this->$k = $json[$k];                        // ?  définis a "$k" la valeur de json  on recupère la variable $k et on lui assigne la valeur du json
            } elseif (
                $this->schema[$k]['nullable'] == 1 &&        // ?  $k dans le schema est nullable, il vaut 1
                $this->schema[$k]['default'] == ''
            ) {                                              // ? et qu'il n'a pas de valeur par defaut
                $this->$k = null;                            // ?  on Lui donne une valeur par defaut qui sera "null"
            } else {
                $this->$k = $this->schema[$k]['default'];        // ? si $k n'a pas de valeur, on lui donne celle du schema par defaut
            }
        }
    }
    public function nextGuid(int $length = 16): string
    {
        $guid = microtime(true) * 10000;   // ? timestamp
        $guid = base_convert($guid, 10, 36); //? convertir en base 36, et on part de base 10 jus'qua 36
        while (strlen($guid) < $length) { 
            $random = rand(0, 35);  //   ? on va chercher un random entre 0 et 35             
            $random = base_convert($random, 10, 36); // ? on le converti en base 36 
            $guid = $guid.$random;    // ?   on récupère le guid  du dessu et on le concatène (ajoute)un random                            
        }

        return $guid;
    }

    public function data() : array
{
  $data = (array) clone $this;             // ?  le clone copie le model(id, nom, etc...) recréé un json 
  foreach($data as $key => $value){    // ? pour chaque clé  valeurs dans data       
    if(!isset($this->schema[$key])){   // ? si on ne récupère pas la $key
      unset($data[$key]);             // ?  sa supprime la variable $key de  $data
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