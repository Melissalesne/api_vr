<?php namespace Schemas ;

class Label{

	const COLUMNS =[
		'Id_label'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'nom'=> ['type' =>'varchar(150)' ,'nullable' =>'1' ,'default' => ''],
		'img_src'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'img_alt'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}