<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
      public function index()
{
   
    if (Auth::user()->role == 'admin' || Auth::user()->role == 'manager') {
        $tasks      = Task::latest()->take(5)->get(); 
        $comments   = Comment::latest()->take(5)->get();
    }
     else
     
     {

        $tasks      = Task::where('assigned_to', Auth::id())->latest()->take(5)->get();
        $comments   = Comment::where('user_id', Auth::id())->latest()->take(5)->get();
    }


    return view('dashboard', compact('tasks',  'comments'));
}
}
