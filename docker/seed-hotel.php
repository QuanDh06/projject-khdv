<?php
error_reporting(E_ALL);

$pdo = new PDO(
    'mysql:host=mysql;dbname=easyappointments;charset=utf8mb4',
    'user',
    'password',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]
);
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

// 1. Hotel Working Plan (06:00 - 23:00 every day, short break 13:30-14:00 for shift handover)
$working_plan = json_encode([
    'monday'    => ['start' => '06:30', 'end' => '22:30', 'breaks' => [['start' => '13:30', 'end' => '14:00']]],
    'tuesday'   => ['start' => '06:30', 'end' => '22:30', 'breaks' => [['start' => '13:30', 'end' => '14:00']]],
    'wednesday' => ['start' => '06:30', 'end' => '22:30', 'breaks' => [['start' => '13:30', 'end' => '14:00']]],
    'thursday'  => ['start' => '06:30', 'end' => '22:30', 'breaks' => [['start' => '13:30', 'end' => '14:00']]],
    'friday'    => ['start' => '06:30', 'end' => '23:00', 'breaks' => [['start' => '13:30', 'end' => '14:00']]],
    'saturday'  => ['start' => '06:30', 'end' => '23:00', 'breaks' => [['start' => '13:30', 'end' => '14:00']]],
    'sunday'    => ['start' => '06:30', 'end' => '22:30', 'breaks' => [['start' => '13:30', 'end' => '14:00']]],
], JSON_UNESCAPED_UNICODE);

$settings_updates = [
    'company_name'               => 'Grand Metropole Hotel & Resort',
    'company_email'              => 'concierge@grandmetropole.vn',
    'company_link'               => 'http://localhost',
    'default_language'           => 'vietnamese',
    'default_timezone'           => 'Asia/Ho_Chi_Minh',
    'date_format'                => 'DMY',
    'time_format'                => 'military',
    'first_weekday'              => 'monday',
    'company_working_plan'       => $working_plan,
    'appointment_status_options' => json_encode(['Đã đặt', 'Đã xác nhận', 'Khách đang sử dụng', 'Hoàn thành', 'Đã đổi lịch', 'Đã huỷ'], JSON_UNESCAPED_UNICODE),
];

$stmt_setting = $pdo->prepare("UPDATE ea_settings SET value = :val, update_datetime = NOW() WHERE name = :name");
foreach ($settings_updates as $k => $v) {
    $stmt_setting->execute([':val' => $v, ':name' => $k]);
}

