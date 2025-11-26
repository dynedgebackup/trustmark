<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class FeesDescription extends Model
{
    
    public function updateActiveInactive($id,$columns){
        return DB::table('fees')->where('id',$id)->update($columns);
    }  
    public function updateData($id,$columns){
        return DB::table('fees')->where('id',$id)->update($columns);
    }
    public function addData($postdata){
        DB::table('fees')->insert($postdata);
        return DB::getPdo()->lastInsertId();
    }
    
    public function getEditDetails($id){
        return DB::table('fees')->where('id',$id)->first();
    }
   
}
