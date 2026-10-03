<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminSession;
Route::get('/', [FacultyController::class,'index']);
Route::get('/api/faculty',[FacultyController::class,'data']);
Route::post('/api/faculty',[FacultyController::class,'store']);
Route::put('/api/faculty/{faculty}',[FacultyController::class,'update']);
Route::get('/admin',[AdminController::class,'login']);
Route::prefix('api/admin')->group(function(){
 Route::post('/login',[AdminController::class,'authenticate'])->middleware('throttle:5,1'); Route::post('/logout',[AdminController::class,'logout']);
 Route::middleware(AdminSession::class)->group(function(){
  Route::get('/dashboard',[AdminController::class,'dashboard']);
  Route::put('/faculty/{faculty}',[AdminController::class,'updateFaculty']);
  Route::post('/faculty/{faculty}/delete',[AdminController::class,'softDelete']);
  Route::post('/faculty/{faculty}/restore',[AdminController::class,'restore']);
  Route::get('/admins',[AdminController::class,'admins']); Route::post('/admins',[AdminController::class,'createAdmin']);
  Route::patch('/admins/{adminUser}/active',[AdminController::class,'setAdminActive']);
  Route::get('/activity',[AdminController::class,'activity']); Route::get('/export',[AdminController::class,'export']);
 });
});
