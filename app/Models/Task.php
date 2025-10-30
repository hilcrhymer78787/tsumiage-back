<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';

    protected $primaryKey = 'task_id';

    protected $fillable = [
        'task_user_id',
        'task_name',
        'task_sort_key',
    ];

    protected $casts = [
        'task_id' => 'integer',
        'task_user_id' => 'integer',
        'task_sort_key' => 'integer',
    ];

    public function works(): HasMany
    {
        return $this->hasMany(Work::class, 'work_task_id', 'task_id');
    }

    public function work(): HasOne
    {
        return $this->hasOne(Work::class, 'work_task_id', 'task_id');
    }
}
