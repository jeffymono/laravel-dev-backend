<?php

namespace App\Models\Ignug;

use App\Models\Ignug\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolPeriod extends Model
{
    use HasFactory;
    protected $connection = 'pgsql-ignug';
    protected $fillable = [
        'code',
        'name',
        'start_date',
        'end_date',
        'ordinary_start_date',
        'ordinary_end_date',
        'extraordinary_start_date',
        'extraordinary_end_date',
        'especial_start_date',
        'especial_end_date'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function subjectTeachers(){
        return $this->hasMany(SubjectTeacher::class);
    }
    public function status()
    {
        return $this->belongsTo(Catalogue::class, "status_id");
    }
    public function evaluation()
    {
        return $this->hasMany(Evaluation::class);
    }
}