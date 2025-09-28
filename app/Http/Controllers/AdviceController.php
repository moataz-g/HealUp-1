<?php

namespace App\Http\Controllers;

use App\Models\Advice;
use App\Models\ChatSession;
use Illuminate\Support\Facades\Auth;

class AdviceController extends Controller
{
    public function index()
    {
        $advices = Advice::latest()->get();
        $chatSessions = ChatSession::where('user_id', Auth::id())
            ->with('advice')
            ->latest()
            ->get();

        return view('pages.advices', compact('advices', 'chatSessions'));    
    }

    public function show($id)
    {
        $advice = Advice::findOrFail($id);
        return view('pages.advice-show', compact('advice'));
    }
}
