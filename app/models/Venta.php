<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'ventacripto';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fechaBaja';

    protected $fillable = [
        'fecha', 'cantidad', 'id_usuario', 'id_criptomoneda', 'foto', 'fechaBaja'
    ];
}