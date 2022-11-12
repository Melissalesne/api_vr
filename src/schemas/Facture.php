<?php namespace Schemas ;

class Facture{

	const COLUMNS =[
		'Id_facture'=> ['type' =>'varchar(255)' ,'nullable' =>'' ,'default' => ''],
		'reference'=> ['type' =>'varchar(50)' ,'nullable' =>'1' ,'default' => ''],
		'date_facturation'=> ['type' =>'date' ,'nullable' =>'1' ,'default' => ''],
		'montant_HT'=> ['type' =>'decimal(5,2)' ,'nullable' =>'1' ,'default' => ''],
		'montant_TVA'=> ['type' =>'decimal(2,2)' ,'nullable' =>'1' ,'default' => ''],
		'Id_commande'=> ['type' =>'varchar(255)' ,'nullable' =>'1' ,'default' => ''],
		'is_deleted'=> ['type' =>'tinyint(1)' ,'nullable' =>'1' ,'default' => ''],
	];
}