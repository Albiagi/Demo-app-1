<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function index() {
        //take data user then save to $user variable
        $user = Auth::user();
        //condition if user has level
        if($user){
            //if user level as admin
            if($user->level == 'admin'){
                //user will direct to admin page
                return redirect()->intended('admin');
            } 
            //if user level as user
            else if($user->level == 'user'){
                //user will direct to user page
                return redirect()->intended('user');
            }
        }
        return view('login');
    }

    public function proses_login(Request $request) {
        //we make validation when login button clicked
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        //take data request username and password
        $credential = $request->only('username', 'password');

        //check if data username and password valid
        if(Auth::attempt($credential)){
            //if success save data user in variable $user
            $user = Auth::user();
            //check again if level user as admin then redirect to admin page
            if($user->level == 'admin'){
                return redirect()->intended('admin');
            }
            //but if user level as user then redirect to user page
            else if($user->level == 'user'){
                return redirect()->to('dashboard');
            }
            //if don't have role then go to page home or "/"
            return redirect()->intended('home');
        }
        //if user data not valid then return to login user with error
        return redirect('login')->withInput()->withErrors(['login_gagal' => 'These Credentials does not match our record']);
    }

    // show register view
    public function register() {
        return view('register');
    }

    //action register form
    public function proses_register(Request $request) {
        //validation process for registration
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'username' => 'required|unique:user',
            'email' => 'required|email|unique:user',
            'password' => 'required'
        ]);

        //if fail, back to register page with error message
        if($validator->fails()){
            return redirect('register')->withErrors($validator)->withInput();
        }

        //if success input level & hash password
        $request['level'] = 'user';
        $request['password'] = Hash::make($request->password);

        //input all data request to table user
        User::create($request->all());

        //if success for all action, back to login page
        return redirect()->route('login');
    }

    //logout function
    public function logout(Request $request) {
        //logout must be delete the session
        $request->session()->flush();

        //run logout function in auth
        Auth::logout();

        //back to login
        return redirect('login');
    }
}
