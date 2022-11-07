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
use Services\DatabaseService;
use Models\Model;
use Models\ModelList;


$model= new Model("produit", ["nom"=>"vinyle rouge"]);
$articleData = $model->data();    // TODO On execute la fonction data du Model  
$modelList= new ModelList("produit", [["nom"=>"vinyle bleue"], ["nom"=>" vinyle verte"]]);
$modelListeData = $modelList->data();
$test = $modelList->idList();




$request = HttpRequest::instance();
// $tables = DatabaseService::getTables();



if ($_ENV['env'] == 'dev' && !empty($request->route) && $request->route[0] == 'init') {
    if (Initializer::start($request)) {
        HttpResponse::send(["message" => "Api Initialized"]);
    }
    HttpResponse::send(["message" => "Api Not Initialized, try again..."]);
}



if (!empty($request->route)) {
    $const = strtoupper($request->route[0]);
    $key = "Schemas\Table::$const";
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
// if(empty($request->route) || !in_array($request->route[0], $tables)){
//     HttpResponse::exit();

// }
// Initializer::writeTableFile(true);
// // $controller = new DatabaseController($request);
// // $result = $controller->execute();
// // HttpResponse::send(["method"=>$request->method, "route"=>$request->route]);

// // $request = $_SERVER['REQUEST_METHOD'] . '/'. filter_var(trim($_SERVER['REQUEST_URI'], '/'), FILTER_SANITIZE_URL);

// $controller = new DatabaseController($request);
// $result = $controller -> execute ();
// HttpResponse :: send ([ "data" => $result ]);
// //  HttpRequest::instance();
// // echo $request;