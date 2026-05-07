<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ReadLists extends Model
{
    //the UUID
    use HasUuids;

    /**
     * name of the table
     */
    protected $table = 'read_lists';

    /**
     * Name of the Id
     * @var string
     */
    protected $primaryKey = 'read_list_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'read_list_id';

    //the mass asignable attributes
    protected $fillable = [
        'name',
        'description',
        'user',
    ];

    /**
     * Relation with the user table
     * @return BelongsTo<User, ReadLists>
     */
    public function userRelation(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user','user_id')->withTimeStamps();
    }

    /**
     * Relation with the intermediate table with works
     * @return BelongsToMany<Works, ReadLists, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function workRelation(): BelongsToMany
    {
        return $this->belongsToMany(Works::class,'read_lists_works','read_list','work')->withTimestamps();
    }
}
