<?php


namespace gdbp\controleur;


use gdbp\modele\Compte;
use gdbp\modele\Liste;
use gdbp\modele\Livre;
use gdbp\vue\VueBibliotheque;
use gdbp\vue\VueLivre;
use Illuminate\Database\QueryException;
use Slim\Slim;
use const gdbp\vue\AFFICHER_AJOUT_LIVRE;
use const gdbp\vue\AFFICHER_LIVRE;
use const gdbp\vue\AFFICHER_MA_BIBLIOTHEQUE;

class ControleurBibliotheque
{

    public function afficherBibliotheque()
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $data = Compte::find($_SESSION['id_connect'])->livres->take(50);
            $vue = new VueBibliotheque(["data" => $data]);
            $vue->render(AFFICHER_MA_BIBLIOTHEQUE);
        } else {
            Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
        }
    }

    public static function rendreDonneeAjax($champUser, $id)
    {
        $res = array();
        if ($id === null) {
            $data = Compte::find($_SESSION['id_connect'])->livres->take(50);
        } else {
            $data = Liste::find($id)->livres;
        }
        foreach ($data as $livre) {
            if (str_contains(str_replace(" ", "", strtolower($livre['titre'])), str_replace(" ", "", strtolower($champUser)))
                or trim($champUser) == "") {
                $urlLivre1 = $livre['couverture'];
                $titre = $livre['titre'];
                if ($titre === null) {
                    $titre = "Aucun titre pour ce livre";
                }
                $urlLivre = Slim::getInstance()->urlFor('livre', ["isbn" => $livre['ISBN']]);
                $description = $livre['description'];
                if ($description === null or trim($description) === "") {
                    $description = "Aucune description pour ce livre";
                } else {
                    if(strlen($description) > 100) {
                        $description = trim(substr($description,0,100)).' ...';
                    }
                }
                $score = ControleurAffichage::calculerNbEtoile($livre['score']);
                array_push($res, ['champ' => $champUser, 'titre' => $titre, 'urlImage' => $urlLivre1, 'urlLivre' => $urlLivre,
                    'description' => $description, 'score' => $score]);
            }
        }
        echo json_encode($res);
    }

    public function enregistrerNouveauLivre($isbn) {
        $livre = Livre::find($isbn);
        if($livre!==null) {
            try {
                $livre->comptes()->attach($_SESSION['id_connect']);
                Slim::getInstance()->redirect(Slim::getInstance()->urlFor("maBibliotheque"));
            } catch (QueryException $qe) {
                $vue = new VueLivre(["err" => "Ce livre est déjà dans votre bibliothèque"]);
                $vue->render(AFFICHER_AJOUT_LIVRE);
            }
        }
    }

    public function ajouterNouveauLivreBibli($isbn)
    {
        $data = Livre::find($isbn);
        $data2 = Liste::whereNotNull('updated_at')->orderBy("updated_at","asc")->take(3)->get()->toArray();
        $livre = Livre::find($isbn);
        try {
            $livre->comptes()->attach($_SESSION['id_connect']);
            Slim::getInstance()->redirect(Slim::getInstance()->urlFor("maBibliotheque"));
        } catch (QueryException $qe) {
            $vue = new VueLivre(["data"=>$data,"listeRecente"=>$data2,"err"=>"Ce livre est déjà dans votre bibliothèque"]);
            $vue->render(AFFICHER_LIVRE);
        }
    }
}