<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class Onlineplatforms extends Model
{
    //
     public function updateData($id,$columns){
        return DB::table('platform_url')->where('id',$id)->update($columns);
    }
    public function addData($postdata){
        DB::table('platform_url')->insert($postdata);
        return DB::getPdo()->lastInsertId();
    }
    public function getEditDetails($id){
        return DB::table('platform_url')->where('id',$id)->first();
    }
    public function updateActiveInactive($id,$columns){
        return DB::table('platform_url')->where('id',$id)->update($columns);
    }  
}
