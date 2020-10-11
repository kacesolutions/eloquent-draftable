<?php 

namespace Kace\Draftable;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;

trait Draftable
{
    public static function bootDraftable(): void
    {
        static::addGlobalScope('published', function (Builder $query) {
            $query
                ->whereNotNull('published_at')
                ->where('published_at', '<=', Carbon::now());
        });
    }

    /**
     * Include draft records in query results.
     */
    public function scopeWithDrafts(Builder $query): void
    {
        $query->withoutGlobalScope('published');
    }

    /**
     * Exclude published record from query results.
     */
    public function scopeOnlyDrafts(Builder $query): void
    {
        $query->withDrafts()->where(function (Builder $query) {
            $query
                ->whereNull('published_at')
                ->orWhere('published_at', '>', Carbon::now());
        });
    }

    /**
     * Determine if the record was published.
     */
    public function isPublished(): bool
    {
        return ! is_null($this->published_at)
            && $this->published_at <= Carbon::now();
    }

    /**
     * Determine if the record is draft.
     */
    public function isDraft(): bool
    {
        return ! $this->isPublished();
    }

    /**
     * Set value of the model's published_at column.
     */
    public function setPublishedAt(?DateTimeInterface $date): self
    {
        if (! is_null($date)) {
            $date = Carbon::parse($date);
        }

        $this->published_at = $date;

        return $this;
    }

    /**
     * Set value of model's published status.
     */
    public function setPublished(bool $published): self
    {
        if (! $published) {
            return $this->setPublishedAt(null);
        }

        if ($this->isDraft()) {
            return $this->setPublishedAt(Carbon::now());
        }

        return $this;
    }

    /**
     * Schedule the model's to be publish now or in the future.
     */
    public function publishAt(?DateTimeInterface $date): self
    {
        $this->setPublishedAt($date)
            ->save();

        return $this;
    }

    /**
     * Mark the model's as published.
     */
    public function publish(bool $publish = true): self
    {
        $this->setPublished($publish)
            ->save();

        return $this;
    }

    /**
     * Mark the model's as draft.
     */
    public function draft(): self
    {
        $this->setPublished(false);

        return $this;
    }
}
