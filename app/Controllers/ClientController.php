<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Client;
use App\Models\User;
use App\Models\Lead;
use App\Models\Activity;
use App\Models\Task;
use App\Services\DuplicateDetector;

class ClientController extends Controller
{
    private Client $clientModel;
    private User $userModel;
    private DuplicateDetector $duplicateDetector;

    public function __construct()
    {
        $this->clientModel = new Client();
        $this->userModel = new User();
        $this->duplicateDetector = new DuplicateDetector();
    }

    public function index(Request $request): Response
    {
        $page = (int)$request->query('page', 1);
        $search = $request->query('search');
        $assignedTo = $this->isAdmin() ? ($request->query('assigned_to') ? (int)$request->query('assigned_to') : null) : $this->userId();
        $sort = (string)$request->query('sort', 'created_at');
        $order = (string)$request->query('order', 'DESC');

        $clientsData = $this->clientModel->getWithRelations($page, 10, $search, $assignedTo, $sort, $order);
        $managers = $this->isAdmin() ? $this->userModel->getManagers() : [];

        return $this->render('clients.index', [
            'pageTitle' => 'Mijozlar',
            'clients' => $clientsData,
            'search' => $search,
            'assignedTo' => $assignedTo,
            'sort' => $sort,
            'order' => $order,
            'managers' => $managers,
        ]);
    }

    public function create(Request $request): Response
    {
        $managers = $this->isAdmin() ? $this->userModel->getManagers() : [];

        return $this->render('clients.create', [
            'pageTitle' => 'Yangi mijoz qoʻshish',
            'managers' => $managers,
        ]);
    }

    public function store(Request $request): Response
    {
        $data = [
            'name' => trim((string)$request->input('name')),
            'phone' => trim((string)$request->input('phone')),
            'email' => trim((string)$request->input('email')),
            'company' => trim((string)$request->input('company')),
            'address' => trim((string)$request->input('address')),
            'notes' => trim((string)$request->input('notes')),
            'assigned_to' => $this->isAdmin() ? (int)$request->input('assigned_to', $this->userId()) : $this->userId(),
            'created_by' => $this->userId(),
        ];

        // Validation
        if (empty($data['name'])) {
            return $this->redirect('/clients/create', [
                'error' => 'Mijoz ismi yoki kompaniya nomini kiritish shart.',
                '_old_input' => $data
            ]);
        }

        // Duplicate check
        $dupCheck = $this->duplicateDetector->check($data['phone'], $data['email']);
        if ($dupCheck['has_duplicate'] && !$request->has('force_save')) {
            $dupNames = implode(', ', array_map(fn($d) => $d['name'] . ' (' . ($d['phone'] ?? $d['email']) . ')', $dupCheck['duplicates']));
            return $this->redirect('/clients/create', [
                'error' => "Bunday telefon yoki emailga ega mijoz allaqachon mavjud: {$dupNames}. Baribir saqlash uchun tasdiqlang.",
                'show_force_save' => true,
                '_old_input' => $data
            ]);
        }

        $clientId = $this->clientModel->create($data);

        return $this->redirect('/clients/' . $clientId, [
            'success' => 'Mijoz muvaffaqiyatli saqlandi.'
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $client = $this->clientModel->findWithDetails($id);
        if (!$client) {
            return $this->redirect('/clients', ['error' => 'Mijoz topilmadi.']);
        }

        // Authorization: Admin or Assigned Manager
        $this->authorizeResource((int)$client['assigned_to']);

        // Related Leads
        $leadModel = new Lead();
        $leads = $leadModel->where('client_id', $id);

        // Related Activities
        $activityModel = new Activity();
        $activities = $activityModel->forClient($id);

        // Related Tasks
        $taskModel = new Task();
        $tasks = $taskModel->where('client_id', $id);

        return $this->render('clients.show', [
            'pageTitle' => $client['name'] . ' — Mijoz kartasi',
            'client' => $client,
            'leads' => $leads,
            'activities' => $activities,
            'tasks' => $tasks,
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            return $this->redirect('/clients', ['error' => 'Mijoz topilmadi.']);
        }

        $this->authorizeResource((int)$client['assigned_to']);
        $managers = $this->isAdmin() ? $this->userModel->getManagers() : [];

        return $this->render('clients.edit', [
            'pageTitle' => 'Mijozni tahrirlash',
            'client' => $client,
            'managers' => $managers,
        ]);
    }

    public function update(Request $request, int $id): Response
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            return $this->redirect('/clients', ['error' => 'Mijoz topilmadi.']);
        }

        $this->authorizeResource((int)$client['assigned_to']);

        $name = trim((string)$request->input('name'));
        if (empty($name)) {
            return $this->redirect("/clients/{$id}/edit", [
                'error' => 'Mijoz ismini kiritish shart.',
                '_old_input' => $request->all()
            ]);
        }

        $data = [
            'name' => $name,
            'phone' => trim((string)$request->input('phone')),
            'email' => trim((string)$request->input('email')),
            'company' => trim((string)$request->input('company')),
            'address' => trim((string)$request->input('address')),
            'notes' => trim((string)$request->input('notes')),
        ];

        if ($this->isAdmin() && $request->has('assigned_to')) {
            $data['assigned_to'] = (int)$request->input('assigned_to');
        }

        $this->clientModel->update($id, $data);

        return $this->redirect("/clients/{$id}", [
            'success' => 'Mijoz maʼlumotlari muvaffaqiyatli yangilandi.'
        ]);
    }

    public function delete(Request $request, int $id): Response
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            return $this->redirect('/clients', ['error' => 'Mijoz topilmadi.']);
        }

        $this->authorizeResource((int)$client['assigned_to'], 'Mijozni oʻchirish huquqingiz yoʻq.');

        $this->clientModel->delete($id);

        return $this->redirect('/clients', [
            'success' => 'Mijoz muvaffaqiyatli oʻchirildi.'
        ]);
    }
}
