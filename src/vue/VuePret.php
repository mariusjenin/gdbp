<?php


namespace gdbp\vue;

use gdbp\controleur\ControleurAffichage;
use gdbp\modele\Compte;
use gdbp\modele\Livre;
use Slim\Slim;

const FORMULAIRE_PRET = 1;


class VuePret
{
    public $arr;

    public function __construct($a)
    {
        $this->arr = $a;
    }

    private function afficherPrets()
    {

        $html = "<div class=\"row\">
          <header class=\"page-header\">
					<h1 class=\"page-title\">Mes Prêts</h1>
				</header>
        </div>

        <div class=\"row\">
                <h1 class='h3'>Livres que j'ai prêté :</h1>
				        <div class=\"col-sm-12\">";

      //  $html.= "<div class=\"endessoustitre\" class=\"contenumeslistes\">";
        $compte = $this->arr['data'];
        $prets = $compte->pretsPret();
        $comp = 1;
        foreach ($prets->get() as $pret) {
            $livre = $pret->livre;
            if ($livre !== null) {
                $urlLivre1 = $livre->couverture;
                $titre = $livre['titre'];
                $urlLivre = Slim::getInstance()->urlFor('livre', ["isbn" => $livre['ISBN']]);
                if ($pret['dateRendu'] === null) {
                    $dateARendre = "Date de rendu : " . ControleurAffichage::dateUStoFR($pret['dateARendre']);
                    $date_pret=date_create($pret['dateARendre']);
                    $date_actuelle = date_create();
                    $diff = date_diff($date_pret,$date_actuelle);
                    $diff = intval($diff->format("%R%a"));
                    if($diff>=0){
                      $couleur = "#db5757";
                    }else{
                      $couleur = "#db8d55";
                    }
                } else {
                    $dateARendre = "Rendu le : " . ControleurAffichage::dateUStoFR($pret['dateRendu']);
                    $couleur = "#96db55";
                }

                $html .= <<<END
                    <div class="col-lg-3 col-md-6 mb-4" style="padding-bottom:10px">
                      <div class="text-center card h-100" style="background-color:$couleur;padding-bottom:10px;border:solid 1px #ccc;border-radius:5px">
                        <a href="$urlLivre"><img class="imgvignettelivre card-img-top" style="max-height:400px" src="$urlLivre1"></a>
                        <div class="card-body">
                          <h4 class="card-title">
                            <a style="color:#333" href="$urlLivre">$titre</a>
                          </h4>
                          <p class="card-text"style="padding:5px;">$dateARendre</p>
                        </div>
                      </div>
                    </div>
END;
                if ($comp % 2 === 0 && $comp!=0) {
                    $html .= "<!-- Séparateur toutes les 2 cartes -->
          <div class=\"col-xs-12 d-none d-sm-none d-lg-none d-md-block\"></div>
          <!-- -->";
                } elseif ($comp % 4 == 0 && $comp!=0) {
                    $html .= "<!-- Séparateur toutes les 4 cartes -->
          <div class=\"col-xs-12 d-none d-sm-none d-md-none d-lg-block\"></div>
          <!-- -->";
                }
                $comp++;
            }
        }

        //marius séparer ici
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class=\"row\">";
        $html .= "<h1 class='h3'>Livres que l'on m'a prêté :</h1>";
        $html .= "<div class=\"col-sm-12\" style=\"margin-top:30px\">";

        $prets = $compte->pretsDesti();
        $comp = 1;
        foreach ($prets->get() as $pret) {
            $livre = $pret->livre;
            if ($livre !== null) {
                $urlLivre1 = $livre->couverture;
                $titre = $livre['titre'];
                $urlLivre = Slim::getInstance()->urlFor('livre', ["isbn" => $livre['ISBN']]);
                if ($pret['dateRendu'] === null) {
                    $dateARendre = "Date de rendu : " . ControleurAffichage::dateUStoFR($pret['dateARendre']);
                    $date_pret=date_create($pret['dateARendre']);
                    $date_actuelle = date_create();
                    $diff = date_diff($date_pret,$date_actuelle);
                    $diff = intval($diff->format("%R%a"));
                    if($diff>=0){
                      $couleur = "#db5757";
                    }else{
                      $couleur = "#db8d55";
                    }
                } else {
                    $dateARendre = "Rendu le : " . ControleurAffichage::dateUStoFR($pret['dateRendu']);
                    $couleur = "#96db55";
                }
                $html .= <<<END
                    <div class="col-lg-3 col-md-6 mb-4" style="padding-bottom:10px">
                      <div class="text-center card h-100" style="background-color:$couleur;padding-bottom:10px;border:solid 1px #ccc;border-radius:5px">
                        <a href="$urlLivre"><img class="imgvignettelivre card-img-top" style="max-height:400px"
                         src="$urlLivre1"></a>
                        <div class="card-body">
                          <h4 class="card-title">
                            <a style="color:#333" href="$urlLivre">$titre</a>
                          </h4>
                          <p class="card-text"style="padding:5px;">$dateARendre</p>
                        </div>
                      </div>
                    </div>
END;
                if ($comp % 2 === 0 && $comp!=0) {
                    $html .= "<!-- Séparateur toutes les 2 cartes -->
                      <div class=\"col-xs-12 d-none d-sm-none d-lg-none d-md-block\"></div>
                      <!-- -->";
                } elseif ($comp % 4 == 0 && $comp!=0) {
                    $html .= "<!-- Séparateur toutes les 4 cartes -->
                      <div class=\"col-xs-12 d-none d-sm-none d-md-none d-lg-block\"></div>
                      <!-- -->";
                }
                $comp++;
            }
        }

        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }

