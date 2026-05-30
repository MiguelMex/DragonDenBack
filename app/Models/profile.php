<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class profile extends Model
{
    use HasUuids;
    protected $primaryKey = 'profile_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'profile_id';
    protected $fillable = [
        'user_id',
        'nickname',
        'name',
        'description',
        'avatar',
    ];

    /**
     * Relation to th user table
     * @return BelongsTo<User, profile>
     */
    public function userRelation(): BelongsTo{
        return $this->belongsTo(User::class, 'user_id','user_id');
    }

    /**
     * Relationship with the read_history table
     * 
     * @return HasMany
     */
    public function historiesRelation(): HasMany
    {
        return $this->hasMany(ReadHistory::class,'user');
    }

    public function wipsRelation(): HasMany
    {
        return $this->hasMany(WorksInProgress::class,'author');
    }

    /**
     * Relation with the Works table
     * 
     * @return HasMany
     */
    public function worksRelation(): HasMany
    {
        return $this->hasMany(Works::class,'author');
    }

    /**
     * Relation with the readlist table
     * @return HasMany<ReadLists, User>
     */
    public function readListsRelation():HasMany
    {
        return $this->hasMany(ReadLists::class,'user','profile_id');
    }

    /**
     * Relationship with the comment table
     * 
     * @return HasMany
     */
    public function commentsRelation(): HasMany
    {
        return $this->hasMany(Comments::class,'user');
    }

    /**
     * Relation with the collections table
     * 
     * 
     * @return HasMany<Collections, User>
     */
    public function collectionsRelation():HasMany
    {
        return $this->hasMany(Collections::class,'user');
    }

    public function following():BelongsToMany{
        return $this->belongsToMany(profile::class,'following','user_id','follower_id')->withTimestamps();
    }

    public function followers():BelongsToMany{
        return $this->belongsToMany(profile::class,'following','follower_id','user_id')->withTimestamps();
    }

}
