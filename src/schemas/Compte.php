<?php namespace Schemas ;

class Compte{

	const COLUMNS =[
		'Id_compte'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'email'=> ['type' =>'varchar(100)' ,'nullable' =>'1' ,'default' => ''],
		'mot_de_passe'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}