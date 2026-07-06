<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function showLogin()
    {
        if (session()->has('student')) {
            return redirect()->route('detection.form');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'matric_no' => 'required|string|max:20',
            'password'  => 'required|string',
        ]);

        $result  = $this->authService->execute(
            $request->input('matric_no'),
            $request->input('password')
        );
        $student = $result['student'];

        if (!$student) {
            return back()
                ->withErrors(['matric_no' => $result['error']])
                ->withInput(['matric_no' => $request->input('matric_no')]);
        }

        session()->put('student', [
            'id'        => $student->id,
            'matric_no' => $student->matric_no,
            'full_name' => $student->full_name,
        ]);

        session()->regenerate();

        return redirect()->route('detection.form');
    }

    public function logout(Request $request)
    {
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
