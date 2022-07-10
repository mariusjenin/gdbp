<?php

namespace gdbp\modele;

use Illuminate\Database\Eloquent\Model;

class Liste extends Model
{
    protected $table = 'liste';
    protected $primaryKey = 'numListe';
    public $timestamps = true;

    public function themes()
    {
        return $this->belongsToMany('\gdbp\modele\Theme', 'themeliste', 'numListe', 'numTheme', 'numListe');
    }

    public function livres()
    {
        return $this->belongsToMany('\gdbp\modele\Livre', 'contient', 'numListe', 'ISBN', 'numListe');
    }

}
