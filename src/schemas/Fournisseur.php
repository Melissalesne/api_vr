<?php namespace Schemas ;

class Fournisseur{

	const COLUMNS =[
		'Id_fournisseur'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'nom'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'adresse'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
	];
}