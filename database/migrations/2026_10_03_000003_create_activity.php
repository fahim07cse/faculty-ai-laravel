<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up(){Schema::create('admin_activity_log_simple',function(Blueprint $t){$t->id();$t->string('admin_username');$t->string('action');$t->string('record_id')->nullable();$t->text('details')->nullable();$t->timestamps();});}public function down(){Schema::dropIfExists('admin_activity_log_simple');}};
