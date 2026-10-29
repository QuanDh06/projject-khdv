<?php defined('BASEPATH') or exit('No direct script access allowed');

// Cấu hình gửi Email thật qua Gmail SMTP
// Mật khẩu ứng dụng có thể đặt trong file nội bộ storage/logs/smtp_pass.key (đã được .gitignore ẩn khỏi GitHub)
// hoặc qua biến môi trường SMTP_PASS.
$local_smtp_key_file = FCPATH . 'storage/logs/smtp_pass.key';
$local_smtp_pass = is_file($local_smtp_key_file) ? trim((string) file_get_contents($local_smtp_key_file)) : (getenv('SMTP_PASS') ?: '');

$config['useragent'] = 'Easy!Appointments';
$config['protocol'] = 'smtp';
$config['mailtype'] = 'html';
$config['smtp_debug'] = '0';
$config['smtp_auth'] = TRUE;
$config['smtp_host'] = 'smtp.gmail.com';
$config['smtp_user'] = 'quandam049@gmail.com';
$config['smtp_pass'] = $local_smtp_pass;
$config['smtp_crypto'] = 'tls';
$config['smtp_port'] = 587;
$config['from_name'] = 'Grand Metropole Hotel & Resort';
$config['from_address'] = 'quandam049@gmail.com';
$config['reply_to'] = 'quandam049@gmail.com';
$config['crlf'] = "\r\n";
$config['newline'] = "\r\n";
