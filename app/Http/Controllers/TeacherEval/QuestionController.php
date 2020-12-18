<?php

namespace App\Http\Controllers\TeacherEval;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Models\TeacherEval\Question;
use App\Models\TeacherEval\Answer;
use App\Models\TeacherEval\EvaluationType;
use App\Models\Ignug\Catalogue;
use App\Models\Ignug\State;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('status', 'type', 'evaluationType')->get();

        if (sizeof($questions)=== 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Preguntas no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $questions,
            'msg' => [
                'summary' => 'Preguntas',
                'detail' => 'Se consulto correctamente preguntas',
                'code' => '200',
            ]], 200);
    }

    public function show($id)
    {
        $question = Question::findOrFail($id);
        if (!$question) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Pregunta no encontrada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $question,
            'msg' => [
                'summary' => 'Pregunta',
                'detail' => 'Se consulto correctamente la pregunta',
                'code' => '200',
            ]], 200);
    }  

    public function store(Request $request){

        $catalogues = json_decode(file_get_contents(storage_path(). '/catalogues.json'), true);
        $data = $request->json()->all();

       $dataQuestion = $data['question'];
       $dataEvaluationType= $data['evaluation_type'];
       $dataType= $data['type'];
       $dataStatus = $data['status'];
       
        $question = new Question();
        $question->code = $dataQuestion['code'];
        $question->order = $dataQuestion['order'];
        $question->name = $dataQuestion['name'];
        $question->description = $dataQuestion['description'];
        
        $evaluationType = EvaluationType::findOrFail($dataEvaluationType['id']);
        $type = Catalogue::find($dataType['id']);
        $status = Catalogue::findOrFail($dataStatus['id']);
  
        $question->evaluationType()->associate($evaluationType);
        $question->type()->associate($type);
        $question->state()->associate(State::where('code', $catalogues['state']['type']['active'])->first());
        $question->status()->associate($status);

        $question->save();

        $answersIds = array();
        $catalogueStatus = Catalogue::where('type','STATUS')->Where('code','1')->first();
        $answers = Answer::where('status_id', $catalogueStatus->id)
        ->get();
        foreach ($answers as $answer) {
            array_push($answersIds,$answer->id);
        }

        $question->answers()->attach( $answersIds); 
        
        if (!$question) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Pregunta no creada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $question,
            'msg' => [
                'summary' => 'Pregunta',
                'detail' => 'Se creo correctamente la pregunta',
                'code' => '201',
            ]], 201);
    }
    public function update(Request $request, $id)
    {
        $data = $request->json()->all();

        $dataQuestion = $data['question'];
        $dataEvaluationType = $data['evaluation_type'];
        $dataType= $data['type'];
        $dataStatus = $data['status'];

        $question = Question::findOrFail($id);
        $question->code = $dataQuestion['code'];
        $question->order = $dataQuestion['order'];
        $question->name = $dataQuestion['name'];
        $question->description = $dataQuestion['description'];
        
        $type = Catalogue::find($dataType['id']);
        $evaluationType = EvaluationType::findOrFail($dataEvaluationType['id']);
        $status = Catalogue::findOrFail($dataStatus['id']);

        $question->evaluationType()->associate($evaluationType);
        $question->type()->associate($type);
        $question->status()->associate($status);
        
        $question->save();
        
        if (!$question) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Pregunta no actualizada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $question,
            'msg' => [
                'summary' => 'Pregunta',
                'detail' => 'Se actualizo correctamente la pregunta',
                'code' => '201',
            ]], 201);
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->state_id = '2';
        $question->save();

        if (!$question) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Pregunta no eliminada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $question,
            'msg' => [
                'summary' => 'Pregunta',
                'detail' => 'Se elimino correctamente la pregunta',
                'code' => '201',
            ]], 201);
    }

}

