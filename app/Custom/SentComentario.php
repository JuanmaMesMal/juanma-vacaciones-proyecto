<?php

namespace App\Custom;

use App\Models\Comentario;

class SentComentario {

    private $comentarios = []; 

    public function addComentario(Comentario $comentario): void {
        if (!in_array($comentario->id, $this->comentarios)) {
            $this->comentarios[] = $comentario->id;
        }
    }

    public function isComentario(Comentario $comentario): bool {
        return in_array($comentario->id, $this->comentarios);
    }

    public function getIds(): array {
        return $this->comentarios;
    }
}