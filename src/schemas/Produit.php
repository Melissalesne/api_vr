<?php namespace Schemas ;

class Produit{

	const COLUMNS =[
		'Id_produit'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'nom'=> ['type' =>'varchar(100)' ,'nullable' =>'1' ,'default' => ''],
		'image'=> ['type' =>'varchar(150)' ,'nullable' =>'1' ,'default' => ''],
		'prix'=> ['type' =>'decimal(5,2)' ,'nullable' =>'1' ,'default' => ''],
		'annee'=> ['type' =>'varchar(30)' ,'nullable' =>'1' ,'default' => ''],
		'slug'=> ['type' =>'varchar(150)' ,'nullable' =>'1' ,'default' => ''],
		'stock_quantites'=> ['type' =>'int(11)' ,'nullable' =>'1' ,'default' => ''],
		'tx_tva'=> ['type' =>'double' ,'nullable' =>'1' ,'default' => ''],
		'tx_reduction'=> ['type' =>'double' ,'nullable' =>'1' ,'default' => ''],
		'top_ventes'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
		'nouveautes'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
		'Id_label'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
	];
}