<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Tasks\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function index()
    {
        $tareas = Task::all();
        return view('tasks.index', compact('tareas'));
    }
}
