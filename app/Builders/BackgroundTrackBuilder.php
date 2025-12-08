<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class BackgroundTrackBuilder extends Builder
{
     /**
     * Search by title
     */
    public function searchFilter($search): self
    {
        return $this->when($search, fn ($q) => $q->where('background_tracks.title', 'ILIKE', "%$search%"));
    }

    public function publishFilter($publish): self
    {
        $publishArray = array_filter(explode(',', $publish), fn ($v) => $v !== '');

        $publishArray = array_map(fn($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN), $publishArray);

        return $this->when(count($publishArray), fn($q) => $q->whereIn('background_tracks.publish', $publishArray));
    }


    public function genreFilter($genre): self
    {
        $genreArray = array_filter(explode(',', $genre), fn ($v) => $v);
        
        return $this->when(count($genreArray), fn ($q) => $q->whereIn('background_tracks.genre', $genreArray));
    }

    public function goalFilter($goal): self
    {
        $goalArray = array_filter(explode(',', $goal), fn ($v) => $v);
        
        return $this->when(count($goalArray), fn ($q) => $q->whereIn('background_tracks.goal', $goalArray));
   }

}
