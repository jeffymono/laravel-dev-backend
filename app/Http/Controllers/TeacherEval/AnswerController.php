<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherEval\Answer;
use App\Models\TeacherEval\AnswerQuestion;
use App\Models\TeacherEval\Question;
use App\Models\Ignug\Catalogue;
use App\Models\Ignug\State;
use Illuminate\Database\Eloquent\Builder;

class AnswerController extends Controller
{
    public function index()
    {
        $answers = Answer::with('status')->get();

        if (sizeof($answers)=== 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Respuestas no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $answers,
            'msg' => [
                'summary' => 'Respuestas',
                'detail' => 'Se consultó correctamente respuestas',
                'code' => '200',
            ]], 200);
    }

    public function show($id)
    {
        $answer = Answer::findOrFail($id);
        
        if (!$answer) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Respuesta no encontrada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $answer,
            'msg' => [
                'summary' => 'Respuesta',
                'detail' => 'Se consultó correctamente respuesta',
                'code' => '200',
            ]], 200);
    }

    public function store(Request $request)
    {
        $catalogues = json_decode(file_get_contents(storage_path() . "/catalogues.json"), true);
        $data = $request->json()->all();

        $dataAnswer = $data['answer'];
        $dataStatus= $data['status'];
        $state = State::firstWhere('code', $catalogues['state']['type']['active']);
        $status = Catalogue::find($dataStatus['id']);
       
        $existAnswer =  Answer::where('code',$dataAnswer['code'])->orWhere('name',$dataAnswer['name'])->orWhere('order',$dataAnswer['order'])->first();
        $answer= null;
        if (!$existAnswer) {
            $answer = new Answer();
            $answer->code = $dataAnswer['code'];
            $answer->order = $dataAnswer['order'];
            $answer->name = $dataAnswer['name'];
            $answer->value = $dataAnswer['value'];

            $answer->state()->associate($state);
            $answer->status()->associate($status);
            $answer->save();

            $answerQuestionHasAnswer = AnswerQuestion::firstWhere("answer_id", Answer::firstWhere('code', $dataAnswer['code'])->id);
            $questions = Question::where('status_id', $status->id)->get();
            if (!$answerQuestionHasAnswer&& sizeof($questions)!==0) {
                $questionsIds = array();
                $status = Catalogue::where('type', 'STATUS')->Where('code', '1')->first();
                foreach ($questions as $question) {
                    array_push($questionsIds, $question->id);
                }
                $answer->questions()->attach($questionsIds);
            }
        
            if (!$answer) {
                return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Respuestas no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
            }
            return response()->json(['data' => $answer,
            'msg' => [
                'summary' => 'Respuestas',
                'detail' => 'Se creó correctamente las respuestas',
                'code' => '201',
            ]], 201);
        }else{
            return response()->json(['data' => $answer,
            'msg' => [
                'summary' => 'Respuestas',
                'detail' => 'La respuesta ya está registrada',
                'code' => '400',
            ]], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->json()->all();

        $dataAnswer = $data['answer'];
        $dataStatus= $data['status'];

        $answer = Answer::findOrFail($id);
        $answer->code = $dataAnswer['code'];
        $answer->order = $dataAnswer['order'];
        $answer->name = $dataAnswer['name'];
        $answer->value = $dataAnswer['value'];

        $status = Catalogue::find($dataStatus['id']);

        $answer->status()->associate($status);
        
        $answer->save();
        
        if (!$answer) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Respuesta no encontrada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $answer,
            'msg' => [
                'summary' => 'Respuesta',
                'detail' => 'Se actualizó correctamente la respuesta',
                'code' => '201',
            ]], 201);
    }

    public function destroy($id)
    {
        $answer = Answer::findOrFail($id);

        $answer->state_id = '2';
        $answer->save();

        if (!$answer) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Respuesta no encontrada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $answer,
            'msg' => [
                'summary' => 'Respuesta',
                'detail' => 'Se eliminó correctamente la respuesta',
                'code' => '201',
            ]], 201);
    }
}
