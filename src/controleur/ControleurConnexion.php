<?php

namespace gdbp\controleur;

use gdbp\modele\Compte;
use gdbp\vue\VueConnexion;
use gdbp\vue\VueListe;
use Slim\Slim;
use const gdbp\vue\AFFICHER_MES_LISTES;
use const gdbp\vue\INTERFACE_CONNEXION;
use const gdbp\vue\INTERFACE_DECONNEXION;
use const gdbp\vue\INTERFACE_INSCRIPTION;
use const gdbp\vue\INTERFACE_MAUVAISE_COMBINAISON;
use const gdbp\vue\INTERFACE_MAUVAISE_INSCRIPTION;

class ControleurConnexion
{

    public function afficherInterfaceConnexion()
    {
        if (!(isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null)) {
            $vue = new VueConnexion(null);
            $vue->render(INTERFACE_CONNEXION);
        } else {
            Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
        }
    }

    public function afficherInterfaceInscription()
    {
        $vue = new VueConnexion(null);
        $vue->render(INTERFACE_INSCRIPTION);
        if (!(isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null)) {
            $vue = new VueConnexion(null);
            $vue->render(INTERFACE_INSCRIPTION);
        } else {
            Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
        }
    }

    public function sInscrire()
    {
        $app = Slim::getInstance();
        if (isset($_POST['email']) && isset($_POST['mdp']) && isset($_POST['pseudo'])) {

            if(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
                $mail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $mdp = filter_var($_POST['mdp'], FILTER_SANITIZE_STRING);
                $pseudo = filter_var($_POST['pseudo'], FILTER_SANITIZE_STRING) .
                    $login = Compte::select("identifiant")->where('mail', '=', $mail)->count();
                if ($login == 0) {
                    if (strlen($mdp) < 5) {
                        $vue = new VueConnexion(['err' => "Le mot de passe doit être supérieur à 4 caractères"]);
                        $vue->render(INTERFACE_MAUVAISE_INSCRIPTION);
                    } else if ($mdp == filter_var($_POST['mdpconf'], FILTER_SANITIZE_STRING)) {
                        $this->creerUser($mail, $pseudo, $mdp);
                        $app->redirect($app->urlFor('connexion'));
                    } else {
                        $vue = new VueConnexion(['err' => "Mot de passe non identique"]);
                        $vue->render(INTERFACE_MAUVAISE_INSCRIPTION);
                    }
                } else {
                    $vue = new VueConnexion(['err' => "Email déjà utilisée"]);
                    $vue->render(INTERFACE_MAUVAISE_INSCRIPTION);
                }
            } else {
                $vue = new VueConnexion(['err' => "Email incorrect"]);
                $vue->render(INTERFACE_MAUVAISE_INSCRIPTION);
            }
        }

    }

    public function seConnecter()
    {
        $app = Slim::getInstance();
        if (isset($_POST['email']) && isset($_POST['mdp'])) {
            $id = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $mdp = filter_var($_POST['mdp'], FILTER_SANITIZE_STRING);
            $login = Compte::select("identifiant")->where('mail', '=', "$id")->count();
            if ($login == 1 and password_verify($mdp, Compte::select("mdp")->where('mail', '=', "$id")->get()->toArray()[0]["mdp"])) {
                $compte = Compte::where('mail', '=', "$id")->first();
                $_SESSION['id_connect'] = $compte->identifiant;
                $app->redirect($app->urlFor('racine'));
            } else {
                $vue = new VueConnexion(['err' => "Combinaison email / mot de passe incorrect"]);
                $vue->render(INTERFACE_MAUVAISE_COMBINAISON);
            }
        }
    }

    public function seDeconnecter()
    {
        $app = Slim::getInstance();
        if (isset($_POST['oui'])) {
            $_SESSION['id_connect'] = null;
        }
        $app->redirect($app->request->getRootUri());
    }

    public function afficherInterfaceDeconnexion()
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $vue = new VueConnexion(null);
            $vue->render(INTERFACE_DECONNEXION);
        } else {
            Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
        }
    }

    private function creerUser($mail, $pseudo, $mdp)
    {
        $compte = new Compte();
        $compte->mail = $mail;
        $hash = password_hash($mdp, PASSWORD_DEFAULT, ['cost' => 12]);
        $compte->mdp = $hash;
        $compte->pseudo = $pseudo;
        $compte->save();
    }

}
