<?php

namespace App\Http\Controllers;
use App\User;
use App\Country;
use Auth;
use Session;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function userLoginRegister(){
        return view('users.login_register');
    }

    public function register(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            //check if user already exist
           // echo "<pre>"; print_r($data); die;
           $usersCount = User::where('email',$data['email'])->count();
           if($usersCount>0){
               return redirect()->back()->with('flash_message_error','Email already exist!');
           }else{
               $user = new User;
               $user->name = $data['name'];
               $user->email = $data['email'];
               $user->password = bcrypt($data['password']);
               $user->save();
               if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                Session::put('frontSession',$data['email']);
                   return redirect('/cart');
               }
           }
        }
    }

    public function login(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                Session::put('frontSession',$data['email']);
                return redirect('/cart');
            }else{
                return redirect()->back()->with('flash_message_error','Invalid Username or Password');
            }
        }
    }

    public function logout(){
        Auth::logout(); 
        Session::forget('frontSession'); 
        return redirect('/');
    }

    public function account(){
        $user_id = Auth::user()->id;
        $userDetails = User::find($user_id);
        $countries = Country::get();
        return view('users.account')->with(compact('countries','userDetails'));
    }

    public function checkEmail(Request $request){
        //CHeck if user email already exist
        $data = $request->all();
        $usersCount = User::where('email',$data['email'])->count();
        if($usersCount>0){
            echo "false";
        }else{
            echo "true";
        }
    }
}