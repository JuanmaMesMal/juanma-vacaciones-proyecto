<?php

namespace Database\Seeders;

use App\Models\Tipo;
use Illuminate\Database\Seeder;

class TipoSeeder extends Seeder {
    public function run(): void
    {
        $categorias = ['Playa', 'Montaña', 'Ciudad', 'Aventura', 'Crucero', 'Relax'];

        foreach ($categorias as $nombre) {
            Tipo::create(['nombre' => $nombre]);
        }
    }
}