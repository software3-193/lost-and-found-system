@extends('layouts.app')
@section('title', 'الرئيسية')
@section('wrap', 'wide')
@section('content')
<div class="dashboard-head">
    <div>
        <h1>أهلاً {{ auth()->user()->name }}</h1>
        <p class="sub">دورك في النظام: <span class="badge">{{ auth()->user()->isAdmin() ? 'مدير' : 'طالب' }}</span></p>
    </div>
    @if(auth()->user()->isAdmin())
        <a class="btn btn-link" href="{{ route('admin.home') }}">لوحة إدارة المدير</a>
    @endif
</div>

@if(session('success'))<div class="success-alert" role="status">{{ session('success') }}</div>@endif
@if($errors->has('claim'))<div class="alert">{{ $errors->first('claim') }}</div>@endif

<section class="panel section-panel" id="add-item">
    <div class="section-heading">
        <div><h2>إضافة بلاغ جديد</h2><p class="muted">سيتم إرسال البلاغ إلى المدير للمراجعة قبل نشره.</p></div>
    </div>
    <form method="POST" action="{{ route('items.store') }}" class="form-grid">
        @csrf
        <div>
            <label for="type">نوع البلاغ</label>
            <select id="type" name="type" required>
                <option value="lost" @selected(old('type') === 'lost')>غرض مفقود — أبحث عنه</option>
                <option value="found" @selected(old('type') === 'found')>غرض تم العثور عليه</option>
            </select>
            @error('type')<p class="err">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="title">اسم الغرض</label>
            <input type="text" id="title" name="title" maxlength="120" value="{{ old('title') }}" placeholder="مثال: حقيبة سوداء" required>
            @error('title')<p class="err">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="category">الفئة</label>
            <select id="category" name="category" required>
                <option value="">اختر الفئة</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            @error('category')<p class="err">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="item_date">تاريخ الفقد أو العثور</label>
            <input type="date" id="item_date" name="item_date" max="{{ now()->toDateString() }}" value="{{ old('item_date') }}" required>
            @error('item_date')<p class="err">{{ $message }}</p>@enderror
        </div>
        <div class="full-field">
            <label for="location">المكان</label>
            <input type="text" id="location" name="location" maxlength="150" value="{{ old('location') }}" placeholder="مثال: مكتبة الجامعة" required>
            @error('location')<p class="err">{{ $message }}</p>@enderror
        </div>
        <div class="full-field">
            <label for="description">وصف الغرض</label>
            <textarea id="description" name="description" rows="4" maxlength="3000" placeholder="اكتب تفاصيل تساعد على التعرف على الغرض..." required>{{ old('description') }}</textarea>
            @error('description')<p class="err">{{ $message }}</p>@enderror
        </div>
        <div class="full-field"><button type="submit" class="btn">إرسال البلاغ للمراجعة</button></div>
    </form>
</section>

