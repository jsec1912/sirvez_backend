<?php

use Illuminate\Support\Facades\Broadcast;
//use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('presence-online-user', function ($user) {    
    //Log::info('online-user channel: '.$user->email);
    return $user;    
});

Broadcast::channel('online-user', function ($user) {    
    //Log::info('online-user channel: '.$user->email);
    return $user;    
});
