<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Lead;
use App\Models\Client;
use App\Models\User;
use App\Models\Activity;
use App\Models\Task;
use App\Models\LeadStageHistory;
use App\Services\LeadService;

class LeadController extends Controller
{
    private Lead $leadModel;
    private Client $clientModel;
    private User $userModel;
    private LeadService $leadService;

    public function __construct()
    {
        $this->leadModel = new Lead();
        $this->clientModel = new Client();
        $this->userModel = new User();
        $this->leadService = new LeadService();
    }

    public function index(Request $request): Response
    {
        $viewType = $request->query('view', 'pipeline'); // 'pipeline' or 'table'
        $search = $request->query('search');
        $stage = $request->query('stage');
        $userId = $this->isAdmin() ? ($request->query('user_id') ? (int)$request->query('user_id') : null) : $this->userId();
        $minAmount = $request->query('min_amount');
        $maxAmount = $request->query('max_amount');
        $page = (int)$request->query('page', 1);

        $managers = $this->isAdmin() ? $this->userModel->getManagers() : [];

        if ($viewType === 'pipeline') {
            $pipeline = $this->leadModel->getPipelineGrouped($userId);
            return $this->render('leads.index', [
                'pageTitle' => 'Bitimlar (Voronka)',
                'viewType' => 'pipeline',
                'pipeline' => $pipeline,
                'managers' => $managers,
                'selectedUser' => $userId,
                'stages' => Lead::STAGES,
            ]);
        }

        $leadsData = $this->leadModel->getWithRelations(
            $page,
            15,
            $search,
            $stage,
            $userId,
            $minAmount,
            $maxAmount
        );

        return $this->render('leads.index', [
            'pageTitle' => 'Bitimlar (Roʻyxat)',
            'viewType' => 'table',
            'leads' => $leadsData,
            'search' => $search,
            'stage' => $stage,
            'selectedUser' => $userId,
            'minAmount' => $minAmount,
            'maxAmount' => $maxAmount,
            'managers' => $managers,
            'stages' => Lead::STAGES,
        ]);
    }

    public function create(Request $request): Response
    {
        $clientId = (int)$request->query('client_id');
        $clients = $this->isAdmin() 
            ? $this->clientModel->all('name ASC') 
            : $this->clientModel->where('assigned_to', $this->userId());

        $managers = $this->isAdmin() ? $this->userModel->getManagers() : [];

        return $this->render('leads.create', [
            'pageTitle' => 'Yangi bitim qoʻshish',
            'clients' => $clients,
            'clientId' => $clientId,
            'managers' => $managers,
            'stages' => Lead::STAGES,
        ]);
    }

