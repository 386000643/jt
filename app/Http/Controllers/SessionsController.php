<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class SessionsController extends Controller
{

    //guest指定未登录用户可以访问的动作
    public function __construct(){
        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
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
           //匹配成功，判断是否有激活
            if (Auth::user()->activated) {
                session()->flash('success','登录成功');
                //intended跳转至上一次请求访问的页面
                return redirect()->intended(route('users.show',[Auth::user()]));
            }else{
                session()->flash('warning','账户尚未激活，请检查邮箱');
                return redirect('/');
            }

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
