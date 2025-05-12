<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class TaskController extends Controller
{
 public function index()
{
    $userId =Auth::id();

    $tasks = Task::where('created_by', $userId)
                ->orWhere('assigned_to', $userId)
                ->with(['category', 'assignedTo']) // eager load related models
                ->get();

    $categories = Category::all(); // assuming you're passing this for the modal

    return view('tasks.index', compact('tasks', 'categories'));
}



        public function store(Request $request)
        {
            // dd($request->toArray());
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:pending,in_progress,completed',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'file' => 'nullable',
                'assigned_to' => 'required|exists:users,id',
                'category_id' => 'required|exists:categories,id',
            ]);
             

            $data['created_by'] = Auth::id();
             if ($request->hasFile('file')) {
                        $dataFile = $request->file('file');
                        $fileName = time() . '.' . $dataFile->getClientOriginalExtension();
                        $filePath = $dataFile->storeAs('tasks', $fileName, 'public');
                        $data['file'] = $filePath;
                    }
            Task::create($data);
            return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
        }
  

     public function delete($id)
    {
       $task=Task::find($id);
       if( !$task){
            return redirect()->route('users.index')->with('error', 'task not found.');
       }

       $task->delete();

        return redirect()->route('tasks.index')->with('success', 'task deleted successfully.');
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'assigned_to' => 'required|exists:users,id',
        ]);
           $task=Task::find($id);

         if ($request->hasFile('file')) {
              if ($task->file) {
                            Storage::disk('public')->delete($task->file);
                        }
                        $dataFile = $request->file('file');
                        $fileName = time() . '.' . $dataFile->getClientOriginalExtension();
                        $filePath = $dataFile->storeAs('tasks', $fileName, 'public');
                        $data['file'] = $filePath;
                    }

       
        $task->update($data);


        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }


}