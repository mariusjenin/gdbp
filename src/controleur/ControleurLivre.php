<?php


namespace gdbp\controleur;

use gdbp\modele\Auteur;
use gdbp\modele\Liste;
use gdbp\modele\Livre;
use gdbp\vue\VueLivre;
use Google_Client;
use Google_Service_Books;
use Slim\Slim;
use const gdbp\vue\AFFICHER_AJOUT_LIVRE;
use const gdbp\vue\AFFICHER_LIVRE;
const SCORE_10 = 20;

class ControleurLivre
{

    public function afficherLivre($ISBN)
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $data = Livre::find($ISBN);
            if ($data !== null) {
                $data2 = Liste::where('identifiant','=',$_SESSION['id_connect'])->whereNotNull('updated_at')->orderBy("updated_at","desc")->take(3)->get()->toArray();
                $vue = new VueLivre(['data' => $data,'listeRecente'=>$data2]);
                $vue->render(AFFICHER_LIVRE);
            } else {
                Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
            }
        } else Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }

    public function afficherRechercheLivre()
    {
        if (isset($_SESSION['id_connect']) and $_SESSION['id_connect'] != null) {
            $vue = new VueLivre(null);
            $vue->render(AFFICHER_AJOUT_LIVRE);
        } else Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }


    public function requeteGoogle($ISBN)
    {
        $client = new Google_Client();
        $service = new Google_Service_Books($client);
        $optParams = array('printType' => 'books', 'maxResults' => '4',"projection"=>'lite');
        $results = $service->volumes->listVolumes($ISBN, $optParams);
        $res = array();
        foreach ($results as $item) {
            if (isset($item['volumeInfo']['industryIdentifiers'][1]['type']) and $item['volumeInfo']['industryIdentifiers'][1]['type'] === "ISBN_10")
                $ISBN = $item['volumeInfo']['industryIdentifiers'][1]['identifier'];
            else if (isset($item['volumeInfo']['industryIdentifiers'][0]['type']) and $item['volumeInfo']['industryIdentifiers'][0]['type'] === "ISBN_10"){
                $ISBN = $item['volumeInfo']['industryIdentifiers'][0]['identifier'];
            }
            $requete = Livre::find($ISBN);
            if ($requete===null) {
                $livre = $this->ajoutLivre($item,$ISBN);
            } else {
                $livre = $requete;
            }
            array_push($res,$livre);
        }
        return $res;
    }

    public function requeteAmazon($ISBN)
    {
        $secret = 'f98bb98f-e2fd-40ac-b9f3-3e44791a6dcc';
        $url = "http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&Operation=ItemSearch&AWSAccessKeyId=$secret&AssociateTag=mercieliott-21"; //."&ResponseGroup=XXX”;
        $host = parse_url($url, PHP_URL_HOST);
        $timestamp = gmstrftime("%Y-%m-%dT%H:%M:%S.000Z");
        $url = $url . "&Timestamp=" . $timestamp;
        $paramstart = strpos($url, "?");
        $workurl = substr($url, $paramstart + 1);
        $workurl = str_replace(",", "%2C", $workurl);
        $workurl = str_replace(":", "%3A", $workurl);
        $params = explode("&", $workurl);
        sort($params);
        $signstr = "GET\n" . $host . "\n/onca/xml\n" . implode("&", $params);
        $signstr = base64_encode(hash_hmac('sha256', $signstr, $secret, true));
        $signstr = urlencode($signstr);
        $signedurl = $url . "&Signature=" . $signstr;
        $request = $signedurl;

        $response = simplexml_load_file($request);
        return $response;
    }

    public function afficherNouveauLivre()
    {
        if (isset($_POST['isbn'])) {
            $isbn = $_POST['isbn'];
            $data = $this->requeteGoogle($isbn);
            $vue = new VueLivre(['data' => $data]);
            $vue->render(AFFICHER_AJOUT_LIVRE);
        } else {
            Slim::getInstance()->redirect(Slim::getInstance()->urlFor('ajouterLivre'));
        }
    }


    private function definirImage($arr)
    {
        if ($arr !== null) {
            foreach ($arr as $key => $value) {
                if ($value !== null and $key !== "smallThumbnail") {
                    return $value;
                }
            }
        }
            //return Slim::getInstance()->request->getRootURI() . '/web_avec_Bootstrap/assets/images/default.png';
            return "https://puu.sh/FlhqW/0ddb035261.png";
    }

    private function ajouterAuteur($arr, $isbn)
    {
        foreach ($arr as $key) {
            $aut = explode(' ', $key);
            if (isset($aut[1]))
                $nomAut = $aut[1];
            else
                $nomAut = "";
            $prenomAut = $aut[0];
            $auteur = Auteur::where('nomAuteur', '=', $nomAut)->first();
            if ($auteur === null) {
                $auteur = new Auteur();
                $auteur->nomAuteur = $nomAut;
                $auteur->prenomAuteur = $prenomAut;
                $auteur->save();
                $auteur->livres()->attach($isbn);
            } else {
                $auteur->livres()->attach($isbn);
            }
        }
    }

    private function ajoutLivre($item,$ISBN)
    {
        $livre = new Livre();
        $livre->ISBN = $ISBN;
        $livre->titre = $item['volumeInfo']['title'];
        if (isset($item['volumeInfo']['categories'])) {
            $livre->genre = $item['volumeInfo']['categories'][0];
        } else {
            $livre->genre = null;
        }
        $livre->datePublication = $this->validateDate($item['volumeInfo']['publishedDate']);
        $livre->editeur = $item['volumeInfo']['publisher'];
        $livre->format = $item['volumeInfo']['printType'];
        $livre->nbPages = $item['volumeInfo']['pageCount'];
        $livre->description = $item['volumeInfo']['description'];
        $livre->couverture = $this->definirImage($item['volumeInfo']['imageLinks']);
        $livre->score = $item['volumeInfo']['averageRating'] * SCORE_10;
        $livre->save();
        if (isset($item['volumeInfo']['authors'])) {
            $this->ajouterAuteur($item['volumeInfo']['authors'], $ISBN);
        }
        return $livre;
    }

    /**
     * inspired by PA
     * @param $publishedDate
     * @return false|string
     */
    private function validateDate($publishedDate)
    {
        $explode = explode("-",$publishedDate);
        switch (count($explode)) {
            case 1 :
                $date = $publishedDate."-01-01";
                break;
            case 2 :
                $date = $publishedDate."-01";
                break;
            default :
                if (strlen($explode[2])>2)
                    $date = $explode[0]."-".$explode[1]."-".substr($explode[2],0,2);
                else
                    $date = $publishedDate;
                break;
        }
        return $date;
    }
}
