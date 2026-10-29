# Grand Metropole Hotel & Resort — Hệ Thống Đặt Lịch & Quản Lý Dịch Vụ Khách Sạn 5 Sao

Hệ thống đặt lịch trực tuyến và điều phối dịch vụ nội khu dành cho khách sạn – khu nghỉ dưỡng 5 sao (**Hotel Concierge & Service Booking Management System**).

---

## Tính năng chính

- **Đặt dịch vụ trực tuyến cho khách lưu trú:** Giao diện sang trọng (Navy & Gold), cho phép khách chọn danh mục dịch vụ, chuyên viên/đầu mối phụ trách, ngày giờ trống theo thời gian thực và nhận email xác nhận tự động.
- **Quản lý lịch biểu trực quan (Back-Office):** Xem lịch theo Ngày / Tuần / Tháng hoặc theo từng nhân sự phụ trách; kéo-thả đổi giờ hẹn, khóa lịch nghỉ/bảo trì.
- **Phân quyền 3 cấp:**
  - **Quản trị viên (Admin):** Cấu hình hệ thống, danh mục, dịch vụ, nhân sự, ca làm việc.
  - **Lễ tân / Điều phối viên (Secretary):** Tiếp nhận yêu cầu tại quầy hoặc qua hotline, đặt/sửa/hủy lịch và tra cứu hồ sơ khách.
  - **Nhân viên / Chuyên viên (Provider):** Theo dõi lịch phục vụ cá nhân được phân công.
- **Thông báo Email tự động:** Tích hợp Gmail SMTP gửi thư xác nhận/cập nhật/hủy lịch hẹn cho khách hàng và nhân viên phụ trách.
- **Đóng gói Docker Compose:** Khởi chạy toàn bộ môi trường (Nginx, PHP-FPM, MySQL, phpMyAdmin, Cloudflare Tunnel) chỉ với 1 lệnh.

---

## Danh mục dịch vụ triển khai

1. **Spa & Chăm Sóc Sức Khỏe (Le Spa):** Trị liệu đá nóng Himalaya, Chăm sóc da mặt chuyên sâu, Xông hơi thảo dược & Tắm khoáng Onsen.
2. **Ẩm Thực & Nhà Hàng (Fine Dining):** Đặt bàn tối L'Étoile, Trà chiều Hoàng gia, Tiệc BBQ riêng tư tại Villa.
3. **Đưa Đón & Tham Quan (Concierge & Limousine):** Đưa đón sân bay bằng Mercedes S-Class, City Tour nửa ngày, Du thuyền ngắm hoàng hôn.
4. **Thể Thao & Giải Trí (Fitness & Recreation):** Huấn luyện viên Gym/Yoga cá nhân, Thuê sân Tennis.
5. **Hội Nghị & Sự Kiện (MICE & Meetings):** Phòng họp VIP Executive Boardroom, Sảnh tiệc Grand Ballroom.

---

## Hướng dẫn khởi chạy nhanh (Docker)

### 1. Khởi động hệ thống
```powershell
docker compose up -d
```

### 2. Khởi tạo cấu trúc bảng và nạp dữ liệu mẫu khách sạn (chỉ cần chạy lần đầu trên máy mới)
```powershell
docker compose exec php-fpm php index.php console migrate
docker compose exec php-fpm php docker/seed-hotel.php
```

### 3. Truy cập hệ thống
- **Trang khách hàng đặt lịch:** http://localhost/index.php
- **Trang quản trị (Back-Office):** http://localhost/index.php/login
- **Quản lý cơ sở dữ liệu (phpMyAdmin):** http://localhost:8080 (`root` / `secret`)

---

## Tài khoản đăng nhập mặc định

| Vai trò | Tên đăng nhập | Mật khẩu |
| :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `administrator` | `administrator` |
| **Lễ tân / Điều phối (Secretary)** | `lanhuong`, `minhtrang` | `12345678` |
| **Chuyên viên / Nhân viên (Provider)** | `hoangbach`, `khanhlinh`, `baoson`, `thuyduong`, `quoctrung`, `maiphuong`, `giahuy` | `12345678` |
