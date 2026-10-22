<?php

use App\Core\Router;
use App\Core\Response;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ClientController;
use App\Controllers\LeadController;
use App\Controllers\ActivityController;
use App\Controllers\TaskController;
use App\Controllers\CalendarController;
use App\Controllers\ReportController;
use App\Controllers\UserController;

// Root redirect
Router::get('/', function() {
    return Response::redirect('/dashboard');
});

// Health check endpoint
Router::get('/api/health', function() {
    return Response::json(['status' => 'ok', 'app' => 'LeadPulse CRM', 'time' => date('Y-m-d H:i:s')]);
});
Router::get('/health', function() {
    return Response::json(['status' => 'ok', 'app' => 'LeadPulse CRM', 'time' => date('Y-m-d H:i:s')]);
});

// Authentication Routes
Router::get('/login', [AuthController::class, 'showLogin'], ['guest']);
Router::post('/login', [AuthController::class, 'login'], ['guest']);
Router::post('/logout', [AuthController::class, 'logout'], ['auth']);

// Dashboard
Router::get('/dashboard', [DashboardController::class, 'index'], ['auth']);

// Clients CRUD
Router::get('/clients', [ClientController::class, 'index'], ['auth']);
Router::get('/clients/create', [ClientController::class, 'create'], ['auth']);
Router::post('/clients', [ClientController::class, 'store'], ['auth']);
Router::get('/clients/{id}', [ClientController::class, 'show'], ['auth']);
Router::get('/clients/{id}/edit', [ClientController::class, 'edit'], ['auth']);
Router::post('/clients/{id}/update', [ClientController::class, 'update'], ['auth']);
Router::post('/clients/{id}/delete', [ClientController::class, 'delete'], ['auth']);

// Leads & Pipeline CRUD
Router::get('/leads', [LeadController::class, 'index'], ['auth']);
Router::get('/leads/create', [LeadController::class, 'create'], ['auth']);
Router::post('/leads', [LeadController::class, 'store'], ['auth']);
Router::get('/leads/{id}', [LeadController::class, 'show'], ['auth']);
Router::get('/leads/{id}/edit', [LeadController::class, 'edit'], ['auth']);
Router::post('/leads/{id}/update', [LeadController::class, 'update'], ['auth']);
Router::post('/leads/{id}/delete', [LeadController::class, 'delete'], ['auth']);
Router::post('/leads/{id}/stage', [LeadController::class, 'updateStage'], ['auth']);

// Activities
Router::get('/activities', [ActivityController::class, 'index'], ['auth']);
Router::post('/activities', [ActivityController::class, 'store'], ['auth']);

// Tasks & Reminders
Router::get('/tasks', [TaskController::class, 'index'], ['auth']);
Router::get('/tasks/create', [TaskController::class, 'create'], ['auth']);
Router::post('/tasks', [TaskController::class, 'store'], ['auth']);
Router::get('/tasks/{id}/edit', [TaskController::class, 'edit'], ['auth']);
Router::post('/tasks/{id}/update', [TaskController::class, 'update'], ['auth']);
Router::post('/tasks/{id}/complete', [TaskController::class, 'complete'], ['auth']);

// Calendar
Router::get('/calendar', [CalendarController::class, 'index'], ['auth']);

// Reports & CSV Export
Router::get('/reports', [ReportController::class, 'index'], ['auth']);
Router::get('/reports/export', [ReportController::class, 'export'], ['auth']);

// Users (Admin Only)
Router::get('/users', [UserController::class, 'index'], ['auth', 'admin']);
Router::get('/users/create', [UserController::class, 'create'], ['auth', 'admin']);
Router::post('/users', [UserController::class, 'store'], ['auth', 'admin']);
Router::get('/users/{id}/edit', [UserController::class, 'edit'], ['auth', 'admin']);
Router::post('/users/{id}/update', [UserController::class, 'update'], ['auth', 'admin']);
