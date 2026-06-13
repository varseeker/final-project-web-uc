<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Inventory\InventoryMenuSyncService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct(
        private InventoryMenuSyncService $inventoryMenuSync,
    ) {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function attemptLogin(Request $request)
    {
        $attempted = $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );

        if (! $attempted) {
            return false;
        }

        if (! Auth::user()->isPosStaff()) {
            $this->guard()->logout();

            throw ValidationException::withMessages([
                $this->username() => ['Akun ini tidak memiliki akses ke POS. Hanya staf yang dapat login.'],
            ]);
        }

        return true;
    }

    protected function authenticated(Request $request, $user)
    {
        $this->inventoryMenuSync->ensureSynced();
    }

    protected function loggedOut(Request $request)
    {
        return redirect()->route('login');
    }
}
