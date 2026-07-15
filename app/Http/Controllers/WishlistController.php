<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Course;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::where('user_id', auth()->id())->with('course')->latest()->get();
        return view('wishlist.index', compact('wishlist'));
    }

    public function toggle(Request $request, Course $course)
    {
        $item = Wishlist::where('user_id', auth()->id())->where('course_id', $course->id)->first();
        if ($item) {
            $item->delete();
            return response()->json(['status' => 'removed', 'message' => 'Removed from wishlist']);
        }
        Wishlist::create(['user_id' => auth()->id(), 'course_id' => $course->id]);
        return response()->json(['status' => 'added', 'message' => 'Added to wishlist']);
    }

    public function destroy(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== auth()->id()) abort(403);
        $wishlist->delete();
        return response()->json(['message' => 'Removed from wishlist']);
    }
}
