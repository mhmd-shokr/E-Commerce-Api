<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(){
        $notification=Auth::user()->notifications;
        return response()->json(["Notification"=>$notification]);
    }

    public function unRead(){
        $unReadNotification=Auth::user()->unreadNotifications;
        return response()->json(["Notification"=>$unReadNotification]);
    }

    public function markAsRead($id){
        $notification=Auth::user()->notifications->findOrFail($id);
        $markAsReadNotification=$notification->markAsRead();
        return response()->json(["Notification"=>$markAsReadNotification]);
    }
}
