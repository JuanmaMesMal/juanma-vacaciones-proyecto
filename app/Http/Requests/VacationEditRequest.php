<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacationEditRequest extends VacationCreateRequest {

    function rules(): array {
        $rules = parent::rules();
        return $rules;
    }
}