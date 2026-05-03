<?php

namespace App\Services;

use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @param array<string, mixed> $data
     */
    public function registerMember(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'member',
            ]);

            Member::create([
                'user_id' => $user->id,
                'business_name' => $data['business_name'],
                'nib' => $data['nib'],
                'phone' => $data['phone'],
                'status' => 'pending',
            ]);

            return $user;
        });
    }
}