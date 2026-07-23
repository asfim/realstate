<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscriber::latest();

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        $subscribers = $query->paginate(15);
        $totalSubscribers = Subscriber::count();

        return view('backend.newsletters.index', compact('subscribers', 'totalSubscribers'));
    }

    public function destroy(Subscriber $newsletter)
    {
        $newsletter->delete();
        return redirect()->route('admin.newsletters.index')->with('success', 'Newsletter subscriber removed successfully.');
    }
}
