<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Vacation extends Model
{
    protected $table = 'vacation';

    protected $fillable = [
        'titulo',
        'descripcion',
        'precio',
        'pais',
        'idtipo',
    ];

    function foto(): HasOne {
        return $this->hasOne('App\Models\Foto', 'idvacation');
    }
    
    function comentario(): HasMany {
        return $this->hasMany('App\Models\Comentario', 'idvacation');
    }

    function tipo(): BelongsTo {
        return $this->belongsTo('App\Models\Tipo', 'idtipo');
    }

    public function reserva(): HasOne {
        return $this->hasOne('App\Models\Reserva', 'idvacation');
    }

}
