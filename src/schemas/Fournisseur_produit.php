<?php namespace Schemas ;

class Fournisseur_produit{

	const COLUMNS =[
		'Id_fournisseur'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'Id_produit'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'Id_Fournisseur_produits'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'prix'=> ['type' =>'double' ,'nullable' =>'1' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}