// 2. Update 1 Admin (id = 1)
$pdo->prepare("
    UPDATE ea_users
    SET first_name = 'Quân',
        last_name = 'Đàm',
        email = 'gm@grandmetropole.vn',
        phone_number = '0901000888',
        address = '15 Ngô Quyền, Hoàn Kiếm',
        city = 'Hà Nội',
        notes = 'Tổng Quản lý Điều hành (General Manager) - Grand Metropole Hotel & Resort',
        timezone = 'Asia/Ho_Chi_Minh',
        language = 'vietnamese',
        update_datetime = NOW()
    WHERE id = 1
")->execute();

// Clear old records except Admin (id = 1)
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("TRUNCATE TABLE ea_appointments");
$pdo->exec("TRUNCATE TABLE ea_services_providers");
$pdo->exec("TRUNCATE TABLE ea_secretaries_providers");
$pdo->exec("TRUNCATE TABLE ea_services");
$pdo->exec("TRUNCATE TABLE ea_service_categories");
$pdo->exec("DELETE FROM ea_user_settings WHERE id_users > 1");
$pdo->exec("DELETE FROM ea_users WHERE id > 1");
$pdo->exec("ALTER TABLE ea_users AUTO_INCREMENT = 2");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

// 3. Insert 5 Hotel Service Categories
$categories = [
    ['1. Lưu trú, Nhận phòng VIP & Tham quan', 'Dịch vụ Private Check-in tại Lounge, nghỉ theo giờ (Day-use Suite) và tham quan khảo sát khách sạn'],
    ['2. Ẩm thực, Bàn tiệc & Trà chiều', 'Đặt bàn nhà hàng Fine Dining, Buffet Hải sản, Trà chiều kiểu Anh và Bàn tiệc rượu vang riêng tư'],
    ['3. Đưa đón Sân bay & Xe sang Limousine', 'Dịch vụ xe Mercedes S-Class / Limousine đưa đón sân bay Nội Bài và thuê xe có tài xế riêng theo giờ'],
    ['4. Wellness, Spa, Bể bơi & Thể thao', 'Trị liệu Spa 5 sao, phòng xông hơi Jjimjilbang, Cabana bể bơi vô cực và sân Tennis/Pickleball có HLV'],
    ['5. Phòng Họp, Hội nghị & Sự kiện VIP', 'Đặt phòng họp hội đồng (Boardroom), Business Center và tư vấn tổ chức tiệc cưới / hội thảo'],
];
$stmt_cat = $pdo->prepare("INSERT INTO ea_service_categories (create_datetime, update_datetime, name, description) VALUES (NOW(), NOW(), ?, ?)");
$cat_ids = [];
foreach ($categories as $c) {
    $stmt_cat->execute($c);
    $cat_ids[] = (int) $pdo->lastInsertId();
}

// 4. Insert 14 Realistic Hotel Services
$services = [
    // Cat 0: Lưu trú & VIP Check-in
    [
        'name' => 'Thủ tục Nhận phòng Đặc quyền (VIP Club Lounge Check-in)',
        'duration' => 30,
        'price' => 0,
        'currency' => 'VNĐ',
        'description' => 'Đón tiếp riêng tại Executive Lounge tầng 18, dùng đồ uống chào mừng (Welcome Drink) và làm thủ tục nhận phòng nhanh không cần chờ tại sảnh.',
        'color' => '#3b82f6',
        'location' => 'Executive Club Lounge - Tầng 18',
        'attendants' => 2,
        'cat_id' => $cat_ids[0],
    ],
    [
        'name' => 'Gói Nghỉ dưỡng Trong ngày – Deluxe Day-Use Suite (4 giờ)',
        'duration' => 240,
        'price' => 2200000,
        'currency' => 'VNĐ',
        'description' => 'Sử dụng phòng Deluxe Suite tiêu chuẩn 5 sao trong 4 giờ, bao gồm quyền sử dụng bể bơi vô cực, phòng Gym và trà/cà phê trong phòng.',
        'color' => '#2563eb',
        'location' => 'Khối Tháp Chính - Tầng 8 đến Tầng 15',
        'attendants' => 3,
        'cat_id' => $cat_ids[0],
    ],
    [
        'name' => 'Trải nghiệm Tham quan Khách sạn & Tư vấn Hạng phòng (Hotel Tour)',
        'duration' => 45,
        'price' => 0,
        'currency' => 'VNĐ',
        'description' => 'Quản gia dẫn khách tham quan trực tiếp các hạng phòng Presidential Suite, khu tiện ích và tư vấn gói lưu trú dài hạn / đoàn MICE.',
        'color' => '#60a5fa',
        'location' => 'Sảnh Chính (Grand Lobby) - Tầng 1',
        'attendants' => 2,
        'cat_id' => $cat_ids[0],
    ],

    // Cat 1: Ẩm thực & Nhà hàng
    [
        'name' => 'Đặt bàn Tiệc tối Fine Dining – Nhà hàng Pháp Le Beaulieu',
        'duration' => 120,
        'price' => 1850000,
        'currency' => 'VNĐ',
        'description' => 'Thực đơn Set Menu 6 món chuẩn Michelin kèm tư vấn rượu vang (Sommelier) trong không gian cổ điển sang trọng.',
        'color' => '#e11d48',
        'location' => 'Nhà hàng Le Beaulieu - Tầng 2',
        'attendants' => 5,
        'cat_id' => $cat_ids[1],
    ],
    [
        'name' => 'Đặt bàn Buffet Hải sản & Tôm hùm Nướng',
        'duration' => 120,
        'price' => 1250000,
        'currency' => 'VNĐ',
        'description' => 'Thưởng thức hơn 100 món hải sản tươi sống, tôm hùm bỏ lò phô mai, quầy sashimi Nhật Bản và quầy tráng miệng.',
        'color' => '#f43f5e',
        'location' => 'Nhà hàngThe Verandah - Tầng 1',
        'attendants' => 10,
        'cat_id' => $cat_ids[1],
    ],
    [
        'name' => 'Trà chiều Hoàng gia Anh (Royal High Tea Set - 2 khách)',
        'duration' => 90,
        'price' => 790000,
        'currency' => 'VNĐ',
        'description' => 'Thưởng thức bộ sưu tập bánh ngọt Pháp, bánh scone nướng nóng và trà TWG thượng hạng trong tiếng đàn Piano sống.',
        'color' => '#ec4899',
        'location' => 'La Terrasse Café & Lounge - Sảnh Vườn',
        'attendants' => 6,
        'cat_id' => $cat_ids[1],
    ],

    // Cat 2: Xe đưa đón & Concierge
    [
        'name' => 'Xe sang Mercedes S450 Đón/Tiễn Sân bay Nội Bài (1 chiều)',
        'duration' => 60,
        'price' => 1500000,
        'currency' => 'VNĐ',
        'description' => 'Tài xế riêng mặc vest đón tại sảnh sân bay bằng bảng tên, khăn lạnh, nước khoáng Evian và Wifi tốc độ cao trên xe.',
        'color' => '#0ea5e9',
        'location' => 'Sảnh Đón Xe VIP - Cổng Ngô Quyền',
        'attendants' => 2,
        'cat_id' => $cat_ids[2],
    ],
    [
        'name' => 'Thuê xe Limousine DCar 9 chỗ Tham quan Phố cổ (3 giờ)',
        'duration' => 180,
        'price' => 2800000,
        'currency' => 'VNĐ',
        'description' => 'Xe Limousine ghế massage hạng thương gia phục vụ gia đình/đoàn khách VIP tham quan nội thành theo lịch trình riêng.',
        'color' => '#0284c7',
        'location' => 'Sảnh Đón Xe VIP - Cổng Ngô Quyền',
        'attendants' => 2,
        'cat_id' => $cat_ids[2],
    ],

    // Cat 3: Wellness, Spa & Thể thao
    [
        'name' => 'Liệu trình Hoàng gia – Massage Đá nóng & Thảo mộc (90 phút)',
        'duration' => 90,
        'price' => 1150000,
        'currency' => 'VNĐ',
        'description' => 'Phòng trị liệu VIP riêng biệt có bồn sục Jacuzzi, kết hợp đá bazan ấm và tinh dầu trầm hương giúp phục hồi năng lượng sau chuyến bay.',
        'color' => '#10b981',
        'location' => 'Le Spa du Metropole - Tầng 4',
        'attendants' => 3,
        'cat_id' => $cat_ids[3],
    ],
    [
        'name' => 'Tổ hợp Xông hơi Đá muối Himalaya & Bồn sục Onsen',
        'duration' => 60,
        'price' => 650000,
        'currency' => 'VNĐ',
        'description' => 'Sử dụng khu xông hơi khô đá muối, xông ướt thảo dược và ngâm khoáng nóng Onsen chuẩn Nhật.',
        'color' => '#14b8a6',
        'location' => 'Khu Thủy trị liệu Onsen - Tầng 4',
        'attendants' => 6,
        'cat_id' => $cat_ids[3],
    ],
    [
        'name' => 'Đặt lều nghỉ Cabana Riêng tư tại Bể bơi Vô cực (2 giờ)',
        'duration' => 120,
        'price' => 950000,
        'currency' => 'VNĐ',
        'description' => 'Giữ chỗ lều nghỉ Cabana hướng toàn cảnh thành phố kèm đĩa trái cây nhiệt đới và 2 ly cocktail đặc trưng tại Sky Bar.',
        'color' => '#06b6d4',
        'location' => 'Bể bơi Vô cực Rooftop - Tầng 20',
        'attendants' => 4,
        'cat_id' => $cat_ids[3],
    ],
    [
        'name' => 'Thuê Sân Tennis / Pickleball Tiêu chuẩn Quốc tế (60 phút)',
        'duration' => 60,
        'price' => 550000,
        'currency' => 'VNĐ',
        'description' => 'Bao gồm sân thi đấu có mái che, vợt tập luyện, bóng mới, khăn tắm và nước điện giải cho người chơi.',
        'color' => '#22c55e',
        'location' => 'Khu Thể thao Ngoài trời - Cánh Đông',
        'attendants' => 2,
        'cat_id' => $cat_ids[3],
    ],

    // Cat 4: Phòng họp & Hội nghị
    [
        'name' => 'Thuê Phòng Họp Hội đồng VIP – Executive Boardroom (2 giờ)',
        'duration' => 120,
        'price' => 3500000,
        'currency' => 'VNĐ',
        'description' => 'Phòng họp 16 ghế da cao cấp, màn hình LED 110 inch, hệ thống họp trực tuyến Polycom và tiệc trà Teabreak giữa giờ.',
        'color' => '#8b5cf6',
        'location' => 'Business Center - Tầng 3',
        'attendants' => 2,
        'cat_id' => $cat_ids[4],
    ],
    [
        'name' => 'Tư vấn & Khảo sát Sảnh Tiệc cưới / Hội thảo Grand Ballroom',
        'duration' => 60,
        'price' => 0,
        'currency' => 'VNĐ',
        'description' => 'Làm việc trực tiếp với Giám đốc Sự kiện để khảo sát sảnh tiệc 500 khách, lên phương án trang trí và dùng thử thực đơn tiệc.',
        'color' => '#f59e0b',
        'location' => 'Sảnh Tiệc Thăng Long Ballroom - Tầng 2',
        'attendants' => 2,
        'cat_id' => $cat_ids[4],
    ],
];

$stmt_srv = $pdo->prepare("
    INSERT INTO ea_services
    (create_datetime, update_datetime, name, duration, price, currency, description, attendants_number, slot_interval, color, location, is_private, id_service_categories)
    VALUES (NOW(), NOW(), :name, :duration, :price, :currency, :description, :attendants, 30, :color, :location, 0, :cat_id)
");
$srv_ids = [];
foreach ($services as $s) {
    $stmt_srv->execute([
        ':name'        => $s['name'],
        ':duration'    => $s['duration'],
        ':price'       => $s['price'],
        ':currency'    => $s['currency'],
        ':description' => $s['description'],
        ':attendants'  => $s['attendants'],
        ':color'       => $s['color'],
        ':location'    => $s['location'],
        ':cat_id'      => $s['cat_id'],
    ]);
    $srv_ids[] = (int) $pdo->lastInsertId();
}

// 5. Insert 7 Providers (Nhân viên / Quản gia / Trưởng bộ phận khách sạn) - role = 2
$default_pw_hash = password_hash('12345678', PASSWORD_BCRYPT, ['cost' => 12]);
$providers = [
    [
        'first_name' => 'Hoàng Bách',
        'last_name'  => 'Nguyễn (Quản gia Trưởng - Butler)',
        'email'      => 'hoangbach@grandmetropole.vn',
        'phone'      => '0902111222',
        'notes'      => 'Trưởng bộ phận Quản gia VIP (Chief Butler) – Phụ trách đón tiếp VIP Lounge, nghỉ dưỡng Day-use Suite và Hotel Tour.',
        'username'   => 'hoangbach',
        'services'   => [$srv_ids[0], $srv_ids[1], $srv_ids[2]],
    ],
    [
        'first_name' => 'Khánh Linh',
        'last_name'  => 'Trần (Giám sát Khối Lưu trú)',
        'email'      => 'khanhlinh@grandmetropole.vn',
        'phone'      => '0902222333',
        'notes'      => 'Chuyên viên Quan hệ Khách hàng (Guest Relations Manager) – Phụ trách nhận phòng VIP, Day-use Suite và tư vấn lưu trú.',
        'username'   => 'khanhlinh',
        'services'   => [$srv_ids[0], $srv_ids[1], $srv_ids[2], $srv_ids[10]],
    ],
    [
        'first_name' => 'Bảo Sơn',
        'last_name'  => 'Lê (Quản lý Nhà hàng & Sommelier)',
        'email'      => 'baoson@grandmetropole.vn',
        'phone'      => '0902333444',
        'notes'      => 'Quản lý Khối Ẩm thực (F&B Manager) – Phụ trách đặt bàn Fine Dining Le Beaulieu, Buffet Hải sản và Trà chiều Hoàng gia.',
        'username'   => 'baoson',
        'services'   => [$srv_ids[3], $srv_ids[4], $srv_ids[5]],
    ],
    [
        'first_name' => 'Thuỳ Dương',
        'last_name'  => 'Vũ (Trưởng nhóm Đón tiếp Nhà hàng)',
        'email'      => 'thuyduong@grandmetropole.vn',
        'phone'      => '0902444555',
        'notes'      => 'Trưởng nhóm Hostess & Lounge – Phụ trách sắp xếp bàn tiệc VIP, Trà chiều và Cabana tại Sky Bar.',
        'username'   => 'thuyduong',
        'services'   => [$srv_ids[3], $srv_ids[4], $srv_ids[5], $srv_ids[10]],
    ],
    [
        'first_name' => ' Quốc Trung',
        'last_name'  => 'Đặng (Trưởng bộ phận Concierge & Xe VIP)',
        'email'      => 'quoctrung@grandmetropole.vn',
        'phone'      => '0902555666',
        'notes'      => 'Chief Concierge – Điều phối đội xe Mercedes S450 đưa đón sân bay Nội Bài và Limousine tham quan thành phố.',
        'username'   => 'quoctrung',
        'services'   => [$srv_ids[6], $srv_ids[7], $srv_ids[2]],
    ],
    [
        'first_name' => 'Mai Phương',
        'last_name'  => 'Bùi (Quản lý Le Spa & Wellness)',
        'email'      => 'maiphuong@grandmetropole.vn',
        'phone'      => '0902666777',
        'notes'      => 'Giám đốc Trị liệu Le Spa – Phụ trách Massage đá nóng, tổ hợp xông hơi Onsen, bể bơi vô cực và khu thể thao.',
        'username'   => 'maiphuong',
        'services'   => [$srv_ids[8], $srv_ids[9], $srv_ids[10], $srv_ids[11]],
    ],
    [
        'first_name' => 'Gia Huy',
        'last_name'  => 'Phan (Giám đốc Hội nghị & Sự kiện)',
        'email'      => 'giahuy@grandmetropole.vn',
        'phone'      => '0902777888',
        'notes'      => 'Banquet & MICE Director – Phụ trách đặt phòng họp Hội đồng VIP và tư vấn sảnh tiệc cưới / hội nghị Grand Ballroom.',
        'username'   => 'giahuy',
        'services'   => [$srv_ids[12], $srv_ids[13], $srv_ids[3]],
    ],
];

$stmt_user = $pdo->prepare("
    INSERT INTO ea_users
    (create_datetime, update_datetime, first_name, last_name, email, mobile_number, phone_number, address, city, notes, timezone, language, is_private, id_roles)
    VALUES (NOW(), NOW(), :first_name, :last_name, :email, :phone, :phone, '15 Ngô Quyền, Hoàn Kiếm', 'Hà Nội', :notes, 'Asia/Ho_Chi_Minh', 'vietnamese', 0, :role)
");

$stmt_uset = $pdo->prepare("
    INSERT INTO ea_user_settings
    (id_users, username, password, salt, working_plan, working_plan_exceptions, notifications, google_sync, caldav_sync, sync_past_days, sync_future_days, calendar_view)
    VALUES (:uid, :username, :password, '', :wp, '{}', 1, 0, 0, 30, 90, 'default')
");

$stmt_sp = $pdo->prepare("INSERT INTO ea_services_providers (id_users, id_services) VALUES (?, ?)");

$prov_ids = [];
foreach ($providers as $p) {
    $stmt_user->execute([
        ':first_name' => trim($p['first_name']),
        ':last_name'  => $p['last_name'],
        ':email'      => $p['email'],
        ':phone'      => $p['phone'],
        ':notes'      => $p['notes'],
        ':role'       => 2,
    ]);
    $uid = (int) $pdo->lastInsertId();
    $prov_ids[] = $uid;

    $stmt_uset->execute([
        ':uid'      => $uid,
        ':username' => $p['username'],
        ':password' => $default_pw_hash,
        ':wp'       => $working_plan,
    ]);

    foreach ($p['services'] as $sid) {
        $stmt_sp->execute([$uid, $sid]);
    }
}

// 6. Insert 2 Secretaries (Lễ tân Khách sạn) - role = 4
$secretaries = [
    [
        'first_name' => 'Lan Hương',
        'last_name'  => 'Phạm (Lễ tân Trưởng - Ca Sáng)',
        'email'      => 'lanhuong@grandmetropole.vn',
        'phone'      => '0903111222',
        'notes'      => 'Lễ tân Trưởng sảnh chính (Front Desk Supervisor) – Điều phối lịch đặt toàn bộ các bộ phận khách sạn.',
        'username'   => 'lanhuong',
        'providers'  => $prov_ids, // Quản lý cả 7 bộ phận
    ],
    [
        'first_name' => 'Minh Trang',
        'last_name'  => 'Đỗ (Lễ tân Quốc tế - Ca Chiều)',
        'email'      => 'minhtrang@grandmetropole.vn',
        'phone'      => '0903222333',
        'notes'      => 'Lễ tân Quầy Concierge & Dịch vụ Khách hàng – Hỗ trợ đặt bàn nhà hàng, xe sân bay, Spa và phòng họp.',
        'username'   => 'minhtrang',
        'providers'  => $prov_ids, // Quản lý cả 7 bộ phận
    ],
];

$stmt_sec_prov = $pdo->prepare("INSERT INTO ea_secretaries_providers (id_users_secretary, id_users_provider) VALUES (?, ?)");
foreach ($secretaries as $sec) {
    $stmt_user->execute([
        ':first_name' => $sec['first_name'],
        ':last_name'  => $sec['last_name'],
        ':email'      => $sec['email'],
        ':phone'      => $sec['phone'],
        ':notes'      => $sec['notes'],
        ':role'       => 4,
    ]);
    $sec_id = (int) $pdo->lastInsertId();
    $stmt_uset->execute([
        ':uid'      => $sec_id,
        ':username' => $sec['username'],
        ':password' => $default_pw_hash,
        ':wp'       => null,
    ]);
    foreach ($sec['providers'] as $pid) {
        $stmt_sec_prov->execute([$sec_id, $pid]);
    }
}

// 7. Insert 10 Customers (Khách lưu trú & Khách VIP) - role = 3
$customers = [
    ['Quân', 'Đàm', 'quandam049@gmail.com', '0912345678', 'Khách hạng thẻ Platinum – Phòng Presidential Suite 1801'],
    ['Phương Thảo', 'Vũ', 'phuongthao.vu@gmail.com', '0987654321', 'Khách lưu trú phòng 1205 – Yêu thích trà chiều và Le Spa'],
    ['Đức Minh', 'Hoàng', 'ducminh.hoang@gmail.com', '0978123456', 'Giám đốc Công ty Đầu tư Á Châu – Thường đặt phòng họp VIP và bàn tiếp khách Fine Dining'],
    ['Ngọc Trâm', 'Đặng', 'ngoctram.dang@gmail.com', '0965432109', 'Khách lưu trú phòng 1502 – Đặt xe đón sân bay và nghỉ dưỡng cuối tuần'],
    ['Tuấn Kiệt', 'Lý', 'tuankiet.ly@gmail.com', '0918223344', 'Doanh nhân TP.HCM ra công tác – Cần xe S450 đón Nội Bài'],
    ['Hồng Nhung', 'Mai', 'hongnhung.mai@gmail.com', '0936556677', 'Đặt khảo sát sảnh tiệc cưới Grand Ballroom cho 350 khách'],
    ['Anh Tuấn', 'Trương', 'anhtuan.truong@gmail.com', '0945778899', 'Khách gia đình phòng 908 – Đặt Buffet Hải sản 4 người'],
    ['Thanh Tùng', 'Ngô', 'thanhtung.ngo@gmail.com', '0983112233', 'Hội viên CLB Thể thao Metropole – Đặt sân Pickleball & Onsen định kỳ'],
    ['Bích Ngọc', 'Hồ', 'bichngoc.ho@gmail.com', '0972445566', 'Khách VIP đặt gói Day-Use Suite và Cabana bể bơi vô cực'],
    ['Minh Khôi', 'Đinh', 'minhkhoi.dinh@gmail.com', '0913998877', 'Trưởng đoàn khách ngoại giao – Đặt Limousine tham quan Phố cổ'],
];
$cust_ids = [];
foreach ($customers as $c) {
    $stmt_user->execute([
        ':first_name' => $c[0],
        ':last_name'  => $c[1],
        ':email'      => $c[2],
        ':phone'      => $c[3],
        ':notes'      => $c[4],
        ':role'       => 3,
    ]);
    $cust_ids[] = (int) $pdo->lastInsertId();
}

// 8. Insert 14 Sample Appointments across yesterday, today, tomorrow, and day after tomorrow
$yesterday = date('Y-m-d', strtotime('-1 day'));
$today     = date('Y-m-d');
$tomorrow  = date('Y-m-d', strtotime('+1 day'));
$day_after = date('Y-m-d', strtotime('+2 days'));

$sample_appointments = [
    // Yesterday
    [
        'start' => "$yesterday 10:00:00", 'end' => "$yesterday 11:00:00",
        'srv' => 6, 'prov' => 4, 'cust' => 4,
        'notes' => 'Đón chuyến bay VN214 từ TP.HCM tại Sảnh VIP A Nội Bài',
        'status' => 'Hoàn thành',
    ],
    [
        'start' => "$yesterday 18:30:00", 'end' => "$yesterday 20:30:00",
        'srv' => 3, 'prov' => 2, 'cust' => 2,
        'notes' => 'Bàn góc cửa sổ nhìn ra vườn, kỷ niệm ngày cưới',
        'status' => 'Hoàn thành',
    ],

    // Today
    [
        'start' => "$today 08:30:00", 'end' => "$today 09:30:00",
        'srv' => 6, 'prov' => 4, 'cust' => 0,
        'notes' => 'Xe Mercedes S450 đón khách VIP về nhận phòng sớm',
        'status' => 'Hoàn thành',
    ],
    [
        'start' => "$today 09:30:00", 'end' => "$today 10:00:00",
        'srv' => 0, 'prov' => 0, 'cust' => 0,
        'notes' => 'Check-in đặc quyền tại Executive Lounge tầng 18, chuẩn bị rượu Champagne',
        'status' => 'Hoàn thành',
    ],
    [
        'start' => "$today 10:00:00", 'end' => "$today 12:00:00",
        'srv' => 12, 'prov' => 6, 'cust' => 2,
        'notes' => 'Họp Hội đồng Quản trị 12 thành viên, chuẩn bị Teabreak lúc 10:45',
        'status' => 'Khách đang sử dụng',
    ],
    [
        'start' => "$today 10:30:00", 'end' => "$today 12:00:00",
        'srv' => 8, 'prov' => 5, 'cust' => 1,
        'notes' => 'Phòng Spa đôi hướng hồ, tinh dầu oải hương',
        'status' => 'Đã xác nhận',
    ],
    [
        'start' => "$today 14:30:00", 'end' => "$today 16:00:00",
        'srv' => 5, 'prov' => 3, 'cust' => 3,
        'notes' => 'Bàn Trà chiều cạnh đàn Piano cho 2 khách',
        'status' => 'Đã xác nhận',
    ],
    [
        'start' => "$today 15:00:00", 'end' => "$today 17:00:00",
        'srv' => 10, 'prov' => 1, 'cust' => 8,
        'notes' => 'Cabana số 02 bể bơi tầng 20, chuẩn bị sẵn khay trái cây nhiệt đới',
        'status' => 'Đã đặt',
    ],
    [
        'start' => "$today 16:30:00", 'end' => "$today 17:30:00",
        'srv' => 11, 'prov' => 5, 'cust' => 7,
        'notes' => 'Sân Pickleball số 1, thuê thêm 4 vợt carbon',
        'status' => 'Đã xác nhận',
    ],
    [
        'start' => "$today 19:00:00", 'end' => "$today 21:00:00",
        'srv' => 4, 'prov' => 2, 'cust' => 6,
        'notes' => 'Gia đình 4 người lớn + 1 ghế trẻ em tại khu Buffet Hải sản',
        'status' => 'Đã xác nhận',
    ],

    // Tomorrow
    [
        'start' => "$tomorrow 09:00:00", 'end' => "$tomorrow 12:00:00",
        'srv' => 7, 'prov' => 4, 'cust' => 9,
        'notes' => 'Đưa đoàn tham quan Văn Miếu - Hồ Gươm - Nhà hát Lớn',
        'status' => 'Đã xác nhận',
    ],
    [
        'start' => "$tomorrow 10:00:00", 'end' => "$tomorrow 14:00:00",
        'srv' => 1, 'prov' => 0, 'cust' => 8,
        'notes' => 'Nghỉ dưỡng trong ngày trước chuyến bay tối',
        'status' => 'Đã đặt',
    ],
    [
        'start' => "$tomorrow 14:30:00", 'end' => "$tomorrow 15:30:00",
        'srv' => 13, 'prov' => 6, 'cust' => 5,
        'notes' => 'Khảo sát sảnh tiệc cưới 35 bàn và trao đổi thực đơn',
        'status' => 'Đã xác nhận',
    ],

    // Day after tomorrow
    [
        'start' => "$day_after 18:30:00", 'end' => "$day_after 20:30:00",
        'srv' => 3, 'prov' => 2, 'cust' => 0,
        'notes' => 'Tiệc tối tiếp đối tác tại phòng VIP Le Beaulieu',
        'status' => 'Đã đặt',
    ],
];

$stmt_app = $pdo->prepare("
    INSERT INTO ea_appointments
    (create_datetime, update_datetime, book_datetime, start_datetime, end_datetime, location, notes, hash, color, status, is_unavailability, id_users_provider, id_users_customer, id_services)
    VALUES (NOW(), NOW(), NOW(), :start, :end, :loc, :notes, :hash, :color, :status, 0, :prov, :cust, :srv)
");

foreach ($sample_appointments as $a) {
    $srv_index = $a['srv'];
    $stmt_app->execute([
        ':start'  => $a['start'],
        ':end'    => $a['end'],
        ':loc'    => $services[$srv_index]['location'],
        ':notes'  => $a['notes'],
        ':hash'   => substr(bin2hex(random_bytes(8)), 0, 12),
        ':color'  => $services[$srv_index]['color'],
        ':status' => $a['status'],
        ':prov'   => $prov_ids[$a['prov']],
        ':cust'   => $cust_ids[$a['cust']],
        ':srv'    => $srv_ids[$srv_index],
    ]);
}

echo "SEED_HOTEL_OK: "
    . count($cat_ids) . " categories, "
    . count($srv_ids) . " services, "
    . count($prov_ids) . " providers, "
    . count($secretaries) . " secretaries, "
    . count($cust_ids) . " customers, "
    . count($sample_appointments) . " appointments.\n";
