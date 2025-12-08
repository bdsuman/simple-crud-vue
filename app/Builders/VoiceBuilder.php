<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class VoiceBuilder extends Builder
{
     /**
     * Search by title
     */
    public function searchFilter($search): self
    {
        return $this->when($search, fn ($q) => $q->whereAny(['voices.name','voices.description'], 'ILIKE', "%$search%"));
    }

    public function ageFilter($age): self
    {
        $ageArray = array_filter(explode(',', $age), fn ($v) => $v);
        
        return $this->when(count($ageArray), fn ($q) => $q->whereIn('voices.age', $ageArray));
    }

    public function languageFilter($language): self
    {
        $languageArray = array_filter(explode(',', $language), fn ($v) => $v);
        
        return $this->when(count($languageArray), fn ($q) => $q->whereIn('voices.language', $languageArray));
    }
    
    public function genderFilter($gender): self
    {
        $genderArray = array_filter(explode(',', $gender), fn ($v) => $v);
        
        return $this->when(count($genderArray), fn ($q) => $q->whereIn('voices.gender', $genderArray));
    }

}
