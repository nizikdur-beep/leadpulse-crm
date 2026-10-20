<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Activity;
use App\Models\Client;
use App\Models\Lead;

class ActivityController extends Controller
{
    private Activity $activityModel;
    private Client $clientModel;
    private Lead $leadModel;

    public function __construct()
    {
        $this->activityModel = new Activity();
        $this->clientModel = new Client();
        $this->leadModel = new Lead();
    }

    public function index(Request $request): Response
    {
        $userId = $this->isAdmin() ? null : $this->userId();
        $activities = $this->activityModel->getRecent(50, $userId);

        return $this->render('activities.index', [
            'pageTitle' => 'Faoliyatlar tarixi',
            'activities' => $activities,
            'types' => Activity::TYPES,
        ]);
    }

    public function store(Request $request): Response
    {
        $type = (string)$request->input('type', 'note');
        $note = trim((string)$request->input('note'));
        $clientId = $request->input('client_id') ? (int)$request->input('client_id') : null;
        $leadId = $request->input('lead_id') ? (int)$request->input('lead_id') : null;

        if (empty($note)) {
            $redirectUrl = $leadId ? "/leads/{$leadId}" : ($clientId ? "/clients/{$clientId}" : '/activities');
            return $this->redirect($redirectUrl, [
                'error' => 'Faoliyat mazmuni yoki izohini yozish shart.'
            ]);
        }

        // Validate client/lead existence if provided
        if ($leadId && !$clientId) {
            $lead = $this->leadModel->find($leadId);
            if ($lead) {
                $clientId = (int)$lead['client_id'];
            }
        }

        $this->activityModel->create([
            'lead_id' => $leadId,
            'client_id' => $clientId,
            'user_id' => $this->userId(),
            'type' => $type,
            'note' => $note,
        ]);

        $redirectUrl = $leadId ? "/leads/{$leadId}" : ($clientId ? "/clients/{$clientId}" : '/activities');
        return $this->redirect($redirectUrl, [
            'success' => 'Faoliyat muvaffaqiyatli qayd etildi.'
        ]);
    }
}
