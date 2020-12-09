<?php

namespace App\Models\TeacherEval;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Ignug\State;
use App\Models\Ignug\Catalogue;
use App\Traits\StatusActiveTrait;
use App\Traits\StatusDeletedTrait;

class Answer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use StatusActiveTrait;
    use StatusDeletedTrait;

    protected $connection = 'pgsql-teacher-eval';
    protected $table = 'teacher_eval.answers';

    protected $fillable = [
        'code',
        'order',
        'name',
        'value',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class)->withTimestamps();
    }

    public function status()
    {
        return $this->belongsTo(Catalogue::class);
    }

}