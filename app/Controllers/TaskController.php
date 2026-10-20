<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Task;
use App\Models\Client;
use App\Models\Lead;
use App\Models\User;

class TaskController extends Controller
{
    private Task $taskModel;
    private Client $clientModel;
    private Lead $leadModel;
    private User $userModel;

    public function __construct()
    {
        $this->taskModel = new Task();
        $this->clientModel = new Client();
        $this->leadModel = new Lead();
        $this->userModel = new User();
    }

    public function index(Request $request): Response
    {
        $page = (int)$request->query('page', 1);
        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');
        $userId = $this->isAdmin() ? ($request->query('user_id') ? (int)$request->query('user_id') : null) : $this->userId();

        $tasksData = $this->taskModel->getWithRelations(
            $page,
            15,
            $search,
            $status,
            $priority,
            $userId
        );

        $managers = $this->isAdmin() ? $this->userModel->getManagers() : [];

        return $this->render('tasks.index', [
            'pageTitle' => 'Vazifalar va eslatmalar',
            'tasks' => $tasksData,
            'search' => $search,
            'status' => $status,
            'priority' => $priority,
            'selectedUser' => $userId,
            'managers' => $managers,
            'statuses' => Task::STATUSES,
            'priorities' => Task::PRIORITIES,
        ]);
    }

    public function create(Request $request): Response
    {
        $clientId = $request->query('client_id') ? (int)$request->query('client_id') : null;
        $leadId = $request->query('lead_id') ? (int)$request->query('lead_id') : null;

        $clients = $this->isAdmin() 
            ? $this->clientModel->all('name ASC') 
            : $this->clientModel->where('assigned_to', $this->userId());

        $leads = $this->isAdmin()
            ? $this->leadModel->all('title ASC')
            : $this->leadModel->where('user_id', $this->userId());

        $managers = $this->isAdmin() ? $this->userModel->getManagers() : [];

        return $this->render('tasks.create', [
            'pageTitle' => 'Yangi vazifa yaratish',
            'clientId' => $clientId,
            'leadId' => $leadId,
            'clients' => $clients,
            'leads' => $leads,
            'managers' => $managers,
            'priorities' => Task::PRIORITIES,
        ]);
    }

    public function store(Request $request): Response
    {
        $title = trim((string)$request->input('title'));
        $dueDate = $request->input('due_date');
        $priority = (string)$request->input('priority', 'medium');
        $description = trim((string)$request->input('description'));
        $clientId = $request->input('client_id') ? (int)$request->input('client_id') : null;
        $leadId = $request->input('lead_id') ? (int)$request->input('lead_id') : null;
        $userId = $this->isAdmin() ? (int)$request->input('user_id', $this->userId()) : $this->userId();

        if (empty($title) || empty($dueDate)) {
            return $this->redirect('/tasks/create', [
                'error' => 'Vazifa sarlavhasi va muddatini kiritish shart.',
                '_old_input' => $request->all()
            ]);
        }

        $this->taskModel->create([
            'lead_id' => $leadId,
            'client_id' => $clientId,
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'due_date' => $dueDate,
            'priority' => $priority,
            'status' => 'open',
        ]);

        return $this->redirect('/tasks', [
            'success' => 'Vazifa muvaffaqiyatli rejalashtirildi.'
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return $this->redirect('/tasks', ['error' => 'Vazifa topilmadi.']);
        }

        $this->authorizeResource((int)$task['user_id']);

        $clients = $this->isAdmin() 
            ? $this->clientModel->all('name ASC') 
            : $this->clientModel->where('assigned_to', $this->userId());

        $leads = $this->isAdmin()
            ? $this->leadModel->all('title ASC')
            : $this->leadModel->where('user_id', $this->userId());

        $managers = $this->isAdmin() ? $this->userModel->getManagers() : [];

        return $this->render('tasks.edit', [
            'pageTitle' => 'Vazifani tahrirlash',
            'task' => $task,
            'clients' => $clients,
            'leads' => $leads,
            'managers' => $managers,
            'statuses' => Task::STATUSES,
            'priorities' => Task::PRIORITIES,
        ]);
    }

    public function update(Request $request, int $id): Response
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return $this->redirect('/tasks', ['error' => 'Vazifa topilmadi.']);
        }

        $this->authorizeResource((int)$task['user_id']);

        $title = trim((string)$request->input('title'));
        $dueDate = $request->input('due_date');

        if (empty($title) || empty($dueDate)) {
            return $this->redirect("/tasks/{$id}/edit", [
                'error' => 'Vazifa sarlavhasi va muddatini kiritish shart.',
                '_old_input' => $request->all()
            ]);
        }

        $data = [
            'title' => $title,
            'description' => trim((string)$request->input('description')),
            'due_date' => $dueDate,
            'priority' => (string)$request->input('priority', 'medium'),
            'status' => (string)$request->input('status', 'open'),
            'client_id' => $request->input('client_id') ? (int)$request->input('client_id') : null,
            'lead_id' => $request->input('lead_id') ? (int)$request->input('lead_id') : null,
        ];

        if ($this->isAdmin() && $request->has('user_id')) {
            $data['user_id'] = (int)$request->input('user_id');
        }

        $this->taskModel->update($id, $data);

        return $this->redirect('/tasks', [
            'success' => 'Vazifa muvaffaqiyatli yangilandi.'
        ]);
    }

    public function complete(Request $request, int $id): Response
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return $this->redirect('/tasks', ['error' => 'Vazifa topilmadi.']);
        }

        $this->authorizeResource((int)$task['user_id']);

        $this->taskModel->update($id, [
            'status' => 'done'
        ]);

        return $this->redirect('/tasks', [
            'success' => 'Vazifa bajarildi deb belgilandi!'
        ]);
    }
}
