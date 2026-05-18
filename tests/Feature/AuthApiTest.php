<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pengunjung dapat melakukan registrasi akun baru', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => ['id', 'name', 'email', 'role'],
                'token'
            ]
        ])
        ->assertJson([
            'status' => 'success',
            'data' => [
                'user' => [
                    'name' => 'Budi Santoso',
                    'email' => 'budi@example.com',
                    'role' => 'user',
                ]
            ]
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'budi@example.com',
        'role' => 'user',
    ]);
});

test('registrasi gagal jika email sudah terdaftar', function () {
    User::factory()->create([
        'email' => 'budi@example.com',
    ]);

    $response = $this->postJson('/api/register', [
        'name' => 'Budi Baru',
        'email' => 'budi@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonStructure(['status', 'errors']);
});

test('user dapat login dengan kredensial yang valid', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'user@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user',
                'token'
            ]
        ]);
});

test('login gagal jika password salah', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'user@example.com',
        'password' => 'password_salah',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'status' => 'error',
            'message' => 'Kredensial tidak valid'
        ]);
});

test('user dapat mengambil informasi profil sendiri', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ]);
});

test('user dapat melakukan logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
});
