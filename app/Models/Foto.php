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
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        return asset('storage/' . $this->path);
    }
}
