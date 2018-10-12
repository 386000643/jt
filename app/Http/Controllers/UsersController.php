<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{



    public function __construct(){
        //只有登录用户才能访问edit和update
        $this->middleware('auth',[
            'except'=>['create','index','store','show','confirmEmail']
        ]);

        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
    //注册
    public function create(){
        return view('users.create');
    }

     public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
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

    //发送激活邮件
     protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@yousails.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    //确认邮件
    public function confirmEmail($token){
        $user=User::where('activation_token',$token)->firstOrFail();
        $user->activated=true;
        $user->activation_token=null;
        $user->save();

        Auth::login($user);
        session()->flash('success','激活成功');
        return redirect()->route('users.show',[$user]);
    }

}
