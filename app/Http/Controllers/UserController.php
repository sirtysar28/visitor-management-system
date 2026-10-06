<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Support\Vms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department')
            ->when(request('search'), fn ($q, $v) => $q->where(fn ($q2) => $q2
                ->where('name', 'like', "%{$v}%")
                ->orWhere('email', 'like', "%{$v}%")))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', [
            'user' => new User(['role' => User::ROLE_HOST, 'is_active' => true]),
            'departments' => Department::orderBy('name')->get(),
            'roles' => User::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        Vms::audit('create', 'user', 'Menambahkan user ' . $data['name']);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.form', [
            'user' => $user,
            'departments' => Department::orderBy('name')->get(),
            'roles' => User::ROLES,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        Vms::audit('update', 'user', 'Memperbarui user ' . $user->name);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $name = $user->name;
        $user->delete();

        Vms::audit('delete', 'user', 'Menghapus user ' . $name);

        return back()->with('success', 'User berhasil dihapus.');
    }

    private function validated(Request $request, ?User $user): array
    {
        $emailRule = $user
            ? ['required', 'email', Rule::unique('users')->ignore($user->id)]
            : ['required', 'email', 'unique:users'];

        $passwordRule = $user
            ? ['nullable', 'string', Password::min(6)]
            : ['required', 'string', Password::min(6)];

        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => $emailRule,
            'password' => $passwordRule,
            'role' => ['required', 'in:' . implode(',', array_keys(User::ROLES))],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'position' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
