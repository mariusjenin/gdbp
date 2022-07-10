<?php

namespace gdbp\vue;

use gdbp\controleur\ControleurAffichage;
use gdbp\controleur\ControleurAvis;
use Slim\Slim;
const AFFICHER_LIVRE = 1;
const AFFICHER_AJOUT_LIVRE = 2;

class VueLivre
{
    public $arr;

    public function __construct($a)
    {
        $this->arr = $a;
    }

    private function rendreListeChoix(){
        $res = "";
        if ($this->arr !==null) {
            foreach ($this->arr as $key) {
                foreach ($key as $k) {
                $nbEtoiles = ControleurAffichage::calculerNbEtoile($k->score);
                $auteurs = "";
                $comp = 0;
                $lienAjout = Slim::getInstance()->urlFor("ajoutLivreBibli",["isbn"=>$k->ISBN]);
                foreach ($k->auteurs as $aut) {
                    if ($comp != 0) {
                        $auteurs .= ", ";
                    }
                    $auteurs .= $aut['nomAuteur'];
                    $comp++;
                }
                if ($auteurs == "") {
                    $auteurs = "Aucun auteurs pour ce livre";
                }
                $res .= <<<END
<div class="col-xs-12 text-center card h-100" style="margin-top:20px;padding-left:0px;border:solid 1px #ccc;border-radius:5px">
                  <div class="col-sm-4" style="padding-left:0px;">
                    <img class="card-img-top" src="{$k->couverture}">
                  </div>
                  <div class="col-sm-8">
                    <div class="card-body" style="">
                      <h4 class="card-title">
                        {$k->titre}
                      </h4>
                      <p class="card-text" style="padding:5px">Genre : {$k->genre}</p>
                      <p class="card-text" style="padding:5px">Auteur : $auteurs</p>
                      <p class="card-text" style="padding:5px">Editeur : {$k->editeur}</p>
                      <p class="card-text" style="padding:5px">Format : {$k->format}</p>
                    </div>
                    <div class="card-footer">
                      <p style="font-size:2.5rem">$nbEtoiles</p>
                    </div>
                    <form method="post" action="$lienAjout" enctype="multipart/form-data"><button class="btn-dark btn btn-ajout" type=submit><i class="fas fa-plus plus-btn-ajout"></i><span style="vertical-align:middle;">Ajouter ce Livre</span></button></form>
                  </div>
                </div>
END;
            }
        }
        }
        return $res;
    }
    private function afficherAjoutLivre() {
        $urlRacine = Slim::getInstance()->urlFor('racine');
        $urlRecherche = Slim::getInstance()->urlFor('ajouterLivre');
        $img = Slim::getInstance()->request->getRootURI() . '/web_avec_Bootstrap/assets/images/LivreAjout.png';
        $urlPost = Slim::getInstance()->urlFor('ajouterLivrePost');
        if (isset($this->arr['err'])) {
            $err = $this->arr['err'];
            $err = <<<END
            <div class="col-xs-12 text-danger" style='font-size:1.7rem;margin-bottom: 10px'>
            $err
</div>
END;

            $choix = "";
        } else {
            $err = "";
            $choix = $this->rendreListeChoix();
        }
        return <<<END

        <!-- container -->
        <div class="container">

          <div class="row">
            <ol class="breadcrumb">
      <li><a href="$urlRacine">Accueil</a></li>
      <li>Livres</li>
      <li><a href="$urlRecherche">Recherche livre</a></li>
    </ol>
<article class="col-sm-12 maincontent">
        <header class="page-header">
          <h1 class="page-title">Ajouter un livre</h1>
        </header>

        <div class="col-md-8 col-xs-12" style="margin-top:30px">

          <p style='color:#555;font-size:1.7rem'>
            Si vous disposez d'un scanner de code vous pouvez l'utiliser pour scanner votre livre :<br>
            Pour cela vous devez <strong>sélectionner le champ ci-dessous</strong> et <strong>scanner <span style="color:red">le code-barre</span></strong> au dos de votre ouvrage. cela rentrera automatiquement le code ISBN de celui-ci.</p>
          <p style='color:#555;font-size:1.7rem'>
            Si vous n'avez pas de scanner pas de panique vous pouvez le faire manuellement :<br>
            Il vous faudra tout simplement <strong>recopier <span style="color:red">le code<span></strong> qui se trouve juste à coté du code-barre et <strong>valider votre saisie</strong> une fois fait.</p>
            $err
          <form method="post" action="$urlPost" enctype="multipart/form-data">
          <div class="form-group md-form active-pink active-pink-2 mb-3 mt-0">
  					<input class="form-control input-lg" style="font-size:2rem" type="text" name="isbn" placeholder="Rechercher" aria-label="Rechercher">
  				</div>
          <button type="submit" style="font-size:1.7rem" class="col-xs-12 btn-primary btn btn-lg">Valider le code ISBN</button>
          </form>
          $choix
        </div>
        <div class="col-md-4 d-none d-lg-block">
          <img class="col-xs-12" src="$img">
        </div>
        <!-- /.row -->

      </article>
      <!-- /Article -->
          </div>
</div>
END;
    }

