<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationSetting extends Model
{
    protected $fillable = ['api_token'];

    protected $hidden = ['api_token'];

    protected $casts = ['api_token' => 'encrypted'];
}
