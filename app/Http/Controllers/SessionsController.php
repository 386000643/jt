<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class SessionsController extends Controller
{
    //登录界面
    public function create(){
        return view('sessions.create');
    }

    //登录成功
    public function store(Request $request){
        //验证格式是否准确
        $credentials=$this->validate($request,[
            'email'=>'required|email|max:255',
            'password'=>'required',
        ]);

        //与数据库里面的数据进行对比
       if (Auth::attempt($credentials,$request->has('remember'))) {
           //登录成功
            session()->flash('success','登录成功');
            return redirect()->route('users.show',[Auth::user()]);
       }else{
        //登录失败

            session()->flash('danger','邮箱与密码不符');
            return redirect()->back();
       }

    }

    //退出登录
    public function destroy(){
        Auth::logout();
        session()->flash('success','退出成功');
        return redirect('login');
    }
}
