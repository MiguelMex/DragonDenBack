<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use PhpParser\Builder\Class_;

class Comments extends Model
{
    //the UUID
    use HasUuids;

    /**
     * name of the table
     */
    protected $table = 'comments';

    /**
     * Name of the Id
     * 
     * @var string
     */
    protected $primaryKey = 'comment_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'comment_id';

    /**
     * The fillable atributes
     */
    protected $fillable = [
        'response',
        'text',
        'chapter',
        'user',
    ];

    /**
     * The relation with the works table
     * 
     * @var BelongsTo
     */    
    public function chapterRelation():BelongsTo
    {
        return $this->belongsTo(Chapters::class,'chapter','chapter_id');
    }

    /**
     * The relation with the users table
     * 
     * @return BelongsTo
     */
    public function userRelation():BelongsTo
    {
        return $this->belongsTo(User::class,'user','user_id');
    }

    /**
     * Nullable relation with parent comment
     */
    public function parent():BelongsTo
    {
        return $this->belongsTo(Comments::class,'response','comment_id');
    }

    /**
     * Relation with possible children
     */
    public function children():HasMany
    {
        return $this->hasMany(Comments::class,'response','comment_id');
    }
}
