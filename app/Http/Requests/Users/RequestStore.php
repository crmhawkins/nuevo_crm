<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class RequestStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:25',
            'surname' => 'required',
            'username' => 'required|unique:admin_user',
            'email' => 'required|email|unique:admin_user',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre no puede estar vacio',
            'surname.required' => 'Los apellido no puede estar vacio',
            'username.required' => 'El nombre de usuario no puede estar vacio',
            'username.unique' => 'Ese nombre de usuario ya existe',
            'email.required' => 'El email no puede estar vacio',
            'email.unique' => 'Ese email ya existe',
            'email.email' => 'Debe ser un email valido',
        ];
    }
}
