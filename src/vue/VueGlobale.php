<?php


namespace gdbp\vue;


class VueGlobale
{
    public static function getHeader($app, $page)
    {
        $urlRacine = $app->urlFor('racine');
        $urlMesListes = $app->urlFor('getListes');
        $urlBibliotheque = $app->urlFor('maBibliotheque');
        $urlProfil = $app->urlFor('monProfil');
        $urlPret = $app->urlFor('mesPrets');
        $urlAjoutLivre = $app->urlFor('ajouterLivre');
        $urlConnexion = $app->urlFor('connexion');
        $urlAvis = $app->urlFor('mesAvis');
        $urlDeconnexion = $app->urlFor('deconnexion');
        $active = ["acc" => "", "livres" => "", "profil" => "", "connection" => ""];
        switch ($page) {
            case null:
                $active['acc'] = "active";
                break;
            case "Ma Bibliothèque" :
            case "Mes Listes" :
            case "Mes Livres" :
            case "Mes Avis" :
            case "Mes Prêts":
            case "Ajouter un livre":
                $active['livres'] = "active";
                break;
            case "Mon Profil" :
                $active['profil'] = "active";
                break;
            default :
                $active['connection'] = "active";
                break;
        }
        $connectionThingUp = "<li class='{$active['acc']}'><a href=\"$urlRacine\">Accueil</a></li>";
        if (!isset($_SESSION['id_connect'])) {
            $urlMesListes = $urlConnexion;
            $urlBibliotheque = $urlConnexion;
            $urlProfil = $urlConnexion;
            $urlAvis = $urlConnexion;
            $urlPret = $urlConnexion;
            $connectionThingUp .= "
<li><a class=\"btn class='{$active['connection']}'\" href=\"$urlConnexion\">S'inscrire / Se connecter</a></li>";
        } else {
            $connectionThingUp .= "
<li class=\"{$active['livres']} dropdown\">
						<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Mes Livres<b class=\"caret\"></b></a>
						<ul class=\"dropdown-menu\">
							<li><a href=\"$urlBibliotheque\">Ma Bibliothèque</a></li>
							<li><a href=\"$urlMesListes\">Mes Listes</a></li>
							<li><a href=\"$urlAvis\">Mes Avis</a></li>
							<li><a href=\"$urlPret\">Mes Prêts</a></li>
              <li><a href=\"$urlAjoutLivre\">Ajouter un Livre</a></li>
						</ul>
					</li>
					<li class='{$active['profil']}'><a href=\"$urlProfil\">Mon Profil</a></li>
					<li class='{$active['connection']}'><a class=\"btn\" href=\"$urlDeconnexion\">Se déconnecter</a></li>";
        }
        $urlCSS = $app->request->getRootURI() . '/web_avec_Bootstrap/assets/css';
        $urlAssets = $app->request->getRootURI() . '/web_avec_Bootstrap/assets';
        $urlLogo = $app->request->getRootUri() . '/web_avec_Bootstrap/assets/images/logo.png';
        $urlFavicon = $app->request->getRootUri() . '/web_avec_Bootstrap/assets/images/gt_favicon.png';
        if ($page !== null) {
            $adaptPage = "<header id=\"head\" class=\"secondary\"></header>";
        } else {
            $adaptPage = "";
        }
        return <<<END
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport"    content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author"      content="Marius Jenin">

	<title>Gestion de Bibliothèques Personnelles</title>

	<link rel="shortcut icon" href="$urlFavicon">

	<link rel="stylesheet" media="screen" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
	<link rel="stylesheet" href="$urlCSS/bootstrap.min.css">
	<link rel="stylesheet" href="$urlCSS/font-awesome.min.css">

	<!-- Custom styles for the template -->
	<link rel="stylesheet" href="$urlCSS/bootstrap-theme.css" media="screen" >
	<link rel="stylesheet" href="$urlCSS/main.css">
    <link rel="stylesheet" href="$urlCSS/perso.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="$urlAssets/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="$urlAssets/slick/slick-theme.css"/>
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="../src/vue/assets/js/html5shiv.js"></script>
	<script src="../src/vue/assets/js/respond.min.js"></script>
	<![endif]-->
</head>

<body class="home">
	<!-- Fixed navbar -->
	<div class="navbar navbar-inverse navbar-fixed-top headroom" >
		<div class="container">
			<div class="navbar-header">
				<!-- Button for smallest screens -->
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
				<a class="navbar-brand" href="$urlRacine"><img src="$urlLogo" alt="Logo GDBP"></a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav pull-right">
					$connectionThingUp
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div>
	<!-- /.navbar -->
	$adaptPage
END;

    }

    public static function getFooter($app, $script)
    {
        $urlRacine = $app->urlFor('racine');
        $urlBibliotheque = $app->urlFor('maBibliotheque');
        $urlProfil = $app->urlFor('monProfil');
        $urlConnexion = $app->urlFor('connexion');
        $urlJS = $app->request->getRootURI() . '/web_avec_Bootstrap/assets/js';
        $connectionThingDown = "";
        $urlDeconnexion = $app->urlFor('deconnexion');
        if (!isset($_SESSION['id_connect'])) {
            $urlBibliotheque = $urlConnexion;
            $urlProfil = $urlConnexion;
            $connectionThingDown = "<b><a href=\"$urlConnexion\">Se connecter</a></b>";
        } else {
            $connectionThingDown = "<a href=\"$urlBibliotheque\">Mes Livres</a> |
								<a href=\"$urlProfil\">Mon Profil</a> |
								<b><a href=\"$urlDeconnexion\">Se déconnecter</a></b>";
        }
        return <<<END
<footer id="footer" class="top-space">

		<div class="footer1">
			<div class="container">
				<div class="row">

					<div class="col-md-3 widget">
						<h3 class="widget-title">Contact</h3>
						<div class="widget-body">
							<a href="mailto:marius2401@laposte.net">marius2401@laposte.net</a>
							<a href="mailto:theo.pennerat@gmail.com">theo.pennerat@gmail.com</a>
							<a href="mailto:damien.michel0301@gmail.com">damien.michel0301@gmail.com</a>
							<a href="mailto:tom.mendez.mailbox@gmail.com">tom.mendez.mailbox@gmail.com</a><br>
						</div>
					</div>

				</div> <!-- /row of widgets -->
			</div>
		</div>

		<div class="footer2">
			<div class="container">
				<div class="row">

					<div class="col-md-6 widget">
						<div class="widget-body">
							<p class="simplenav">
								<a href="$urlRacine">Accueil</a> |
								$connectionThingDown
							</p>
						</div>
					</div>

					<div class="col-md-6 widget">
						<div class="widget-body">
							<p class="text-right">
								Designed by Marius JENIN
							</p>
						</div>
					</div>

				</div> <!-- /row of widgets -->
			</div>
		</div>

	</footer>





	<!-- JavaScript libs are placed at the end of the document so the pages load faster -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="http://netdna.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script type="module" src="$urlJS/imagevignettelivre.js"></script>
	<script src="$urlJS/headroom.min.js"></script>
	<script src="$urlJS/jQuery.headroom.min.js"></script>
	<script src="$urlJS/template.js"></script>
    $script
</body>
</html>
END;

    }
}
