<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\UploadAble;
use getID3;

/**
 * @group Upload Module
 * @authenticated
 */
class MediaController extends Controller
{
    use UploadAble;
    /**
     * File
     *
     * Upload an image, audio, or PDF file.  
     * - Images will be resized/cropped and optimized.  
     * - MP3 files will be analyzed to include duration metadata.  
     *
     * @bodyParam file file required The file to upload. Allowed types: jpg, jpeg, png, webp, pdf, mp3. Max size: 50MB.
     *
     * @response 200 scenario="Image uploaded successfully" {
     *   "message": "File uploaded successfully",
     *   "path": "http://localhost/storage/uploads/example.jpg",
     *   "duration_seconds": null,
     *   "duration_hms": null
     * }
     *
     * @response 200 scenario="MP3 uploaded successfully" {
     *   "message": "File uploaded successfully",
     *   "path": "http://localhost/storage/uploads/song.mp3",
     *   "duration_seconds": 123,
     *   "duration_hms": "02:03"
     * }
     */
    public function upload(Request $request)
    {
        // ✅ Validate file first
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf,mp3|max:51200', // 50MB limit
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        // ✅ If it's audio, analyze duration with getID3
        $duration = null;
        $formattedDuration = null;

        if ($extension === 'mp3') {
            $getID3 = new getID3;
            $info = $getID3->analyze($file->getRealPath());
            $duration = $info['playtime_seconds'] ?? null;
            $formattedDuration = $info['playtime_string'] ?? null;
        }

        // ✅ Handle upload options (only for images)
        $imageTypes = ['jpg', 'jpeg', 'png', 'webp'];
        $options = [];

        if (in_array($extension, $imageTypes)) {
            $options = [
                'resize' => ['width' => 800, 'height' => 600],
                'crop' => ['width' => 300, 'height' => 300, 'x' => 50, 'y' => 50],
                'quality' => 85,
            ];
        }

        // ✅ Upload the file using trait
        $path = $this->uploadFile($file, 'uploads', 'public', null, $options);

        // ✅ Build response
        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => asset('storage/' . $path),
            'duration_seconds' => $duration,
            'duration_hms' => $formattedDuration,
        ]);
    }
}
