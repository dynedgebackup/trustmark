<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class MenuGroup extends Model
{
    
    public function updateActiveInactive($id,$columns){
        return DB::table('menu_groups')->where('id',$id)->update($columns);
    }  
    public function updateData($id,$columns){
        return DB::table('menu_groups')->where('id',$id)->update($columns);
    }
    public function addData($postdata){
        DB::table('menu_groups')->insert($postdata);
        return DB::getPdo()->lastInsertId();
    }
    
    public function getEditDetails($id){
        return DB::table('menu_groups')->where('id',$id)->first();
    }
   
}
