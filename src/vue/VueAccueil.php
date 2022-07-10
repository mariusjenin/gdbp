<?php

namespace gdbp\vue;

use Slim\Slim;

class VueAccueil
{
    public $arr;

    public function __construct($a)
    {
        $this->arr = $a;
    }

    private function carrousel() {
        $res = "";
        if (isset($this->arr['data'])) {
            $adapt="";
            $message = $this->arr['message'];
            foreach ($this->arr['data'] as $key) {
                $couv = $key['couverture'];
                $url = Slim::getInstance()->urlFor("livre",["isbn"=>$key['ISBN']]);
               $adapt .="<div class=\"col-xs-2\">
                  <a href='$url'><img  src=\"$couv\" width=\"100%\" ></a>
                </div>";
            }
            $res = <<<END
<h2 class="thin">$message</h2>
<br>
<div class="carrousel">
    $adapt
  </div>
END;

        }
        return $res;
    }

    private function afficherAccueil()
    {
        $carr = $this->carrousel();
        return <<<END
	<!-- Header -->
	<header id="head">
		<div class="container">
			<div class="row">
				<h1 class="lead">GESTION DE BIBLIOTHEQUES PERSONNELLES</h1>
				<p class="thin tagline h3">Votre outil numérique pour gérer votre bibliothèque en ligne et
				découvrir une multitude de nouveaux ouvrages</p>

			</div>
		</div>
	</header>
	<!-- /Header -->

	<!-- Intro -->
	<div class="container-fluid text-center">
		<br> <br>
		$carr
		<br> <br>
  </div>
  <div class="container text-center">
		<h2 class="thin">GDBP</h2><h3 class="thin">La gestion de vos livres à travers un outil ergonomique</h3>
		<p class="text-muted">
			Nous avons pensé à vos amis ou à votre famille à qui vous pouvez prêter des livres : <br>rendez-vous dans la rubrique <i>Mes livres > Mes prêts</i></p>
	</div>
	<!-- /Intro-->

	<!-- Highlights - jumbotron -->
	<div class="jumbotron top-space">
		<div class="container">

			<h3 class="text-center thin">Pourquoi GDBP va vous être utile ?</h3>

			<div class="row">
				<div class="col-md-3 col-sm-6 highlight">
					<div class="h-caption"><h4><i class="fa fa-book fa-5"></i>Votre bibliothèque</h4></div>
					<div class="h-body text-center">
						<p>Notre site va vous permettre d'ajouter tous vos livres en rentrant leur code unique de livre (que l'on appelle ISBN).</p>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 highlight">
					<div class="h-caption"><h4><i class="fa fa-archive fa-5"></i>Gestion comme bon vous semble</h4></div>
					<div class="h-body text-center">
						<p>La création de listes va vous donner la possibilité de réunir tous vos livres favoris en un seul lieu ou encore de regrouper une partie de votre bibliothèque par thèmes</p>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 highlight">
					<div class="h-caption"><h4><i class="fa fa-barcode fa-5"></i>Toutes les informations à portée de main</h4></div>
					<div class="h-body text-center">
						<p>Notre outil utilise le code unique ISBN de chaque livre pour pouvoir vous fournir et vous présenter toutes les informations de ceux-ci.</p>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 highlight">
					<div class="h-caption"><h4><i class="far fa-handshake fa-5"></i>Partage avec les autres utilisateurs</h4></div>
					<div class="h-body text-center">
						<p>Non seulement vous pouvez échanger avec les autres internautes quant à certains livres mais vous pouvez aussi noter chaque livre que vous possédez pour que tout le monde profite de votre expérience</p>
					</div>
				</div>
			</div> <!-- /row  -->

		</div>
	</div>
	<!-- /Highlights -->

	<!-- container -->
	<div class="container">
		<div class="jumbotron top-space">
			<div class="row">
            <div class="col-lg-6 mx-auto">

                <!-- CUSTOM BLOCKQUOTE -->
                <blockquote class="blockquote blockquote-custom bg-white p-5 shadow rounded">
                    <div class="blockquote-custom-icon shadow-sm"><i class="fa fa-quote-left text-white"></i></div>
                    <p class="mb-0 mt-2 font-italic">“Lire un bon livre, c’est faire une rencontre.”</p>
                    <footer class="blockquote-footer pt-4 mt-4 border-top">Tania de Montaigne
                    </footer>
                </blockquote><!-- END -->

            </div>
        </div>
  		</div>
</div>	<!-- /container -->

END;

    }

    public function render()
    {
        $content = $this->afficherAccueil();
        $app = Slim::getInstance();
        $urlAssests = $app->request->getRootURI() . '/web_avec_Bootstrap/assets';
        $script = <<<END
<script type="text/javascript" src="$urlAssests/slick/slick.min.js"></script>
<script type="text/javascript" src="$urlAssests/js/script_perso_carrousel.js"></script>
END;
        echo VueGlobale::getHeader($app, null) . $content . VueGlobale::getFooter($app, $script);
    }
}
