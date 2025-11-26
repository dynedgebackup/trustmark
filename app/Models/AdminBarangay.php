<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class AdminBarangay extends Model
{
  public function getregionsDetails($id){
      return DB::table('municipalities AS pm')
      ->join('regions AS pr', 'pr.id', '=', 'pm.reg_no')
      ->join('provinces AS pp', 'pp.id', '=', 'pm.prov_no')
      ->select('pm.id','pm.mun_desc','pm.mun_no','pp.prov_desc','pp.prov_no','pr.reg_region','pr.reg_no','pm.is_active')->where('pm.id',$id)
      ->get()->toArray(); 
  }
    public function updateActiveInactive($id,$columns){
        return DB::table('barangays')->where('id',$id)->update($columns);
    }  
    public function updateData($id,$columns){
        return DB::table('barangays')->where('id',$id)->update($columns);
    }
    public function getProvincesName($id){
        return DB::table('provinces')->select('id','prov_desc')->where('id',$id)
        ->first(); 
    }
    public function getRegionName($id){
      return DB::table('regions')->select('id','reg_region')->where('id',$id)
      ->first(); 
  }
    public function getRegionId($id){
        return DB::table('municipalities')->select('id','reg_no','prov_no','mun_desc')->where('id',$id)
        ->first(); 
    }
  
    public function addData($postdata){
        DB::table('barangays')->insert($postdata);
        return DB::getPdo()->lastInsertId();
    }
    
    public function getEditDetails($id){
        return DB::table('barangays')->where('id',$id)->first();
    }
    public function provinceRegionsAjaxList($search=""){
      $page=1;
      if(isset($_REQUEST['page'])){
        $page = (int)$_REQUEST['page'];
      }
      $length = 20;
      $offset = ($page - 1) * $length;

      $sql = DB::table('provinces AS a')
            ->join('regions AS b', 'b.id', '=', 'a.reg_no')
            ->select('a.id','a.prov_no','a.prov_desc','b.reg_region','b.reg_description')->where('a.is_active','1');
        $sql->where(function ($sql) use($search) {
          if(is_numeric($search)){
            $sql->Where('a.id',$search);
          }else{
            $sql->where(DB::raw('LOWER(b.reg_region)'),'like',"%".strtolower($search)."%")
                 ->orWhere(DB::raw("CONCAT('[', b.reg_region, ' - ', b.reg_description, ']=>[',a.prov_desc, ']')"), 'like', "%" . $search . "%")
                  ->orWhere(DB::raw("CONCAT('[', b.reg_region, '-', b.reg_description, ']=>[',a.prov_desc, ']')"), 'like', "%" . $search . "%");
          }
        });
      
      $sql->orderBy('a.prov_desc','ASC');
      $data_cnt=$sql->count();
      $sql->offset((int)$offset)->limit((int)$length);

      $data=$sql->get();
      return array("data_cnt"=>$data_cnt,"data"=>$data);
    }
    public function getBarngayMunProvRegionAjaxList($search=""){
      $page=1;
      if(isset($_REQUEST['page'])){
        $page = (int)$_REQUEST['page'];
      }
      $length = 20;
      $offset = ($page - 1) * $length;

      $sql = DB::table('municipalities AS pm')
        ->join('regions AS pr', 'pr.id', '=', 'pm.reg_no')
        ->join('provinces AS pp', 'pp.id', '=', 'pm.prov_no')
        ->select('pm.id','pm.mun_desc','pm.mun_no','pp.prov_desc','pp.prov_no','pr.reg_region','pr.reg_no','pm.is_active')->where('pm.is_active',1);
      if(!empty($search)){
        $sql->where(function ($sql) use($search) {
          if(is_numeric($search)){
            $sql->Where('pm.id',$search);
          }else{
            $sql->orWhere(DB::raw("CONCAT(LOWER(pm.mun_desc), ', ', LOWER(pp.prov_desc), ', ', LOWER(pr.reg_region))"), 'like', "%" . strtolower($search) . "%");
          }
        });
      }
      $sql->orderBy('mun_desc','ASC');
      $data_cnt=$sql->count();
      $sql->offset((int)$offset)->limit((int)$length);

      $data=$sql->get();
      return array("data_cnt"=>$data_cnt,"data"=>$data);
    }
    
}
