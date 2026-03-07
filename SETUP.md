# Hướng dẫn cài đặt TKB School

## Yêu cầu hệ thống
- **XAMPP** (PHP >= 8.1 + MySQL/MariaDB)
- **Composer** (https://getcomposer.org)
- **Node.js** >= 16 + npm (https://nodejs.org)
- **Git**

---

## BƯỚC 1: Export CSDL từ máy gốc (trước khi chuyển)

```bash
# Mở terminal trong thư mục XAMPP (hoặc cmd):
d:\xampp\mysql\bin\mysqldump -u root tkb_school > tkb_school.sql

# Hoặc dùng phpMyAdmin:
# Vào http://localhost/phpmyadmin → chọn DB "tkb_school" → Export → Go
# Lưu file tkb_school.sql
```

> ⚠️ Copy file `tkb_school.sql` vào thư mục gốc project trước khi push Git.

---

## BƯỚC 2: Clone project trên máy mới

```bash
git clone <URL_GITHUB> tkb_school
cd tkb_school
```

---

## BƯỚC 3: Cài dependencies

```bash
# Cài PHP packages
composer install

# Cài Node packages (cho Vite build assets)
npm install
```

---

## BƯỚC 4: Cấu hình môi trường

```bash
# Tạo file .env từ mẫu
copy .env.example .env

# Tạo APP_KEY mới
php artisan key:generate
```

Mở file `.env` và sửa phần database:
```env
DB_DATABASE=tkb_school
DB_USERNAME=root
DB_PASSWORD=
```

---

## BƯỚC 5: Import CSDL

### Cách 1: Dùng command line
```bash
# Tạo database trước
d:\xampp\mysql\bin\mysql -u root -e "CREATE DATABASE IF NOT EXISTS tkb_school"

# Import dữ liệu
d:\xampp\mysql\bin\mysql -u root tkb_school < tkb_school.sql
```

### Cách 2: Dùng phpMyAdmin
1. Mở http://localhost/phpmyadmin
2. Tạo database mới tên `tkb_school`
3. Chọn database → Import → Chọn file `tkb_school.sql` → Go

---

## BƯỚC 6: Build assets & chạy

```bash
# Build CSS/JS (chỉ cần 1 lần)
npm run build

# Xóa cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Chạy server
php artisan serve
```

Truy cập: http://localhost:8000/admin

---

## Tóm tắt nhanh (copy-paste)

```bash
git clone <URL> tkb_school
cd tkb_school
composer install
npm install
copy .env.example .env
php artisan key:generate
# Sửa .env: DB_DATABASE=tkb_school
# Import DB qua phpMyAdmin hoặc mysql CLI
npm run build
php artisan config:clear
php artisan serve
```

---

## Ghi chú
| Package            | Phiên bản | Mục đích                    |
|--------------------|-----------|------------------------------|
| PHP                | >= 8.1    | Runtime                     |
| Laravel            | 10.x     | Framework                   |
| Filament           | 3.2      | Admin panel                 |
| maatwebsite/excel  | 3.1      | Import/Export Excel          |
| Vite               | 4.x      | Build CSS/JS                |

### Cấu trúc CSDL (20 migrations)
- `users` — Tài khoản admin
- `teachers` — Giáo viên (quota, GVCN, ca dạy)
- `class_rooms` — Lớp học (ca sáng/chiều)
- `subjects` — Môn học (lý thuyết/thực hành)
- `schedules` — Lịch dạy
- `settings` — Cấu hình hệ thống
- `rooms` — Phòng chức năng
- `fixed_periods` — Tiết cố định (Chào cờ, SH)
- `subject_teacher` — Liên kết GV-Môn
- `room_subject` — Liên kết Phòng-Môn
