<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MicropostsController extends Controller
{
    public function index()
    {
        $data = [];  //必要なの？   A:今はおっしゃる通りいらない。
        if (\Auth::check()) {  //ログイン中かチェック
            $user = \Auth::user();  //ログイン中のユーザー情報
            $microposts = $user->feed_microposts()->orderBy('created_at', 'desc')->paginate(10);  //ログイン中のユーザーの投稿、登録順、1頁10件
            
            $data = [
                'user' => $user,
                'microposts' => $microposts, 
            ];
            $data += $this->counts($user);  //意味不明（+=）
            // 変形１：　$data += ['count_microposts' => xxxxx];
            // 変形２：　$data = $data + ['count_microposts' => xxxxx];
            //     ->最終的に $data は 
            /*
                    $data = [
                        'user' => $user,
                        'microposts' => $microposts, 
                        'count_microposts' => xxxxx,
                    ];
            */
            
            // int型：
            // $a += 2;  ---- つまり -->   $a = $a + 2;
            
            // string型：
            // $a += 'test';  --- つまり --> $a = $a.'test';
            
            // 配列：
            // $a = ['1', '2'];
            // $a += ['a', 'b', 'c'];  --- つまり --> $a = array_merge($a, ['a', 'b', 'c']);  -- つまり --> ['1', '2', 'a', 'b', 'c'];
            return view('users.show', $data);
        }else {
            return view('welcome');
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:191',
        ]);
        
        //$request->user()＝ログイン者のUserのレコード（決まり文句）を取り出す
        //↓
        //microposts()＝micropostテーブルの中のuser_idが自身（User.php）のidと等しいものを取ってくる
        //↓
        //create＝続けて指示する項目と合わせて保存
        $request->user()->microposts()->create([
            'content' => $request->content,
        ]);
        
        //別の書き方
        //$micropost = new \App\Micropost();
        //$micropost->content =$request->content;
        //$micropost->user_id =Auth::user()->id;
        //$micropost->save();
        
        return redirect()->back();
    }
    
    public function destroy($id)
    {
        $micropost = \App\Micropost::find($id);

        if (\Auth::id() === $micropost->user_id) {
            $micropost->delete();
        }

        return redirect()->back();
    }
    
    public function show($id)
    {
        $user = User::find($id);
        $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);

        $data = [
            'user' => $user,
            'microposts' => $microposts,
        ];

        $data += $this->counts($user);

        return view('users.show', $data);
    }
}
