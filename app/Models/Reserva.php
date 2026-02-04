<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    protected $table = 'reserva';

    //los campos que se rellenan manualmente
    protected $fillable = [
        'idvacation',
        'iduser',
    ];

    function vacation(): BelongsTo {
        return $this->belongsTo('App\Models\Vacation', 'idvacation');
    }

    function user(): BelongsTo {
        return $this->belongsTo('App\Models\User', 'iduser');
    }

}
