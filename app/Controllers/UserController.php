<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class UserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(Request $request): Response
    {
        $users = $this->userModel->getAllUsers();

        return $this->render('users.index', [
            'pageTitle' => 'Foydalanuvchilar va xodimlar',
            'users' => $users,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->render('users.create', [
            'pageTitle' => 'Yangi foydalanuvchi qoʻshish',
        ]);
    }

    public function store(Request $request): Response
    {
        $name = trim((string)$request->input('name'));
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');
        $role = (string)$request->input('role', 'manager');
        $isActive = $request->has('is_active') ? 1 : 0;

        if (empty($name) || empty($email) || empty($password)) {
            return $this->redirect('/users/create', [
                'error' => 'Ism, email va parolni toʻldirish shart.',
                '_old_input' => $request->all()
            ]);
        }

        if (strlen($password) < 6) {
            return $this->redirect('/users/create', [
                'error' => 'Parol kamida 6 ta belgidan iborat boʻlishi kerak.',
                '_old_input' => $request->all()
            ]);
        }

        // Check if email already registered
        if ($this->userModel->findByEmail($email)) {
            return $this->redirect('/users/create', [
                'error' => 'Ushbu email bilan foydalanuvchi allaqachon mavjud.',
                '_old_input' => $request->all()
            ]);
        }

        $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => in_array($role, ['admin', 'manager'], true) ? $role : 'manager',
            'is_active' => $isActive,
        ]);

        return $this->redirect('/users', [
            'success' => 'Yangi foydalanuvchi muvaffaqiyatli qoʻshildi.'
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->redirect('/users', ['error' => 'Foydalanuvchi topilmadi.']);
        }

        return $this->render('users.edit', [
            'pageTitle' => 'Foydalanuvchini tahrirlash',
            'editUser' => $user,
        ]);
    }

    public function update(Request $request, int $id): Response
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->redirect('/users', ['error' => 'Foydalanuvchi topilmadi.']);
        }

        $name = trim((string)$request->input('name'));
        $email = trim((string)$request->input('email'));
        $role = (string)$request->input('role', $user['role']);
        $password = (string)$request->input('password');
        $isActive = $request->has('is_active') ? 1 : 0;

        if (empty($name) || empty($email)) {
            return $this->redirect("/users/{$id}/edit", [
                'error' => 'Ism va email kiritilishi shart.'
            ]);
        }

        // Email uniqueness check
        $existing = $this->userModel->findByEmail($email);
        if ($existing && (int)$existing['id'] !== $id) {
            return $this->redirect("/users/{$id}/edit", [
                'error' => 'Ushbu email boshqa foydalanuvchi tomonidan band qilingan.'
            ]);
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'role' => in_array($role, ['admin', 'manager'], true) ? $role : 'manager',
            'is_active' => $isActive,
        ];

        // Only update password if provided
        if (!empty($password)) {
            if (strlen($password) < 6) {
                return $this->redirect("/users/{$id}/edit", [
                    'error' => 'Parol kamida 6 ta belgidan iborat boʻlishi kerak.'
                ]);
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return $this->redirect('/users', [
            'success' => 'Foydalanuvchi muvaffaqiyatli yangilandi.'
        ]);
    }
}
