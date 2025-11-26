<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class ApplicationStatusCannedMessage extends Model
{
    public function getAppStatusDetails($id){
        return DB::table('application_status')->select('id','name')->where('id',$id)
        ->get()->toArray(); 
    }
    
    public function updateActiveInactive($id,$columns){
        return DB::table('application_canned_messages')->where('id',$id)->update($columns);
    }  
    public function updateData($id,$columns){
        return DB::table('application_canned_messages')->where('id',$id)->update($columns);
    }
    public function addData($postdata){
        DB::table('application_canned_messages')->insert($postdata);
        return DB::getPdo()->lastInsertId();
    }
    
    public function getEditDetails($id){
        return DB::table('application_canned_messages')->where('id',$id)->first();
    }
    public function applicationStatusAjaxList($search=""){
        $page=1;
        if(isset($_REQUEST['page'])){
          $page = (int)$_REQUEST['page'];
        }
        $length = 20;
        $offset = ($page - 1) * $length;
  
        $sql = DB::table('application_status AS a')
              ->select('a.id','a.name');
          $sql->where(function ($sql) use($search) {
            if(is_numeric($search)){
              $sql->Where('a.id',$search);
            }else{
              $sql->where(DB::raw('LOWER(a.name)'),'like',"%".strtolower($search)."%");
            }
          });
        
        $sql->orderBy('a.name','ASC');
        $data_cnt=$sql->count();
        $sql->offset((int)$offset)->limit((int)$length);
  
        $data=$sql->get();
        return array("data_cnt"=>$data_cnt,"data"=>$data);
    }
    
}
