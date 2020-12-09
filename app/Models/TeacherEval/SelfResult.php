<?php

namespace App\Models\teacherEval;


use App\Models\Ignug\State;
use App\Models\Ignug\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\StatusActiveTrait;
use App\Traits\StatusDeletedTrait;

class SelfResult extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use StatusActiveTrait;
    use StatusDeletedTrait;

    protected $connection = 'pgsql-teacher-eval';
    protected $table = 'teacher_eval.self_results';

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function answerQuestion()
    {
        return $this->belongsTo(AnswerQuestion::class);
    }
}
