<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class NuevaSolicitudRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'grupo' => 'required',
            'tipo_solicitud' => 'required',
            'fecha' => 'required|date_format:d/m/Y|after:today',
            'prioridad' => 'required',
            'descripcion' => 'required',
            'ref' => 'exists:solicitudes.solicitudes,id'
        ];
    }
}
