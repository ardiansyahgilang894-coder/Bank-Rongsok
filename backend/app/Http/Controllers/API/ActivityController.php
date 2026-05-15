<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Activity::with('images', 'createdBy')
                ->latest()
                ->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'nullable|string',
            'status' => 'required|in:planning,ongoing,completed,cancelled',
        ]);

        $validated['created_by'] = Auth::id();

        $activity = Activity::create($validated);

        return response()->json($activity->load('images', 'createdBy'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        return response()->json($activity->load('images', 'createdBy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        $this->authorize('update', $activity);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'date' => 'date',
            'location' => 'nullable|string',
            'status' => 'in:planning,ongoing,completed,cancelled',
        ]);

        $activity->update($validated);

        return response()->json($activity->load('images', 'createdBy'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $this->authorize('delete', $activity);

        $activity->delete();

        return response()->json(['message' => 'Activity deleted successfully']);
    }

    /**
     * Upload images for activity
     */
    public function uploadImage(Request $request, Activity $activity)
    {
        Log::info('Upload image called for activity: ' . $activity->id);

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'caption' => 'nullable|string',
        ]);

        Log::info('Validation passed');

        if ($request->hasFile('image')) {
            Log::info('File found');

            try {
                $imageFile = $request->file('image');
                $originalName = $imageFile->getClientOriginalName();
                
                Log::info('Original filename: ' . $originalName);

                // Generate nama file unik dengan format webp
                $filename = Str::uuid() . '.webp';
                $path = 'activities/' . $filename;

                Log::info('Converting image to WebP');

                // Baca file ke memory
                $imagePath = $imageFile->getRealPath();
                
                // Tentukan mime type asli
                $mimeType = $imageFile->getMimeType();
                Log::info('MIME type: ' . $mimeType);

                // Load image berdasarkan tipe
                if (in_array($mimeType, ['image/jpeg', 'image/jpg'])) {
                    $image = imagecreatefromjpeg($imagePath);
                } elseif ($mimeType === 'image/png') {
                    $image = imagecreatefrompng($imagePath);
                } elseif ($mimeType === 'image/webp') {
                    $image = imagecreatefromwebp($imagePath);
                } else {
                    throw new \Exception('Unsupported image format');
                }

                if (!$image) {
                    throw new \Exception('Failed to load image');
                }

                Log::info('Image loaded, saving as WebP');

                // Simpan ke WebP
                $webpContent = ob_get_clean();
                ob_start();
                imagewebp($image, null, 80);
                $webpContent = ob_get_clean();
                imagedestroy($image);

                Log::info('Saving to storage');

                // Simpan ke storage/public/activities
                Storage::disk('public')->put($path, $webpContent);

                Log::info('Creating database record');

                ActivityImage::create([
                    'activity_id' => $activity->id,
                    'image_path' => $path,
                    'caption' => $validated['caption'] ?? null,
                ]);

                return response()->json([
                    'message' => 'Image uploaded successfully'
                ], 201);

            } catch (\Exception $e) {
                Log::error('Error uploading image: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Failed to upload image: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'message' => 'No image file found'
        ], 400);
    }

    /**
     * Delete image from activity gallery
     */
    public function deleteImage(Activity $activity, ActivityImage $image)
    {
        $this->authorize('update', $activity);

        // Pastikan image milik activity
        if ($image->activity_id !== $activity->id) {

            return response()->json([
                'message' => 'Image tidak ditemukan untuk kegiatan ini'
            ], 404);
        }

        // Hapus file jika ada
        if (
            $image->image_path &&
            Storage::disk('public')->exists($image->image_path)
        ) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Hapus record database
        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully'
        ]);
    }
}
