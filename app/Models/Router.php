<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    protected $fillable = ['name', 'ip', 'port', 'username', 'password', 'use_ssl', 'status', 'last_checked', 'notes'];

    protected $casts = ['use_ssl' => 'boolean', 'port' => 'integer', 'last_checked' => 'datetime'];
}