    public function store(Request $request): Response
    {
        $clientId = (int)$request->input('client_id');
        $title = trim((string)$request->input('title'));
        $amount = (float)$request->input('amount', 0);
        $stage = (string)$request->input('stage', 'new');
        $source = trim((string)$request->input('source'));
        $notes = trim((string)$request->input('notes'));
        $nextContact = $request->input('next_contact_at') ?: null;
        $userId = $this->isAdmin() ? (int)$request->input('user_id', $this->userId()) : $this->userId();

        if (empty($title) || empty($clientId)) {
            return $this->redirect('/leads/create', [
                'error' => 'Bitim sarlavhasi va mijozni tanlash shart.',
                '_old_input' => $request->all()
            ]);
        }

        $leadId = $this->leadService->createLead([
            'client_id' => $clientId,
            'user_id' => $userId,
            'title' => $title,
            'amount' => $amount,
            'stage' => $stage,
            'source' => $source,
            'notes' => $notes,
            'next_contact_at' => $nextContact,
        ], $this->userId());

        return $this->redirect('/leads/' . $leadId, [
            'success' => 'Bitim muvaffaqiyatli yaratildi.'
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $lead = $this->leadModel->findWithDetails($id);
        if (!$lead) {
            return $this->redirect('/leads', ['error' => 'Bitim topilmadi.']);
        }

        $this->authorizeResource((int)$lead['user_id']);

        $historyModel = new LeadStageHistory();
        $history = $historyModel->forLead($id);

        $activityModel = new Activity();
        $activities = $activityModel->forLead($id);

        $taskModel = new Task();
        $tasks = $taskModel->where('lead_id', $id);

        return $this->render('leads.show', [
            'pageTitle' => $lead['title'] . ' — Bitim tafsilotlari',
            'lead' => $lead,
            'history' => $history,
            'activities' => $activities,
            'tasks' => $tasks,
            'stages' => Lead::STAGES,
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $lead = $this->leadModel->findWithDetails($id);
        if (!$lead) {
            return $this->redirect('/leads', ['error' => 'Bitim topilmadi.']);
        }

        $this->authorizeResource((int)$lead['user_id']);

        $clients = $this->isAdmin() 
            ? $this->clientModel->all('name ASC') 
            : $this->clientModel->where('assigned_to', $this->userId());

        $managers = $this->isAdmin() ? $this->userModel->getManagers() : [];

        return $this->render('leads.edit', [
            'pageTitle' => 'Bitimni tahrirlash',
            'lead' => $lead,
            'clients' => $clients,
            'managers' => $managers,
            'stages' => Lead::STAGES,
        ]);
    }

    public function update(Request $request, int $id): Response
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return $this->redirect('/leads', ['error' => 'Bitim topilmadi.']);
        }

        $this->authorizeResource((int)$lead['user_id']);

        $title = trim((string)$request->input('title'));
        if (empty($title)) {
            return $this->redirect("/leads/{$id}/edit", [
                'error' => 'Bitim sarlavhasini kiritish shart.',
                '_old_input' => $request->all()
            ]);
        }

        $newStage = (string)$request->input('stage', $lead['stage']);
        $lostReason = trim((string)$request->input('lost_reason', ''));

        // If stage changed, update via LeadService for history logging
        if ($newStage !== $lead['stage']) {
            $this->leadService->updateStage($id, $newStage, $this->userId(), $lostReason);
        }

        $data = [
            'title' => $title,
            'amount' => (float)$request->input('amount', 0),
            'source' => trim((string)$request->input('source')),
            'notes' => trim((string)$request->input('notes')),
            'next_contact_at' => $request->input('next_contact_at') ?: null,
            'lost_reason' => $newStage === 'lost' ? $lostReason : null,
        ];

        if ($this->isAdmin() && $request->has('user_id')) {
            $data['user_id'] = (int)$request->input('user_id');
        }

        if ($request->has('client_id')) {
            $data['client_id'] = (int)$request->input('client_id');
        }

        $this->leadModel->update($id, $data);

        return $this->redirect("/leads/{$id}", [
            'success' => 'Bitim muvaffaqiyatli yangilandi.'
        ]);
    }

    public function updateStage(Request $request, int $id): Response
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            if ($request->query('format') === 'json' || !empty($request->json())) {
                return $this->json(['success' => false, 'message' => 'Bitim topilmadi.'], 404);
            }
            return $this->redirect('/leads', ['error' => 'Bitim topilmadi.']);
        }

        $this->authorizeResource((int)$lead['user_id']);

        $newStage = (string)$request->input('stage');
        $lostReason = $request->input('lost_reason');

        try {
            $this->leadService->updateStage($id, $newStage, $this->userId(), $lostReason);

            if ($request->query('format') === 'json' || !empty($request->json())) {
                return $this->json([
                    'success' => true,
                    'message' => 'Bosqich muvaffaqiyatli yangilandi.',
                    'new_stage' => $newStage
                ]);
            }

            return $this->redirect("/leads/{$id}", [
                'success' => 'Bitim bosqichi muvaffaqiyatli oʻzgartirildi.'
            ]);
        } catch (\Throwable $e) {
            if ($request->query('format') === 'json' || !empty($request->json())) {
                return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return $this->redirect("/leads/{$id}", ['error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request, int $id): Response
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return $this->redirect('/leads', ['error' => 'Bitim topilmadi.']);
        }

        $this->authorizeResource((int)$lead['user_id'], 'Bitimni oʻchirish huquqingiz yoʻq.');

        $this->leadModel->delete($id);

        return $this->redirect('/leads', [
            'success' => 'Bitim muvaffaqiyatli oʻchirildi.'
        ]);
    }
}
