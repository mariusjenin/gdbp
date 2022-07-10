<?php

namespace gdbp\vue;

use Slim\Slim;
const INTERFACE_CONNEXION = 1;
const INTERFACE_MAUVAISE_COMBINAISON = 2;
const INTERFACE_INSCRIPTION = 3;
const INTERFACE_MAUVAISE_INSCRIPTION = 4;
const INTERFACE_DECONNEXION = 5;

class VueConnexion
{
    public $arr;

    public function __construct($a)
    {
        $this->arr = $a;
    }

    private function afficherInterfaceInscription($valueErr)
    {
        $urlInscription = Slim::getInstance()->urlFor('formIns');
        $urlConnexion = Slim::getInstance()->urlFor('connexion');
        if ($valueErr) {
            $err = $this->arr['err'];
            $error = "<p class=\"erreurconnectinscript\" style='color : red'>$err</p>";
        } else {
            $error = "";
        }
        return <<<END
<div class="row">

			<!-- Article main content -->
			<article class="col-xs-12 maincontent">
				<header class="page-header">
					<h1 class="page-title">S'inscrire</h1>
				</header>

				<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-body">
							<h3 class="thin text-center">Créer un compte</h3>
							<p class="text-center text-muted">Si vous possédez déjà un compte vous pouvez vous connecter <a href="$urlConnexion">ici</a></p>
							$error
							<hr>

							<form method="post" action="$urlInscription" enctype="multipart/form-data">
								<div class="top-margin">
									<label>Email<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="email" required>
								</div>
                                <div class="top-margin">
									<label>Pseudo<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="pseudo" required>
								</div>
								<div class="row top-margin">
									<div class="col-sm-6">
										<label>Mot de passe <span class="text-danger">*</span></label>
										<input type="password" class="form-control" name="mdp" required>
									</div>
									<div class="col-sm-6">
										<label>Confirmation<span class="text-danger"> *</span></label>
										<input type="password" class="form-control" name="mdpconf" required>
									</div>
								</div>

								<hr>

								<div class="row">
									<div class="col-lg-4 text-right">
										<button class="btn btn-action" type="submit">Valider</button>
									</div>
								</div>
							</form>
						</div>
					</div>

				</div>

			</article>
			<!-- /Article -->

		</div>
END;
    }

    private function afficherInterfaceConnexion($valueErr)
    {
        $app = Slim::getInstance();
        $urlInscription = $app->urlFor('inscription');
        $urlConnexion = $app->urlFor('formCo');
        if ($valueErr) {
            $err = $this->arr['err'];
            $error = "<p class=\"erreurconnectinscript\" style='color : red'>$err</p>";
        } else {
            $error = "";
        }
        return <<<END
<div class="row">

			<!-- Article main content -->
			<article class="col-xs-12 maincontent">
				<header class="page-header">
					<h1 class="page-title">Se connecter</h1>
				</header>

				<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-body">
							<h3 class="thin text-center">Se connecter à votre compte</h3>
							<p class="text-center text-muted">Si vous ne possédez pas encore de compte vous pouvez vous inscrire <a href="$urlInscription">ici</a></p>
							$error
							<hr>

							<form method="post" action="$urlConnexion" enctype="multipart/form-data">
								<div class="top-margin">
									<label>Email <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="email" required>
								</div>
								<div class="top-margin">
									<label>Mot de passe <span class="text-danger">*</span></label>
									<input type="password" class="form-control" name="mdp" required>
								</div>

								<hr>

								<div class="row">
									<div class="col-lg-4 text-right">
										<button class="btn btn-action" type="submit">Valider</button>
									</div>
								</div>
							</form>
						</div>
					</div>

				</div>

			</article>
			<!-- /Article -->

		</div>
END;
    }

    private function afficherInterfaceDeconnnexion($valueErr)
    {
        $urlDeconnexion = Slim::getInstance()->urlFor('formDeco');
        return <<<END
<div class="row">

			<!-- Article main content -->
			<article class="col-xs-12 maincontent">
				<header class="page-header">
					<h1 class="page-title">Se déconnecter</h1>
				</header>

				<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-body">
							<h3 class="thin text-center">Voulez-vous vraiment vous déconnecter ?</h3>
							<hr>

							      <form class='formdeconnect text-center' method='post' action='$urlDeconnexion' enctype='multipart/form-data'>

          <button class="boutondeconnect btn-primary btn " type=submit name ="oui" value='Deconnexion'>Oui</button>
          <button class="boutondeconnect btn-primary btn" type=submit name ="non" value='Deconnexion'>Non</button>
      </form>
						</div>
					</div>

				</div>

			</article>
			<!-- /Article -->

		</div>
END;
    }

    public function render($selecteur)
    {
        $app = Slim::getInstance();
        $content = "";
        $co = "Se connecter";
        switch ($selecteur) {
            case INTERFACE_CONNEXION:
            {
                $content = $this->afficherInterfaceConnexion(false);
                break;
            }
            case INTERFACE_MAUVAISE_COMBINAISON :
            {
                $content = $this->afficherInterfaceConnexion(true);
                break;
            }
            case INTERFACE_INSCRIPTION :
            {
                $content = $this->afficherInterfaceInscription(false);
                $co = "S'inscrire";
                break;
            }
            case INTERFACE_MAUVAISE_INSCRIPTION :
            {
                $content = $this->afficherInterfaceInscription(true);
                $co = "S'inscrire";
                break;
            }
            case INTERFACE_DECONNEXION :
            {
                $content = $this->afficherInterfaceDeconnnexion(false);
                $co = "Se déconnecter";
                break;
            }
        }
        $urlRacine = $app->urlFor('racine');
        $html = <<<END
	<!-- container -->
	<div class="container">

		<ol class="breadcrumb">
			<li><a href="$urlRacine">Accueil</a></li>
			<li class="active">$co</li>
		</ol>
		$content
	</div>	<!-- /container -->
END;
        echo VueGlobale::getHeader($app, $co) . $html . VueGlobale::getFooter($app, null);
    }

}
