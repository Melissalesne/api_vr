<?php

use Tools\Initializer;

$env = 'dev';
$_ENV = json_decode(file_get_contents("src/Configs/" . $env . ".config.json"), true);
$_ENV['env'] = $env;

require_once 'autoload.php';

// use Helpers\HttpResponse;
// $data="OK";
// // HttpResponse::send(["data"=>$data]);

use Controllers\DatabaseController;
use Helpers\HttpRequest;
use Helpers\HttpResponse;
use Models\Model;
use Models\ModelList;
use Services\DatabaseService;

//tests
// $model= new Model("produit", ["nom"=>"une veste rouge"]);
// $articleData = $model->data();          //on execute la fonction data du model
// $modelList= new ModelList("produit", [["nom"=>"une veste bleue"], ["nom"=>"une veste verte"]]);
// $modelListeData = $modelList->data();
// $test = $modelList->idList();
//fin de test

use Helpers\CustomeToken; 

$tokenFromDataArray = CustomeToken::create(['name' => "Melissa", 'id' => ""]);
$encoded = $tokenFromDataArray->encoded;
$tokenFromEncodedString = CustomeToken::create($encoded);
$decoded = $tokenFromEncodedString->decoded;
$test = $tokenFromEncodedString->isValid();
$bp = true;




$request = HttpRequest::instance();
$tables = DatabaseService::getTables();

if ($_ENV['env'] == 'dev' && !empty($request->route) && $request->route[0] == 'init') {
    if (Initializer::start($request)) {
        HttpResponse::send(["message" => "Api Initialized"]);
    }
    HttpResponse::send(["message" => "Api Not Initialized, try again..."]);
}



if (!empty($request->route)) {
    $const = strtoupper($request->route[0]);
    $key = "Schemas\\Tables::$const";
    if (!defined($key)) {
        HttpResponse::exit(404);
    }
} else {
    HttpResponse::exit(404);
}
$controller = new DatabaseController($request);
$result = $controller->execute();
if ($result) {
    HttpResponse::send(["data" => $result], 200);
}

