<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genres extends Model
{
    //The UUID
    use HasUuids;

    /**
     * name of the table
     */
    protected $table = 'genres';

    /**
     * Name of the Id
     * 
     * @var string
     */
    protected $primaryKey = 'genre_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'genre_id';

    /**
     * the mass asingnable attributes
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Relation with the works table
     * 
     * @var BelongsToMany
     */
    public function workRelation(): BelongsToMany
    {
        return $this->belongsToMany(Works::class,'genre_works','genre_id','work_id');
    }
}
