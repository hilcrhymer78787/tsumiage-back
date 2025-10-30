<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Work extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'work_id';

    protected $fillable = [
        'work_id',
        'work_date',
        'work_task_id',
        'work_user_id',
        'work_state',
    ];

    protected $casts = [
        'work_id' => 'integer',
        'work_date',
        'work_task_id' => 'integer',
        'work_user_id' => 'integer',
        'work_state' => 'integer',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'work_task_id', 'task_id');
    }
}
