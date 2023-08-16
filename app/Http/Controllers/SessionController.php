<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
  public function create()
  {
    return view('session.create');
  }

  public function store()
  {
    // get email, password
    // validasi
    // validasi user password cek database
    // buka view home
    // notifikasi success / gagal

    $attributes = request()->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if (auth()->attempt($attributes)) {
        return redirect('/home')->with('success', 'Welcome back');
      }
  
      return back()
        ->withInput()
        ->withErrors(['email' => 'Your credentials could not be verified']);
  }

  public function destroy()
  {
    $loggedout = auth()->user()->name;
    auth()->logout();

    return redirect('/')->with('success', "See you again {$loggedout}");
  }
}
