<?php

namespace App\Http\Controllers\Api\Testimonial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Testimonial\StoreTestimonialRequest;
use App\Http\Requests\Api\Testimonial\UpdateTestimonialRequest;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Traits\UploadAble;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

/** 
 * @group Testimonial Module
 * @subgroup Testimonial
 * @authenticated
 */
class TestimonialController extends Controller
{
    use UploadAble;
    protected $options;

    public function __construct()
    {
        $this->options = [
            // 'crop' => ['width' => 300, 'height' => 300, 'x' => 50, 'y' => 50],
            'resize' => ['width' => 800, 'height' => 600],
            'quality' => 85
        ];
    }
    /**
     * List
     *
     * @queryParam sort_by string The column to order by. Example: id
     * @queryParam sort_dir string Order direction (ASC|DESC). Example: DESC

     * @queryParam page integer Number of page. Example: 1.
     * @queryParam per_page integer Number of items per page. Example: 10.
     * @queryParam search string Search by title,author_name,job_title Example: Jhon
     * @queryParam publish boolean Comma-separated list of publish to filter by Filter by Example: true
     *
     * @response 200 {
     *   "status": true,
     *   "message": "testimonial_fetched_successfully",
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
        $testimonial = Testimonial::query()
            ->publishFilter($request->publish)
            ->searchFilter($request->search)
            ->orderBy('id', 'DESC')
            ->paginate($perPage);

        return success_response(TestimonialResource::collection($testimonial), true, 'testimonial_fetched_successfully');
    }

    /**
     * Store
     *
     * @param StoreTestimonialRequest $request
     * @return JsonResponse
     *
     * @response 201 {
     *  "status": true,
     *  "message": "success_story_created",
     *  "data": {
     *      "id": 21,
     *      "author_name": "author_name",
     *      "job_title": "job_title",
     *      "title": "title",
     *      "content": "content",
     *      "rating": 5,
     *      "avatar": "testimonial/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "avatar_url": "http://localhost:8000/storage/testimonial/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "publish": true,
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     *
     */
    public function store(StoreTestimonialRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->safe()->except('avatar');
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->uploadFile($request->avatar, 'testimonial/avatar', config('filesystems.default'), null, $this->options);
            }

            $testimonial = Testimonial::create($data);
            DB::commit();
            return success_response(new TestimonialResource($testimonial->fresh()), false, 'success_story_created');
        } catch (Exception $e) {
            DB::rollBack();
            return error_response('testimonial_update_failed', 500);
        }
    }

    /**
     * Show
     *
     * @param Testimonial $testimonial
     *
     * @response 200 {
     *   "status": true,
     *   "message": "testimonial_fetched_successfully",
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
     *                  "publish": true,
     *                  "created_at": "2025-08-28T05:09:49.000000Z",
     *                  "updated_at": "2025-08-28T05:09:49.000000Z"
     *              }
     *             ],
     * }
     */
    public function show(Testimonial $testimonial): JsonResponse
    {
        return success_response(new TestimonialResource($testimonial), false, 'testimonial_fetched_successfully');
    }

    /**
     * Update
     *
     * @param UpdateTestimonialRequest $request
     * @param Testimonial $testimonial
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *  "status": true,
     *  "message": "success_story_updated",
     *  "data": {
     *      "id": 21,
     *      "author_name": "Jane Smith",
     *      "job_title": "Product Manager",
     *      "title": "Outstanding Support!",
     *      "content": "The team provided exceptional support throughout the project.",
     *      "rating": 5,
     *      "avatar": "testimonial/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "avatar_url": "http://localhost:8000/storage/testimonial/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "status": true,
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     *
     */
    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->safe()->except('avatar');
            if ($request->hasFile('avatar')) {


                $data['avatar'] = $this->uploadFile(
                    $request->avatar,
                    'testimonial/avatar',
                    config('filesystems.default'),
                    null,
                    $this->options
                );
            }

            $testimonial->update($data);

            DB::commit();

            return success_response(
                new TestimonialResource($testimonial->fresh()),
                false,
                'success_story_updated'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return error_response('testimonial_update_failed', 500);
        }
    }

    /**
     * Delete
     *
     * Soft delete the specified testimonial.  
     * The record is not permanently removed; instead, its `deleted_at` timestamp is set  
     * and the `title` & `author_name` is updated with a `DELETED_` prefix to indicate it has been deleted.
     * @param Testimonial $testimonial
     *
     * @response 200 {
     *   "status": true,
     *   "message": "success_story_deleted",
     *   "data": []
     * }
     *
     */

    public function destroy(Testimonial $testimonial): JsonResponse
    {
        $testimonial->title = 'DELETED_' . $testimonial->title;
        $testimonial->author_name = 'DELETED_' . $testimonial->author_name;
        $testimonial->save();
        $testimonial->delete();

        return success_response([], false, 'success_story_deleted');
    }
    /**
     * Publish
     *
     * This endpoint switches the `publish` status of the given Testimonial.
     * If it was published, it will be unpublished, and vice versa.
     *
     * @param Testimonial $testimonial
     *
     * @response 200 {
     *  "status": true,
     *  "message": "success_story_updated",
     *  "data": {
     *      "id": 21,
     *      "author_name": "Jane Smith",
     *      "job_title": "Product Manager",
     *      "title": "Outstanding Support!",
     *      "content": "The team provided exceptional support throughout the project.",
     *      "rating": 5,
     *      "avatar": "testimonial/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "avatar_url": "http://localhost:8000/storage/testimonial/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "status": true,
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     */
    public function togglePublish(Testimonial $testimonial): JsonResponse
    {
        $testimonial->publish = $testimonial->publish ? false : true;
        $testimonial->save();

        return success_response(new TestimonialResource($testimonial), false, 'testimonial_update_successfully');
    }
}
