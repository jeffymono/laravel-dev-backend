<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use App\Models\Ignug\State;
use App\Models\Ignug\Teacher;
use App\Models\TeacherEval\DetailEvaluation;
use App\Models\TeacherEval\Evaluation;
use App\Models\TeacherEval\EvaluationType;
use Illuminate\Http\Request;

class DetailEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $detailEvaluations = DetailEvaluation::with('evaluation')->where('detail_evaluationable_id', $request->user_id)
            ->where('result', null)->get();

        if (sizeof($detailEvaluations) !== 0) {
            return response()->json(['data' => $detailEvaluations,
                'msg' => [
                    'summary' => 'Detalle evaluaciones',
                    'detail' => 'Se consulto correctamente detalle evaluaciones',
                    'code' => '200',
                ]], 200);
        } else if (sizeof($detailEvaluations) === 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Detalle evaluación no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404',
                ]], 404);
        }
    }

    public function show($id)
    {
        $detailEvaluation = DetailEvaluation::findOrFail($id);
        if (!$detailEvaluation) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Detalle evaluación no encontrada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404',
                ]], 404);
        }
        return response()->json(['data' => $detailEvaluation,
            'msg' => [
                'summary' => 'Detalle evaluación',
                'detail' => 'Se consulto correctamente detalle evaluación',
                'code' => '200',
            ]], 200);
    }

    public function store(Request $request)
    {
        $catalogues = json_decode(file_get_contents(storage_path() . '/catalogues.json'), true);
        $data = $request->json()->all();
        $dataEvaluation = $data['evaluation'];
        $dataEvaluators = $data['evaluators'];

        foreach ($dataEvaluators as $evaluator) {
            $detailEvaluation = new DetailEvaluation;
            $detailEvaluation->state()->associate(State::firstWhere('code', $catalogues['state']['type']['active'])->first());
            $detailEvaluation->detailEvaluationable()->associate(Teacher::findOrFail($evaluator['id']));
            $detailEvaluation->evaluation()->associate(Evaluation::findOrFail($dataEvaluation['id']));
            $detailEvaluation->save();
        }

        if (!$detailEvaluation) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Detalle evaluación no creada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404',
                ]], 404);
        }
        return response()->json(['data' => $detailEvaluation,
            'msg' => [
                'summary' => 'Detalle evaluación',
                'detail' => 'Se creo correctamente detalle evaluación',
                'code' => '201',
            ]], 201);
    }
}
