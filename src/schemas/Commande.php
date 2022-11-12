<?php namespace Schemas ;

class Commande{

	const COLUMNS =[
		'Id_commande'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'reference'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'date_achat'=> ['type' =>'date' ,'nullable' =>'1' ,'default' => ''],
		'statut'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'mode_paiement'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'Id_client'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}