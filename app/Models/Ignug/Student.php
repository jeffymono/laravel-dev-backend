<?php

namespace App\Models\Ignug;

use App\Models\Authentication\User;
use App\Models\Attendance\Attendance;
use App\Models\TeacherEval\DetailEvaluation;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Student extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $connection = 'pgsql-ignug';
    protected $fillable = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->morphMany(Attendance::class, 'attendanceable');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function careers()
    {
        return $this->morphToMany(Career::class, 'careerable');
    }

    public function detailEvaluations(){
        return $this->morphToMany(DetailEvaluation::class, 'detail_evaluationable','detail_evaluations','detail_evaluationable_id','detail_evaluationable_type');
    }
}
