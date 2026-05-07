<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CollectionsWorks extends Model
{
    //the UUID
    use HasUuids;

    /**
     * Table name
     */
    protected $table = 'collections_works';

    /**
     * Doesn't have a primary key
     */

    /**
     * Intermediate table, doesn't need fillables
     */
}
