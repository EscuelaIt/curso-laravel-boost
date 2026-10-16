<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are not mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Remove the relations with the users when the project is deleted.
     *
     * Projects are soft deleted, so the cascade of the pivot foreign key never runs.
     */
    protected static function booted(): void
    {
        static::deleting(function (Project $project): void {
            $project->users()->detach();
        });
    }

    /**
     * Get the users associated with the project.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Scope projects whose name or description contains the given keyword.
     *
     * @param  Builder<Project>  $query
     */
    #[Scope]
    protected function similar(Builder $query, ?string $keyword): void
    {
        if (blank($keyword)) {
            return;
        }

        $query->where(function (Builder $query) use ($keyword): void {
            $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        });
    }
}
