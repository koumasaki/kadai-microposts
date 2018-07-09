<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    //UserがフォローしているUser達
    //`user_followテーブル`の　｀user_id が自分のID｀　で　｀follow_idに該当するUserを複数｀　を返します
    //第一引数Modelクラス、第二引数：中間テーブル
    //第三引数：中間テーブルの自分のIDを示すカラム名
    //第四引数：中間テーブルの関係先（この場合フォローしている）のIDを示すカラム名
    //withTimestamps() created_at、updated_atを保存
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    //UserをフォローしているUser達
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        //フォローしているか確認
        $exist = $this->is_following($userId);
        //自分自身でないか確認
        $its_me = $this->id == $userId;
        
        if($exist || $its_me) {
            // 既にフォローしている、または自分自身->何もしない
            return false;
        } else {
            //上記でなければフォローする（中間テーブルのレコードを保存）
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        //フォローしているか確認
        $exist = $this->is_following($userId);
        //自分自身でないか確認
        $its_me = $this->id == $userId;
        
        if($exist && !$its_me) {
            // 既にフォローしていればフォローを外す（中間テーブルのレコードを削除）
            $this->followings()->detach($userId);
            return true;
        } else {
            return false;
        }
    }
    
    //is_following($userId)メソッドの役割は　Userが引数で渡されたuserIdのUserをフォローしているかを判定する事
    //followings()  でフォローしているUserを全て取得
    //where(‘follow_id’, $userId)でそのなかで、follow_id = userId で絞り込み
    //exists() で存在すれば true なければ false を返す
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();  
    }
    
     public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
}
