<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tipo extends Model
{
    protected $table = 'tipo';

    //los campos que se rellenan manualmente
    protected $fillable = [
        'nombre',
    ];

    
    function vacations() {
        return $this->hasMany('App\Models\Vacation', 'idtipo');
    }

}
