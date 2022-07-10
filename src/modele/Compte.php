<?php

namespace gdbp\modele;

use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    protected $table = 'compte';
    protected $primaryKey = 'identifiant';
    public $timestamps = false;

    public function listes()
    {
        return $this->hasMany('\gdbp\modele\Liste', 'identifiant');
    }

    public function avis()
    {
        return $this->hasMany('\gdbp\modele\Avis', 'identifiant');
    }

    public function livres()
    {
        return $this->belongsToMany('\gdbp\modele\Livre', 'possede', 'identifiant', 'ISBN', 'identifiant');
    }

    public function pretsPret()
    {
        return $this->hasMany('\gdbp\modele\Pret', 'idPreteur');
    }

    public function pretsDesti()
    {
        return $this->hasMany('\gdbp\modele\Pret', 'idEmprunteur');
    }
}
