<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class following extends Model
{
    //the UUID
    use HasUuids;

    /**
     * Table name
     */
    protected $table = 'following';

    protected $fillable = ['follower_id','user_id'];
}
