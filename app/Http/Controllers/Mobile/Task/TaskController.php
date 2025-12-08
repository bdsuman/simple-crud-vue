<?php

namespace App\Http\Controllers\Mobile\Task;

use App\Models\Task;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** 
 * @group Task
 * @authenticated
 */
class TaskController extends Controller
{
    /**
     * List published tasks with filtering and pagination
     *
     * @queryParam sort_by string The column to order by. Example: id
     * @queryParam sort_dir string Order direction (ASC|DESC). Example: DESC
     * @queryParam page integer Number of page. Example: 1.
     * @queryParam per_page integer Number of items per page. Example: 10.
     * @queryParam search string Search by title,author_name,job_title Example: Jhon
     *
     * @response 200 {
     *   "status": true,
     *   "message": "task_fetched_successfully",
     *   "data": [
     *     {
     *       "id": 9,
     *       "title": "Voluptatibus quidem minima atque.",
     *       "description": "Temporibus tempora eum nesciunt doloremque. ae nostrum.",
     *       "is_completed": false,
     *       "avatar": "https://placehold.co/100x100",
     *       "avatar_url": "https://placehold.co/100x100",
     *       "created_at": "2025-08-28T05:09:49.000000Z",
     *       "updated_at": "2025-08-28T05:09:49.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "total": 40,
     *     "last_page": 40,
     *     "per_page": 1,
     *     "current_page": 1
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 10), 1), 100);

        $tasks = Task::query()
            ->searchFilter($request->search)
            ->orderBy('id', 'DESC')
            ->paginate($perPage);

        return success_response(
            TaskResource::collection($tasks),
            true,
            'task_fetched_successfully'
        );
    }

    /**
     * Show a single published task
     *
     * @param Task $task
     *
     * @response {
     *   "status": true,
     *   "message": "task_fetched_successfully",
     *   "data": {
     *     "id": 9,
     *     "author_name": "Mrs. Petra Pollich",
     *     "job_title": "Credit Checkers Clerk",
     *     "title": "Voluptatibus quidem minima atque.",
     *     "description": "Temporibus tempora eum nesciunt doloremque. ae nostrum.",
     *     "is_completed": false,
     *     "avatar": "https://placehold.co/100x100",
     *     "avatar_url": "https://placehold.co/100x100",
     *     "created_at": "2025-08-28T05:09:49.000000Z",
     *     "updated_at": "2025-08-28T05:09:49.000000Z"
     *   }
     * }
     */
    public function show(Task $task): JsonResponse
    {
        return success_response(
            $task->makeHidden('avatar_url')->append('avatar_url'),
            false,
            'task_fetched_successfully'
        );
    }
}
