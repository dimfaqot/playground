<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// landing
$routes->get('/auth/(:any)', 'Landing::auth/$1');
$routes->get('/iot/fqt/085175006585/(:any)', 'Iot::grup_iot/$1');
$routes->get('/iot/fqt/08970576585/kasir', 'Kasir::index');
$routes->get('/', 'Landing::index');
// $routes->get('/(:any)/(:any)', 'Landing::index/$1/$2');
$routes->post('/landing/cek_user', 'Landing::cek_user');
$routes->post('/landing/encode_jwt', 'Landing::encode_jwt');
$routes->get('/tv', 'Landing::tv');
$routes->post('/landing/status_tv', 'Landing::status_tv');

// home
$routes->get('/home', 'Home::index');
$routes->get('/home/(:num)', 'Home::index/$1');
$routes->post('/home/delete', 'Home::delete');
$routes->get('logout', 'Home::logout');
$routes->post('/home/switch_tema', 'Home::switch_tema');
$routes->post('/home/statistik', 'Home::statistik');
$routes->post('/home/statistik_bulanan', 'Home::statistik_bulanan');
$routes->post('/home/laporan', 'Home::laporan');
$routes->post('/home/metode_tap', 'Home::metode_tap');
$routes->post('/home/cek_metode_tap', 'Home::cek_metode_tap');
$routes->post('/home/delete_id_metode_tap', 'Home::delete_id_metode_tap');
$routes->post('/home/header', 'Home::header');
$routes->post('/home/update_header', 'Home::update_header');
$routes->post('/home/csrf', 'Home::csrf');
$routes->post('/home/upload_file', 'Home::upload_file');

$routes->post('/home/notif', 'Home::notif');
$routes->get('/home/kantin/notif', 'Home::notif_kantin');
$routes->get('/home/poin/notif', 'Home::notif_poin');
$routes->post('/home/read_notif', 'Home::read_notif');

// menu
$routes->get('/menu', 'Menu::index');
$routes->post('/menu/add', 'Menu::add');
$routes->post('/menu/update', 'Menu::update');

// shift
$routes->get('/shift', 'Shift::index');
$routes->post('/shift/add', 'Shift::add');
$routes->post('/shift/update', 'Shift::update');
$routes->post('/shift/cari_user', 'Shift::cari_user');
$routes->post('/shift/tes', 'Shift::tes');

// koperasi
$routes->get('/koperasi', 'Koperasi::index');
$routes->post('/koperasi/add', 'Koperasi::add');
$routes->post('/koperasi/add_pencairan', 'Koperasi::add_pencairan');
$routes->post('/koperasi/update', 'Koperasi::update');

// options
$routes->get('/options', 'Options::index');
$routes->post('/options/add', 'Options::add');
$routes->post('/options/update', 'Options::update');

// settings
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/add', 'Settings::add');
$routes->post('/settings/update', 'Settings::update');

// options
$routes->get('/user', 'User::index');
$routes->post('/user/add', 'User::add');
$routes->post('/user/update', 'User::update');
$routes->post('/user/update_db', 'User::update_db');
$routes->post('/user/topup', 'User::topup');
$routes->post('/user/update_hp', 'User::update_hp');

// options
$routes->get('/barang', 'Barang::index');
$routes->get('/barang/(:any)', 'Barang::index/$1');
$routes->post('/barang/add', 'Barang::add');
$routes->post('/barang/update', 'Barang::update');

// kantin
$routes->get('/kantin', 'Kantin::index');
$routes->post('/kantin/no_nota', 'Kantin::no_nota');
$routes->post('/kantin/cari_barang', 'Kantin::cari_barang');
$routes->post('/kantin/transaksi', 'Kantin::transaksi');
$routes->post('/kantin/cari_user', 'Kantin::cari_user');

// barber
$routes->get('/barber', 'Barber::index');
$routes->post('/barber/no_nota', 'Barber::no_nota');
$routes->post('/barber/cari_barang', 'Barber::cari_barang');
$routes->post('/barber/transaksi', 'Barber::transaksi');
$routes->post('/barber/cari_user', 'Barber::cari_user');

// pengeluaran
$routes->get('/pengeluaran', 'Pengeluaran::index');
$routes->get('/pengeluaran/(:any)/(:num)/(:any)', 'Pengeluaran::index/$1/$2/$3');
$routes->post('/pengeluaran/add', 'Pengeluaran::add');
$routes->post('/pengeluaran/update', 'Pengeluaran::update');
$routes->post('/pengeluaran/cari_barang', 'Pengeluaran::cari_barang');

// guest
$routes->get('guest/laporan/(:any)/(:num)', 'Guest::laporan/$1/$2');
$routes->get('/guest/nota/(:any)', 'Guest::nota/$1');

// options
$routes->get('/perangkat', 'Perangkat::index');
$routes->post('/perangkat/add', 'Perangkat::add');
$routes->post('/perangkat/update', 'Perangkat::update');
$routes->post('/perangkat/update_blur', 'Perangkat::update_blur');

