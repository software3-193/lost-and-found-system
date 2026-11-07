@extends('layouts.app')
@section('title', 'لوحة المدير')
@section('wrap', 'wide')
@section('content')
<div class="dashboard-head"><div><h1>لوحة المدير</h1><p class="sub">مراجعة البلاغات وطلبات إثبات الملكية والإشراف على تسليم الأغراض.</p></div><a class="btn btn-link" href="{{ route('dashboard') }}">العودة إلى النظام</a></div>
@if(session('success'))<div class="success-alert" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert"><ul class="error-list">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<section class="section-block">
    <div class="section-heading"><div><h2>مراجعة البلاغات</h2><p class="muted">وافق على البلاغ لنشره في قائمة المفقودات والمعثورات، أو ارفضه.</p></div><span class="count-pill">{{ $items->total() }} بلاغ</span></div>
    @if($items->isNotEmpty())
        <div class="admin-list">
            @foreach($items as $item)
                <article class="admin-card">
                    <div class="admin-card-content">
                        <div class="item-card-top"><span class="type-pill {{ $item->type === 'lost' ? 'lost' : 'found' }}">{{ $item->type === 'lost' ? 'غرض مفقود' : 'غرض تم العثور عليه' }}</span><span class="status-pill status-{{ $item->status }}">{{ ['pending'=>'قيد المراجعة','published'=>'منشور','rejected'=>'مرفوض','returned'=>'تم التسليم'][$item->status] }}</span></div>
                        <h3>{{ $item->title }}</h3>
                        <p class="muted">{{ $item->category }} · {{ $item->item_date->format('Y-m-d') }} · {{ $item->location }}</p>
                        <p>{{ $item->description }}</p>
                        <p class="muted">مقدم البلاغ: {{ $item->user->name }} ({{ $item->user->email }})</p>
                        @if($item->admin_note)<p class="admin-note">ملاحظتك السابقة: {{ $item->admin_note }}</p>@endif
                    </div>
                    @if(in_array($item->status, ['pending', 'published', 'rejected']))
                        <form method="POST" action="{{ route('admin.items.status', $item) }}" class="admin-action-form">
                            @csrf @method('PATCH')
                            <label for="item-note-{{ $item->id }}">ملاحظة للطالب (اختياري)</label>
                            <textarea id="item-note-{{ $item->id }}" name="admin_note" rows="2" maxlength="1000">{{ $item->admin_note }}</textarea>
                            <div class="inline-actions">
                                <button class="btn" name="status" value="published" type="submit">{{ $item->status === 'published' ? 'تأكيد النشر' : 'موافقة ونشر' }}</button>
                                <button class="btn btn-danger" name="status" value="rejected" type="submit">رفض البلاغ</button>
                            </div>
                        </form>
                    @endif
                </article>
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $items->links() }}</div>
    @else
        <div class="empty-state">لا توجد بلاغات حتى الآن.</div>
    @endif
</section>

<section class="section-block">
    <div class="section-heading"><div><h2>طلبات إثبات الملكية</h2><p class="muted">راجع وصف الطالب وتأكد من ملكيته قبل الموافقة على التسليم.</p></div><span class="count-pill">{{ $claims->total() }} طلب</span></div>
    @if($claims->isNotEmpty())
        <div class="admin-list">
            @foreach($claims as $claim)
                <article class="admin-card">
                    <div class="admin-card-content">
                        <div class="item-card-top"><h3>{{ $claim->item->title }}</h3><span class="status-pill status-{{ $claim->status }}">{{ ['pending'=>'قيد المراجعة','approved'=>'تم تأكيد الملكية','rejected'=>'مرفوض','delivered'=>'تم التسليم'][$claim->status] }}</span></div>
                        <p class="muted">{{ $claim->item->category }} · {{ $claim->item->location }} · تاريخ العثور: {{ $claim->item->item_date->format('Y-m-d') }}</p>
                        <p><strong>مقدم الطلب:</strong> {{ $claim->user->name }} ({{ $claim->user->email }})</p>
                        <div class="proof-box"><strong>تفاصيل إثبات الملكية:</strong><p>{{ $claim->proof }}</p></div>
                        @if($claim->admin_note)<p class="admin-note">ملاحظتك السابقة: {{ $claim->admin_note }}</p>@endif
                    </div>
                    @if($claim->status === 'pending')
                        <form method="POST" action="{{ route('admin.claims.status', $claim) }}" class="admin-action-form">
                            @csrf @method('PATCH')
                            <label for="claim-note-{{ $claim->id }}">ملاحظة للطالب (اختياري)</label>
                            <textarea id="claim-note-{{ $claim->id }}" name="admin_note" rows="2" maxlength="1000">{{ $claim->admin_note }}</textarea>
                            <div class="inline-actions">
                                <button class="btn" name="status" value="approved" type="submit">تأكيد الملكية</button>
                                <button class="btn btn-danger" name="status" value="rejected" type="submit">رفض الطلب</button>
                            </div>
                        </form>
                    @elseif($claim->status === 'approved')
                        <form method="POST" action="{{ route('admin.claims.delivered', $claim) }}" class="admin-action-form" onsubmit="return confirm('هل تؤكد أنك سلّمت الغرض لصاحبه؟')">
                            @csrf @method('PATCH')
                            <button class="btn" type="submit">تأكيد تسليم الغرض</button>
                        </form>
                    @endif
                </article>
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $claims->links() }}</div>
    @else
        <div class="empty-state">لا توجد طلبات إثبات ملكية حتى الآن.</div>
    @endif
</section>
@endsection
