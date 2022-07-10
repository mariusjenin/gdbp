<?php


namespace gdbp\vue;


use gdbp\controleur\ControleurAffichage;
use gdbp\modele\Liste;
use Slim\Slim;

const AFFICHER_UNE_LISTE = 1;
const AFFICHER_MES_LISTES = 2;
const AFFICHER_FORMULAIRE_LISTE = 3;
const AFFICHER_FORMULAIRE_AJOUT_LIVRE = 4;

class VueListe
{
    public $arr;

    public function __construct($a)
    {
        $this->arr = $a;
    }

    private function rendreUrlLivre ($liste) {
        $res = array();
        $comp = 0;
        foreach ($liste->livres as $key) {
            array_push($res,$key->couverture);
            $comp++;
            if ($comp > 3) {
                break;
            }
        }
        return $res;
    }

    private function formulaireListe(){
        $error = "";
        $descr = $this->arr['data'][0]['descr'];
        $nomListe = $this->arr['data'][0]['nomListe'];
        $theme = $this->arr['data'][0]['theme'];
        if (isset($this->arr['err'])) {
            $error= $this->arr['err'];
            $error = "<p class=\"erreurconnectinscript\" style='color : red;align-items: center'>$error</p>";
        }
        $urlAction = Slim::getInstance()->urlFor("creerListePost");
      $html=<<<END
      <form method="post" action="$urlAction" enctype="multipart/form-data">
        <div>
        $error
          <label>Nom de la liste <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="nomListe" required value="$nomListe">
        </div>
        <div class="row top-margin">
          <label class="col-xs-12">Description</label>
          <div class="col-xs-12"><textarea class="form-control" name="addDescrliste" id="addDescrliste" rows="5">$descr</textarea></div>
        </div>
        <div class="row top-margin">
          <label class="col-xs-12">Thème</label>
          <div class="col-xs-12"><textarea class="form-control" name="themeListe" id="themeListe">$theme</textarea></div>
        </div>

        <div class="row top-margin">
          <div class="col-xs-12">
            <button class="col-xs-12 btn btn-action" type="submit">Valider</button>
          </div>
        </div>
      </form>
END;
      return $html;
    }

    private function mesListes()
    {
        $html = "<div class=\"endessoustitre\" class=\"contenumeslistes\">";
        $compte = $this->arr['data'];
        $comp = 1;
        foreach ($compte->listes as $liste) {
            $urlListe = Slim::getInstance()->urlFor('afficherListe', ["id" => $liste['numListe']]);
            $reps = $this->rendreUrlLivre($liste);
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
            $nomListe = $liste['nomListe'];
            $description = $liste['description'];
            if ($description === null or trim($description) === "") {
                $description = "Aucune description pour ce livre";
            } else {
                if(strlen($description) > 100) {
                    $description = trim(substr($description,0,100)).' ...';
                }
            }
            $arr = array();
            foreach (Liste::find($liste['numListe'])->livres as $l) {
                array_push($arr, $l['score']);
            }
            if (count($arr) !== 0)
            $score = ControleurAffichage::calculerNbEtoile(ControleurAffichage::moyenneScore($arr));
            else
            $score = "☆ ☆ ☆ ☆ ☆";
            $html .= <<<END
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4" style="margin-bottom:30px">
            <div class="text-center card h-100" style="border:solid 1px #ccc;border-radius:5px">
              <div class="col-sm-12" style="padding:0px;padding-bottom:10px">
                <a href="$urlListe">
                  <div class="imagegrillelistes imagegrillelistes1 col-xs-6" style="background-image:url($urlLivre0)"></div>
                  <div class="imagegrillelistes imagegrillelistes2 col-xs-6" style="background-image:url($urlLivre1)"></div>
                  <div class="imagegrillelistes imagegrillelistes3 col-xs-6" style="background-image:url($urlLivre2)"></div>
                  <div class="imagegrillelistes imagegrillelistes4 col-xs-6" style="background-image:url($urlLivre3)"></div>
                </a>
              </div>
              <div class="card-body">
                <h4 class="card-title">
                  <a href="$urlListe">$nomListe</a>
                </h4>
                <p class="card-text" style="padding:5px">$description</p>
              </div>
              <div class="card-footer">
                <p style="font-size:2.5rem">$score</p>
              </div>
            </div>
          </div>
END;
            if ($comp % 2 === 0 && $comp!=0) {
                $html .= "<!-- Séparateur toutes les 2 cartes -->
          <div class=\"col-xs-12 d-none d-sm-none d-md-block d-lg-none d-xl-none\"></div>
          <!-- -->";
            } elseif ($comp % 3 === 0 && $comp!=0) {
                $html .= "<!-- Séparateur toutes les 3 cartes -->
          <div class=\"col-xs-12 d-none d-sm-none d-md-none d-lg-block d-xl-none\"></div>
          <!-- -->";
            } elseif ($comp % 4 === 0 && $comp!=0) {
                $html .= "<!-- Séparateur toutes les 4 cartes -->
          <div class=\"col-xs-12 d-none d-sm-none d-md-none d-lg-none d-xl-block\"></div>
          <!-- -->";
            }
            $comp++;
        }
        return $html;
    }

