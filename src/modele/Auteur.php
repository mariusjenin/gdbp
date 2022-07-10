<?php

namespace gdbp\modele;

use Illuminate\Database\Eloquent\Model;

class Auteur extends Model
{
    protected $table = 'auteur';
    protected $primaryKey = 'numAuteur';
    public $timestamps = false;

    public function livres()
    {
        return $this->belongsToMany('gdbp\modele\Livre', 'aecrit', 'numAuteur', 'ISBN', 'numAuteur');
    }
}
