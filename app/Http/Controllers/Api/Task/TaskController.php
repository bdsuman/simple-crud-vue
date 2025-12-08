<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Task\StoreTaskRequest;
use App\Http\Requests\Api\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskArchives;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadAble;

/** 
 * @group Task Module
 * @subgroup Task
 * @authenticated
 */
class TaskController extends Controller
{
    use UploadAble;

    /**
     * Upload options for image optimization
     */
    private const UPLOAD_OPTIONS = [
        'resize' => ['width' => 800, 'height' => 600],
        'quality' => 85
    ];

    /**
     * List all tasks with filtering and pagination
     *
     * @queryParam sort_by string The column to order by. Example: id
     * @queryParam sort_dir string Order direction (ASC|DESC). Example: DESC
     * @queryParam page integer Number of page. Example: 1.
     * @queryParam per_page integer Number of items per page. Example: 10.
     * @queryParam search string Search by title,author_name,job_title Example: Jhon
     * @queryParam publish boolean Filter by publish status. Example: true
     *
     * @response 200 {
     *   "status": true,
     *   "message": "task_fetched_successfully",
     *     "data": [
     *              {
     *                  "id": 9,
     *                  "author_name": "Mrs. Petra Pollich",
     *                  "job_title": "Credit Checkers Clerk",
     *                  "title": "Voluptatibus quidem minima atque.",
     *                  "content": "Temporibus tempora eum nesciunt doloremque. ae nostrum.",
     *                  "rating": 5,
     *                  "avatar": "https://placehold.co/100x100",
     *                  "avatar_url": "https://placehold.co/100x100",
     *                  "status": true,
     *                  "created_at": "2025-08-28T05:09:49.000000Z",
     *                  "updated_at": "2025-08-28T05:09:49.000000Z"
     *              }
     *             ],
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
                ->isCompletedFilter($request->is_completed)
                ->searchFilter($request->search)
                ->orderBy('id', 'DESC')
                ->paginate($perPage);

        return success_response(TaskResource::collection($tasks), true, 'task_fetched_successfully');
    }

    /**
     * Store a new task
     *
     * @param StoreTaskRequest $request
     * @return JsonResponse
     *
     * @response 201 {
     *  "status": true,
     *  "message": "success_task_created",
     *  "data": {
     *      "id": 21,
     *      "author_name": "author_name",
     *      "job_title": "job_title",
     *      "title": "title",
     *      "content": "content",
     *      "rating": 5,
     *      "avatar": "task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "avatar_url": "http://localhost:8000/storage/task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "publish": true,
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->safe()->except('avatar');

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->uploadFile(
                    $request->avatar,
                    'task/avatar',
                    config('filesystems.default'),
                    null,
                    self::UPLOAD_OPTIONS
                );
            }

            $task = Task::create($data);
            DB::commit();

            return success_response(
                new TaskResource($task->makeHidden('avatar_url')->append('avatar_url')),
                false,
                'success_task_created'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return error_response('task_creation_failed', 500);
        }
    }

    /**
     * Show a single task
     *
     * @param Task $task
     *
     * @response 200 {
     *   "status": true,
     *   "message": "task_fetched_successfully",
     *     "data": {
     *                  "id": 9,
     *                  "author_name": "Mrs. Petra Pollich",
     *                  "job_title": "Credit Checkers Clerk",
     *                  "title": "Voluptatibus quidem minima atque.",
     *                  "content": "Temporibus tempora eum nesciunt doloremque. ae nostrum.",
     *                  "rating": 5,
     *                  "avatar": "https://placehold.co/100x100",
     *                  "avatar_url": "https://placehold.co/100x100",
     *                  "publish": true,
     *                  "created_at": "2025-08-28T05:09:49.000000Z",
     *                  "updated_at": "2025-08-28T05:09:49.000000Z"
     *              }
     * }
     */
    public function show(Task $task): JsonResponse
    {
        return success_response(
            new TaskResource($task),
            false,
            'task_fetched_successfully'
        );
    }

    /**
     * Update an existing task
     *
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return JsonResponse
     *
     * @response 200 {
     *  "status": true,
     *  "message": "success_task_updated",
     *  "data": {
     *      "id": 21,
     *      "author_name": "Jane Smith",
     *      "job_title": "Product Manager",
     *      "title": "Outstanding Support!",
     *      "content": "The team provided exceptional support throughout the project.",
     *      "rating": 5,
     *      "avatar": "task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "avatar_url": "http://localhost:8000/storage/task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "status": true,
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->safe()->except('avatar');

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->uploadFile(
                    $request->avatar,
                    'task/avatar',
                    config('filesystems.default'),
                    null,
                    self::UPLOAD_OPTIONS
                );
            }

            $task->update($data);
            DB::commit();

            return success_response(
                new TaskResource($task->fresh()),
                false,
                'success_task_updated'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return error_response('task_update_failed', 500);
        }
    }

    /**
     * Delete a task with soft-delete and data masking
     *
     * Soft delete the specified task.  
     * The record is not permanently removed; instead, its `deleted_at` timestamp is set  
     * and the `title` & `author_name` is updated with a `DELETED_` prefix.
     *
     * @param Task $task
     *
     * @response 200 {
     *   "status": true,
     *   "message": "success_task_deleted",
     *   "data": []
     * }
     */
    public function destroy(Task $task): JsonResponse
    {
        TaskArchives::create([
            'original_task_id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'avatar' => $task->avatar,
            'is_completed' => $task->is_completed,
        ]);
        
        $task->delete();

        return success_response([], false, 'success_task_deleted');
    }

    /**
     * Toggle publish status of a task
     *
     * This endpoint switches the `publish` status of the given Task.
     * If it was published, it will be unpublished, and vice versa.
     *
     * @param Task $task
     *
     * @response 200 {
     *  "status": true,
     *  "message": "success_task_updated",
     *  "data": {
     *      "id": 21,
     *      "author_name": "Jane Smith",
     *      "job_title": "Product Manager",
     *      "title": "Outstanding Support!",
     *      "content": "The team provided exceptional support throughout the project.",
     *      "rating": 5,
     *      "avatar": "task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "avatar_url": "http://localhost:8000/storage/task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "status": true,
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     */
    public function toggleCompleted(Task $task): JsonResponse
    {
        $task->update(['is_completed' => !$task->is_completed]);

        return success_response(
            new TaskResource($task->fresh()),
            false,
            'success_task_updated'
        );
    }
   
}
