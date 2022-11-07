<?php namespace Schemas ;

class Livraison{

	const COLUMNS =[
		'Id_livraison'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'nom_transporteur'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'adresse'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'ville'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'code_postal'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'pays'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'poids'=> ['type' =>'decimal(25,2)' ,'nullable' =>'1' ,'default' => ''],
		'numero_suivi'=> ['type' =>'int(11)' ,'nullable' =>'1' ,'default' => ''],
		'frais_expedition'=> ['type' =>'decimal(1,1)' ,'nullable' =>'1' ,'default' => ''],
		'date_envoi'=> ['type' =>'date' ,'nullable' =>'1' ,'default' => ''],
		'creation_date'=> ['type' =>'date' ,'nullable' =>'1' ,'default' => ''],
		'estime_arrive'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'type_livraison'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'Id_commande'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
	];
}