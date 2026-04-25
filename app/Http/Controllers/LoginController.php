<?php

namespace App\Http\Controllers;

use App\Enums\SessionKey;
use App\Models\Admin;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => 'logout']);
    }

    /**
     * Show the login form
     */
    public function login($login_url)
    {
        $data = array_column(
            \App\Models\BusinessSetting::whereIn('type', ['employee_login_url','admin_login_url'])
                ->get(['type','value'])->toArray(),
            'value',
            'type'
        );

        $loginTypes = [
            'admin' => 'admin_login_url',
            'employee' => 'employee_login_url'
        ];

        $user_type = array_search($login_url, $data);
        abort_if(!$user_type, 404);

        $role = array_search($user_type, $loginTypes, true);
        abort_if($role === null, 404);

        return view('admin-views.auth.login', compact('role'));
    }

    /**
     * Handle login submission
     */
    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        // Admin login
        if ($request->role == 'admin') {
            $data = Admin::where('email', $request->email)
                         ->where('admin_role_id', 1)
                         ->first();

            if (!$data) {
                return redirect()->back()->withInput($request->only('email', 'remember'))
                                         ->withErrors(['Credentials do not match.']);
            } elseif ($data->status != 1) {
                return redirect()->back()->withInput($request->only('email', 'remember'))
                                         ->withErrors(['You are blocked! Contact admin.']);
            }

        // Employee login
        } elseif ($request->role == 'employee') {
            $data = Admin::where('email', $request->email)
                         ->where('admin_role_id', '!=', 1)
                         ->first();

            if (!$data) {
                return redirect()->back()->withInput($request->only('email', 'remember'))
                                         ->withErrors(['Credentials do not match.']);
            } elseif ($data->status != 1) {
                return redirect()->back()->withInput($request->only('email', 'remember'))
                                         ->withErrors(['You are blocked! Contact admin.']);
            }

        } else {
            Toastr::error('Role missing');
            return back();
        }

        $role = $this->loginAttempt($request->role, $request->email, $request->password, $request->remember);

        if ($role === 'admin' || $role === 'employee') {
            return redirect()->route('admin.dashboard.index');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
                                 ->withErrors(['Credentials do not match.']);
    }

    /**
     * Attempt login
     */
    private function loginAttempt($role, $email, $password, $remember = false)
    {
        if ($role === 'admin' || $role === 'employee') {
            if (auth('admin')->attempt(['email' => $email, 'password' => $password], $remember)) {
                return $role;
            }
        }
        return false;
    }
}
