<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\CrmLead;
use App\Models\ERP\CrmLeadActivity;
use Illuminate\Http\Request;

class CrmActivityController extends Controller
{
    public function store(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'type'                => 'required|in:call,meeting,site_visit,email,whatsapp,note',
            'subject'             => 'required|string|max:255',
            'description'         => 'nullable|string',
            'activity_date'       => 'required|date',
            'next_follow_up_date' => 'nullable|date',
            'outcome'             => 'nullable|string|max:50',
        ]);

        $validated['lead_id'] = $lead->id;
        $validated['admin_id'] = auth('admin')->id();

        CrmLeadActivity::create($validated);

        if ($request->filled('next_follow_up_date')) {
            $lead->update(['next_follow_up_at' => $request->next_follow_up_date]);
        }

        $lead->score = $lead->calculateScore();
        $lead->save();

        return back()->with('success', 'Follow-up activity recorded.');
    }
}

