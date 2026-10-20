<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class AuthController extends Controller
{
    public function showLogin(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/dashboard');
        }

        return $this->render('auth.login', [
            'pageTitle' => 'Tizimga kirish'
        ], 'layouts.auth');
    }

    public function login(Request $request): Response
    {
        $email = trim((string)$request->input('email', ''));
        $password = (string)$request->input('password', '');
        $ip = $request->ip();

        if (empty($email) || empty($password)) {
            return $this->redirect('/login', [
                'error' => 'Email va parolni kiriting.'
            ]);
        }

        $result = Auth::attempt($email, $password, $ip);

        if (!$result['success']) {
            return $this->redirect('/login', [
                'error' => $result['message'],
                '_old_input' => ['email' => $email]
            ]);
        }

        return $this->redirect('/dashboard', [
            'success' => 'Xush kelibsiz, ' . $result['user']['name'] . '!'
        ]);
    }

    public function logout(Request $request): Response
    {
        Auth::logout();
        return $this->redirect('/login', [
            'success' => 'Siz tizimdan muvaffaqiyatli chiqdingiz.'
        ]);
    }
}
