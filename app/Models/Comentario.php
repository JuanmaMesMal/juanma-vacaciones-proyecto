<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comentario extends Model
{
    protected $table = 'comentario';

    protected $fillable = [
        'iduser',
        'idvacation',
        'texto',
    ];

    function vacation(): BelongsTo {
        return $this->belongsTo('App\Models\Vacation', 'idvacation');
    }

    function user(): BelongsTo {
        return $this->belongsTo('App\Models\User', 'iduser');
    }

}
