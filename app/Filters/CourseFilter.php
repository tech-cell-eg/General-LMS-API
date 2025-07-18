<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CourseFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter => $value) {
            if (method_exists($this, $filter)) {
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
        // Handle both string 'true/false' and boolean true/false
        $featured = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $query->where('is_featured', $featured);
    }
}
