<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Works extends Model
{
    /**
     * Uses UUID
     */
    use HasUuids;

    /**
     * table name
     */
    protected $table = 'works';

    /**
     * Name of the Id
     */
    protected $primaryKey = 'work_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'work_id';

    /**
     * all fillable rows
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'cover',
        'author',
        'age_restriction',
        'work_in_progress',
    ];

    /**
     * Relation with the users table
     * 
     * @return BelongsTo<User, Works>
     */
    public function authorRelation(): BelongsTo
    {
        return $this->belongsTo(User::class,'author','user_id');
    }

    /**
     * Relation with the AgeRestrictions table
     * 
     * @return BelongsTo<AgeRestrictions, Works>
     */
    public function ageRestrictionRelation(): BelongsTo
    {
        return $this->belongsTo(AgeRestrictions::class,'age_restriction','age_restriction_id');
    }

    /**
     * Relation to the chapter table
     * 
     * @return HasMany<Chapters, Works>
     */
    public function chapterRelation(): HasMany
    {
        return $this->hasMany(Chapters::class,'work');
    }

    /**
     * Relation qith the comment table
     * 
     * @return HasMany<Comments, Works>
     */
    public function commentRelation():HasMany
    {
        return $this->hasMany(Comments::class,'work');
    }

    /**
     * Relation with the intermediate table with genres
     * 
     * @return BelongsToMany<Genres, Works, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function genresRelation(): BelongsToMany
    {
        return $this->belongsToMany(Genres::class,'genre_works','work_id','genre_id');
    }

    /**
     * Relation with the readHistory table
     * @return HasMany<ReadHistory, Works>
     */
    public function readHistoriesRelation(): HasMany
    {
        return $this->hasMany(ReadHistory::class,'work');
    }

    /**
     * Relation to inteemediate table with readLists
     * @return BelongsToMany<ReadLists, Works, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function readListsRelation():BelongsToMany
    {
        return $this->belongsToMany(ReadLists::class,'read_lists_works','work','read_list');
    }

    public function collectionsRelation():BelongsToMany
    {
        return $this->belongsToMany(Collections::class,'collections_works','work_id','collection_id');
    }

    public function wipsRelation():BelongsTo{
        return $this->belongsTo(WorksInProgress::class,'work_in_progress','work_in_progress_id');
    }
}
