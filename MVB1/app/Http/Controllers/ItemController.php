<?php

namespace App\Http\Controllers;

use App\Models\ClaimRequest;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'category' => ['nullable', 'string', 'max:60'],
            'type' => ['nullable', 'in:lost,found'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $items = Item::query()
            ->with('user:id,name')
            ->where('status', Item::STATUS_PUBLISHED)
            ->when($filters['category'] ?? null, fn ($query, $value) => $query->where('category', $value))
            ->when($filters['type'] ?? null, fn ($query, $value) => $query->where('type', $value))
            ->when($filters['date_from'] ?? null, fn ($query, $value) => $query->whereDate('item_date', '>=', $value))
            ->when($filters['date_to'] ?? null, fn ($query, $value) => $query->whereDate('item_date', '<=', $value))
            ->when($filters['q'] ?? null, fn ($query, $value) => $query->where(function ($nested) use ($value) {
                $nested->where('title', 'like', "%{$value}%")
                    ->orWhere('description', 'like', "%{$value}%")
                    ->orWhere('location', 'like', "%{$value}%");
            }))
            ->latest('item_date')
            ->paginate(9)
            ->withQueryString();

        $myClaims = ClaimRequest::query()
            ->with('item:id,title,category,type')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $myItems = Item::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('dashboard', [
            'items' => $items,
            'myClaims' => $myClaims,
            'myItems' => $myItems,
            'categories' => Item::CATEGORIES,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:lost,found'],
            'title' => ['required', 'string', 'max:120'],
            'category' => ['required', 'in:'.implode(',', Item::CATEGORIES)],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            'location' => ['required', 'string', 'max:150'],
            'item_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $request->user()->items()->create($data + ['status' => Item::STATUS_PENDING]);

        return redirect()->route('dashboard')->with('success', 'تم إرسال البلاغ بنجاح، وسيظهر بعد مراجعة المدير.');
    }

    public function claim(Request $request, Item $item): RedirectResponse
    {
        abort_unless($item->type === Item::TYPE_FOUND && $item->status === Item::STATUS_PUBLISHED, 404);
        abort_if($item->user_id === $request->user()->id, 403, 'لا يمكنك تقديم طلب ملكية لغرض أضفته بنفسك.');

        $data = $request->validate([
            'proof' => ['required', 'string', 'min:20', 'max:3000'],
        ], [
            'proof.min' => 'يرجى تقديم تفاصيل كافية لإثبات ملكيتك للغرض (20 حرفاً على الأقل).',
        ]);

        $existing = ClaimRequest::where('item_id', $item->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['claim' => 'سبق أن قدمت طلباً لهذا الغرض.'])->withInput();
        }

        $item->claims()->create([
            'user_id' => $request->user()->id,
            'proof' => $data['proof'],
            'status' => ClaimRequest::STATUS_PENDING,
        ]);

        return redirect()->route('dashboard')->with('success', 'تم إرسال طلب إثبات الملكية إلى المدير للمراجعة.');
    }
}
