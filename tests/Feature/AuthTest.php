<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use WithFaker;

    /**
     * Тест регистрации пользователя.
     *
     * @return void
     */
    public function test_user_can_register()
    {
        // Генерация уникальных данных пользователя
        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'Pass12345', // Соответствует требованиям валидации
        ];

        // Отправка POST-запроса на регистрацию
        $response = $this->postJson('/api/register', $userData);

        // Проверка, что ответ имеет статус 201 (Создано)
        $response->assertStatus(201);

        // Проверка структуры JSON-ответа
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            'token',
        ]);

        // Проверка наличия пользователя в базе данных
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);

        // Удаление созданного пользователя после теста
        $user = User::where('email', $userData['email'])->first();
        if ($user) {
            $user->delete();
        }
    }

    /**
     * Тест аутентификации пользователя.
     *
     * @return void
     */
    public function test_user_can_login()
    {
        // Создание пользователя напрямую через модель
        $password = 'Pass12345';
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make($password),
        ]);

        // Подготовка данных для логина
        $loginData = [
            'email' => $user->email,
            'password' => $password,
        ];

        // Отправка POST-запроса на логин
        $response = $this->postJson('/api/login', $loginData);

        // Проверка, что ответ имеет статус 200 (OK)
        $response->assertStatus(200);

        // Проверка структуры JSON-ответа
        $response->assertJsonStructure([
            'token',
        ]);

        // Удаление созданного пользователя после теста
        $user->delete();
    }

    /**
     * Тест получения информации о пользователе.
     *
     * @return void
     */
    public function test_authenticated_user_can_get_me()
    {
        // Создание пользователя напрямую через модель
        $password = 'Pass12345';
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make($password),
        ]);

        // Генерация JWT-токена для пользователя
        $token = JWTAuth::fromUser($user);

        // Отправка GET-запроса с токеном
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/me');

        // Проверка, что ответ имеет статус 200 (OK)
        $response->assertStatus(200);

        // Проверка содержимого JSON-ответа
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        // Удаление созданного пользователя после теста
        $user->delete();
    }

    /**
     * Тест выхода из системы.
     *
     * @return void
     */
    public function test_authenticated_user_can_logout()
    {
        // Создание пользователя напрямую через модель
        $password = 'Pass12345';
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make($password),
        ]);

        // Генерация JWT-токена для пользователя
        $token = JWTAuth::fromUser($user);

        // Отправка POST-запроса с токеном для выхода
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/logout');

        // Проверка, что ответ имеет статус 200 (OK)
        $response->assertStatus(200);

        // Проверка содержимого JSON-ответа
        $response->assertJson([
            'message' => 'Успешный выход',
        ]);

        // Удаление созданного пользователя после теста
        $user->delete();
    }
}
