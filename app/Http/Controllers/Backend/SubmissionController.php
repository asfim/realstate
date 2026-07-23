<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::with(['featuredProject', 'property'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        $submissions = $query->paginate(15)->withQueryString();

        return view('backend.submissions.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        if (!$submission->is_read) {
            $submission->update(['is_read' => true]);
        }

        $submission->load(['featuredProject', 'property']);

        return view('backend.submissions.show', compact('submission'));
    }

    public function destroy(Submission $submission)
    {
        $submission->delete();

        return redirect()->route('admin.submissions.index')->with('success', 'Submission deleted successfully.');
    }
}
