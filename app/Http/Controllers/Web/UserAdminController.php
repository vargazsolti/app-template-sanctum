<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function issueToken(Request $request)
    {
        $plain = $request->user()->createToken('ui-session')->plainTextToken;
        return response()->json(['token' => $plain]);
    }
}
