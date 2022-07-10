<?php

namespace gdbp\controleur;

use gdbp\modele\Compte;
use gdbp\modele\Livre;
use gdbp\vue\VueAccueil;
use Slim\Slim;
use const gdbp\vue\AFFICHER_ACCUEIL;
const SEUIL = 0.80;


class ControleurAffichage
{

    public function afficherPageAccueil()
    {
        if (isset($_SESSION['id_connect']) && $_SESSION['id_connect'] !== null) {
            $res = null;
            if ($res === null) {
                $data = Livre::select("couverture", "titre", "ISBN")->orderBy("score", "desc")->take("18")->get()->toArray();
                $message = "Livres les mieux notés !";
            } else {
                $data = $res;
                $message = "Recommandations via un autre utilisateur !";
            }
            $vue = new VueAccueil(['data' => $data,'message'=>$message]);
            $vue->render();
        } else {
            $vue = new VueAccueil(null);
            $vue->render();
        }
    }

    public function onError()
    {
        Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }

    public static function calculerNbEtoile($score)
    {
        $res = "";
        $nbPlein = 5;
        if ($score <= 10.0) {
            $nbPlein = 0;
        } elseif ($score <= 20.0) {
            $nbPlein = 1;
        } elseif ($score <= 40.0) {
            $nbPlein = 2;
        } elseif ($score <= 60.0) {
            $nbPlein = 3;
        } elseif ($score <= 85.0) {
            $nbPlein = 4;
        }
        for ($i = 0; $i < $nbPlein; $i++) {
            $res .= "★ ";
        }
        for ($j = 0; $j < 5 - $nbPlein; $j++) {
            $res .= "☆ ";
        }
        return trim($res);
    }

    public static function moyenneScore($arr)
    {
        $res = 0.0;
        foreach ($arr as $key => $value) {
            $res += $value;
        }
        return $res / count($arr);
    }

    public static function dateUStoFR($date)
    {
        $date = explode('-', $date);
        return substr($date[2], 0, 2) . '/' . $date[1] . '/' . $date[0];
    }

    public static function dateFRtoUS($date)
    {
        $date = explode('-', $date);
        return substr($date[2], 0, 2) . '-' . $date[1] . '-' . $date[0];
    }

    public static function compareBib()
    {
        $cAll = Compte::all();
        $cUser = Compte::where("identifiant", "=", $_SESSION['id_connect'])->first();
        $livresUser = $cUser->livres->toArray();
        $seuilLivres = round(count($livresUser) * SEUIL);
        foreach ($cAll as $key) {
            $livres = $key->livres->toArray();
            if (count($livres) >= $seuilLivres and $key->identifiant !== $cUser->identifiant) {
                $compUser = 0 ;
                $livresPareil = 0 ;
                $res = array();
                while (isset($livresUser[$compUser])) {
                    $livreOk = false;
                    foreach ($livres as $key2) {
                        if ($key2['ISBN']===$livresUser[$compUser]['ISBN']) {
                            $livreOk = true;
                            $livresPareil++;
                        }
                    }
                    if (!$livreOk) {
                        array_push($res,$key['ISBN']);
                    }
                    $compUser++;
                }
                if (count($res) !== 0) {
                    $ratio = count($livresUser) / count($res);
                    if ($ratio > SEUIL) {
                        return $res;
                    }
                }
            }
        }
        return null;
    }

    public static function viaLivreCouverture()
    {
        $cUser = Compte::where("identifiant", "=", $_SESSION['id_connect'])->first();
        if ($cUser->ISBNDeProfil!==null) {
            $theme = Livre::where("ISBN","=",$cUser->ISBNDeProfil)->first()->themes();
        }
        return null;
    }

}
