<?php  namespace Controllers;


use Helpers\HttpRequest;
use Services\DatabaseService;




class AuthController { 

    public function __construct(HttpRequest $request)
    {
        $this->controller = $request->route[0]; // ? récupére la route 
        // http://portfolio-api/auth

        $this->function = isset($request->route[1]) ? $request->route[1] : null;
        // http://portfolio-api/auth/login

        $request_body = file_get_contents('php://input');
        $this->body = json_decode($request_body, true) ?: []; // ? renvoie l'objet dans un tableau associatif 

        $this->action = $request->method;
        // Methode declarée dans le fetch de react
    }

    public function execute()
    {

        $function = $this->function;
        // $function = /login , /check
        $result = self::$function();
        // self fais référence à la Class en cours, :: signifie utilise la fonction 
        return $result;
    }
    /**
     // ? cette fonction va vérifier si les données envoyer par l'utilisateur sont correcte et sont renvoyer en BDD ? 
     *
     * @return void
     */
    public function connexion() 
    {
        $dbs = new DatabaseService('compte'); // ? je créer une instance de la class database service pour aller vérifier la class de la table "compte"

        $email = filter_var($this->body['email'], FILTER_SANITIZE_EMAIL); // ? va supprimer tout les caractères illégaux

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // ? si c'est pas une adresse mail 
            return ["result" => false]; // ? renvoie la valeur a false
        }

        $compte = $dbs->selectWhere("email = ? AND is_deleted = ?", [$email, 0]); // ?? je comprend pas 
        $prefix = $_ENV['config']->hash->prefix; 

        if (count($compte) == 1 && password_verify($this->body['mot_de_passe'], $prefix . $compte[0]->mot_de_passe)) { // ? si le nombre de compte est égale a 1 
            // ? et va vérifier si le MDP entré par l'utilisateur concorde au mdp hashé en BDD

            $dbs = new DatabaseService("role"); // ? je créer une instance de la class database service pour aller vérifier la class de la table "role"
            $role = $dbs->selectWhere("Id_role = ? AND is_deleted = ?", [$compte[0]->Id_role, 0]); // ?? je comprend pas 

            //? Créer un Token à partir d'un tableau associatif

            $tokenFromDataArray = Token::create(['email' => $compte[0]->email, 'mot_de_passe' => $compte[0]->mot_de_passe]);
            $encoded = $tokenFromDataArray->encoded;

            return ["result" => true, "role" => $role[0]->weight, "id" => $compte[0]->Id_compte, "token" => $encoded];
        }

        return ["result" => false];
    }
}


    ?>