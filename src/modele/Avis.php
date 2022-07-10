<?php

namespace gdbp\modele;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    protected $table = 'avis';
    protected $primaryKey = 'numAvis';
    public $timestamps = true;

    public function livresAvis()
    {
        return $this->hasMany('\gdbp\modele\Livre', 'ISBNAvis');
    }

    public function livresConseil()
    {
        return $this->hasMany('\gdbp\modele\Livre', 'ISBNConseil');
    }

    public function compte()
    {
        return $this->belongsTo('\gdbp\modele\Compte', 'identifiant');
    }

    public function reponses()
    {
        return $this->hasMany('\gdbp\modele\Avis', 'numAvisRepondu');
    }

    public function repond()
    {
        return $this->belongsTo('\gdbp\modele\Avis', 'numAvisRepondu');
    }
}
