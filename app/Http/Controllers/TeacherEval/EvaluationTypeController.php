<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use App\Models\TeacherEval\EvaluationType;
use App\Models\Ignug\State;
use App\Models\Ignug\Catalogue;

use Illuminate\Http\Request;

class EvaluationTypeController extends Controller
{
    public function index()
    {
        $evaluationTypes = EvaluationType::where('state_id',State::where('code', '1')->first()->id)
        ->with('status')->get();
        // return response()->json(['data'=>$evaluationTypes],200);
        if (sizeof($evaluationTypes)=== 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Tipos de Evaluaciones no encontrando',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $evaluationTypes,
            'msg' => [
                'summary' => 'Tipos de Evaluación',
                'detail' => 'Se consulto correctamente tipos de evaluación',
                'code' => '200',
            ]], 200);
    }

    public function show($id)
    {
        $evaluationType =  EvaluationType::findOrFail($id);
//        $catalogue =  Catalogue::where('id',$id)->get();
        return response()->json([
            'data' => [
                'evaluationType' => $evaluationType
            ]]);
    }

    public function store(Request $request)
    {
        $catalogues = json_decode(file_get_contents(storage_path() . "/catalogues.json"), true);

       $data = $request->json()->all();

       $dataEvaluationType = $data['evaluationType'];
       //$dataParentCode = $data['parent_code'];
       $state = State::firstWhere('code', $catalogues['state']['type']['active']);
       $dataStatus= $data['status'];

        $evaluationType = new EvaluationType();
        $evaluationType->code = $dataEvaluationType['code'];
        $evaluationType->name = $dataEvaluationType['name'];
        $evaluationType->percentage = $dataEvaluationType['percentage'];
        $evaluationType->global_percentage = $dataEvaluationType['global_percentage'];

        $status = Catalogue::find($dataStatus['id']);
        //$parentCode = EvaluationType::find($dataParentCode['id']);
        $evaluationType->status()->associate($status);
        $evaluationType->state()->associate($state);
        // if (!$parentCode) {
        //     $evaluationType->parent_id = null;
        // }else{
        //     $evaluationType->parent()->associate($parentCode);
        // }

        $evaluationType->save();
        if (!$evaluationType) {
            return response()->json([
                'data' <> null ,
                'msg' => [
                    'summary' => 'Datos Repetidos',
                    'detail' => 'Intenta de nuevo',
                    'code' => '400'
                ]], 400);
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Tipo de Evaluación no encontrada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
                
                
        }
        return response()->json(['data' => $evaluationType,
            'msg' => [
                'summary' => 'Tipo de Evaluación',
                'detail' => 'Se creó correctamente el tipo de evaluación',
                'code' => '201',
            ]], 201);
    }

    public function update(Request $request, $id)
    {
        $catalogues = json_decode(file_get_contents(storage_path() . "/catalogues.json"), true);

        $data = $request->json()->all();
        $dataEvaluationType = $data['evaluationType'];
        $dataStatus= $data['status'];
        //$dataParentCode = $data['parent_code'];

        $state = State::firstWhere('code', $catalogues['state']['type']['active']);
        $status = Catalogue::find($dataStatus['id']);
        $evaluationType = EvaluationType::findOrFail($id);
        $evaluationType->code = $dataEvaluationType['code'];
        $evaluationType->name = $dataEvaluationType['name'];
        $evaluationType->percentage = $dataEvaluationType['percentage'];
        $evaluationType->global_percentage = $dataEvaluationType['global_percentage'];

        // $parentCode = EvaluationType::find($dataParentCode['id']);
                // if (!$parentCode) {
        //     $evaluationType->parent_id = null;
        // }else{
        //     $evaluationType->parent()->associate($parentCode);
        // }
        $evaluationType->status()->associate($status);
        $evaluationType->state()->associate($state);

        $evaluationType->save();
        if (!$evaluationType) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Tipo de Evaluacion no encontrada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $evaluationType,
            'msg' => [
                'summary' => 'Tipo de Evaluación',
                'detail' => 'Se actualizó correctamente el tipo de evaluación',
                'code' => '201',
            ]], 201);
    }

    public function destroy($id)
    {
        $evaluationType = EvaluationType::findOrFail($id);
/*         $catalogue->delete(); */
/*         $evaluationType->update([
            'state_id'=>'3'
        ]); */

        $evaluationType->state_id = '3';
        $evaluationType->save();

        return response()->json([
            'data' => [
                'evaluationTypes' => $evaluationType
            ]
        ], 201);
    }

}
