<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //获取所有用户
        $users=User::all();
        $followers=$users->slice(1);//除掉第一个用户
        $follower_ids=$followers->pluck('id')->toArray();
        //获取第一个用户
        $user=User::first();
        $user_id=$user->id;
        //其他用户都关注第一个用户
        foreach ($followers as $follower) {
            $follower->follow($user_id);
        }
        //第一个用户关注其他用户
        $user->follow($follower_ids);
    }
}
