<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class BackgroundImageBuilder extends Builder
{
    /**
     * Search by title
     */
    public function searchFilter($search): self
    {
        return $this->when($search, fn($q) => $q->whereAny(['background_images.title', 'background_images.topic'], 'ILIKE', "%$search%"));
    }

    public function publishFilter($publish): self
    {
        $publishArray = array_filter(explode(',', $publish), fn($v) => $v !== '');

        $publishArray = array_map(fn($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN), $publishArray);

        return $this->when(count($publishArray), fn($q) => $q->whereIn('background_images.publish', $publishArray));
    }
}
