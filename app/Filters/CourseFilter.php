<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CourseFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter => $value) {
            if ($filter === 'sort_by') {
                $this->applySorting($query, $value);
                continue;
            }

            if (method_exists($this, $filter) && $value !== null) {
                $this->$filter($query, $value);
            }
        }

        return $query;
    }

    protected function rate(Builder $query, $value): void
    {
        $query->having('reviews_avg_rating', '=', $value);
    }

    protected function price(Builder $query, $value): void
    {
        $query->where('price', '=', $value);
    }

    protected function category(Builder $query, $value): void
    {
        $query->where('category_id', $value);
    }

    protected function sections(Builder $query, $value): void
    {
        $query->having('sections_count', '=', $value);
    }

    protected function is_featured(Builder $query, $value): void
    {
        $featured = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $query->where('is_featured', $featured);
    }

    protected function applySorting(Builder $query, string $sortBy): void
    {
        switch ($sortBy) {
            case 'highest_rated':
                $query->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'most_reviews':
                $query->orderBy('reviews_count', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
        }
    }
}
