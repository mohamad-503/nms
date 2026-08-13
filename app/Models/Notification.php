<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['channel', 'recipient', 'subject', 'message', 'status'];
}
