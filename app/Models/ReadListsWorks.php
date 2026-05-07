<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ReadListsWorks extends Model
{
    //The UUID
    use HasUuids;

    /**
     * table name
     */
    protected $table = 'read_lists_works';
    protected $fillable = ['work','read_list'];

    /**
     * Intemediate table doesn't need fillables
     */
}
