<?php

namespace App\Http\Controllers;

use App\Models\ClaimRequest;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $items = Item::with('user:id,name')->latest()->paginate(10, ['*'], 'items_page');
        $claims = ClaimRequest::with(['item', 'user:id,name'])->latest()->paginate(10, ['*'], 'claims_page');

        return view('admin', compact('items', 'claims'));
    }

    public function updateItemStatus(Request $request, Item $item): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:published,rejected'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_if($item->status === Item::STATUS_RETURNED, 422, 'لا يمكن تعديل بلاغ تم تسليم غرضه.');

        $item->update([
            'status' => $data['status'],
            'admin_note' => $data['admin_note'] ?? null,
        ]);

        return back()->with('success', $data['status'] === 'published' ? 'تمت الموافقة على البلاغ ونشره.' : 'تم رفض البلاغ.');
    }

    public function updateClaimStatus(Request $request, ClaimRequest $claim): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_unless($claim->item->type === Item::TYPE_FOUND, 422, 'طلبات إثبات الملكية متاحة للأغراض التي تم العثور عليها فقط.');
        abort_unless($claim->item->status === Item::STATUS_PUBLISHED, 422, 'هذا الغرض غير متاح حالياً للمراجعة.');

        DB::transaction(function () use ($claim, $data) {
            $lockedItem = Item::whereKey($claim->item_id)->lockForUpdate()->firstOrFail();

            if ($data['status'] === ClaimRequest::STATUS_APPROVED) {
                $alreadyApproved = ClaimRequest::where('item_id', $lockedItem->id)
                    ->where('status', ClaimRequest::STATUS_APPROVED)
                    ->where('id', '!=', $claim->id)
                    ->exists();

                if ($alreadyApproved) {
                    abort(422, 'يوجد طلب ملكية آخر تمت الموافقة عليه لهذا الغرض.');
                }
            }

            $claim->update([
                'status' => $data['status'],
                'admin_note' => $data['admin_note'] ?? null,
            ]);
        });

        return back()->with('success', $data['status'] === 'approved' ? 'تم تأكيد الملكية. أكمل إجراء التسليم عند تسليم الغرض.' : 'تم رفض طلب إثبات الملكية.');
    }

    public function markDelivered(ClaimRequest $claim): RedirectResponse
    {
        DB::transaction(function () use ($claim) {
            $lockedClaim = ClaimRequest::whereKey($claim->id)->lockForUpdate()->firstOrFail();
            $item = Item::whereKey($lockedClaim->item_id)->lockForUpdate()->firstOrFail();

            abort_unless($lockedClaim->status === ClaimRequest::STATUS_APPROVED, 422, 'يجب تأكيد الملكية قبل تسجيل التسليم.');
            abort_unless($item->status === Item::STATUS_PUBLISHED, 422, 'تم إغلاق هذا البلاغ أو تسليمه مسبقاً.');

            $lockedClaim->update(['status' => ClaimRequest::STATUS_DELIVERED]);
            $item->update(['status' => Item::STATUS_RETURNED]);
        });

        return back()->with('success', 'تم تسجيل تسليم الغرض وإغلاق البلاغ.');
    }
}
