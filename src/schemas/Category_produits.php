<?php namespace Schemas ;

class Category_produits{

	const COLUMNS =[
		'Id_produit'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'Id_category'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}