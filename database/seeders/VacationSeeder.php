<?php

namespace Database\Seeders;

use App\Models\Vacation;
use App\Models\Tipo;
use App\Models\Foto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VacationSeeder extends Seeder
{
    public function run(): void
    {
        $paises = ['Grecia', 'Islandia', 'Egipto', 'Tailandia', 'Suiza', 'Brasil', 'Marruecos'];
        $tipos = Tipo::all();

        for ($i = 1; $i <= 50; $i++) {
            $pais = $paises[array_rand($paises)];
            
            $vacation = Vacation::create([
                // Corregido: Ahora el título incluye el número $i para ser único
                'titulo' => "Increíble viaje a $pais #$i", 
                'descripcion' => "Descubre los secretos de $pais en este tour exclusivo. Incluye todo lo necesario para tu confort.",
                'precio' => rand(500, 4500),
                'pais' => $pais,
                'idtipo' => $tipos->random()->id,
                'created_at' => now()->subDays(rand(1, 60)),
            ]);

            Foto::create([
                'idvacation' => $vacation->id,
                'path' => 'https://picsum.photos/seed/' . Str::random(10) . '/800/600'
            ]);
        }
    }
}