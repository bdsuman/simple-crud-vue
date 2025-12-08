<?php
namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class SessionFeedbackSubmissionBuilder extends Builder
{
    public function overviewSearch(string $search): self
    {
        $base = 'session_feedback_submissions';

        return $this
            ->leftJoin('users as u', 'u.id', '=', "$base.user_id")
            ->leftJoin('sessions as s', 's.id', '=', "$base.session_id")
            ->where(function ($q) use ($search, $base) {
                $q->where('u.full_name', 'ILIKE', "%$search%")
                  ->orWhere('s.type', 'ILIKE', "%$search%")
                  ->orWhere("$base.fid", 'ILIKE', "%$search%");
            })
            ->select("$base.*");
    }
}

