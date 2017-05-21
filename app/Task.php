<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'completed'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'task_user');
    }
}
