<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin');
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role'  => ['required', 'string', 'in:cashier,admin,super-admin'],
        ];

        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        } else {
            $rules['password'] = ['nullable', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama staf wajib diisi.',
            'email.required'     => 'Alamat surel (email) wajib diisi.',
            'email.unique'       => 'Alamat surel sudah digunakan oleh pengguna lain.',
            'role.required'      => 'Hak akses peran (role) wajib dipilih.',
            'role.in'            => 'Hak akses yang dipilih tidak valid.',
            'password.required'  => 'Kata sandi awal wajib diisi untuk pengguna baru.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ];
    }
}