<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /**
     * For the UUID
     */
    use HasUuids;

    /**
     * id parameters
     * 
     * @var string
     */
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'user_id';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Relation with the profile
     * @return HasOne<profile, User>
     */
    public function profileRelation(): HasOne{
        return $this->hasOne(profile::class,'user_id','profile_id');
    }

    // /**
    //  * Relationship with the read_history table
    //  * 
    //  * @return HasMany
    //  */
    // public function historiesRelation(): HasMany
    // {
    //     return $this->hasMany(ReadHistory::class,'user');
    // }

    // public function wipsRelation(): HasMany
    // {
    //     return $this->hasMany(WorksInProgress::class,'author');
    // }

    // /**
    //  * Relation with the Works table
    //  * 
    //  * @return HasMany
    //  */
    // public function worksRelation(): HasMany
    // {
    //     return $this->hasMany(Works::class,'author');
    // }

    // /**
    //  * Relation with the readlist table
    //  * @return HasMany<ReadLists, User>
    //  */
    // public function readListsRelation():HasMany
    // {
    //     return $this->hasMany(ReadLists::class,'user');
    // }

    // /**
    //  * Relationship with the comment table
    //  * 
    //  * @return HasMany
    //  */
    // public function commentsRelation(): HasMany
    // {
    //     return $this->hasMany(Comments::class,'user');
    // }

    // /**
    //  * Relation with the collections table
    //  * 
    //  * 
    //  * @return HasMany<Collections, User>
    //  */
    // public function collectionsRelation():HasMany
    // {
    //     return $this->hasMany(Collections::class,'user');
    // }

    // public function following():BelongsToMany{
    //     return $this->belongsToMany(User::class,'following','user_id','follower_id')->withTimestamps();
    // }

    // public function followers():BelongsToMany{
    //     return $this->belongsToMany(User::class,'following','follower_id','user_id')->withTimestamps();
    // }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
}
