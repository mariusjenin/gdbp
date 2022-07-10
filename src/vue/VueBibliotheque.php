<?php


namespace gdbp\vue;

use gdbp\controleur\ControleurAffichage;
use Slim\Slim;

const AFFICHER_MA_BIBLIOTHEQUE = 1;

class VueBibliotheque
{
    private $arr;

    public function __construct($a)
    {
        $this->arr = $a;
    }

    private function afficherBibli()
    {
        $html = "";
        $comp = 1;
        foreach ($this->arr['data'] as $livre) {
            $urlImgLivre = $livre['couverture'];
            $titre = $livre['titre'];
            if ($titre === null) {
                $titre = "Aucun titre pour ce livre";
            }
            $description = $livre['description'];
            if ($description === null or trim($description) === "") {
                $description = "Aucune description pour ce livre";
            } else {
                if(strlen($description) > 100) {
                    $description = trim(substr($description,0,100)).' ...';
                }
            }
            $urlLivre = Slim::getInstance()->urlFor('livre', ["isbn" => $livre['ISBN']]);
            $score = ControleurAffichage::calculerNbEtoile($livre['score']);
            $html .= <<<END
					<div class="col-lg-3 col-md-6 mb-4" style="padding-bottom:10px">
						<div class="text-center card h-100" style="min-height:500px;padding-bottom:10px;border:solid 1px #ccc;border-radius:5px">
							<a href="$urlLivre"><img class="imgvignettelivre card-img-top" src="$urlImgLivre" style="max-height: 400px" alt="image du livre"></a>
							<div class="card-body">
								<h4 class="card-title">
									<a href="$urlLivre">$titre</a>
								</h4>
								<p class="card-text" style="padding:5px">$description</p>
							</div>
							<div class="card-footer">
								<p>$score</p>
							</div>
						</div>
					</div>
END;
            if ($comp % 2 === 0 && $comp!=0) {
                $html .= "<!-- Séparateur toutes les 2 cartes -->
					<div class=\"col-xs-12 d-none d-sm-none d-lg-none d-md-block\"></div>
					<!-- -->";
            }
            if ($comp % 4 == 0 && $comp!=0) {
                $html .= "<!-- Séparateur toutes les 4 cartes -->
					<div class=\"col-xs-12 d-none d-sm-none d-md-none d-lg-block\"></div>
					<!-- -->";
            }
            $comp++;
        }
        return $html;
    }

    public function render($selecteur)
    {
        $app = Slim::getInstance();
        switch ($selecteur) {
            case AFFICHER_MA_BIBLIOTHEQUE:
            default :
                $content = $this->afficherBibli();
                break;
        }
        $urlAjoutLivre = $app->urlFor('ajouterLivre');
        $urlBibliotheque = $app->urlFor('maBibliotheque');
        $urlRacine = $app->urlFor('racine');
        $page = "Ma Bibliothèque";
        $html = <<<END
	<!-- container -->
	<div class="container">
        <ol class="breadcrumb">
			<li><a href="$urlRacine">Accueil</a></li>
			<li>Mes Livres</li>
			<li><a href="$urlBibliotheque">$page</a></li>
		</ol>
		<div class="row">

			<!-- Article main content -->
			<article class="col-sm-12 maincontent">
				<header class="page-header">
          <div class="row">
					  <div class="col-xs-12 col-sm-6"><h1 class="page-title">$page</h1></div>
            <div class="col-xs-12 col-sm-6"><button onclick="window.location.href='$urlAjoutLivre'" class="btn-dark btn btn-ajout btn-right-page-title" type=submit><i class="fas fa-plus plus-btn-ajout"></i><span style="vertical-align:middle;">Ajouter un Livre</span></button></div>
          </div>
				</header>
				<!-- Search form -->
				<div class="md-form active-pink active-pink-2 mb-3 mt-0">
					<input class="form-control" type="text" placeholder="Rechercher un livre" aria-label="Rechercher">
				</div>
				<div class="container" id="adaptive-content" style="margin-top:30px">
                $content
                </div>
			</article>
			<!-- /Article -->
		</div>
</div>	<!-- /container -->
END;
        $urlJS = $app->request->getRootURI() . '/web_avec_Bootstrap/assets/js';
        $script = "<script type=\"module\" src=\"$urlJS/script_search_biblio.js\"></script>";
        echo VueGlobale::getHeader($app, $page) . $html . VueGlobale::getFooter($app, $script);
    }

}
