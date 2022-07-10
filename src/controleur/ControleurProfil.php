<?php


namespace gdbp\controleur;

use gdbp\modele\Compte;
use gdbp\vue\VueProfil;
use Slim\Slim;
use const gdbp\vue\AFFICHER_PROFIL;

class ControleurProfil
{
    public function afficherProfil()
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $data = Compte::find($_SESSION['id_connect'])->toArray();
            $vue = new VueProfil(['data' => $data,'id'=>$_SESSION['id_connect']]);
            $vue->render(AFFICHER_PROFIL);
        } else {
            Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
        }
    }

    public function afficherProfilUser($id)
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $data = Compte::find($id)->toArray();
            $vue = new VueProfil(['data' => $data,'id'=>$id]);
            $vue->render(AFFICHER_PROFIL);
        } else {
            Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
        }
    }

    public function changerLivreDeProfil($isbn)
    {
        if ($isbn!==null) {
            $compte = Compte::find($_SESSION['id_connect']);
            $compte->ISBNDeProfil = $isbn;
            $compte->save();
        }
        Slim::getInstance()->redirect(Slim::getInstance()->urlFor("livre",['isbn'=>$isbn]));
    }
}