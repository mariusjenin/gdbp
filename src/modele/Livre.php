<?php

namespace gdbp\modele;

use Illuminate\Database\Eloquent\Model;

class Livre extends Model
{
    protected $table = 'livre';
    protected $primaryKey = 'ISBN';
    public $timestamps = false;

    public function prets()
    {
        return $this->hasMany('gdbp\modele\Pret', 'ISBN');
    }

    public function auteurs()
    {
        return $this->belongsToMany('\gdbp\modele\Auteur', 'aecrit', 'ISBN', 'numAuteur', 'ISBN');
    }

    public function listes()
    {
        return $this->belongsToMany('gdbp\modele\Liste', 'contient', 'ISBN', 'numListe', 'ISBN');
    }

    public function themes()
    {
        return $this->belongsToMany('gdbp\modele\Theme', 'themelivre', 'ISBN', 'numTheme', 'ISBN');
    }

    public function comptes()
    {
        return $this->belongsToMany('gdbp\modele\Compte', 'possede', 'ISBN', 'identifiant', 'ISBN');
    }

    public function ISBNConseilles()
    {
        return $this->hasMany('gdbp\modele\Avis', 'ISBNConseil');
    }

    public function ISBNAvis()
    {
        return $this->hasMany('gdbp\modele\Avis', 'ISBNAvis');
    }
}
