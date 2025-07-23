<?php
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}', function ($user, $userId) {
return (int) $user->id === (int) $userId;
});

Broadcast::channel('typing.{typerId}', function ($user, $userId) {
return true;
});
