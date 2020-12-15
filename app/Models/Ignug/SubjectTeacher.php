<?php

namespace App\Models\Ignug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TeacherEval\StudentResult;


class SubjectTeacher extends Model
{
    use HasFactory;
    protected $connection = 'pgsql-ignug';
    protected $table= 'subject_teacher';

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function studentResults(){
        return $this->hasMany(StudentResult::class);
    }
    public function schoolPeriod()
    {
        return $this->belongsTo(SchoolPeriod::class);
    }

}
