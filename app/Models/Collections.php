<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collections extends Model
{
    //The UUID
    use HasUuids;

    /**
     * The table name
     */
    protected $table = 'collections';

    /**
     * The primary key of the table
     * 
     * @var string
     */
    protected $primaryKey = 'collection_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'collection_id';

    /**
     * The mass asignable atributes
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'user',
    ];

    /**
     * Relationship with the works
     */
    public function workRelation():BelongsToMany
    {
        return $this->belongsToMany(Works::class, 'collections_works','collection_id','work_id');
    }

    /**
     * Relation with the user table
     * 
     * @return BelongsTo<User, Collections>
     */
    public function userRelation():BelongsTo
    {
        return $this->belongsTo(profile::class,'user','profile_id');
    }
}
