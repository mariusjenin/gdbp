<?php

namespace gdbp\modele;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'theme';
    protected $primaryKey = 'numTheme';
    public $timestamps = false;

    public function listes()
    {
        return $this->belongsToMany('\gdbp\modele\Liste', 'themeliste', 'numTheme', 'numListe', 'numTheme');
    }

    public function livres()
    {
        return $this->belongsToMany('\gdbp\modele\Livre', 'themelivre', 'numTheme', 'ISBN', 'numTheme');
    }
}
