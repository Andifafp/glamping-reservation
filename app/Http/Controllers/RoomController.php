<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Daftar dan detail kamar untuk pengunjung. Penanggung jawab: Fais.
 */
class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('id')->get();
        $activeCategory = $categories->firstWhere('slug', $request->query('kategori'))?->slug;

        $rooms = RoomType::active()
            ->with('category')
            ->when($activeCategory, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $activeCategory)))
            ->orderBy('price_per_night')
            ->get();

        // Cek ketersediaan hanya jika kedua tanggal diisi dan valid
        $validator = Validator::make($request->only('check_in', 'check_out'), [
            'check_in'  => ['nullable', 'required_with:check_out', 'date', 'after_or_equal:today'],
            'check_out' => ['nullable', 'required_with:check_in', 'date', 'after:check_in'],
        ], [], [
            'check_in'  => 'tanggal check-in',
            'check_out' => 'tanggal check-out',
        ]);

        $checkIn = $checkOut = null;

        if ($validator->passes() && $request->filled(['check_in', 'check_out'])) {
            $checkIn = $request->date('check_in')->toDateString();
            $checkOut = $request->date('check_out')->toDateString();

            $rooms->each(function (RoomType $room) use ($checkIn, $checkOut) {
                $room->available_units = $room->availableUnits($checkIn, $checkOut);
            });
        }

        return view('rooms.index', compact('categories', 'rooms', 'activeCategory', 'checkIn', 'checkOut'))
            ->withErrors($validator);
    }

    public function show(RoomType $roomType): View
    {
        abort_unless($roomType->is_active, 404);

        $roomType->load('category');

        $otherRooms = RoomType::active()
            ->with('category')
            ->whereKeyNot($roomType->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('rooms.show', [
            'room'       => $roomType,
            'otherRooms' => $otherRooms,
        ]);
    }
}
