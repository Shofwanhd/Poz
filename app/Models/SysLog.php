<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysLog extends Model
{
    protected $fillable = ['user_id', 'action', 'model', 'model_id', 'field', 'oldValue', 'newValue', 'actionDate'];
}
