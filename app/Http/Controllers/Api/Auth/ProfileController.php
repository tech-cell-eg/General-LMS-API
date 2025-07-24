<?php
namespace App\Http\Controllers\Api\Auth;

use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\Auth\UserResource;
use App\Http\Requests\Auth\UpdateProfileRequest;

class ProfileController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $user = auth()->user()->load(['profile', 'instructor.links']);
        return $this->success(new UserResource($user), 'Profile retrieved successfully');
    }

    public function update(UpdateProfileRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = $request->user();
            $data = $request->validated();
            $profileData = [];
            $instructorData = [];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $this->updateAvatar($user, $request->file('avatar'));
            }

            // Separate profile data
            foreach (['bio', 'headline', 'language_preferences', 'social_links'] as $field) {
                if (isset($data[$field])) {
                    $profileData[$field] = $data[$field];
                    unset($data[$field]);
                }
            }

            // Update user data
            $user->update($data);

            // Update or create profile
            if (!empty($profileData)) {
                $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);
            }

            // Update instructor data if user is instructor
            if ($user->isInstructor() && isset($data['instructor'])) {
                $this->updateInstructorData($user, $data['instructor']);
            }

            return $this->success(
                new UserResource($user->fresh()->load(['profile', 'instructor', 'instructor.links'])),
                'Profile updated successfully'
            );
        });
    }

    protected function updateAvatar($user, $avatarFile)
    {
        // Delete old avatar if exists
        if ($user->avatar_url) {
            $oldAvatarPath = str_replace(asset('storage/'), '', $user->avatar_url);
            Storage::delete('public/' . $oldAvatarPath);
        }

        $path = $avatarFile->store('avatars', 'public');
        $user->update(['avatar_url' => asset('storage/' . $path)]);
    }

    protected function updateInstructorData($user, $instructorData)
    {
        $instructor = $user->instructor()->updateOrCreate(['user_id' => $user->id], [
            'title' => $instructorData['title'] ?? null,
            'professional_experience' => $instructorData['professional_experience'] ?? null,
        ]);

        // Update areas of expertise
        if (isset($instructorData['areas_of_expertise'])) {
            $instructor->update([
                'areas_of_expertise' => $instructorData['areas_of_expertise']
            ]);
        }

        // Update links if provided
        if (isset($instructorData['links'])) {
            $this->updateInstructorLinks($instructor, $instructorData['links']);
        }
    }

    protected function updateInstructorLinks($instructor, $links)
    {
        // Get existing link IDs
        $existingLinkIds = collect($links)->pluck('id')->filter()->toArray();

        // Delete links not present in the request
        $instructor->links()
            ->whereNotIn('id', $existingLinkIds)
            ->delete();

        // Update or create links
        foreach ($links as $link) {
            $linkData = [
                'title' => $link['title'],
                'url' => $link['url'],
                'icon_class' => $link['icon_class'] ?? null
            ];

            if (isset($link['id'])) {
                $instructor->links()
                    ->where('id', $link['id'])
                    ->update($linkData);
            } else {
                $instructor->links()->create($linkData);
            }
        }
    }

}