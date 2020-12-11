<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ignug\State;
use App\Models\TeacherEval\Question;
use App\Models\TeacherEval\EvaluationType;
use App\Models\Ignug\Catalogue;

class QuestionByEvaluationTypeController extends Controller
{
    public function selfEvaluation()
    {
        $evaluationTypeDocencia = EvaluationType::where('code', '3')->first();
        $evaluationTypeGestion = EvaluationType::where('code', '4')->first();

        $status = Catalogue::where('type', 'STATUS')->Where('code', '1')->first();

        $questions = Question::with(['evaluationType','answers' => function ($query) use ($status) {
            $query->where('status_id', $status->id)
            ->orderBy('order');
        }])
        ->where('status_id', $status->id)
        ->where(function ($query) use ($evaluationTypeDocencia,$evaluationTypeGestion) {
            $query->where('evaluation_type_id', $evaluationTypeDocencia->id)
                  ->orWhere('evaluation_type_id', $evaluationTypeGestion->id);
        })
        ->orderBy('order')
        ->get();

        if (sizeof($questions)=== 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'No se han creado preguntas y respuestas para el formulario',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $questions,
            'msg' => [
                'summary' => 'Formulario',
                'detail' => 'Se creó correctamente el formulario',
                'code' => '200',
            ]], 200);
    }

    public function studentEvaluation()
    {
        $evaluationTypeDocencia = EvaluationType::where('code', '5')->first();
        $evaluationTypeGestion = EvaluationType::where('code', '6')->first();

        $question = Question::with('answers')
        ->where('evaluation_type_id', $evaluationTypeDocencia->id)
        ->orWhere('evaluation_type_id', $evaluationTypeGestion->id)
        ->get();

        if (sizeof($question)=== 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Preguntas no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $question,
            'msg' => [
                'summary' => 'Preguntas',
                'detail' => 'Se consultó correctamente Preguntas',
                'code' => '200',
            ]], 200);
    }
    
    public function pairEvaluation()
    {
        $catalogues = json_decode(file_get_contents(storage_path(). '/catalogues.json'), true);

        $evaluationTypeDocencia = EvaluationType::where('code', '7')->first();
        $evaluationTypeGestion = EvaluationType::where('code', '8')->first();
        $state = State::where('code', $catalogues['state']['type']['active'])->first();

        $question = Question::with(['answers' => function ($query) use ($state) {
            $query->where('state_id', $state->id);
        }])
            ->where('evaluation_type_id', $evaluationTypeDocencia->id)
            ->orWhere('evaluation_type_id', $evaluationTypeGestion->id)
            ->where('state_id', $state->id)
            ->get();

        if (sizeof($question) === 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Preguntas no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404',
                ]], 404);
        }
        return response()->json(['data' => $question,
            'msg' => [
                'summary' => 'Preguntas',
                'detail' => 'Se consultó correctamente Preguntas',
                'code' => '200',
            ]], 200);
    }
}