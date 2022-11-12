<?php namespace Schemas ;

class Commande_produit{

	const COLUMNS =[
		'Id_commande'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'Id_produit'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'Id_Commandes_produits'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'prix'=> ['type' =>'double' ,'nullable' =>'1' ,'default' => ''],
		'taux_tva'=> ['type' =>'double' ,'nullable' =>'1' ,'default' => ''],
		'quantite'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}