    private function afficherUneListe()
    {
        $app=Slim::getInstance();
        $descrListe="";
        if($this->arr['data']->description!="pas de description"){
          $descrListe.="<div class='col-xs-12' style='margin-bottom:30px'><p style='font-size:1.8rem'><strong style='font-size:2rem'>Description</strong> : ".$this->arr['data']->description."</p></div>";
        } else {
          $descrListe="";
        }
        $html = $descrListe;
        $liste = $this->arr['data'];
        $comp = 0;
        foreach ($liste->livres as $livre) {
            $urlImageLivre1 = $livre['couverture'];
            $titre = $livre['titre'];
            if ($titre === null) {
                $titre = "Aucun titre pour ce livre";
            }
            $description = $livre['description'];
            if ($description === null or trim($description) === "") {
                $description = "Aucune description pour ce livre";
            } else {
                if(strlen($description) > 50) {
                    $description = trim(substr($description,0,50)).' ...';
                }
            }
            $urlLivre = $app->urlFor('livre', ["isbn" => $livre['ISBN']]);
            $score = ControleurAffichage::calculerNbEtoile($livre['score']);
            $html .= <<<END
					<div class="col-lg-3 col-md-6 mb-4" style="padding-bottom:10px">
						<div class="text-center card h-100" style="min-height:500px;padding-bottom:10px;border:solid 1px #ccc;border-radius:5px">
							<a href="$urlLivre"><img class="imgvignettelivre card-img-top" src="$urlImageLivre1" style="max-height: 400px"></a>
							<div class="card-body">
								<h4 class="card-title">
									<a href="$urlLivre">$titre</a>
								</h4>
								<p class="card-text"style="padding:5px">$description</p>
							</div>
							<div class="card-footer">
								<p style="font-size:2.5rem">$score</p>
							</div>
						</div>
					</div>
END;
            if ($comp % 2 === 0) {
                $html .= "<!-- Séparateur toutes les 2 cartes -->
					<div class=\"col-xs-12 d-none d-sm-none d-lg-none d-md-block\"></div>
					<!-- -->";
            } elseif ($comp % 4 == 0) {
                $html .= "<!-- Séparateur toutes les 4 cartes -->
					<div class=\"col-xs-12 d-none d-sm-none d-md-none d-lg-block\"></div>
					<!-- -->";
            }
            $comp++;
        }
        return $html;
    }

    private function formulaireListeLivre()
    {
        $urlAction=Slim::getInstance()->urlFor('postListeLivre',['id'=>$this->arr['data']->numListe]);
        $opt="";
        foreach ($this->arr['livres'] as $item) {
            $title = $item['titre'];
            $isbn = $item['ISBN'];
            $urlLivre = Slim::getInstance()->urlFor('livre', ["isbn" => $isbn]);
            $opt.="<li class=\"list-group-item\">
                    <div class=\"checkbox\">
                      <label class=\"col-xs-11\" for='$isbn'>
                        <input type=\"checkbox\" id='$isbn' name='$isbn'>
                        $title
                      </label>
                      <a class=\"col-xs-1\" href=\"$urlLivre\"> Voir</a>
                    <div>
                  </li>";
        }
        return <<<END
<form class="form-group center-block" method="post" action="$urlAction" enctype="multipart/form-data">

<ul class="list-group" style="font-size: 1.6rem;max-height: 650px;overflow-y: scroll; ">
$opt
</ul>
<div class="col-sm-12 text-right">
  <button style="width:250px;font-size: 1.8rem;" class="center-block btn btn-action" type="submit">Valider</button>
</div>
</form>
END;

    }

