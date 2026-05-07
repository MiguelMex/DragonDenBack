<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorksInProgress extends Model
{
    //the UUID
    use HasUuids;

    protected $table = 'wips';

    /**
     * Id parameters
     * @var string
     */
    protected $primaryKey = 'work_in_progress_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'work_in_progress_id';

    /**
     * The attributes
     */
    protected $fillable = [
        'title',
        'author'
    ];

    /**
     * Relation with drafts table
     * @return HasMany<Drafts, WorksInProgress>
     */
    public function draftsRelation():HasMany
    {
        return $this->hasMany(Drafts::class,'work_in_progress','work_in_progress_id');
    }

    /**
     * Relation with user
     * @return BelongsTo<User, WorksInProgress>
     */
    public function userRelation():BelongsTo
    {
        return $this->belongsTo(User::class,'author','user_id');
    }

    public function workRelation():HasOne{
        return $this->hasOne(Works::class,'work_in_progress','work_in_progress_id');
    }
}
