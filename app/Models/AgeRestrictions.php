<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgeRestrictions extends Model
{
    //The UUID
    use HasUuids;

    /**
     * The table name
     */
    protected $table = 'age_restrictions';

    /**
     * Id parameters
     */
    protected $primaryKey = 'age_restriction_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'age_restriction_id';    

    /**
     * the fillable variables of the table
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relation with the works table
     * 
     * @return HasMany<Works, AgeRestrictions>
     */
    public function workRelation(): HasMany
    {
        return $this->hasMany(Works::class,'age_restriction');
    }
}
