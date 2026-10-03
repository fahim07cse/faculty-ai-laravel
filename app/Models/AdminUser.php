<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
class AdminUser extends Authenticatable {
 protected $table='admin_login_users'; protected $guarded=[]; protected $hidden=['password','remember_token']; protected $casts=['is_active'=>'boolean'];
}
