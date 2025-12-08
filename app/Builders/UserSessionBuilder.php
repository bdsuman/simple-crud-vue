<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class UserSessionBuilder extends Builder
{
    public function overviewSearch(string $search): self
    {
        $base = 'user_sessions';

        return $this
            ->leftJoin('users as u', 'u.id', '=', "$base.user_id")
            ->leftJoin('sessions as s', 's.id', '=', "$base.session_id")
            ->leftJoin('paths as p', 'p.id', '=', "$base.path_id")
            ->where(function ($q) use ($search, $base) {
                $q->Where("$base.ai_voice_preference", 'ILIKE', "%$search%")
                ->orWhere("$base.preferred_tenure", 'ILIKE', "%$search%")
                //   ->orWhere("$base.daily_session_duration", 'ILIKE', "%$search%")
                //   ->orWhere("$base.preferred_compilation_days", 'ILIKE', "%$search%")
                  ->orWhere('u.full_name', 'ILIKE', "%$search%")
                  ->orWhere('s.name', 'ILIKE', "%$search%")
                  ->orWhere('p.name', 'ILIKE', "%$search%");

                if (is_numeric($search)) {
                    $id = (int) $search;
                    $q->orWhere("$base.id", $id)
                      ->orWhere('u.id', $id)
                      ->orWhere('s.id', $id)
                      ->orWhere('p.id', $id);
                }
            })
            ->select("$base.*");
    }

    public function orderByColumn(string $orderBy, string $direction): self
    {
        $base = 'user_sessions';
        $dir  = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        if ($orderBy === 'user') {
            return $this
                ->leftJoin('users as u', 'u.id', '=', "$base.user_id")
                ->orderBy('u.full_name', $dir)
                ->select("$base.*");
        }

        if ($orderBy === 'session') {
            return $this
                ->leftJoin('sessions as s', 's.id', '=', "$base.session_id")
                ->orderBy('s.name', $dir)
                ->select("$base.*");
        }

        if ($orderBy === 'path') {
            return $this
                ->leftJoin('paths as p', 'p.id', '=', "$base.path_id")
                ->orderBy('p.name', $dir)
                ->select("$base.*");
        }

        if ($orderBy === 'name') {
            return $this
                ->leftJoin('sessions as p', 'p.id', '=', "$base.path_id")
                ->orderBy('p.name', $dir)
                ->select("$base.*");
        }

        if ($orderBy === 'session_duration') {
            return $this;
            // return $this->orderBy("$base.daily_session_duration", $dir);
        }

        if ($orderBy === 'created_date') {
            return $this->orderBy("$base.created_at", $dir);
        }

        if ($orderBy === 'completion_date') {
            return $this->orderBy("$base.completed_at", $dir);
        }

        if ($orderBy === 'target_days') {
            return $this->orderBy("$base.preferred_tenure", $dir);
        }

        if ($orderBy === 'session_status') {
            return $this->orderBy("$base.completed_at", $dir);
        }

        // if ($orderBy === 'extended_session_status') {
        //     return $this->orderBy("$base.preferred_compilation_days", $dir);
        // }

        if (! str_contains($orderBy, '.')) {
            $orderBy = "$base.$orderBy";
        }

        return $this->orderBy($orderBy, $dir);
    }
}