<section class="section-block">
    <div class="section-heading"><div><h2>قائمة المفقودات والمعثورات</h2><p class="muted">ابحث حسب الفئة والتاريخ، أو استخدم الكلمات المفتاحية.</p></div><span class="count-pill">{{ $items->total() }} بلاغ منشور</span></div>
    <form method="GET" action="{{ route('dashboard') }}" class="filter-panel">
        <div><label for="q">بحث</label><input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="اسم الغرض أو الوصف أو المكان"></div>
        <div><label for="filter-category">الفئة</label><select id="filter-category" name="category"><option value="">كل الفئات</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>@endforeach</select></div>
        <div><label for="filter-type">نوع البلاغ</label><select id="filter-type" name="type"><option value="">الكل</option><option value="lost" @selected(($filters['type'] ?? '') === 'lost')>مفقود</option><option value="found" @selected(($filters['type'] ?? '') === 'found')>تم العثور عليه</option></select></div>
        <div><label for="date_from">من تاريخ</label><input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></div>
        <div><label for="date_to">إلى تاريخ</label><input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></div>
        <div class="filter-actions"><button class="btn" type="submit">بحث</button><a class="reset-link" href="{{ route('dashboard') }}">إعادة ضبط</a></div>
    </form>

    @if($items->count())
        <div class="item-grid">
            @foreach($items as $item)
                <article class="item-card">
                    <div class="item-card-top"><span class="type-pill {{ $item->type === 'lost' ? 'lost' : 'found' }}">{{ $item->type === 'lost' ? 'غرض مفقود' : 'غرض تم العثور عليه' }}</span><span class="muted">{{ $item->item_date->format('Y-m-d') }}</span></div>
                    <h3>{{ $item->title }}</h3>
                    <p class="item-category">{{ $item->category }}</p>
                    <p class="item-description">{{ $item->description }}</p>
                    <div class="item-meta"><span>المكان: {{ $item->location }}</span><span>أضيف بواسطة: {{ $item->user->name }}</span></div>
                    @if($item->type === 'found' && $item->user_id !== auth()->id() && !$myClaims->contains('item_id', $item->id))
                        <details class="claim-details">
                            <summary>هذا الغرض يخصني</summary>
                            <form method="POST" action="{{ route('items.claim', $item) }}">
                                @csrf
                                <label for="proof-{{ $item->id }}">أثبت ملكيتك للغرض</label>
                                <textarea id="proof-{{ $item->id }}" name="proof" rows="3" minlength="20" maxlength="3000" placeholder="اذكر تفاصيل خاصة بالغرض لا يعرفها إلا صاحبه..." required></textarea>
                                <button class="btn" type="submit">إرسال طلب الملكية</button>
                            </form>
                        </details>
                    @elseif($item->type === 'found' && $myClaims->contains('item_id', $item->id))
                        <p class="muted claim-sent">لقد أرسلت طلب ملكية لهذا الغرض.</p>
                    @endif
                </article>
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $items->links() }}</div>
    @else
        <div class="empty-state"><strong>لا توجد بلاغات مطابقة.</strong><p>جرّب تغيير الفئة أو التاريخ أو أضف بلاغاً جديداً.</p></div>
    @endif
</section>

<section class="section-block">
    <div class="section-heading"><div><h2>بلاغاتي</h2><p class="muted">حالة البلاغات التي أرسلتها إلى النظام.</p></div></div>
    @if($myItems->isNotEmpty())
        <div class="table-wrap"><table><thead><tr><th>الغرض</th><th>النوع</th><th>التاريخ</th><th>الحالة</th><th>ملاحظة المدير</th></tr></thead><tbody>
        @foreach($myItems as $myItem)<tr><td>{{ $myItem->title }}</td><td>{{ $myItem->type === 'lost' ? 'مفقود' : 'تم العثور عليه' }}</td><td>{{ $myItem->item_date->format('Y-m-d') }}</td><td><span class="status-pill status-{{ $myItem->status }}">{{ ['pending'=>'قيد المراجعة','published'=>'منشور','rejected'=>'مرفوض','returned'=>'تم التسليم'][$myItem->status] }}</span></td><td>{{ $myItem->admin_note ?: '—' }}</td></tr>@endforeach
        </tbody></table></div>
    @else
        <div class="empty-state compact">لم ترسل أي بلاغات بعد.</div>
    @endif
</section>

<section class="section-block">
    <div class="section-heading"><div><h2>طلبات إثبات الملكية</h2><p class="muted">تابع طلباتك المقدمة للأغراض التي تم العثور عليها.</p></div></div>
    @if($myClaims->isNotEmpty())
        <div class="table-wrap"><table><thead><tr><th>الغرض</th><th>تاريخ الطلب</th><th>الحالة</th><th>ملاحظة المدير</th></tr></thead><tbody>
        @foreach($myClaims as $claim)<tr><td>{{ $claim->item->title }}</td><td>{{ $claim->created_at->format('Y-m-d') }}</td><td><span class="status-pill status-{{ $claim->status }}">{{ ['pending'=>'قيد المراجعة','approved'=>'تم تأكيد الملكية','rejected'=>'مرفوض','delivered'=>'تم التسليم'][$claim->status] }}</span></td><td>{{ $claim->admin_note ?: '—' }}</td></tr>@endforeach
        </tbody></table></div>
    @else
        <div class="empty-state compact">لا توجد طلبات ملكية حتى الآن.</div>
    @endif
</section>
@endsection
