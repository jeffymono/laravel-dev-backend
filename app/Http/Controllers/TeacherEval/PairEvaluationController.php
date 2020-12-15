<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use App\Models\Ignug\State;
use App\Models\TeacherEval\AnswerQuestion;
use App\Models\TeacherEval\DetailEvaluation;
use App\Models\TeacherEval\PairResult;
use Illuminate\Http\Request;

class PairEvaluationController extends Controller
{
    public function index()
    {
        $pairResults = PairResult::with('status')->get();

        if (sizeof($pairResults) === 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluaciones no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404',
                ]], 404);
        }
        return response()->json(['data' => $pairResults,
            'msg' => [
                'summary' => 'Evaluaiones',
                'detail' => 'Se consultó correctamente evaluaciones',
                'code' => '200',
            ]], 200);
    }

    public function show($id)
    {
        $pairResult = PairResult::findOrFail($id);
        if (!$pairResult) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluación par no encontrada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404',
                ]], 404);
        }
        return response()->json(['data' => $pairResult,
            'msg' => [
                'summary' => 'Evaluación par',
                'detail' => 'Se consulto correctamente evaluación',
                'code' => '200',
            ]], 200);
    }

    public function storeTeacherEvalutor(Request $request)
    {
        $catalogues = json_decode(file_get_contents(storage_path() . '/catalogues.json'), true);

        $data = $request->json()->all();

        $dataDetailEvaluation = $data['detail_evaluation'];

        $dataAnswerQuestions = $data['answer_questions'];
        $detailEvaluation = DetailEvaluation::findOrFail($dataDetailEvaluation['id']);

        $result = 0;

        foreach ($dataAnswerQuestions as $answer) {
            $answerQuestion = AnswerQuestion::with('answer')->findOrFail($answer['id']);

            $pairResult = new PairResult;
            $pairResult->answerQuestion()->associate($answerQuestion);
            $pairResult->detailEvaluation()->associate($detailEvaluation);
            $pairResult->state()->associate(State::firstWhere('code', $catalogues['state']['type']['active'])->first());
            $pairResult->save();

            $result += (int) $answerQuestion->answer->value;

        }
        if (sizeOf($dataAnswerQuestions) > 0) {

            $detailEvaluation->result = $result / sizeOf($dataAnswerQuestions);
            $detailEvaluation->save();
        }

        if (!$pairResult) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluación no creada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404',
                ]], 404);
        }
        return response()->json(['data' => $pairResult,
            'msg' => [
                'summary' => 'Evaluación creada',
                'detail' => 'Se creó correctamente las evaluación',
                'code' => '201',
            ]], 201);

    }

    public function storeAuthorityEvaluator(Request $request)
    {
        $catalogues = json_decode(file_get_contents(storage_path() . '/catalogues.json'), true);

        $data = $request->json()->all();

        $dataDetailEvaluation = $data['detail_evaluation'];

        $dataAnswerQuestions = $data['answer_questions'];
        $detailEvaluation = DetailEvaluation::findOrFail($dataDetailEvaluation['id']);

        $result = 0;

        foreach ($dataAnswerQuestions as $answer) {
            $answerQuestion = AnswerQuestion::with('answer')->findOrFail($answer['id']);

            $pairResult = new PairResult;
            $pairResult->answerQuestion()->associate($answerQuestion);
            $pairResult->detailEvaluation()->associate($detailEvaluation);
            $pairResult->state()->associate(State::firstWhere('code', $catalogues['state']['type']['active'])->first());
            $pairResult->save();

            $result += (int) $answerQuestion->answer->value;

        }
        if (sizeOf($dataAnswerQuestions) > 0) {

            $detailEvaluation->result = $result / sizeOf($dataAnswerQuestions);
            $detailEvaluation->save();
        }

        if (!$pairResult) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluación no creada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404',
                ]], 404);
        }
        return response()->json(['data' => $pairResult,
            'msg' => [
                'summary' => 'Evaluación creada',
                'detail' => 'Se creó correctamente las evaluación',
                'code' => '201',
            ]], 201);

    }
}
