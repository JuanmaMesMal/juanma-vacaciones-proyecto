<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Foto extends Model
{
    protected $table = 'foto';

    protected $fillable = [
        'idvacation',
        'path',
    ];

    function vacation(): BelongsTo {
        return $this->belongsTo('App\Models\Vacation', 'idvacation');
    }

    public function getPath() {
        // Si el path empieza por http, es una imagen de internet
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        // Si no, es una imagen local guardada en storage
        return asset('storage/' . $this->path);
    }
}
