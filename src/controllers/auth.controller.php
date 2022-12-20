<?php 

namespace Controllers;


use Helpers\HttpRequest;
use Services\DatabaseService;
use Helpers\CustomeToken;
use Services\MailerService;




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
        $dbs = new DatabaseService('compte');  // ? on créer une nouvelle  instance de la class DBS pour la table compte 

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

     /**
      //?  Cette fonction va nous servir à vérifier le token 
      *
      * @return void
      */

      
    public function check() {
        
        $headers = apache_request_headers();
        if(isset($headers["Authorization"])) { // ? si l'entête n'est pas vide 
            $token = $headers["Authorization"]; // ? on ajoute le token dans  l'entête autorization
        }
        
        if(isset($token) && !empty($token)) { // ? si le token exiqte et n'est pas vide 
           $tkn =  CustomeToken::create($token); // ? on créer un token 
           if($tkn->isValid()){ // ? si il est valide 
          
                $decoded = $tkn->decoded;//? on le decode 
               

                    return ["result" => true, "role" => $decoded["compteRole"], "id" => $decoded["compteId"]]; // ? renvoie le result (role, id).
               

            }
               
        }

        return ["result" => false]; // ? sinon on return false 
    }
    /**
     //? vérifie si le compte existe en base de données
     *
     * @return void
     */
    public function register(){


        $dbs = new DatabaseService("compte"); // ? je créer une instance de la class DBS pour la table compte 
        $comptes = $dbs->selectWhere("email = ?", [$this->body['email']]); // ? on selectionne la ligne qui à le meme email 
        if(count($comptes) > 0){ // ?  (donc si un compte existe déjà )
            return ['result'=>false, 'message'=>'email '.$this->body['email'].' already used']; // ? on renvoie un msg qui indique que l'email existe déjà en bdd 
        }
       
        $issuedAt = time();
        $expireAt = $issuedAt + 60 * 60 * 1;
        $serverName = "vr-api";
        $email =  $this->body['email'];
        $nom =  $this->body['nom'];
        $prenom =  $this->body['prenom'];
        $numeroDeTelephone =  $this->body['numeroDeTelephone'];
        $ville =  $this->body['ville'];
        $codePostal =  $this->body['codePostal'];
        $pays =  $this->body['pays'];
        $requestData = [
        'createdAt'  => $issuedAt,
        'usableAt'  => $issuedAt,
        'expireAt'  => $expireAt,
        'email' => $email,
        'nom' => $nom,
        'prenom' => $prenom,
        'numeroDeTelephone' => $numeroDeTelephone,
        'ville' => $ville,
        'codePostal' => $codePostal,
        'pays' => $pays,
      
        
    ];
    $tkn =  CustomeToken::create($requestData); // ? on créer un token 
    
    $href = "http://localhost:3000/register/validate/$tkn->encoded"; // ? il va nous renvoyer vers cette route avec dans l'url le token 

    $ms = new MailerService(); // ? on créer une nouvelle instance de MailService, pour pouvoir envoyer des emails 

    $mailParams = [
        "fromAddress" => ["register@maboutique.com","nouveau compte maBoutique.com"],
        "destAddresses" => [$email],
        "replyAddress" => ["noreply@maBoutique.com", "No Reply"],
        "subject" => "Créer votre compte nomblog.com",
        "body" => 'Click to validate the account creation <br> // ? on clique sur le lien pour valider la création de compte 
                    <a href="'.$href.'">Valider</a> ',
        "altBody" => "Go to $href to validate the account creation"
    ];
    $sent = $ms->send($mailParams); // ? le résultat de l'envoie du mail 
    return ['result'=>$sent['result'], 'message'=> $sent['result'] ?
        "Vérifier votre boîte mail et confirmer la création de votre compte sur maBoutique.com" :
        "Une erreur est survenue, veuiller recommencer l'inscription"];
    }

/**
 //? cette fonction verifie si le token existe bien et va décodé les données, pour permettre de récupérer les données initials 
 *
 * @return void
 */
    public function validate(){ 
        $token = $this->body['token'] ?? "";
            
        if(isset($token) && !empty($token)){ // ? vérifie si le token existe et si il n'est pas vide

            try{
                $tkn = CustomeToken::create($token);// ? on utilise un try catch pour capturé les erreurs si jamais le token n'a pas été créer, dans ce cas le token sera égal à Null
            }catch(Exception $e){
                $tkn = null;
            }
            if (isset($tkn) && $tkn->isValid()) // ? si le token existe et est valide , on decodé le token qui va nous permettrent d'obtenir le tableau de données initial 
            {
                $token = $tkn->decoded;
                $nom = $token["nom"];
                $prenom = $token["prenom"];
                $numeroDeTelephone =  $token["numeroDeTelephone"];
                $ville =  $token["ville"];
                $codePostal =  $token["codePostal"];
                $pays =  $token["pays"];
                $email =  $token["email"];
                return ["result"=>true, "nom"=>$nom, "prenom"=>$prenom, "numeroDeTelephone"=>$numeroDeTelephone, "ville" =>$ville, "codePostal"=>$codePostal, "pays"=>$pays, "email"=>$email]; // ? nous retournera le resul a true (email, mdp...)
            }
        }
        return ['result'=>false]; // ? sinon sa nous return false 
    }



    public function create(){
        $dbs = new DatabaseService("client"); // ? je créer une nouvelle instance dde DatabaseService pour la table client
        $user = $dbs->insertOne(["nom"=>$this->body["nom"],"prenom"=>$this->body["prenom"],
        "numeroDeTelephone"=>$this->body["numeroDeTelephone"], "ville"=>$this->body["ville"],"codePostal"=>$this->body["codePostal"],"pays"=>$this->body["pays"],"email"=>$this->body["email"], "is_deleted"=>0, "Id_role"=>2,"motDePasse"=>$this->body["motDePasse"]]); // ? je créer un nouveau user 
        if($user){
            
            $motDePasse = password_hash($this->body["motDePasse"], PASSWORD_ARGON2ID, [ // ? si le mdp et = au mdp hasché 
                'memory_cost' => 1024,
                'time_cost' => 2,
                'threads' => 2
            ]);
            $prefix = $_ENV['config']->hash->prefix; // ? chercher le prefix dans config pour le MDP
            $motDePasse = str_replace($prefix, "", $motDePasse); // ? remplace le prefix par un nouveau MDP
    
            $dbs = new DatabaseService("compte");// ? je créer une nouvelle instance dde DatabaseService pour la table compte
            $compte = $dbs->insertOne( // ? j'insert les valeurs 
                ["compte"=>$this->body["compte"], 
                "is_deleted"=>0,
                "motDePasse"=>$motDePasse,
                "Id_compte"=> $user->Id_compte ]);
            if($compte){ //?  si le compte à été créer sa return true
                return ["result"=>true];
            }
        }
        return ["result"=>false]; //? sinon return false
    }
}


   