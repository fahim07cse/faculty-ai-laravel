<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Faculty extends Model {
 protected $table='ai_faculty_data';
 protected $guarded=[];
 protected $casts=['is_deleted'=>'boolean','start_time'=>'datetime','completion_time'=>'datetime','last_modified_time'=>'datetime','deleted_at'=>'datetime'];
}
