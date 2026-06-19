<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'exists:users,name'],
            'password' => ['required', 'string']
        ];
    }

    public function passedValidation()
    {
        $email = User::where('name', $this->login)->first('email')['email'];
        $this->merge(['credentials' => [
            'email' => $email,
            'password' => $this->password
        ]]);
    }

    public function messages()
    {
        return [
            'login.exists' => 'Пользователь с таким логином не найден'
        ];
    }
}
