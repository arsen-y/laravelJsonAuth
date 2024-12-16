<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Регистрация пользователя
     */
    public function register(Request $request)
    {
        // Валидация данных
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',              // Минимум 8 символов
                'regex:/[a-z]/',      // Минимум одна строчная буква
                'regex:/[A-Z]/',      // Минимум одна заглавная буква
                'regex:/[0-9]/',      // Минимум одна цифра
            ],
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        // Создание пользователя
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        // Генерация токена
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'), 201);
    }

    /**
     * Аутентификация пользователя и выдача токена
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Валидация данных
        $validator = Validator::make($credentials, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Неверные учетные данные'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Не удалось создать токен'], 500);
        }

        return response()->json(compact('token'));
    }

    /**
     * Получение текущего аутентифицированного пользователя
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Выход из системы
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Успешный выход']);
    }
}
