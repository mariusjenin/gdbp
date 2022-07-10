<?php


namespace gdbp\controleur;


use gdbp\modele\Compte;
use gdbp\modele\Pret;
use gdbp\vue\VuePret;
use Slim\Slim;
use const gdbp\vue\FORMULAIRE_PRET;

class ControleurPret
{

    public function afficherPrets()
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $data = Compte::find($_SESSION['id_connect']);
            $vue = new VuePret(['data' => $data]);
            $vue->render(null);
        } else {
            Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
        }

    }

    public function afficherFormulairePret($isbn)
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $arr = array();
            array_push($arr,["dateDeb"=>null,"dateARendre"=>null,"pseudoEmprunteur"=>"","isbn"=>$isbn]);
            $vue = new VuePret(["data"=>$arr]);
            $vue->render(FORMULAIRE_PRET);
        } else Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }



    public function preter($isbn){
      if (isset($_SESSION['id_connect']) and $_SESSION['id_connect']!=null and isset($_POST['dateDeb']) and isset($_POST['dateARendre']) and isset($_POST['pseudoEmprunteur'])) {
          $pseudoEmprunteur = filter_var($_POST['pseudoEmprunteur'],FILTER_SANITIZE_STRING);
          $compte = Compte::where("pseudo","=",$pseudoEmprunteur)->first();
          if($compte !==null){
            $id = $compte->identifiant;
            $p = new Pret();
            $p->dateDeb=$_POST['dateDeb'];
            $p->dateARendre=$_POST['dateARendre'];
            $p->idPreteur=$_SESSION['id_connect'];
            $p->idEmprunteur=$id;
            $p->ISBN=$isbn;
            $p->save();

            Slim::getInstance()->redirect(Slim::getInstance()->urlFor("mesPrets"));
          }else{
            $arr = array();
            array_push($arr,["dateDeb"=>null,"dateARendre"=>null,"pseudoEmprunteur"=>"","isbn"=>$isbn]);
            $vue = new VuePret(["data"=>$arr,"err"=>"Le pseudonyme entré ne correspond à aucun utilisateur"]);
            $vue->render(FORMULAIRE_PRET);
          }
      }else{
        $arr = array();
        array_push($arr,["dateDeb"=>null,"dateARendre"=>null,"pseudoEmprunteur"=>"","isbn"=>$isbn,'err'=>"Veuillez remplir tous les champs"]);
        $vue = new VuePret(["data"=>$arr]);
        $vue->render(FORMULAIRE_PRET);
      }
    }
}
