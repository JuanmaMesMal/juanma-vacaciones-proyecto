<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacationCreateRequest extends FormRequest {

    function attributes(): array {
        return [
            'titulo'           => 'titulo del anuncio vacacional',
            'descripcion'      => 'descripcion del anuncio vacacional',
            'precio'           => 'precio de las vacaciones',
            'pais'             => 'pais de las vacaciones',
            'idtipo'           => 'tipo de vacaciones',
        ];
    }

    function authorize(): bool {
        return true;
    }

    function messages(): array {
        $max = 'El campo :attribute no puede tener más de :max caracteres.';
        $min = 'El campo :attribute no puede tener menos de :min caracteres.';
        $required = 'El campo :attribute es obligatorio.';
        $string = 'El campo :attribute tiene que ser una cadena de caracteres.';
        $longText = 'El campo :attribute tiene que ser un longText.';
        $integer = 'El campo :attribute tiene que ser un número entero.';
        $numeric = 'El campo :attribute tiene que ser un número (puede ser decimal).';
        
        return [
            'titulo.required'            => $required,
            'titulo.min'                 => $min,
            'titulo.string'              => $string,

            'descripcion.required'       => $required,
            'descripcion.string'         => $string,
            'descripcion.min'            => $min,

            'precio.required'            => $required,
            'precio.numeric'             => $numeric,
            'precio.min'                 => $min,

            'pais.required'              => $required,
            'pais.string'                => $string,
            'pais.min'                   => $min,

            'idtipo.required'            => $required,
            'idtipo.exists'              => 'El tipo seleccionado no existe.',
        ];
    }
    
    function rules(): array {
        return [
            'titulo'            => 'required|min:3|string',
            'descripcion'       => 'required|min:20|string',
            'precio'            => 'required|min:0|numeric',
            'pais'              => 'required|min:0|string',
            'idtipo'            => 'required|exists:tipo,id',
        ];
    }
}