<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'mangel.ojeda99@gmail.com',
            'password' => Hash::make('123'),
            'identification' => '1112496098',
            'dv' => '3',
            'company' => '',
            'trade_name' => '',
            'address' => 'Calle falsa 123',
            'phone' => '3000000000',
            'legal_organization_id' => '2',
            'tribute_id' => '21',
            'identification_document_id' => '3',
            'municipality_id' => '980',
        ]);
    }
}
