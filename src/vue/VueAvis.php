<?php

namespace gdbp\vue;

use gdbp\controleur\ControleurAffichage;
use gdbp\modele\Livre;
use Slim\Slim;

const AFFICHER_AVIS_PROFIL = 1;
const AFFICHER_AVIS_LIVRE = 2;

class VueAvis
{

    public $arr;

    public function __construct($a)
    {
        $this->arr = $a;
    }

    private function profil()
    {
        $content = "";
        $app = Slim::getInstance();
        foreach ($this->arr['data'] as $avis) {
            $date = ControleurAffichage::dateUStoFR($avis['updated_at']);
            $urlImage = Livre::find($avis['ISBNAvis'])->couverture;
            $user = $avis->compte['pseudo'];
            $comment = $avis['contenu'];
            $note = ControleurAffichage::calculerNbEtoile($avis['note']);
            $urlRef = Slim::getInstance()->urlFor('livre', ["isbn" => $avis['ISBNAvis']]);
            $urlProfilGars = Slim::getInstance()->urlFor('affProfilAutre',['id'=>$avis['identifiant']]);
            $content .= <<<END
<!--Commentaire -->
                  <div class="col-xs-12 media" style="padding-top:10px;margin-top:0px;border-top:solid 1px #ccc">
                  <p class="pull-right"><small>$date</small></p>
                  <div class="row media-body">
                    <div class="col-xs-1" style="padding-bottom:10px">
                      <a href="$urlRef"><img  src="$urlImage" width="100%" ></a>
                    </div>
                    <div class="col-xs-11">
                      <a href="$urlProfilGars" ><h4 class="media-heading user_name">$user</h4></a>
                      $comment
                      <p><small>$note</small></p>
                      <p><small><a href="$urlRef">Voir</a></small></p>
                    </div>
                  </div>
                </div>
END;
        }

        return <<<END
<div class="row comments-list">
                <div class="col-xs-12" style="margin-top:20px;border-top:solid 2px"><h2>Mes Avis récents</h2></div>
                $content
               </div>
END;

    }


    private function livre()
    {
        $content = "";
        foreach ($this->arr['data'] as $avis) {
            $date = ControleurAffichage::dateUStoFR($avis['updated_at']);
            if ($avis->compte['ISBNDeProfil']!==null)
                $urlImage = Livre::find($avis->compte['ISBNDeProfil'])->couverture;
            else
                $urlImage = "https://puu.sh/FlhqW/0ddb035261.png";
            $user = $avis->compte['pseudo'];
            $comment = $avis['contenu'];
            $note = ControleurAffichage::calculerNbEtoile($avis['note']);
            $urlRef = Slim::getInstance()->urlFor('livre', ["isbn" => $avis['ISBNAvis']]);
            $urlProfilGars = Slim::getInstance()->urlFor('affProfilAutre',['id'=>$avis['identifiant']]);
            $content .= <<<END
                      <div class="col-xs-12 media" style="padding-top:10px;margin-top:0px;border-top:solid 1px #ccc">
                      <p class="pull-right"><small>$date</small></p>
                      <div class="row media-body">
                        <div class="col-xs-1" style="padding-bottom:10px">
                          <a href="$urlRef"><img  src="$urlImage" width="100%"  alt=""></a>
                        </div>
                        <div class="col-xs-11">
                          <a href="$urlProfilGars" ><h4 class="media-heading user_name">$user</h4></a>
                          $comment
                          <p><small>$note</small></p>
                           <p><small><a href="$urlRef">Voir</a></small></p>
                        </div>
                      </div>
                   </div>
END;

        }
        $urlPosterAvis = Slim::getInstance()->urlFor("ajouterAvis", ['isbn' => $this->arr['isbn']]);
        return <<<END
    <div class="row comments-list">
                    <div class="col-xs-12" style="margin-top:20px;border-top:solid 2px"><h2>Avis</h2></div>
                    $content
</div>

                              <p class="h3" style="margin-top:15px">Laissez un commentaire :</p>
                  <form action="$urlPosterAvis" method="post" class="form-horizontal" id="commentForm" role="form">
                    <div class="form-group col-sm-12">
                      <textarea class="form-control" name="addComment" id="addComment" rows="5" required></textarea>
                    </div>

                    <div class="form-group col-sm-12">
                      <input type="hidden" id="starsPost" name="postEtoiles" value="5">
                      <p class="text-center">
                        <i class="starform fas fa-star" style="font-size: 2.8rem;"></i>
                        <i class="starform fas fa-star" style="font-size: 2.8rem;"></i>
                        <i class="starform fas fa-star" style="font-size: 2.8rem;"></i>
                        <i class="starform fas fa-star" style="font-size: 2.8rem;"></i>
                        <i class="starform fas fa-star" style="font-size: 2.8rem;"></i>
                      </p>
                    </div>

                    <div class="form-group">
                      <div class="col-sm-12">
                        <button class="btn btn-primary btn-circle text-uppercase" type="submit" id="submitComment">Valider le commentaire</button>
                      </div>
                    </div>
                  </form>
END;
    }

    private function pageAvis(){
        $content = "";
        $app = Slim::getInstance();
        foreach ($this->arr['data'] as $avis) {
            $date = ControleurAffichage::dateUStoFR($avis['updated_at']);
            $urlImage = Livre::find($avis['ISBNAvis'])->couverture;
            if ($urlImage===null or $urlImage === "")
                $urlImage = "https://puu.sh/FlhqW/0ddb035261.png";
            $user = $avis->compte['pseudo'];
            $comment = $avis['contenu'];
            $note = ControleurAffichage::calculerNbEtoile($avis['note']);
            $urlRef = Slim::getInstance()->urlFor('livre', ["isbn" => $avis['ISBNAvis']]);
            $content .= <<<END
<!--Commentaire -->
                  <div class="col-xs-12 media" style="padding-top:10px;margin-top:0px;border-top:solid 1px #ccc">
                  <p class="pull-right"><small>$date</small></p>
                  <div class="row media-body">
                    <div class="col-xs-1" style="padding-bottom:10px">
                      <a href="$urlRef"><img  src="$urlImage" width="100%" ></a>
                    </div>
                    <div class="col-xs-11">
                      <h4 class="media-heading user_name">$user</h4>
                      $comment
                      <p><small>$note</small></p>
                      <p><small><a href="$urlRef">Voir</a></small></p>
                    </div>
                  </div>
                </div>
END;
        }
        $urlAvis = $app->urlFor('maBibliotheque');
        $urlRacine = $app->urlFor('racine');
        return <<<END
<!-- container -->
	<div class="container">
        <ol class="breadcrumb">
			<li><a href="$urlRacine">Accueil</a></li>
			<li>Mes Livres</li>
			<li><a href="$urlAvis">Mes Avis</a></li>
		</ol>
		<div class="row">
    <div class="row comments-list">
                    <div class="col-xs-12" style="margin-top:20px;"><h2>Mes Avis</h2></div>
                    $content
</div>
</div>
</div>
END;
    }

    public function render($selecteur)
    {
        switch ($selecteur) {
            case AFFICHER_AVIS_PROFIL :
                $content = $this->profil();
                return $content;
                break;
            case AFFICHER_AVIS_LIVRE :
                $content = $this->livre();
                return $content;
                break;
            default :
                $content = VueGlobale::getHeader(Slim::getInstance(),"Mes Avis").$this->pageAvis().
                    VueGlobale::getFooter(Slim::getInstance(),null);
                echo $content;
                break;
        }
    }
}
