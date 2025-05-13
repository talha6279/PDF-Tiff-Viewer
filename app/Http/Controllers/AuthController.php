<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin/layout');
    }
    public function layout()
    {
        return view('admin/layout2');
    }
    
    public function login_form()
    {
        return view('admin.login');
    }
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        if (Auth::attempt($data)) {
            $user = Auth::user(); 

        session(['user' => $user]);
            return redirect()->route('dashboard')->with(['success' => 'User logged in successfully']);
        } else {
            return redirect()->route('login')->with(['error' => 'Invalid credentials']);
        }
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with(['success' => 'User logged out successfully']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