    private function afficherLivre()
    {
        $livre = $this->arr['data']->toArray();
        $img = $livre['couverture'];
        if ($livre['titre'] == "") {
            $titre = "Aucun titre pour ce livre";
        } else {
            $titre = $livre['titre'];
        }
        if ($genre = $livre['genre'] == "") {
            $genre = "Aucun genre pour ce livre";
        } else {
            $genre = $livre['genre'];
        }
        if ($livre['datePublication'] == "") {
            $datePubli = "Aucune date de publication pour ce livre";
        } else {
            $datePubli = $livre['datePublication'];
        }
        if ($livre['editeur'] == "") {
            $editeur = "Aucun editeur pour ce livre";
        } else {
            $editeur = $livre['editeur'];
        }
            $score = ControleurAffichage::calculerNbEtoile($livre['score']);
        if ($livre['format'] == "") {
            $format = "Aucun format pour ce livre";
        } else {
            $format = $livre['format'];
        }
        if ($livre['description'] == "") {
            $descr = "Aucune description pour ce livre";
        } else {
            $descr = $livre['description'];
        }
        if ($livre['nbPages'] == "") {
            $nbPages = "Aucun nombre de pages pour ce livre";
        } else {
            $nbPages = $livre['nbPages'];
        }
        $auteurs = "";
        $comp = 0;
        foreach ($this->arr['data']->auteurs as $aut) {
            if ($comp != 0) {
                $auteurs .= ", ";
            }
            $auteurs .= $aut['nomAuteur'];
            $comp++;
        }
        if ($auteurs == "") {
            $auteurs = "Aucun auteurs pour ce livre";
        }
        $theme = "";
        $comp = 0;
        foreach ($this->arr['data']->themes as $the) {
            if ($comp != 0) {
                $theme .= ", ";
            }
            $theme .= $the['nomTheme'];
            $comp++;
        }
        if ($theme == "") {
            $theme = "Aucun thèmes pour ce livre";
        }
        $urlRacine = Slim::getInstance()->urlFor('racine');
        $urlLivre = Slim::getInstance()->urlFor('livre',['isbn'=>$livre['ISBN']]);
        $comments = ControleurAvis::afficherAvisLivre($livre['ISBN']);
        $datePubli = ControleurAffichage::dateUStoFR($datePubli);
        $urlAjoutBibli = Slim::getInstance()->urlFor('ajoutLivreBibliViaLivre',['isbn'=>$livre['ISBN']]);
        $urlPret = Slim::getInstance()->urlFor('preter',['isbn'=>$livre['ISBN']]);
        $listesRecentes = "";
        $nbrlistesRecentes=0;
        foreach ($this->arr['listeRecente'] as $item) {
            if($nbrlistesRecentes==0){
              $listesRecentes.=<<<END
              <li class="divider"></li>
              <li style="display: block;padding:20px;padding-top: 3px;padding-bottom:8px;clear: both;font-weight: normal;line-height: 1.428571429;color: #333;white-space: nowrap;">Ajouter à une liste</li>
END;
              $nbrlistesRecentes++;
            }
            $nomListe = $item['nomListe'];
            $urlListe = Slim::getInstance()->urlFor("ajoutLivreListeViaLivre",['id'=>$item['numListe'],'isbn'=>$livre['ISBN']]);
            $listesRecentes .= <<<END
<li><a href=$urlListe style="padding-left:40px">$nomListe</a></li>
END;
        }
        $error = "";
        if (isset($this->arr['err'])) {
            $error = $this->arr['err'];
             $error = "<p class=\"erreurconnectinscript\" style='color : red;align-items: center'>$error</p>";
        }
        $urlChangementLivre = Slim::getInstance()->urlFor("changerLivreDeProfil",["isbn"=>$livre['ISBN']]);
        return <<<END

        <!-- container -->
        <div class="container">

          <div class="row">
            <ol class="breadcrumb">
      <li><a href="$urlRacine">Accueil</a></li>
      <li>Livres</li>
      <li><a href="$urlLivre">$titre</a></li>
    </ol>
            <!-- Article main content -->
            <article class="col-sm-12 maincontent">
              <header class="page-header">
                <div class="row">
      					  <div class="col-xs-12 col-sm-6"><h1 class="page-title">$titre</h1></div>
                  <div class="col-xs-12 col-sm-6">

                    <div class="navbar">
                      <button id="dropdownButtonUnLivre" class="dropdown dropleft btn-dark btn btn-ajout btn-right-page-title text-center" style="padding:0px;width:60px;height:60px" type=submit>
                        <a href="#" style="padding:15px;display:inline-block;height:60px;width:60px;" class="dropdown-toggle" data-toggle="dropdown"><i class="fas fa-bars plus-btn-center" style="font-size:3rem;"></i></a>
                        <ul id="dropdownMenuUnLivre" class="dropdown-menu" style="text-align:left;">
                          <li><a href="$urlAjoutBibli">Ajouter à la bibliothèque</a></li>
                          <li class="divider"></li>
                          <li><a href="$urlChangementLivre">Utiliser en temps que livre de profil</a></li>
                          <li class="divider"></li>
                          <li><a href="$urlPret">Prêter</a></li>
                            $listesRecentes
                        </ul>
                      </button>
                    </div>

                  </div>
                </div>
                $error
              </header>

              <!-- Search form -->
              <div class="row" style="margin-top:30px">

                <div class="col-sm-4">
                  <img src="$img" width="100%" alt="jacket du livre" >
                </div>
                <div class="col-sm-8" style="font-size:1.2em;padding-top : 20px">
                    <p><strong>Auteur(s)</strong> : $auteurs</p>
                    <p><strong>Genre</strong> : $genre</p>
                    <p><strong>Thème</strong> : $theme</p>
                    <p><strong>Date de publication</strong> : $datePubli</p>
                    <p><strong>Editeur</strong> : $editeur</p>
                    <p><strong>Format</strong> : $format</p>
                    <p><strong>Description</strong> : $descr</p>
                    <p><strong>Nombre de pages</strong> : $nbPages</p>
                    <p><strong>Score</strong> :  <span style="font-size:2.5rem">$score</span></p>
                </div>
              </div>

            </article>
            <!-- /Article -->
          </div>
          $comments
</div>
END;
    }

    public function render($selecteur)
    {
        $app = Slim::getInstance();
        $urlJS=$app->request->getRootURI() . '/web_avec_Bootstrap/assets/js';
        switch ($selecteur) {
            case AFFICHER_LIVRE :
            {
                $content = $this->afficherLivre();
                $script=<<<END
      <script type="module" src="$urlJS/star.js"></script>
      <script type="module" src="$urlJS/dropdownButtonUnLivre.js"></script>
END;
                break;
            }
            case AFFICHER_AJOUT_LIVRE :
            {
                $content = $this->afficherAjoutLivre();
                $script=null;
                break;
            }
        }
        echo VueGlobale::getHeader($app, "Mes Livres") . $content . VueGlobale::getFooter($app, $script);
    }
}
