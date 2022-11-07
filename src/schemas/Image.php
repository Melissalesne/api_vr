<?php namespace Schemas ;

class Image{

	const COLUMNS =[
		'Id_images'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'img_src'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'img_alt'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
		'Id_produit'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
	];
}