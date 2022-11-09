<?php namespace Schemas ;

class Artiste{

	const COLUMNS =[
		'Id_artiste'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'nom'=> ['type' =>'varchar(100)' ,'nullable' =>'1' ,'default' => ''],
		'img_src'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'img_alt'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}