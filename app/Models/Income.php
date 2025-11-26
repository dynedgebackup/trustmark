<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class Income extends Model
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
    public function getFeesAjaxList($search=""){
        $page=1;
        if(isset($_REQUEST['page'])){
          $page = (int)$_REQUEST['page'];
        }
        $length = 20;
        $offset = ($page - 1) * $length;
  
        $sql = DB::table('fees')
              ->select('id','name');
          $sql->where(function ($sql) use($search) {
            if(is_numeric($search)){
              $sql->Where('id',$search);
            }else{
              $sql->where(DB::raw('LOWER(name)'),'like',"%".strtolower($search)."%");
            }
          });
        
        $sql->orderBy('name','ASC');
        $data_cnt=$sql->count();
        $sql->offset((int)$offset)->limit((int)$length);
  
        $data=$sql->get();
        return array("data_cnt"=>$data_cnt,"data"=>$data);
    }
}
