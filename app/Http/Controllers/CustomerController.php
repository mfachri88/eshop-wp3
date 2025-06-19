<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ImageHelper;

// use App\Models\Kategori;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer = Customer::orderBy('id', 'desc')->get();
        return view('backend.v_customer.index', [
            'judul' => 'Halaman Customer',
            'index' => $customer
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('backend.v_customer.edit', [
            'judul' => 'Customer',
            'sub' => 'Ubah Customer',
            'edit' => $customer
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
            'nama' => 'required|max:255',
            'hp' => 'required|max:255|unique:customer,hp,' . $id,
        ];
        $validatedData = $request->validate($rules);
        Customer::where('id', $id)->update($validatedData);
        return redirect()->route('backend.customer.index')->with('success', 'Data berhasil diperbaharui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('backend.customer.index')->with('success', 'Data berhasil dihapus');
    }

    // Redirect ke Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback dari Google
    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
            
            // Cek apakah email sudah terdaftar
            $registeredUser = User::where('email', $socialUser->getEmail())->first();
            
            if (!$registeredUser) {
                // Buat user baru
                $user = User::create([
                    'nama' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'role' => '2', // Role customer
                    'status' => 1,
                    'password' => Hash::make('google_' . uniqid()),
                    'hp' => '-', // Nilai default sementara
                ]);
                
                // Buat data customer
                Customer::create([
                    'user_id' => $user->id,
                    'google_id' => $socialUser->id,
                    'google_token' => $socialUser->token,
                ]);
                
                Auth::login($user);
                return redirect()->intended('/')->with('success', 'Berhasil login dengan Google!');
                
            } else {
                // Jika user sudah ada, cek apakah sudah ada record customer
                $existingCustomer = Customer::where('user_id', $registeredUser->id)->first();
                
                if (!$existingCustomer) {
                    Customer::create([
                        'user_id' => $registeredUser->id,
                        'google_id' => $socialUser->id,
                        'google_token' => $socialUser->token,
                    ]);
                }
                
                Auth::login($registeredUser);
                return redirect()->intended('/')->with('success', 'Berhasil login!');
            }
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Terjadi kesalahan saat login dengan Google');
        }
    }

    public function updateAkun(Request $request, $id)
    {
        $customer = Customer::where('user_id', $id)->firstOrFail();
        
        // Validasi
        $rules = [
            'nama' => 'required|max:255',
            'hp' => 'required|min:10|max:13',
            'alamat' => 'required',
            'pos' => 'required',
        ];
        
        if ($request->email != $customer->user->email) {
            $rules['email'] = 'required|max:255|email|unique:user,email,' . $customer->user->id;
        }
        
        $validatedData = $request->validate($rules);
        
        // Update user data
        $customer->user->update([
            'nama' => $validatedData['nama'],
            'hp' => $validatedData['hp'],
            'email' => $validatedData['email'] ?? $customer->user->email,
        ]);
        
        // Update customer data dengan try-catch
        try {
            $customer->alamat = $request->alamat;
            $customer->pos = $request->pos;
            $customer->save();
        } catch (\Exception $e) {
            \Log::error('Error updating customer: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengupdate data alamat: ' . $e->getMessage());
        }
        
        return redirect()->route('customer.akun', $id)->with('success', 'Data berhasil diperbarui');
    }

    public function akun($id)
    {
        $loggedInCustomerId = Auth::user()->id;
        
        // Cek apakah ID yang diberikan sama dengan ID customer yang sedang login
        if ($id != $loggedInCustomerId) {
            return redirect()->route('customer.akun', ['id' => $loggedInCustomerId])
                ->with('msgError', 'Anda tidak berhak mengakses akun ini.');
        }
        
        $customer = Customer::where('user_id', $id)->firstOrFail();
        
        return view('v_customer.edit', [
            'judul' => 'Akun Customer',
            'subJudul' => 'Edit Akun',
            'edit' => $customer
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }

}