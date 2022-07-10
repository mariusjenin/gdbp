<?php
session_start();

use gdbp\controleur\ControleurAffichage;
use gdbp\controleur\ControleurAvis;
use gdbp\controleur\ControleurBibliotheque;
use gdbp\controleur\ControleurConnexion;
use gdbp\controleur\ControleurListe;
use gdbp\controleur\ControleurLivre;
use gdbp\controleur\ControleurProfil;
use gdbp\controleur\ControleurPret;
use \Illuminate\Database\Capsule\Manager as DB;
use Slim\Slim;

require_once('vendor/autoload.php');
$app = new Slim();

$db = new DB();
$db->addConnection(parse_ini_file('src/conf/conf.ini'));
$db->setAsGlobal();
$db->bootEloquent();

$app->get('/',function () {
    $c = new ControleurAffichage();
    $c->afficherPageAccueil();
})->name('racine');

$app->get('/connexion', function () {
    $c = new ControleurConnexion();
    $c->afficherInterfaceConnexion();
})->name('connexion');

$app->post('/connexion', function () {
    $c = new ControleurConnexion();
    $c->seConnecter();
})->name('formCo');

$app->get('/inscription', function () {
    $c = new ControleurConnexion();
    $c->afficherInterfaceInscription();
})->name('inscription');

$app->post('/inscription', function () {
    $c = new ControleurConnexion();
    $c->sInscrire();
})->name('formIns');

$app->get('/deconnexion', function(){
    $c = new ControleurConnexion();
    $c->afficherInterfaceDeconnexion();
})->name('deconnexion');

$app->post('/deconnexion', function(){
    $c = new ControleurConnexion();
    $c->seDeconnecter();
})->name('formDeco');

$app->get('/mesListes', function () {
    $c = new ControleurListe();
    $c->afficherListes();
})->name('getListes');

$app->get('/mesListes/rechercheAjax', function (){
    if (isset($_GET['texte'])) {
        ControleurListe::rendreDonneeAjax($_GET['texte']);
    } else {
        Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }
});

$app->get('/mesListes/:id', function ($id){
    $c = new ControleurListe();
    $c->afficherListe(filter_var($id,FILTER_SANITIZE_NUMBER_INT));
})->name('afficherListe');

$app->get('/mesListes/:id/rechercheAjax', function ($id){
    if (isset($_GET['texte'])) {
        ControleurBibliotheque::rendreDonneeAjax(filter_var($_GET['texte'],FILTER_SANITIZE_STRING),filter_var($id,FILTER_SANITIZE_NUMBER_INT),true);
    } else {
        Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }
});

$app->get('/bibliotheque', function () {
    $c = new ControleurBibliotheque();
    $c->afficherBibliotheque();
})->name('maBibliotheque');

$app->get('/bibliotheque/rechercheAjax', function (){
    if (isset($_GET['texte'])) {
        ControleurBibliotheque::rendreDonneeAjax($_GET['texte'],null,false);
    } else {
        Slim::getInstance()->redirect(Slim::getInstance()->request->getRootUri());
    }
});

$app->get('/profil', function () {
    $c = new ControleurProfil();
    $c->afficherProfil();
})->name('monProfil');

$app->get('/avis', function () {
    $c = new ControleurAvis();
    $c->afficherPageAvis();
})->name('mesAvis');

$app->get('/prets', function () {
    $c = new ControleurPret();
    $c->afficherPrets();
})->name('mesPrets');

$app->get('/livre/:isbn', function ($isbn) {
    $c = new ControleurLivre();
    $c->afficherLivre(filter_var($isbn,FILTER_SANITIZE_NUMBER_INT));
})->name('livre');

$app->get('/ajouterLivre', function () {
    $c = new ControleurLivre();
    $c->afficherRechercheLivre();
})->name('ajouterLivre');

$app->post('/ajouterLivre', function () {
    $c = new ControleurLivre();
    $c->afficherNouveauLivre();
})->name('ajouterLivrePost');

$app->post('/ajouterLivre/:isbn', function ($isbn) {
    $c = new ControleurBibliotheque();
    $c->enregistrerNouveauLivre($isbn);
})->name('ajoutLivreBibli');

$app->get('/ajouterLivre/:isbn', function ($isbn) {
    $c = new ControleurBibliotheque();
    $c->ajouterNouveauLivreBibli($isbn);
})->name('ajoutLivreBibliViaLivre');

$app->get('/profil/:id', function ($id) {
    $c = new ControleurProfil();
    $c->afficherProfilUser($id);
})->name('affProfilAutre');

$app->get('/ajouterLivreListe/:id/:isbn', function ($id,$isbn) {
    $c = new ControleurListe();
    $c->ajouterNouveauLivre($id,$isbn);
})->name('ajoutLivreListeViaLivre');

$app->get('/ajoutLivreListe/:id', function ($id){
    $c = new ControleurListe();
    $c->afficherFormulaireLivreListe($id);
})->name('ajoutLivreListe');

$app->post('/ajouterAvis/:isbn', function ($isbn) {
    $c = new ControleurAvis();
    $c->ajouterAvis($isbn);
})->name('ajouterAvis');

$app->get('/preter/:isbn', function ($isbn) {
    $c = new ControleurPret();
    $c->afficherFormulairePret($isbn);
})->name('preter');

$app->post('/preter/:isbn', function ($isbn) {
    $c = new ControleurPret();
    $c->preter($isbn);
})->name('preterPost');

$app->get('/creerListe', function () {
    $c = new ControleurListe();
    $c->afficherFormulaireListe();
})->name('creerListe');

$app->get('/changerLivreDeProfil/:isbn', function ($isbn) {
    $c = new ControleurProfil();
    $c->changerLivreDeProfil($isbn);
})->name('changerLivreDeProfil');

$app->post('/creerListe', function () {
    $c = new ControleurListe();
    $c->creerListe();
})->name('creerListePost');

$app->post('/ajoutLivreListe/:id', function ($id){
    $c = new ControleurListe();
    $c->ajouterListeLivre($id);
})->name('postListeLivre');

$app->notFound(function (){
    $c = new ControleurAffichage();
    $c->onError();
});
$app->run();
