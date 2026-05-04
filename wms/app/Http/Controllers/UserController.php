<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        return view('users.index', compact('users'));
    }
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }
    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:255','email'=>'required|email|unique:users','phone'=>'nullable|string','password'=>'required|min:8|confirmed','role'=>'required|exists:roles,name']);
        $user = User::create(['name'=>$data['name'],'email'=>$data['email'],'phone'=>$data['phone']??null,'password'=>Hash::make($data['password'])]);
        $user->assignRole($data['role']);
        return redirect()->route('users.index')->with('success','User created.');
    }
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user','roles'));
    }
    public function update(Request $request, User $user)
    {
        $data = $request->validate(['name'=>'required|string','email'=>'required|email|unique:users,email,'.$user->id,'phone'=>'nullable|string','is_active'=>'boolean','role'=>'required|exists:roles,name','password'=>'nullable|min:8|confirmed']);
        $user->update(['name'=>$data['name'],'email'=>$data['email'],'phone'=>$data['phone']??null,'is_active'=>$data['is_active']??true]);
        if (!empty($data['password'])) $user->update(['password'=>Hash::make($data['password'])]);
        $user->syncRoles([$data['role']]);
        return redirect()->route('users.index')->with('success','User updated.');
    }
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error','Cannot delete yourself.');
        $user->delete();
        return back()->with('success','User deleted.');
    }
}
