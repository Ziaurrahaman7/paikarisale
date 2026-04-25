<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\Admin;
use App\Enums\UserRole;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\BaseController;
use Devrabiul\ToastMagic\Facades\ToastMagic;

class LoginController extends BaseController
{
    public function __construct(private readonly Admin $admin, private readonly AdminService $adminService)
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    /**
     * Show login page
     */
    public function index(?Request $request, string $type = null): View
    {
        $loginTypes = [
            UserRole::ADMIN => getWebConfig(name: 'admin_login_url'),
            UserRole::EMPLOYEE => getWebConfig(name: 'employee_login_url')
        ];

        $userType = array_search($type, $loginTypes);
        abort_if(!$userType, 404);

        return view('admin-views.auth.login')->with(['role' => $userType]);
    }

    /**
     * Handle login submission
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $admin = $this->admin->where('email', $request['email'])->first();

        if ($admin && in_array($request['role'], [UserRole::ADMIN, UserRole::EMPLOYEE]) && $admin->status) {

            if ($admin['id'] == 1 && $request['role'] != 'admin') {
                return redirect()->back()->withInput($request->only('email', 'remember'))
                    ->withErrors([translate('Please_login_from_the_admin_login_page')]);
            }

            if ($admin['id'] != 1 && $request['role'] != 'employee') {
                return redirect()->back()->withInput($request->only('email', 'remember'))
                    ->withErrors([translate('Please_login_from_the_employee_login_page')]);
            }

            if ($this->adminService->isLoginSuccessful($request['email'], $request['password'], $request['remember'])) {
                return redirect()->route('admin.dashboard.index');
            }
        }

        ToastMagic::error(translate('credentials_does_not_match_or_your_account_has_been_suspended'));
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    /**
     * Logout admin/employee
     */
    public function logout(): RedirectResponse
    {
        $authType = auth('admin')->id() == 1 ? 'admin' : 'employee';
        $this->adminService->logout();
        session()->flash('success', translate('logged out successfully'));

        if ($authType == 'employee') {
            return redirect('login/' . getWebConfig(name: 'employee_login_url'));
        } else {
            return redirect('login/' . getWebConfig(name: 'admin_login_url'));
        }
    }
}