// options
$routes->get('/billiard', 'Billiard::index');
$routes->post('/billiard/add', 'Billiard::add');
$routes->post('/billiard/add_durasi', 'Billiard::add_durasi');
$routes->post('/billiard/akhiri', 'Billiard::akhiri');
$routes->post('/billiard/users', 'Billiard::users');
$routes->post('/billiard/transaksi', 'Billiard::transaksi');
$routes->post('/billiard/lunas', 'Billiard::lunas');
$routes->post('/billiard/pindah_meja', 'Billiard::pindah_meja');
// options
$routes->get('/ps', 'Ps::index');
$routes->post('/ps/add', 'Ps::add');
$routes->post('/ps/add_durasi', 'Ps::add_durasi');
$routes->post('/ps/akhiri', 'Ps::akhiri');
$routes->post('/ps/users', 'Ps::users');
$routes->post('/ps/transaksi', 'Ps::transaksi');
$routes->post('/ps/lunas', 'Ps::Lunas');
$routes->post('/ps/pindah_meja', 'Ps::pindah_meja');

// statistik
$routes->get('/statistik/(:any)/(:num)/(:any)', 'Statistik::index/$1/$2/$3');

// hutang
$routes->get('/hutang', 'Hutang::index');
$routes->get('/hutang/(:any)', 'Hutang::index/$1');
$routes->post('/hutang/detail', 'Hutang::detail');
$routes->post('/hutang/lunas', 'Hutang::lunas');

// hutang
$routes->get('/cafe/pesanan/(:any)', 'Cafe::pesanan/$1');
$routes->get('/cafe', 'Cafe::index');
$routes->post('/cafe/add_user', 'Cafe::add_user');
$routes->post('/cafe/transaksi', 'Cafe::transaksi');
$routes->post('/cafe/cek_status', 'Cafe::cek_status');
$routes->post('/cafe/update_metode', 'Cafe::update_metode');


// iot web
$routes->post('/log/is_logged', 'Log::is_logged');

$routes->post('/iot/sos', 'Iot::sos');
$routes->post('/iot/play', 'Iot::play');
$routes->post('/iot/update_perangkat', 'Iot::update_perangkat'); //untuk lampu exhaust dll
$routes->post('/iot/cek_absen', 'Iot::cek_absen'); //memasukkan data ke table iot denga kategori absen untuk dibaca di iot
$routes->post('/iot/cek_notif', 'Iot::cek_notif');
$routes->post('/iot/csrf', 'Iot::csrf');
$routes->post('/iot/akhiri', 'Iot::akhiri');
$routes->post('/iot/cari_user', 'Iot::cari_user');
$routes->post('/iot/transaksi', 'Iot::transaksi');
$routes->post('/iot/cek_metode_tap', 'Iot::cek_metode_tap'); // cek saldo lalu masukkan ke table metode untuk dibaca saat tap
$routes->post('/iot/metode_tap', 'Iot::metode_tap'); //mengecek setiap detik apakah sudah ditap, jika sudah maka update data
$routes->post('/iot/delete_id_metode_tap', 'Iot::delete_id_metode_tap'); //hapus data setelah 15 detik
$routes->post('/iot/afk', 'Iot::afk'); //mematikan lampu orang yang main tap tapi waktu belum selesai

// kantin barber
$routes->post('/iot/no_nota', 'Iot::no_nota');
$routes->post('/iot/cari_barang', 'Iot::cari_barang');
$routes->post('/iot/transaction', 'Iot::transaction');
$routes->post('/iot/cek_absen_kantin', 'Iot::cek_absen_kantin');

// iot perangkat iot tap
$routes->post('/iot/absen_tap', 'Iot::absen_tap'); //iot pembeca absen
$routes->post('/iot/bayar_tap', 'Iot::bayar_tap'); //iot pembaca pembayaran
$routes->post('/iot/tapping', 'Iot::iot_tapping'); // iot pembaca tap sebelum transaksi

// url ini dipanngil esp sesuai interval
// mengecek 3 hal:
// 1. Apakah ada yang absen
// 2. Apakah ada yang akan membayar melalui tap
// 3. Status perangkat
$routes->post('/iot/esp', 'Iot::esp'); //status perangkat, absen, tapping, dll
$routes->post('/iot/perangkat', 'Iot::perangkat'); //status khusus perangkat
$routes->post('/iot/ps', 'Iot::ps'); //status khusus perangkat

// iot kasir
$routes->post('/kasir/cari_barang', 'Kasir::cari_barang');
$routes->post('/kasir/cari_user', 'Kasir::cari_user');
$routes->post('/kasir/transaksi', 'Kasir::transaksi');
$routes->post('/kasir/hutang', 'Kasir::hutang');
$routes->post('/kasir/transaksi_hari_ini', 'Kasir::transaksi_hari_ini');
$routes->post('/kasir/cek_absen', 'Kasir::cek_absen');

$routes->get('/notif', 'FirebaseController::index');
$routes->post('/subscribe', 'FirebaseController::saveToken');
$routes->get('/send', 'FirebaseController::sendPushNotification');
$routes->post('/kasir/absen', 'Kasir::absen');
