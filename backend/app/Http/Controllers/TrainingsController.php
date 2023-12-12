<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingDay;
use Illuminate\Http\Request;
use Auth;

class TrainingsController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $trainings = Training::select('name','id','created_at')->where('user_id', $user->id)->orderBy('id','asc') ->get();

        return apiResponse(200,'İşlem Başarılı','İşlem başarıyla gerçekleştirildi', $trainings)->toSuccess();
    }

    public function show($id)
    {
        $user =  Auth::user();

        $training = Training::select('id','name')->with(['days'])->where([
            ['id','=',$id],
            ['user_id','=',$user->id]
        ])->first();

        return apiResponse(200,'','',$training)->toSuccess();
    }

    public function showDays(string $trainingId)
    {
        $user =  Auth::user();

        $days = Training::select('id','name')->with(['days'])->where([
            ['id','=', $trainingId],
            ['user_id','=', $user->id]
        ])->first()->days;

        return apiResponse(200,'','',$days)->toSuccess();
    }

    public function showExercises(string $trainingId, string $dayId)
    {
        $user =  Auth::user();

        $exercises = Training::select('id','name')->with(['days'])->where([
            ['id','=', $trainingId],
            ['user_id','=', $user->id]
        ])->first()->days->find($dayId)->exercises;

        return apiResponse(200,'','',$exercises)->toSuccess();
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $payload = $this->validatePayload($request);

        $training =  Training::create([
            'user_id' => $user->id,
            'name' => $payload['train']['name']
        ]);

        $this->addTrainingDaysByPayload($training, $payload);

        return apiResponse(200,'Başarılı', 'İşlem Başarılı!')->toSuccess();
    }

    public function destroy(Training $training)
    {
        Training::where([
            ['user_id', Auth::user()->id],
            ['id' , $training->id]
        ])->delete();

        return apiResponse(200,'Başarılı', 'İşlem başarıyla tamamlandı')->toSuccess();
    }

    public function update(Training $training,Request $request)
    {
        $payload = $this->validatePayload($request);
        $training->days()->delete();


        $this->addTrainingDaysByPayload($training,$payload);


        return apiResponse(200,'Başarılı', 'İşlem başarıyla tamamlandı',[
            'res' => $training->days()
        ])->toSuccess();
    }

    public function validatePayload(Request $request)
    {
        return $request->validate([
            'train' =>  'required',
            'train.name' => 'required|string',
            'train.days' => 'required|array',
            'train.days.*.name' => 'required|string',
            'train.days.*.exercises' => 'required|array',
            'train.days.*.exercises.*.sets' => 'required|integer',
            'train.days.*.exercises.*.reps' => 'required|integer',
            'train.days.*.exercises.*.selected.value' => 'required|integer|exists:exercises,id',
        ]);
    }

    public function addTrainingDaysByPayload(Training $training,array $payload):void
    {
        foreach($payload['train']['days'] as $day) {
            $trainingDay = $training->days()->create([
                'name' => $day['name']
            ]);

            foreach($day['exercises'] as $exercise) {
                $trainingDay->exercises()->create([
                    'exercise_id' => $exercise['selected']['value'],
                    'sets' => $exercise['sets'],
                    'reps' => $exercise['reps'],
                ]);
            }
        }
    }
}
