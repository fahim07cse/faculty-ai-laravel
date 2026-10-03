<?php
namespace App\Http\Controllers;
use App\Models\Faculty; use Illuminate\Http\Request;
class FacultyController extends Controller {
 public function index(){return view('faculty.index');}
 public function data(Request $r){$q=Faculty::where('is_deleted',false); if($r->filled('q')){$s=mb_strtolower($r->q);$q->where(function($w)use($s){foreach(['name','department','keywords','ai_research','enterprise_projects','future_directions','external_organisations'] as $c)$w->orWhereRaw("LOWER(COALESCE($c,'')) LIKE ?",["%$s%"]);});} return $q->orderBy('name')->get();}
 public function store(Request $r){$d=$this->valid($r); if(Faculty::whereRaw('LOWER(email)=?',[$d['email']])->exists())return response()->json(['message'=>'This email address has already been submitted.'],422); $d['start_time']=$d['completion_time']=$d['last_modified_time']=now();$d['is_deleted']=false;return response()->json(Faculty::create($d),201);}
 public function update(Request $r,Faculty $faculty){$d=$this->valid($r);$d['last_modified_time']=now();$faculty->update($d);return $faculty->fresh();}
 private function valid(Request $r){$d=$r->validate(['email'=>'required|email|max:255','name'=>'nullable|string|max:255','department'=>'nullable|string|max:255','research_groups'=>'nullable|string','keywords'=>'nullable|string','showcase_interest'=>'nullable|string','ai_research'=>'nullable|string','enterprise_projects'=>'nullable|string','future_directions'=>'nullable|string','external_organisations'=>'nullable|string','support_training'=>'nullable|string','additional_comments'=>'nullable|string','consent'=>'nullable|string']);$k=collect(preg_split('/[,;]+/',$d['keywords']??''))->map('trim')->filter();if($k->count()>5)abort(response()->json(['message'=>'Maximum five keywords.'],422));$d['keywords']=$k->implode(', ');return $d;}
}
