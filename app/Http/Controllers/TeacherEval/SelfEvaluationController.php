<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ignug\State;
use App\Models\Ignug\Teacher;
use App\Models\Ignug\Catalogue;
use App\Models\Ignug\SchoolPeriod;
use App\Models\TeacherEval\AnswerQuestion;
use App\Models\TeacherEval\Answer;
use App\Models\TeacherEval\Question;
use App\Models\TeacherEval\SelfResult;
use App\Models\TeacherEval\EvaluationType;
use App\Models\TeacherEval\Evaluation;

class SelfEvaluationController extends Controller
{
    public function store(Request $request)
    {
        $catalogues = json_decode(file_get_contents(storage_path() . "/catalogues.json"), true);
        $data = $request->json()->all();
        
        $dataAnswerQuestions = $data['answer_questions'];
        $teacher = Teacher::firstWhere('user_id', $request->user_id);// user_id viene de un interceptor
        $state = State::firstWhere('code', $catalogues['state']['type']['active']);
        $schoolPeriod = SchoolPeriod::firstWhere('status_id', Catalogue::where('type', 'STATUS')->Where('code', '1')->first()->id);//El id del status es Temporal
        /*                 $from = date('2020-12-01');
                        $to = date('2021-06-01'); */
        //Obetenemos las fechas de inicio y fin del periodo para valiadar la obtencion de respuestas de selfEvaluation.
        $startDatePeriod = date($schoolPeriod->start_date);
        $endDatePeriod = date($schoolPeriod->end_date);

        $evaluationTypeTeaching = EvaluationType::firstWhere('code', '3');
        $evaluationTypeManagement = EvaluationType::firstWhere('code', '4');

        $teacherHasEvaluation = Evaluation::where(function ($query) use ($evaluationTypeTeaching,$evaluationTypeManagement) {
            $query->where('evaluation_type_id', $evaluationTypeTeaching->id)
            ->orWhere('evaluation_type_id', $evaluationTypeManagement->id);
        })
        ->where('teacher_id', $teacher->id)
        ->where('school_period_id', $schoolPeriod->id)
        ->first();

        $selfResult = null;

        if (!$teacherHasEvaluation) {
            foreach ($dataAnswerQuestions as $answerQuestion) {
                $selfResult = new SelfResult();
                $selfResult->state()->associate($state);
                $selfResult->teacher()->associate($teacher);
                $selfResult->answerQuestion()->associate(AnswerQuestion::findOrFail($answerQuestion['id']));
                $selfResult->save();
            }
            
            //Obetenemos todas las autoEvaluaaciones de docencia segun el teacher , tipo de evalaucion y periodo.
            $selfTeachingResults= SelfResult::where('teacher_id', $teacher->id)
            ->whereBetween('created_at', [$startDatePeriod, $endDatePeriod])
            ->with(['answerQuestion'=>function ($answerQuestion) {
                $answerQuestion->with('answer');
            }])->whereHas('answerQuestion', function ($answerQuestion) use ($evaluationTypeTeaching) {
                $answerQuestion->whereHas('question', function ($question) use ($evaluationTypeTeaching) {
                    $question->where('evaluation_type_id', $evaluationTypeTeaching->id);
                });
            })
            ->get();

            $resultsTotalValuesTeaching = 0;
            $resultsTotalTeaching = 0;

            foreach ($selfTeachingResults as $selfTeachingResult) {
                $result = json_decode(json_encode($selfTeachingResult));
            
                $resultsTotalValuesTeaching += (int)$result->answer_question->answer->value;
            }
            
            if (sizeof($selfTeachingResults)>0) {
                $resultsTotalTeaching  = $resultsTotalValuesTeaching/sizeof($selfTeachingResults);
                $this->createEvaluation($teacher, $evaluationTypeTeaching, $resultsTotalTeaching, $schoolPeriod);
            }

            //Obetenemos todas las autoEvaluaaciones de gestion segun el teacher , tipo de evalaucion y periodo..
            $selfManagementResults= SelfResult::where('teacher_id', $teacher->id)
            ->whereBetween('created_at', [$startDatePeriod, $endDatePeriod])
                    ->with(['answerQuestion'=>function ($answerQuestion) {
                        $answerQuestion->with('answer');
                    }])->whereHas('answerQuestion', function ($answerQuestion) use ($evaluationTypeManagement) {
                        $answerQuestion->whereHas('question', function ($question) use ($evaluationTypeManagement) {
                            $question->where('evaluation_type_id', $evaluationTypeManagement->id);
                        });
                    })
                    ->get();

            $resultsTotalValuesManagement = 0;
            $resultsTotalManagement = 0;

            foreach ($selfManagementResults as $selfManagementResult) {
                $result = json_decode(json_encode($selfManagementResult));
                    
                $resultsTotalValuesManagement += (int)$result->answer_question->answer->value;
            }
            
            if (sizeof($selfManagementResults)>0) {
                $resultsTotalManagement  = $resultsTotalValuesManagement/sizeof($selfManagementResults);
                $this->createEvaluation($teacher, $evaluationTypeManagement, $resultsTotalManagement, $schoolPeriod);
            }
        }
        if (!$selfResult) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'AutoEvaluación no creada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $selfResult,
            'msg' => [
                'summary' => 'AutoEvaluaciones',
                'detail' => 'Se creó correctamente las autoEvaluaciones',
                'code' => '201',
            ]], 201);
    }

    //Metodo para guardar en la tabla evaluations.
    public function createEvaluation($teacher, $evaluationType, $result, $schoolPeriod)
    {
        $evaluation = new Evaluation();
    
        $catalogues = json_decode(file_get_contents(storage_path() . "/catalogues.json"), true);
        $evaluation->result = $result;
        $evaluation->percentage = $evaluationType->percentage;

        $state = State::firstWhere('code', $catalogues['state']['type']['active']);
        $status = Catalogue::where('type', 'STATUS')->Where('code', '1')->first();

        $evaluation->state()->associate($state);
        $evaluation->status()->associate($status);
        $evaluation->teacher()->associate($teacher);
        $evaluation->evaluationType()->associate($evaluationType);
        $evaluation->schoolPeriod()->associate($schoolPeriod);

        $evaluation->save();
    }
}
