<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GenresWorks extends Model
{
    //the UUID
    use HasUuids;

    /**
     * name of table
     */
    protected $table = 'genres_works';

    protected $fillable = ['genre','work'];

    /**
     * Intermediate table doesn't need Id
     */

    /**
     * Intermediate table doesn't need fillables
     */
}
