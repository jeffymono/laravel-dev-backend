<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherEval\QuestionController;
use App\Http\Controllers\TeacherEval\EvaluationTypeController;
use App\Http\Controllers\TeacherEval\PairEvaluationController;
use App\Http\Controllers\TeacherEval\SelfEvaluationController;
use App\Http\Controllers\TeacherEval\EvaluationController;
use App\Http\Controllers\TeacherEval\AnswerController;
use App\Http\Controllers\TeacherEval\DetailEvaluationController;
use App\Http\Controllers\TeacherEval\StudentEvaluationController;
use App\Http\Controllers\TeacherEval\CatalogueController;
use App\Http\Controllers\TeacherEval\QuestionByEvaluationTypeController;




Route::apiResource('evaluation_types',EvaluationTypeController::class);
Route::apiResource('questions', QuestionController::class);
Route::apiResource('answers', AnswerController::class);

Route::get('evaluations/registered_self_evaluations', [EvaluationController::class, 'registeredSelfEvaluation']);
Route::get('evaluations/teacher_evaluations', [EvaluationController::class, 'teacherEvaluation']);



Route::apiResource('evaluations', EvaluationController::class);
Route::apiResource('detail_evaluations', DetailEvaluationController::class);
Route::apiResource('student_evaluations', StudentEvaluationController::class);
Route::apiResource('self_evaluations', SelfEvaluationController::class);
Route::apiResource('pair_evaluations', PairEvaluationController::class)->except(['store']);
Route::post('pair_evaluations/teachers',[PairEvaluationController::class,'storeTeacherEvalutor']);
Route::post('pair_evaluations/authorities',[PairEvaluationController::class,'storeAuthorityEvalutor']);

Route::get('catalogues', [CatalogueController::class, 'index']);
Route::get('types_questions/self_evaluations', [QuestionByEvaluationTypeController::class, 'selfEvaluation']);
Route::get('types_questions/student_evaluations', [QuestionByEvaluationTypeController::class, 'studentEvaluation']);
Route::get('types_questions/pair_evaluations', [QuestionByEvaluationTypeController::class, 'pairEvaluation']);
Route::get('types_questions/authorities', [QuestionByEvaluationTypeController::class, 'authorityEvaluation']);

Route::post('evaluations/student_evaluations',[StudentEvaluationController::class, 'calculateResults']);
Route::post('evaluations/pair_evaluations',[EvaluationController::class,'updateEvaluationPair']);
Route::post('evaluations/authorities',[EvaluationController::class,'updateEvaluationAuthorityEvaluator']);



