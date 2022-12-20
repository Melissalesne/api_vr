<?php

use Tools\Initializer;

// $env = 'dev';
// $_ENV = json_decode(file_get_contents("src/Configs/" . $env . ".config.json"), true);
// $_ENV['env'] = $env;

$_ENV['current'] = 'dev';
$config = file_get_contents("src/configs/" . $_ENV["current"] . ".config.json");
$_ENV['config'] = json_decode($config);

require_once 'autoload.php';
require_once 'vendor/autoload.php';

// use Helpers\HttpResponse;
// $data="OK";
// // HttpResponse::send(["data"=>$data]);

use Controllers\DatabaseController;
use Helpers\HttpRequest;
use Helpers\HttpResponse;
use Models\Model;
use Models\ModelList;
use Services\DatabaseService;
use Controllers\AuthController;

$origin = "http://localhost:3000";
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Methods: *");
// ? réception du token 
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Credentials: true");

//tests
// $model= new Model("produit", ["nom"=>"une veste rouge"]);
// $articleData = $model->data();          //on execute la fonction data du model
// $modelList= new ModelList("produit", [["nom"=>"une veste bleue"], ["nom"=>"une veste verte"]]);
// $modelListeData = $modelList->data();
// $test = $modelList->idList();
//fin de test

use Helpers\CustomeToken; 

 $tokenFromDataArray = CustomeToken::create(['email' => "Melissa@test.com", 'mot_de_passe' => "msofixghb"]); //? créer le token dans un tableau 
 $encoded = $tokenFromDataArray->encoded; // ? encode le token
 $tokenFromEncodedString = CustomeToken::create($encoded); // ? recréer un array avec les valeurs 
 $decoded = $tokenFromEncodedString->decoded; // ? avec les valeurs initial (décodé)
 $test = $tokenFromEncodedString->isValid(); // ? on vérifie la validité du token 
  $bp = true;

// $newMail = new MailerService();
// $newMail->send([
//     "fromAddress" => ["newsletter@monblog.com","newsletter monblog.com"],
//     "destAddresses" => ["lesnemelissa14@gmail.com"],
//     "replyAddress" => ["info@maboutique.com","information maboutique.com"],
//     "subject" => "Newsletter maboutique.com",
//     "body" => "this is the HTML message send by <b>monblog.com</b>",
//     "altBody" => "this is the plain text message for non-HTML mail client "]);


//fin test mailer


$request = HttpRequest::instance();
$tables = DatabaseService::getTables();

if ($_ENV['current'] == 'dev' && !empty($request->route) && $request->route[0] == 'init') {
    if (Initializer::start($request)) {
        HttpResponse::send(["message" => "Api Initialized"]);
    }
    HttpResponse::send(["message" => "Api Not Initialized, try again..."]);
}

require_once 'middlewares/auth.middleware.php';
$req = $_SERVER['REQUEST_METHOD'] . "/" . trim($_SERVER["REQUEST_URI"], '/');
$am = new AuthMiddleware($req);
$am->verify();

// -------------------------------Connexion ------------------------------------

if ($_ENV['current'] == 'dev' && !empty($request->route) && $request->route[0] == 'auth') {

    $authController = new AuthController($request);
    $result = $authController->execute();

    if ($result) {
        HttpResponse::send(["data" => $result], 200);
    }
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

