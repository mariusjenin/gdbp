<?php

namespace gdbp\modele;

use Illuminate\Database\Eloquent\Model;

class Pret extends Model
{
    protected $table = 'pret';
    protected $primaryKey = 'numPret';
    public $timestamps = false;

    public function livre()
    {
        return $this->belongsTo('\gdbp\modele\Livre', 'ISBN');
    }

    public function identifiantPreteur()
    {
        return $this->belongsTo('\gdbp\modele\Compte', 'idPreteur');
    }

    public function identifiantDestinataire()
    {
        return $this->belongsTo('\gdbp\modele\Compte', 'idEmprunteur');
    }
}
