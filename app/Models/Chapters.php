<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Chapters extends Model
{
    //the UUID
    use HasUuids;

    /**
     * Name of the table
     */
    protected $table = 'chapters';

    /**
     * Name of the id
     * 
     * @var string
     */
    protected $primaryKey = 'chapter_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'chapter_id';

    /**
     * the fillable variables
     */
    protected $fillable = [
        'name',
        'route',
        'cover',
        'work',
        'draft',
    ];

    /**
     * Relation with the works table
     * 
     * @return BelongsTo<Works, Chapters>
     */
    public function workRelation(): BelongsTo
    {
        return $this->belongsTo(Works::class,'work','work_id');
    }

    /**
     * Relation with the read  history table
     * @return HasMany<ReadHistory, Chapters>
     */
    public function historyRelation(): HasMany
    {
        return $this->hasMany(ReadHistory::class,'chapter','chapter_id');
    }

    public function draftRelation(): BelongsTo{
        return $this->belongsTo(Drafts::class,'draft','draft_id');
    }

    //In case of deletion
    protected static function booted(): void
    {
        static::deleting(function ($chapter) {
            if ($chapter->route && Storage::disk('public')->exists($chapter->route)) {
                Storage::disk('public')->delete($chapter->route);
            }
            //to delete the cover
            if($chapter->cover && Storage::disk('local')->exists($chapter->cover)){
                Storage::disk('public')->delete($chapter->cover);
            }
        });
    }
}
