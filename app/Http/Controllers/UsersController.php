<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{



    public function __construct(){
        //只有登录用户才能访问edit和update
        $this->middleware('auth',[
            'except'=>['create','index','store','show']
        ]);

        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
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
        Auth::login($user);
        session()->flash('success','注册成功');
        return redirect()->route('users.show',[$user]);
    }

    public function show(User $user){
        return view('users.show',compact('user'));
    }

    //编辑
    public function edit(User $user){
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    //更新
    public function update(User $user,Request $request){
    //验证提交的信息是否满足基本规则
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|min:6'
        ]);
        $this->authorize('update',$user);
    //对表内的数据进行更新
        $data=[];
        $data['name']=$request->name;
        if ($request->password) {
            $data['password']=bcrypt($request->password);
        }
        $user->update($data);

    //返回信息
        session()->flash('success','更新成功');
        return redirect()->route('users.show',$user->id);
    }


    //用户列表
    public function index(){
        //$users=User::all();
        $users=User::paginate(10);
        return view('users.index',compact('users'));
    }

    //删除用户
    public function destroy(User $user){
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','删除成功');
        return back();
    }
}
