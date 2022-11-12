<?php namespace Schemas ;

class Interpreter{

	const COLUMNS =[
		'Id_artiste'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'Id_produit'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}