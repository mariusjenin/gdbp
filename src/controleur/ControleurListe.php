<?php


namespace gdbp\controleur;


use Cassandra\Date;
use gdbp\modele\Compte;
use gdbp\modele\Liste;
use gdbp\modele\Livre;
use gdbp\modele\Theme;
use gdbp\vue\VueListe;
use gdbp\vue\VueLivre;
use Illuminate\Database\QueryException;
use Slim\Slim;
use Symfony\Component\Translation\Util\ArrayConverter;
use const gdbp\vue\AFFICHER_AJOUT_LIVRE;
use const gdbp\vue\AFFICHER_FORMULAIRE_AJOUT_LIVRE;
use const gdbp\vue\AFFICHER_LIVRE;
use const gdbp\vue\AFFICHER_UNE_LISTE;
use const gdbp\vue\AFFICHER_MES_LISTES;
use const gdbp\vue\AFFICHER_FORMULAIRE_LISTE;

class ControleurListe
{

    public function afficherListes()
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $data = Compte::find($_SESSION['id_connect']);
            $vue = new VueListe(['data' => $data]);
            $vue->render(AFFICHER_MES_LISTES);
        } else Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }

    public function afficherListe($id)
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $data = Liste::find($id);
            if ($data !== null) {
                $vue = new VueListe(['data' => $data]);
                $vue->render(AFFICHER_UNE_LISTE);
            } else {
                Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
            }

        } else Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }

    public function afficherFormulaireListe()
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $arr = array();
            array_push($arr,["nomListe"=>"","descr"=>"","theme"=>""]);
            $vue = new VueListe(["data"=>$arr]);
            $vue->render(AFFICHER_FORMULAIRE_LISTE);
        } else Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }

    public function creerListe(){
      $nomliste=filter_var($_POST['nomListe'],FILTER_SANITIZE_STRING);
      $descrListe=filter_var($_POST['addDescrliste'],FILTER_SANITIZE_STRING);
      $themeListe=filter_var($_POST['themeListe'],FILTER_SANITIZE_STRING);
      if (Liste::where("nomListe","=",$nomliste)->first() === null) {
          $liste = new Liste();
          $liste->nomliste = $nomliste;
          $liste->description = $descrListe;
          $liste->identifiant = $_SESSION['id_connect'];
          $th = Theme::where("nomTheme","=",$themeListe)->first();
          if($th === null){
            $theme = new Theme();
            $theme->nomTheme = $themeListe;
            $theme->save();
            $num = $theme->numTheme;
          }else{
            $num = $th->numTheme;
          }
          $liste->save();
          $liste->themes()->attach($num);
          $id = $liste->numListe;
          Slim::getInstance()->redirect(Slim::getInstance()->urlFor("afficherListe",['id'=>$id]));
      } else {
          $arr = array();
          array_push($arr,["nomListe"=>$nomliste,"descr"=>$descrListe,"theme"=>""]);
          $v = new VueListe(['data' => $arr,'err'=>"Ce nom de liste existe déjà"]);
          $v->render(AFFICHER_FORMULAIRE_LISTE);
      }
    }

    public static function rendreDonneeAjax($champUser)
    {
        $res = array();
        $data = Compte::find($_SESSION['id_connect'])->listes;
        foreach ($data as $listes) {
            if (str_contains(str_replace(" ", "", strtolower($listes['nomListe'])), str_replace(" ", "", strtolower($champUser)))
                or trim($champUser) == "") {
                $reps = ControleurListe::rendreImages($listes);
                $urlLivre="urlLivre";
                $default = Slim::getInstance()->request->getRootURI() . '/web_avec_Bootstrap/assets/images/default.png';
                $urlLivre0= $default;
                $urlLivre1= $default;
                $urlLivre2= $default;
                $urlLivre3= $default;
                foreach ($reps as $rep=>$value) {
                    $cc = $urlLivre.$rep;
                    $$cc = $value;
                }
                $nom = $listes['nomListe'];
                if ($nom === null) {
                    $nom = "Aucun nom pour cette liste";
                }
                $urlListe = Slim::getInstance()->urlFor('afficherListe', ["id" => $listes['numListe']]);
                $description = $listes['description'];
                if ($description === null or trim($description) === "") {
                    $description = "Aucune description pour ce livre";
                } else {
                    if(strlen($description) > 100) {
                        $description = trim(substr($description,0,100)).' ...';
                    }
                }
                $arr = array();
                foreach (Liste::find($listes['numListe'])->livres as $l) {
                    array_push($arr, $l['score']);
                }
                if (count($arr) !== 0)
                    $score = ControleurAffichage::calculerNbEtoile(ControleurAffichage::moyenneScore($arr));
                else
                    $score = "☆ ☆ ☆ ☆ ☆";
                if ($description === null or trim($description) === "") {
                    $description = "Aucune description pour cette liste";
                }
                array_push($res, ['champ' => $champUser, 'nom' => $nom, 'urlImage1' => $urlLivre0, 'urlImage2' => $urlLivre1, 'urlImage3' => $urlLivre2,
                    'urlImage4' => $urlLivre3, 'urlListe' => $urlListe, 'description' => $description, 'score' => $score]);
            }
        }
        echo json_encode($res);
    }


    public static function rendreImages($listes) {
        $res = array();
        $comp = 0;
        foreach ($listes->livres as $key) {
            array_push($res,$key->couverture);
            $comp++;
            if ($comp > 3) {
                break;
            }
        }
        return $res;
    }

    public function ajouterNouveauLivre($id, $isbn)
    {
        $liste = Liste::find($id);
        if ($liste!==null) {
            try {
                $liste->livres()->attach($isbn);
                $vue = new VueListe(['data' => $liste]);
                $vue->render(AFFICHER_UNE_LISTE);
            }catch (QueryException $qe) {
                $data = Livre::find($isbn);
                $data2 = Liste::where('identifiant','=',$_SESSION['id_connect'])->whereNotNull('updated_at')->orderBy("updated_at","desc")->take(3)->get()->toArray();
                $vue = new VueLivre(["data"=>$data,"listeRecente"=>$data2,"err"=>"Ce livre est déjà dans cette liste"]);
                $vue->render(AFFICHER_LIVRE);
            }
        } else {
            $data = Livre::find($isbn);
            $data2 = Liste::where('identifiant','=',$_SESSION['id_connect'])->whereNotNull('updated_at')->orderBy("updated_at","desc")->take(3)->get()->toArray();
            $vue = new VueLivre(["data"=>$data,"listeRecente"=>$data2,"err"=>"Ce livre est déjà dans cette liste"]);
            $vue->render(AFFICHER_LIVRE);
        }

    }

    public function afficherFormulaireLivreListe($id)
    {
        $liste = Liste::find($id);
        $livres = Compte::find($_SESSION['id_connect'])->livres->toArray();
        $vue = new VueListe(["data"=>$liste,"livres"=>$livres]);
        $vue->render(AFFICHER_FORMULAIRE_AJOUT_LIVRE);
    }

    public function ajouterListeLivre($id)
    {
        $liste = Liste::find($id);
        foreach ($_POST as $key => $value) {
            try {
                $liste->livres()->attach($key);
            } catch (QueryException $qe) {
                //do nothing
            }
        }
        $vue = new VueListe(["data"=>$liste]);
        $vue->render(AFFICHER_UNE_LISTE);
    }
}
