<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Drafts extends Model
{
    //the UUID
    use HasUuids;

    /**
     * name of table
     */
    protected $table = 'drafts';

    /**
     * Name of the Id
     * 
     * @var string
     */
    protected $primaryKey = 'draft_id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $uuidColumn = 'draft_id';

    /**
     * The fillable atributes
     */
    protected $fillable = [
        'name',
        'route',
        'work_in_progress',
    ];

    /**
     * Relation with the table Works In progress
     * 
     * @return BelongsTo<WorksInProgress, Drafts>
     */
    public function wipRelation():BelongsTo
    {
        return $this->belongsTo(WorksInProgress::class,'work_in_progress','work_in_progress_id');
    }

    public function chapterRelation(): HasOne
    {
        return $this->hasOne(Chapters::class,'draft','draft_id');
    }

    //In case of deletion
    protected static function booted(): void
    {
        static::deleting(function ($draft) {
            if ($draft->route && Storage::disk('local')->exists($draft->route)) {
                Storage::disk('local')->delete($draft->route);
            }
        });
    }
}
