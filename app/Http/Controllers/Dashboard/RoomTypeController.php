<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD jenis kamar: kategori, stok unit, tarif per malam, foto. Penanggung jawab: Fais.
 * Foto disimpan di storage/app/public/room-types. Jalankan sekali: php artisan storage:link
 */
class RoomTypeController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        $categoryId = $request->integer('kategori') ?: null;

        $rooms = RoomType::with('category')
            ->withCount(['bookings as active_bookings_count' => fn ($q) => $q->holdingUnits()->where('check_out', '>', today())])
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('category_id')
            ->orderBy('price_per_night')
            ->get();

        return view('dashboard.room-types.index', compact('rooms', 'categories', 'categoryId'));
    }

    public function create(): View
    {
        return view('dashboard.room-types.form', [
            'room'       => new RoomType(['is_active' => true, 'capacity' => 2, 'total_units' => 1]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('room-types', 'public');
        }

        $room = RoomType::create($data);
        ActivityLog::record('room_type.created', "Menambah jenis kamar {$room->name}", $room, [
            'total_units' => $room->total_units, 'price_per_night' => $room->price_per_night,
        ]);

        return redirect()->route('dashboard.room-types.index')->with('status', "Jenis kamar {$room->name} ditambahkan.");
    }

    public function edit(RoomType $roomType): View
    {
        return view('dashboard.room-types.form', [
            'room'       => $roomType,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, RoomType $roomType): RedirectResponse
    {
        $data = $this->validated($request, $roomType);

        if ($data['name'] !== $roomType->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $roomType->id);
        }

        if ($request->boolean('remove_image') && $roomType->image) {
            Storage::disk('public')->delete($roomType->image);
            $data['image'] = null;
        }
        if ($request->hasFile('image')) {
            if ($roomType->image) {
                Storage::disk('public')->delete($roomType->image);
            }
            $data['image'] = $request->file('image')->store('room-types', 'public');
        }

        $roomType->fill($data);
        $changes = array_intersect_key($roomType->getDirty(), array_flip(['total_units', 'price_per_night', 'is_active']));
        $before = array_intersect_key($roomType->getOriginal(), $changes);
        $roomType->save();

        ActivityLog::record('room_type.updated', "Mengubah jenis kamar {$roomType->name}", $roomType, $changes ? [
            'sebelum' => $before, 'sesudah' => $changes,
        ] : []);

        return redirect()->route('dashboard.room-types.index')->with('status', "Jenis kamar {$roomType->name} diperbarui.");
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        // Jenis kamar yang punya riwayat booking tidak boleh dihapus (database menolak).
        if ($roomType->bookings()->exists()) {
            $roomType->update(['is_active' => false]);
            ActivityLog::record('room_type.deactivated', "Menonaktifkan jenis kamar {$roomType->name}", $roomType);

            return back()->with('status', "{$roomType->name} punya riwayat booking, jadi dinonaktifkan (tidak tampil di situs), bukan dihapus.");
        }

        $name = $roomType->name;
        if ($roomType->image) {
            Storage::disk('public')->delete($roomType->image);
        }
        ActivityLog::record('room_type.deleted', "Menghapus jenis kamar {$name}", null, ['name' => $name]);
        $roomType->delete();

        return back()->with('status', "Jenis kamar {$name} dihapus.");
    }

    private function validated(Request $request, ?RoomType $room = null): array
    {
        $data = $request->validate([
            'category_id'     => ['required', Rule::exists('categories', 'id')],
            'name'            => ['required', 'string', 'max:100', Rule::unique('room_types', 'name')->ignore($room?->id)],
            'description'     => ['nullable', 'string', 'max:2000'],
            'facilities'      => ['nullable', 'string', 'max:2000'],
            'capacity'        => ['required', 'integer', 'min:1', 'max:20'],
            'total_units'     => ['required', 'integer', 'min:0', 'max:500'],
            'price_per_night' => ['required', 'integer', 'min:10000', 'max:100000000'],
            'image'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.unique' => 'Nama jenis kamar ini sudah ada.',
            'image.max'   => 'Ukuran foto maksimal 2 MB.',
        ], [
            'category_id'     => 'kategori',
            'name'            => 'nama kamar',
            'capacity'        => 'kapasitas',
            'total_units'     => 'jumlah unit',
            'price_per_night' => 'tarif per malam',
            'image'           => 'foto',
        ]);

        unset($data['image']);
        // Fasilitas diketik satu per baris di form, disimpan sebagai array JSON
        $data['facilities'] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\n/', $data['facilities'] ?? ''))));
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'kamar';
        $slug = $base;
        for ($i = 2; RoomType::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
