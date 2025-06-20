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
        // 1. Temukan customer berdasarkan user_id
        $customer = Customer::where('user_id', $id)->firstOrFail();
        $user = $customer->user;

        // 2. Aturan validasi
        $rules = [
            'nama' => 'required|max:255',
            'hp' => 'required|min:10|max:13',
            'alamat' => 'required',
            'pos' => 'required',
            'foto' => 'image|mimes:jpeg,jpg,png,gif|file|max:1024', // Validasi untuk foto
        ];

        // Validasi email hanya jika diubah
        if ($request->email != $user->email) {
            $rules['email'] = 'required|max:255|email|unique:users,email,' . $user->id;
        }

        $validatedData = $request->validate($rules, [
            'foto.image' => 'Format gambar harus jpeg, jpg, png, atau gif.',
            'foto.max' => 'Ukuran file gambar maksimal adalah 1024 KB.'
        ]);

        // 3. Proses upload foto jika ada file baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                $oldImagePath = public_path('storage/img-customer/') . $user->foto;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Simpan foto baru menggunakan ImageHelper
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $originalFileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'storage/img-customer/';
            
            ImageHelper::uploadAndResize($file, $directory, $originalFileName, 385, 400);
            
            // Simpan nama file baru untuk diupdate ke database
            $user->foto = $originalFileName;
        }

        // 4. Update data di tabel 'users'
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->hp = $request->hp;
        $user->save();

        // 5. Update data di tabel 'customer'
        $customer->alamat = $request->alamat;
        $customer->pos = $request->pos;
        $customer->save();

        return redirect()->route('customer.akun', $user->id)->with('success', 'Data akun berhasil diperbarui');
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