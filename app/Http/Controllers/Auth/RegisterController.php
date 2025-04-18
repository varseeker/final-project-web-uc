<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Nasabah;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'noIdentitas' => ['required', 'string', 'max:255', 'min:16'],
            'nama' => ['required', 'string', 'max:255'],
            'tglLahir' => ['required', 'date'],
            'alamat' => ['required', 'string', 'max:255'],
            'noTelepon' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:nasabah,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        \DB::beginTransaction();

        try {
            // Insert into `nasabah` table
            $nasabah = Nasabah::create([
                'noIdentitas' => $data['noIdentitas'],
                'nama' => $data['nama'],
                'alamat' => $data['alamat'],
                'nomor_telepon' => $data['noTelepon'],
                'email' => $data['email'],
                'tanggal_lahir' => $data['tglLahir'],
                'status_pekerjaan' => $data['pekerjaan'], // Default value
            ]);

            // Insert into `users` table
            $user = User::create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'nasabah', // Default value
            ]);

            $nasabah->update(['user_id' => $user->id]);
            $user->update(['id_nasabah' => $nasabah->id_nasabah]);

            \DB::commit();

            return $user;

        } catch (\Exception $e) {
            \DB::rollBack();

            // Log error for debugging
            \Log::error('Registration Error: ' . $e->getMessage());

            throw $e; // Re-throw exception to handle it higher in the stack
        }
    }

}
