<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role_code', 'avatar', 'created_by'
    ];

    protected $hidden = [
        'password', 'remember_token', 'deleted_at', 'created_at'
    ];

    public function role()
    {
        return $this->belongsTo('App\Role', 'role_code', 'role_code')->withTrashed();
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'username', 'employee_id')->withTrashed();
    }

    public function employee_sync()
    {
        return $this->belongsTo('App\EmployeeSync', 'username', 'employee_id');
    }

}