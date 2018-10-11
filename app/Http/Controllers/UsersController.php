<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    //注册
    public function create(){
        return view('users.create');
    }

    public function store(Request $request){
        $this->validate($request,[
            'name'=>'required|max:50',
            'email'=>'required|max:255|email|unique:users',
            'password'=>'min:6|required|confirmed'
        ]);

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);
        session()->flash('success','注册成功');
        return redirect()->route('users.show',[$user]);
    }

    public function show(User $user){
        return view('users.show',compact('user'));
    }
}
