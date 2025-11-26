<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class AdminMunicipality extends Model
{
  public function getregionsDetails($id){
      return DB::table('provinces AS a')
      ->join('regions AS b', 'b.id', '=', 'a.reg_no')
      ->select('a.id','a.prov_no','a.prov_desc','b.reg_region','b.reg_description')->where('a.id',$id)
      ->get()->toArray(); 
  }
    public function updateActiveInactive($id,$columns){
        return DB::table('municipalities')->where('id',$id)->update($columns);
    }  
    public function updateData($id,$columns){
        return DB::table('municipalities')->where('id',$id)->update($columns);
    }
    public function getRegionId($id){
      return DB::table('provinces')->select('id','reg_no')->where('id',$id)
      ->first(); 
  }
  
    public function addData($postdata){
        DB::table('municipalities')->insert($postdata);
        return DB::getPdo()->lastInsertId();
    }
    
    public function getEditDetails($id){
        return DB::table('municipalities')->where('id',$id)->first();
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
    // public function provincesAjaxList($search=""){
    //     $page=1;
    //     if(isset($_REQUEST['page'])){
    //       $page = (int)$_REQUEST['page'];
    //     }
    //     $length = 20;
    //     $offset = ($page - 1) * $length;
  
    //     $sql = DB::table('provinces AS a')
    //           ->select('a.id','a.prov_desc')->where('a.is_active','1');
    //       $sql->where(function ($sql) use($search) {
    //         if(is_numeric($search)){
    //           $sql->Where('a.id',$search);
    //         }else{
    //           $sql->where(DB::raw('LOWER(a.prov_desc)'),'like',"%".strtolower($search)."%");
    //         }
    //       });
        
    //     $sql->orderBy('a.prov_desc','ASC');
    //     $data_cnt=$sql->count();
    //     $sql->offset((int)$offset)->limit((int)$length);
  
    //     $data=$sql->get();
    //     return array("data_cnt"=>$data_cnt,"data"=>$data);
    // }
    public function provincesAjaxList($request){
      $term=$request->input('term');
      $id = $request->id;
      $query =DB::table('provinces')
          ->select('*', DB::raw('CONCAT(prov_desc) as text'))->where('is_active','1')->where('reg_no','=',$id);
          if(!empty($term) && isset($term)){
          $query->where(function ($sql) use($term) {   
              $sql->where(DB::raw('LOWER(prov_desc)'),'like',"%".strtolower($term)."%");
          });

      }  
      $data = $query->simplePaginate(20);             
      return $data;
  }
    
}
