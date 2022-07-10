<?php


namespace gdbp\controleur;

use gdbp\modele\Liste;
use gdbp\modele\Livre;
use gdbp\vue\VueAvis;
use Slim\Slim;
use gdbp\modele\Avis;
use const gdbp\vue\AFFICHER_AVIS_LIVRE;
use const gdbp\vue\AFFICHER_AVIS_PROFIL;

class ControleurAvis
{
    public function afficherPageAvis()
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $data = Avis::where('identifiant','=',$_SESSION['id_connect'])->orderBy('updated_at','desc')->get()->take(50);
            $vue = new VueAvis(['data'=>$data]);
            $vue->render(null);
        } else {
            Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
        }
    }

    public static function afficherAvisProfil($id) {
        $data = Avis::where('identifiant','=',$id)->orderBy('updated_at','desc')->get()->take(4);
        $vue = new VueAvis(['data'=>$data]);
        return $vue->render(AFFICHER_AVIS_PROFIL);
    }

    public static function afficherAvisLivre($isbn) {
        $data = Avis::where('ISBNAvis','=',$isbn)->orderBy('updated_at','desc')->get()->take(4);
        $vue = new VueAvis(['data'=>$data,'isbn'=>$isbn]);
        return $vue->render(AFFICHER_AVIS_LIVRE);
    }

    public function ajouterAvis($isbn)
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] !== null and isset($_POST['addComment']) and isset($_POST['postEtoiles'])) {
            $a = new Avis();
            $a->ISBNAvis = $isbn;
            $a->identifiant = $_SESSION['id_connect'];
            $a->contenu = filter_var($_POST['addComment'], FILTER_SANITIZE_STRING);
            $a->note=$_POST['postEtoiles']*20;
            $a->save();
            $avis = Avis::where('ISBNAvis',"=",$isbn)->get()->toArray();
            $score=0;
            foreach ($avis as $key) {
                $score += $key['note'];
            }
            $liste = Livre::find($isbn);
            $liste->score =$score/count($avis);
            $liste->update();
        }
        Slim::getInstance()->redirect(Slim::getInstance()->urlFor("livre", ["isbn" => $isbn]));
    }
}
