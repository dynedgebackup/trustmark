<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class Provinces extends Model
{
  public function getregionsDetails($id){
      return DB::table('regions')->select('id','reg_region','reg_description')->where('id',$id)
      ->get()->toArray(); 
  }
    public function updateActiveInactive($id,$columns){
        return DB::table('provinces')->where('id',$id)->update($columns);
    }  
    public function updateData($id,$columns){
        return DB::table('provinces')->where('id',$id)->update($columns);
    }
    public function addData($postdata){
        DB::table('provinces')->insert($postdata);
        return DB::getPdo()->lastInsertId();
    }
    
    public function getEditDetails($id){
        return DB::table('provinces')->where('id',$id)->first();
    }
    public function regionAjaxList($search=""){
        $page=1;
        if(isset($_REQUEST['page'])){
          $page = (int)$_REQUEST['page'];
        }
        $length = 20;
        $offset = ($page - 1) * $length;
  
        $sql = DB::table('regions AS a')
              ->select('a.id','a.reg_region','a.reg_description')->where('a.is_active','1');
          $sql->where(function ($sql) use($search) {
            if(is_numeric($search)){
              $sql->Where('a.id',$search);
            }else{
              $sql->where(DB::raw('LOWER(a.reg_region)'),'like',"%".strtolower($search)."%")
              ->orWhere(DB::raw("CONCAT(a.reg_region, '-', a.reg_description)"), 'like', "%" . $search . "%");
            }
          });
        
        $sql->orderBy('a.reg_region','ASC');
        $data_cnt=$sql->count();
        $sql->offset((int)$offset)->limit((int)$length);
  
        $data=$sql->get();
        return array("data_cnt"=>$data_cnt,"data"=>$data);
    }
    
}
