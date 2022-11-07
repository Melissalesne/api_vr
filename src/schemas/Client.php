<?php namespace Schemas ;

class Client{

	const COLUMNS =[
		'Id_client'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'nom'=> ['type' =>'varchar(100)' ,'nullable' =>'1' ,'default' => ''],
		'prenom'=> ['type' =>'varchar(100)' ,'nullable' =>'1' ,'default' => ''],
		'adresse'=> ['type' =>'varchar(100)' ,'nullable' =>'1' ,'default' => ''],
		'telephone'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'email'=> ['type' =>'varchar(100)' ,'nullable' =>'1' ,'default' => ''],
		'ville'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'code_postal'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'pays'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'Id_compte'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
	];
}