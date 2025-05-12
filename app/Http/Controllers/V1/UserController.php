<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $user=Auth::user();
        if (in_array($user->role, ['admin'])) {
        $users=User::orderBy('name')->get();
        return view('users.index',compact('users'));
       
    }

    }

    public function store(Request $request){
    $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,manager,user',
        ]);

        $password=Hash::make($request->password);
         User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $password,
            'role'     => $request->role,
        ]);
         return redirect()->route('users.index')->with('success', 'User created successfully.');

   
    }

     public function delete($id)
    {
       $user=User::find($id);
       if( !$user){
            return redirect()->route('users.index')->with('error', 'User not found.');
       }

       $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

     public function update(Request $request,$id){
        
    $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:admin,manager,user',
        ]);
        $user = User::find($id);
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role
        ]);          
         return redirect()->route('users.index')->with('success', 'User updated successfully.');

   
    }
}
