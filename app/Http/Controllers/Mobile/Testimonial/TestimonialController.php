<?php

namespace App\Http\Controllers\Mobile\Testimonial;

use App\Models\Testimonial;
use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** 
 * @group Testimonial
 * @authenticated
 */
class TestimonialController extends Controller
{
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
     *                  "publish": true,
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
            $searchFields = ['title', 'author_name', 'job_title'];
            $lang = app('language');
            $perPage = min(max((int) $request->input('per_page', 10), 1), 100);
            $testimonial = Testimonial::query()
                ->withTranslations($lang)
                ->where('testimonials.publish', true)
                ->searchTranslations($request->search, $searchFields)
                ->orderBy('id', 'DESC')
                ->paginate($perPage);

        return success_response(TestimonialResource::collection($testimonial), true, 'testimonial_fetched_successfully');
    }

    /**
     * Show
     *
     * @param Testimonial $testimonial
     *
     * @response {
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
}