    public function render($selecteur)
    {
        $content = "";
        $app = Slim::getInstance();
        $page = "Mes Listes";
        $urlJS = $app->request->getRootURI() . '/web_avec_Bootstrap/assets/js';
        $recherche = "Rechercher une liste";
        $breadcrumb = "";
        $boutonAjoutListe = "";
        $urlCreerListe = $app->urlFor('creerListe');
        $nomListe = $page;
        $searchform = "<!-- Search form -->
				<div class='md-form active-pink active-pink-2 mb-3 mt-0'>
					<input class='form-control' type='text' placeholder='$recherche' aria-label='Rechercher'>
				</div>";
        $script = "";
        switch ($selecteur) {
            case AFFICHER_UNE_LISTE :
            {
                $nomListe = $this->arr['data']->nomListe;
                $page = $nomListe;
                $urlListe = $app->urlFor('afficherListe', ['id' => $this->arr['data']->numListe]);
                $content = $this->afficherUneListe();
                $script = "<script type=\"module\" src=\"$urlJS/script_search_biblio.js\"></script>";
                $recherche = "Rechercher un livre";
                $breadcrumb = "<li><a href=\"$urlListe\">$nomListe</a></li>";
                $urlAjoutLivre = Slim::getInstance()->urlFor("ajoutLivreListe", ["id" => $this->arr['data']->numListe]);
                $boutonAjoutListe = "<div class=\"col-xs-12 col-sm-2\"><button onclick=\"window.location.href='$urlAjoutLivre'\" class=\"btn-dark btn btn-ajout btn-right-page-title\" type=submit><i class=\"fas fa-plus plus-btn-ajout\"></i><span style=\"vertical-align:middle;\">Ajouter un livre à la liste</span></button></div>";
                break;
            }
            case AFFICHER_MES_LISTES :
            {
                $script = "<script src=\"$urlJS/script_search_liste.js\"></script>";
                $boutonAjoutListe = <<<END
                <div class='col-xs-12 col-sm-2'><button onclick="window.location.href='$urlCreerListe'" class='btn-dark btn btn-ajout btn-right-page-title' type=submit><i class='fas fa-plus plus-btn-ajout'></i><span style='vertical-align:middle;'>Créer une liste</span></button></div>
                </div>
END;
                $content = $this->mesListes();
                break;
            }
            case AFFICHER_FORMULAIRE_LISTE :
            {

                $nomListe = "Créer une liste";
                $page = $nomListe;
                $searchform = "";
                $breadcrumb = "<li><a href=\"$urlCreerListe\">Créer une liste</a></li>";
                $content = $this->formulaireListe();
                break;
            }
            case AFFICHER_FORMULAIRE_AJOUT_LIVRE :
                $page = "Ajouter des livres à la liste : " . $this->arr['data']->nomListe;

                $nomListe = $this->arr['data']->nomListe;
                $urlListe = $app->urlFor('afficherListe', ['id' => $this->arr['data']->numListe]);

                $searchform = "";
                $urlAjoutLivreListe = $app->urlFor("ajoutLivreListe", ["id" => $this->arr['data']->numListe]);

                $breadcrumb = "<li><a href=\"$urlListe\">$nomListe</a></li><li><a href=\"$urlAjoutLivreListe\">Ajouter des livres à la liste</a></li>";
                $content = $this->formulaireListeLivre();
                break;
        }
        $urlRacine = $app->urlFor('racine');
        $urlMesListes = $app->urlFor('getListes');
        $html = <<<END
  <!-- container -->
  <div class="container">

    <ol class="breadcrumb">
      <li><a href="$urlRacine">Accueil</a></li>
      <li>Mes Livres</li>
      <li><a href="$urlMesListes">Mes Listes</a></li>
      $breadcrumb
    </ol>

    <div class="row">

      <!-- Article main content -->
      <article class="col-sm-12 maincontent">
        <header class="page-header">
        <div class="row">
          <div class="col-xs-12 col-sm-10"><h1 class="page-title">$page</h1></div>
          $boutonAjoutListe
        </header>

        $searchform

        <div class="container" id="adaptive-content" style="margin-top:30px">

          $content

        </div>
        <!-- /.row -->

      </article>
      <!-- /Article -->
    </div>
  </div>	<!-- /container -->
END;

        echo VueGlobale::getHeader($app, $page) . $html . VueGlobale::getFooter($app, $script);
    }

}
