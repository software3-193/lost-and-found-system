# ميزة Auth — نظام المفقودات (Laravel + XAMPP)

## 1) المتطلبات
- XAMPP (Apache + MySQL) — يفضّل PHP 8.2 أو أحدث
- Composer: https://getcomposer.org

## 2) إنشاء مشروع Laravel
افتح CMD داخل `C:\xampp\htdocs` ونفّذ:

    composer create-project laravel/laravel lostfound
    cd lostfound

## 3) انسخ ملفات هالمجلد فوق المشروع
انسخ مجلدات `app` و `database` و `resources` و `routes` و `public`
إلى مجلد `lostfound` ووافق على استبدال الملفات الموجودة (User.php و web.php و DatabaseSeeder.php).

## 4) قاعدة البيانات
1. شغّل Apache و MySQL من لوحة XAMPP.
2. افتح http://localhost/phpmyadmin وأنشئ قاعدة بيانات اسمها `lostfound`
   (الترميز utf8mb4_unicode_ci).
3. عدّل ملف `.env`:

       DB_CONNECTION=mysql
       DB_HOST=127.0.0.1
       DB_PORT=3306
       DB_DATABASE=lostfound
       DB_USERNAME=root
       DB_PASSWORD=

   وتأكد أن `SESSION_DRIVER=database` (الافتراضي) أو غيّره إلى `file` إذا واجهت مشكلة.

## 5) التشغيل

    php artisan migrate --seed
    php artisan serve

افتح http://127.0.0.1:8000

(أو بدون serve: افتح http://localhost/lostfound/public)

## 6) حساب المدير التجريبي
- البريد: admin@lostfound.test
- كلمة المرور: Admin@12345

## ما الذي تم تنفيذه
- تسجيل حساب طالب (Register) مع تحقق من المدخلات
- تسجيل الدخول والخروج + "تذكرني"
- أدوار: student / admin (عمود role)
- Middleware للأدوار: `RoleMiddleware::class.':admin'`
- حماية الصفحات: guest للتسجيل والدخول، auth للباقي
- واجهة عربية RTL
