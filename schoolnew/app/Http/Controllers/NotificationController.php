<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
	public function index()
	{
		$notifications = Auth::user()->notifications()->latest()->take(15)->get();
		$unreadCount = Auth::user()->unreadNotifications()->count();

		return response()->json([
			'unread_count' => $unreadCount,
			'notifications' => $notifications->map(fn($n) => [
				'id' => $n->id,
				'data' => $n->data,
				'read_at' => $n->read_at,
				'created_at' => $n->created_at->diffForHumans(),
			]),
		]);
	}

	public function markAsRead($id)
	{
		$notification = Auth::user()->notifications()->findOrFail($id);
		$notification->markAsRead();

		return response()->json(['success' => true, 'url' => $notification->data['url'] ?? '#']);
	}

	public function markAllAsRead()
	{
		Auth::user()->unreadNotifications->markAsRead();

		return response()->json(['success' => true]);
	}
}
