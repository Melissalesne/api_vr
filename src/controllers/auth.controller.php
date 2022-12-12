<?php  namespace Controllers;


use Helpers\HttpRequest;
use Services\DatabaseService;
use Helpers\CustomeToken;




class AuthController { 

    /**
     // ? Cette fonction va récupérer les différentes parties des routes sous forme de tableau associatif
     */

    public function __construct(HttpRequest $request)
    {
        $this->controller = $request->route[0]; 
        // http://portfolio-api/auth

        $this->function = isset($request->route[1]) ? $request->route[1] : null;
        // http://portfolio-api/auth/login

        $request_body = file_get_contents('php://input');
        $this->body = json_decode($request_body, true) ?: [];  // ? récupére les données dans un array au format json

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
     // ? cette fonction va vérifier si les données envoyer par l'utilisateur sont correcte et sont renvoyer en BDD 
     *
     * @return void
     */
    public function connexion() 
    {
        $dbs = new DatabaseService('compte');  // ? je créer une instance de la class DBS pour la table compte 

        $email = filter_var($this->body['email'], FILTER_SANITIZE_EMAIL); 

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // ? si l'email n'est pas valide 
            return ["result" => false]; // ? on retourne false 
        }

        $compte = $dbs->selectWhere("email = ? AND is_deleted = ?", [$email, 0]); //? selectionne tt les emails de la bdd qui à pour valeur email et is_deleted à 0 
        $prefix = $_ENV['config']->hash->prefix; // ? va chercher le prefix dans le config pour le mdp

        if (count($compte) == 1 && password_verify($this->body['mot_de_passe'], $prefix . $compte[0]->mot_de_passe)) {  // ? si on a trouvé un compte on verifie le mdp pour voir si il correspond
            


            $dbs = new DatabaseService("role"); // ? je créer une instance de la class DBS pour la table role
            $role = $dbs->selectWhere("Id_role = ? AND is_deleted = ?", [$compte[0]->Id_role, 0]); //? récupére l'id role dans la table compte 

            //? Créer un Token à partir d'un tableau associatif (67,68)

            $tokenFromDataArray = CustomeToken::create(['compteRole' => $role[0]->permission, 'compteId' => $compte[0]->Id_compte]);
            $encoded = $tokenFromDataArray->encoded;



            return ["result" => true, "role" => $role[0]->permission, "id" => $compte[0]->Id_compte, "token" => $encoded]; // ? renvoi le result (role, id ,token )
        }
        
        return ["result" => false]; // ? aucun compte n'a été trouvé ou le mdp ne correspond pas 
        
    }


    public function check() {
        $headers = apache_request_headers();
        if(isset($headers["Authorization"])) {
            $token = $headers["Authorization"];
        }
        
        if(isset($token) && !empty($token)) {
           $tkn =  CustomeToken::create($token);
           if($tkn->isValid()){
          
                $decoded = $tkn->decoded;
               

                    return ["result" => true, "role" => $decoded["compteRole"], "id" => $decoded["compteId"]];
               

            }
               
        }

        return ["result" => false]; 
    }
}


   