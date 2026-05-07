<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadHistory extends Model
{
    //the UUID
    use HasUuids;

    /**
     * table name
     */
    protected $table = 'read_histories';

    /**
     * Name of the Id
     * 
     * @var string
     */
    protected $primaryKey = 'read_history_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'read_history_id';

    /**
     * The mass assinged atributes
     * 
     * @var array
     */
    protected $fillable = [
        'user',
        'work',
        'chapter',
    ];

    /**
     * The relationship with the users table
     */
    public function userRelation():BelongsTo
    {
        return $this->belongsTo(User::class,'user','user_id');
    }

    /**
     * The relationship with the works table
     * 
     * @return BelongsTo
     */
    public function workRelation():BelongsTo
    {
        return $this->belongsTo(User::class,'work','work_id');
    }

    /**
     * The relationship with the chapters table
     * 
     * @return BelongsTo
     */
    public function chapterRelation():BelongsTo
    {
        return $this->belongsTo(Chapters::class,'chapter','chapter_id');
    }
}
