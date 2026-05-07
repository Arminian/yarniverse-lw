<?php

namespace App\Actions\Fortify;

use App\Models\Customer;
use App\Mail\WelcomeCustomer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewCustomer implements CreatesNewUsers
{
    use \App\Concerns\PasswordValidationRules;

    public function create(array $input){
        Validator::make($input, [
            'name' => ['required', 'string', 'max:30'],
            'email' => [
                'required',
                'string',
                'email',
                'max:40',
                Rule::unique(Customer::class),
            ],
            'password' => $this->passwordRules(),
            'phone' => ['nullable', 'string', 'max:20'],
        ])->validate();

        $customer = Customer::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone' => $input['phone'] ?? null,
            'is_active' => true,
        ]);

        return $customer;
    }
}