    public function afficherFormulaire(){
      $error = "";
      $dateDeb = $this->arr['data'][0]['dateDeb'];
      $dateARendre = $this->arr['data'][0]['dateARendre'];
      $pseudoEmprunteur = $this->arr['data'][0]['pseudoEmprunteur'];
      $isbn = $this->arr['data'][0]['isbn'];
      if (isset($this->arr['err'])) {
          $error= $this->arr['err'];
          $error = "<p class=\"erreurconnectinscript\" style='color : red;align-items: center'>$error</p>";
      }
      $urlAction = Slim::getInstance()->urlFor("preterPost",['isbn'=>$isbn]);
    $html=<<<END
    <form method="post" action="$urlAction" enctype="multipart/form-data">
      <div class="row top-margin">
      $error
        <label class="col-xs-12">Date de début du prêt</label>
        <div class="col-xs-12"><input type="date" class="form-control" name="dateDeb" required value="$dateDeb"></div>
      </div>
      <div class="row top-margin">
        <label class="col-xs-12">Date de fin du prêt</label>
        <div class="col-xs-12"><input type="date" class="form-control" name="dateARendre" id="dateARendre" rows="5">$dateARendre</textarea></div>
      </div>
      <div class="row top-margin">
        <label class="col-xs-12">Pseudonyme de l'emprunteur</label>
        <div class="col-xs-12"><input type="text" class="form-control" name="pseudoEmprunteur" id="pseudoEmprunteur">$pseudoEmprunteur</textarea></div>
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

    public function render($selecteur)
    {
        $app = Slim::getInstance();
        $content = "";
        switch ($selecteur) {
            case FORMULAIRE_PRET :
                $content = $this->afficherFormulaire();
                break;
            default:
                $content = $this->afficherPrets();
                break;
        }
        $urlRacine = $app->urlFor('racine');
        $urlPret = $app->urlFor('mesPrets');
        $html = <<<END
	<!-- container -->
	<div class="container">

		<ol class="breadcrumb">
			<li><a href="$urlRacine">Accueil</a></li>
			<li>Mes Livres</li>
			<li><a href="$urlPret">Mes Prêts</a></li>
		</ol>
		$content
	</div>	<!-- /container -->
END;
        echo VueGlobale::getHeader($app, "Mes Prêts") . $html . VueGlobale::getFooter($app, null);
    }
}
