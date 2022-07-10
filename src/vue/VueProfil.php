<?php

namespace gdbp\vue;

use gdbp\controleur\ControleurAvis;
use gdbp\modele\Compte;
use gdbp\modele\Livre;
use Slim\Slim;
const AFFICHER_PROFIL = 1;

class VueProfil
{
    public $arr;

    public function __construct($a)
    {
        $this->arr = $a;
    }

    private function afficherProfil()
    {
        $compte = $this->arr['data'];
        if (Livre::find($compte['ISBNDeProfil']) !== null )
            $img = Livre::find($compte['ISBNDeProfil'])->couverture;
        else
            $img = "https://puu.sh/FlhqW/0ddb035261.png";
        $nom = $compte['pseudo'];
        $mail = $compte['mail'];
        $descr = $compte['description'];
        $html = <<<END
              <!-- Search form -->
              <div class="row" style="margin-top:30px">
                <div class="col-xs-4">
                  <img  src="$img" width="100%" >
                </div>
                <div class="col-sm-8" style="font-size:1.2em;padding-top : 20px">
                      <p><strong>Pseudo</strong> : $nom</p>
                      <p><strong>Email</strong> : $mail</p>
                      <p><strong>Description</strong> : $descr</p>
                </div>
              </div>

END;
        return $html;
    }

    public function render($selecteur)
    {
        $app = Slim::getInstance();
        switch ($selecteur) {
            case AFFICHER_PROFIL :
            {
                $content = $this->afficherProfil();
                break;
            }
        }
        $urlRacine = $app->urlFor('racine');
        $urlProfil = $app->urlFor('monProfil');
        $comments = ControleurAvis::afficherAvisProfil($this->arr['id']);
        $html = <<<END
        <!-- container -->
        <div class="container">
                  <ol class="breadcrumb">
            <li><a href="$urlRacine">Accueil</a></li>
            <li><a href="$urlProfil">Mon profil</a></li>
          </ol>
              <div class="row">

            <!-- Article main content -->
            <article class="col-sm-12 maincontent">
              <header class="page-header">
                <h1 class="page-title">Mon profil</h1>
              </header>
          $content
            $comments
            </article>
            <!-- /Article -->
          </div>
                  </div>	<!-- /container -->

END;
        echo VueGlobale::getHeader($app, "Mon Profil") . $html . VueGlobale::getFooter($app, null);
    }
}
