<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up(){Schema::create('admin_login_users',function(Blueprint $t){$t->id();$t->string('username')->unique();$t->string('password');$t->string('role')->default('admin');$t->boolean('is_active')->default(true);$t->rememberToken();$t->timestamps();});}public function down(){Schema::dropIfExists('admin_login_users');}};
