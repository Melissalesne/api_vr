<?php namespace Schemas ;

class Category{

	const COLUMNS =[
		'Id_category'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'nom_categorie'=> ['type' =>'varchar(150)' ,'nullable' =>'1' ,'default' => ''],
		'img_src'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'img_alt'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}