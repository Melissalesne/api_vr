CREATE TABLE label(
   Id_label VARCHAR(255),
   nom VARCHAR(150),
   img_src VARCHAR(255),
   img_alt VARCHAR(255),
   PRIMARY KEY(Id_label)
);

CREATE TABLE artiste(
   Id_artiste VARCHAR(255),
   nom VARCHAR(100),
   img_src VARCHAR(255),
   img_alt VARCHAR(255),
   PRIMARY KEY(Id_artiste)
);

CREATE TABLE produit(
   Id_produit VARCHAR(255),
   nom VARCHAR(100),
   image VARCHAR(150),
   prix DECIMAL(5,2),
   annee VARCHAR(30),
   slug VARCHAR(150),
   stock_quantites INT,
   tx_tva DOUBLE,
   tx_reduction DOUBLE,
   Id_label VARCHAR(255),
   PRIMARY KEY(Id_produit),
   FOREIGN KEY(Id_label) REFERENCES label(Id_label)
);

CREATE TABLE image(
   Id_images VARCHAR(255),
   img_src VARCHAR(255),
   img_alt VARCHAR(255),
   is_deleted BOOLEAN,
   Id_produit VARCHAR(255),
   PRIMARY KEY(Id_images),
   FOREIGN KEY(Id_produit) REFERENCES produit(Id_produit)
);

CREATE TABLE fournisseur(
   Id_fournisseur VARCHAR(255),
   nom VARCHAR(50),
   adresse VARCHAR(255),
   PRIMARY KEY(Id_fournisseur)
);

CREATE TABLE category(
   Id_category VARCHAR(255),
   nom_categorie VARCHAR(150),
   img_src VARCHAR(255),
   img_alt VARCHAR(255),
   PRIMARY KEY(Id_category)
);

CREATE TABLE fournisseur_produit(
   Id_fournisseur VARCHAR(255),
   Id_produit VARCHAR(255),
   Id_Fournisseur_produits VARCHAR(255),
   prix DOUBLE,
   PRIMARY KEY(Id_fournisseur, Id_produit, Id_Fournisseur_produits),
   FOREIGN KEY(Id_fournisseur) REFERENCES fournisseur(Id_fournisseur),
   FOREIGN KEY(Id_produit) REFERENCES produit(Id_produit)
);

CREATE TABLE role(
   Id_role VARCHAR(255),
   titre VARCHAR(255),
   permission INT,
   is_deleted BOOLEAN,
   PRIMARY KEY(Id_role)
);

CREATE TABLE compte(
   Id_compte VARCHAR(255),
   email VARCHAR(100),
   mot_de_passe VARCHAR(255),
   is_deleted VARCHAR(50),
   Id_role VARCHAR(255),
   PRIMARY KEY(Id_compte),
   FOREIGN KEY(Id_role) REFERENCES role(Id_role)
);

CREATE TABLE client(
   Id_client VARCHAR(255),
   nom VARCHAR(100),
   prenom VARCHAR(100),
   adresse VARCHAR(100),
   telephone VARCHAR(50),
   email VARCHAR(100),
   ville VARCHAR(50),
   code_postal VARCHAR(50),
   pays VARCHAR(50),
   Id_compte VARCHAR(255),
   PRIMARY KEY(Id_client),
   UNIQUE(Id_compte),
   FOREIGN KEY(Id_compte) REFERENCES compte(Id_compte)
);

CREATE TABLE commande(
   Id_commande VARCHAR(255),
   reference VARCHAR(50),
   date_achat DATE,
   statut VARCHAR(50),
   mode_paiement VARCHAR(50),
   Id_client VARCHAR(255),
   PRIMARY KEY(Id_commande),
   FOREIGN KEY(Id_client) REFERENCES client(Id_client)
);

CREATE TABLE livraison(
   Id_livraison VARCHAR(255),
   nom_transporteur VARCHAR(50),
   adresse VARCHAR(255),
   ville VARCHAR(50),
   code_postal VARCHAR(255),
   pays VARCHAR(50),
   poids DECIMAL(25,2),
   numero_suivi INT,
   frais_expedition DECIMAL(1,1),
   date_envoi DATE,
   creation_date DATE,
   estime_arrive VARCHAR(50),
   type_livraison VARCHAR(50),
   Id_commande VARCHAR(255),
   PRIMARY KEY(Id_livraison),
   FOREIGN KEY(Id_commande) REFERENCES commande(Id_commande)
);

CREATE TABLE facture(
   Id_facture VARCHAR(255),
   reference VARCHAR(50),
   date_facturation DATE,
   montant_HT DECIMAL(5,2),
   montant_TVA DECIMAL(2,2),
   Id_commande VARCHAR(255),
   PRIMARY KEY(Id_facture),
   UNIQUE(Id_commande),
   FOREIGN KEY(Id_commande) REFERENCES commande(Id_commande)
);

CREATE TABLE Commande_produit(
   Id_commande VARCHAR(255),
   Id_produit VARCHAR(255),
   Id_Commandes_produits VARCHAR(255),
   prix DOUBLE,
   taux_tva DOUBLE,
   PRIMARY KEY(Id_commande, Id_produit, Id_Commandes_produits),
   FOREIGN KEY(Id_commande) REFERENCES commande(Id_commande),
   FOREIGN KEY(Id_produit) REFERENCES produit(Id_produit)
);

CREATE TABLE Interpréter(
   Id_artiste VARCHAR(255),
   Id_produit VARCHAR(255),
   PRIMARY KEY(Id_artiste, Id_produit),
   FOREIGN KEY(Id_artiste) REFERENCES artiste(Id_artiste),
   FOREIGN KEY(Id_produit) REFERENCES produit(Id_produit)
);

CREATE TABLE Category_produits(
   Id_produit VARCHAR(255),
   Id_category VARCHAR(255),
   PRIMARY KEY(Id_produit, Id_category),
   FOREIGN KEY(Id_produit) REFERENCES produit(Id_produit),
   FOREIGN KEY(Id_category) REFERENCES category(Id_category)
);
