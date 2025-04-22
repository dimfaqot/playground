<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

function user()
{
    $db = db('user');
    $q = $db->where('id', session('id'))->get()->getRowArray();
    return $q;
}
function menus($order = null)
{
    $db = db('menu');
    $menu = [];

    $role = "Public";
    if (user()) {
        $role = user()['role'];
    }

    if ($order == null) {
        if (user()) {
            $menu[] = ['role' => $role, 'menu' => 'Home', 'tabel' => '', 'controller' => 'home', 'icon' => "fa-regular fa-compass", "urutan" => 0, "grup" => ""];
        }
        $q = $db->where('role', $role)->orderBy('urutan', 'ASC')->get()->getResultArray();
        foreach ($q as $i) {
            $menu[] = $i;
        }
    } else {

        if ($order == "grup") {
            if (user()) {
                $menu[] = ['role' => $role, 'menu' => 'Home', 'tabel' => '', 'controller' => 'home', 'icon' => "fa-regular fa-compass", "urutan" => 0, "grup" => ""];
            }

            $q = $db->where('role', $role)->orderBy('urutan', 'ASC')->get()->getResultArray();
            $exist = [];
            foreach ($q as $i) {
                if ($i['grup'] == "") {
                    $menu[] = $i;
                } else {
                    if (!in_array($i['grup'], $exist)) {
                        $menu[] = $i;
                        $exist[] = $i['grup'];
                    }
                }
            }
        } else {
            $q = $db->where('role', $role)->where('grup', $order)->orderBy('urutan', 'ASC')->get()->getResultArray();
            foreach ($q as $i) {
                $menu[] = $i;
            }
        }
    }
    return $menu;
}

function menu($req = null)
{

    $res = [];
    if (user()) {
        if ($req == null) {
            foreach (menus() as $i) {
                if ($i['controller'] == url()) {
                    $res = $i;
                }
            }
        } else {
            foreach (menus() as $i) {
                if ($i['controller'] == $req) {
                    $res = $i;
                }
            }
        }

        if (count($res) == 0) {
            if (user()) {
                gagal(base_url('home'), "Kamu tidak diizinkan!.");
            }
        }
    } else {
        if ($req == null) {
            foreach (menus() as $i) {
                if ($i['controller'] == url()) {
                    $res = $i;
                }
            }
        } else {
            foreach (menus() as $i) {
                if ($i['controller'] == $req) {
                    $res = $i;
                }
            }
        }
    }
    return $res;
}


function set_qr_code($text)
{
    $writer = new PngWriter();
    $qrCode = QrCode::create($text)
        // ->setEncoding(new Encoding('UTF-8'))
        // ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
        ->setSize(300)
        ->setMargin(0)

        ->setForegroundColor(new Color(0, 0, 0));
    // ->setBackgroundColor(new Color(255, 255, 255));

    $result = $writer->write($qrCode);


    $qr = $result->getDataUri();


    return $qr;
}

function db($tabel, $db = null)
{
    if ($db == null || $db == getenv('db_used')) {
        $db = \Config\Database::connect();
    } else {
        $db = \Config\Database::connect(strtolower(str_replace(" ", "_", $db)));
    }
    $db = $db->table($tabel);

    return $db;
}

function get_cols($tabel)
{
    $db = \Config\Database::connect();

    return $db->getFieldNames($tabel);
}

function url($req = null)
{

    $url = service('uri');
    $res = $url->getPath();
    $val = '';
    if ($req == null) {
        if (getenv('is_online') == 0) {
            $req = 2;
        } else {
            $req = 3;
        }
    } else {
        if (getenv('is_online') == 0) {
            $req = $req - 1;
        }
    }


    $exp = explode("/", $res);

    if (array_key_exists($req, $exp)) {
        $val = $exp[$req];
    }

    return $val;
}

function sukses($url, $pesan)
{
    session()->setFlashdata('sukses', $pesan);
    header("Location: " . $url);
    die;
}

function gagal($url, $pesan)
{
    session()->setFlashdata('gagal', $pesan);
    header("Location: " . $url);
    die;
}

function clear($text)
{
    $text = trim($text);
    $text = htmlspecialchars($text);
    return $text;
}

function encode_jwt($data)
{

    $jwt = JWT::encode($data, getenv('key_jwt'), 'HS256');

    return $jwt;
}

function decode_jwt($encode_jwt)
{
    try {

        $decoded = JWT::decode($encode_jwt, new Key(getenv('key_jwt'), 'HS256'));
        $arr = (array)$decoded;

        return $arr;
    } catch (\Exception $e) { // Also tried JwtException
        $data = [
            'status' => '400',
            'message' => $e->getMessage()
        ];

        echo json_encode($data);
        die;
    }
}

function sukses_js($pesan, $data = null, $data2 = null, $data3 = null, $data4 = null, $data5 = null)
{
    $data = [
        'status' => '200',
        'message' => $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4,
        'data5' => $data5
    ];

    echo json_encode($data);
    die;
}

function gagal_js($pesan, $data = null, $data2 = null, $data3 = null, $data4 = null, $data5 = null)
{
    $res = [
        'status' => '400',
        'message' =>  $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4,
        'data5' => $data5
    ];

    echo json_encode($res);
    die;
}


function bulan($req = null)
{
    $bulan = [
        ['romawi' => 'I', 'bulan' => 'Januari', 'angka' => '01', 'satuan' => 1],
        ['romawi' => 'II', 'bulan' => 'Februari', 'angka' => '02', 'satuan' => 2],
        ['romawi' => 'III', 'bulan' => 'Maret', 'angka' => '03', 'satuan' => 3],
        ['romawi' => 'IV', 'bulan' => 'April', 'angka' => '04', 'satuan' => 4],
        ['romawi' => 'V', 'bulan' => 'Mei', 'angka' => '05', 'satuan' => 5],
        ['romawi' => 'VI', 'bulan' => 'Juni', 'angka' => '06', 'satuan' => 6],
        ['romawi' => 'VII', 'bulan' => 'Juli', 'angka' => '07', 'satuan' => 7],
        ['romawi' => 'VIII', 'bulan' => 'Agustus', 'angka' => '08', 'satuan' => 8],
        ['romawi' => 'IX', 'bulan' => 'September', 'angka' => '09', 'satuan' => 9],
        ['romawi' => 'X', 'bulan' => 'Oktober', 'angka' => '10', 'satuan' => 10],
        ['romawi' => 'XI', 'bulan' => 'November', 'angka' => '11', 'satuan' => 11],
        ['romawi' => 'XII', 'bulan' => 'Desember', 'angka' => '12', 'satuan' => 12]
    ];

    $res = $bulan;
    foreach ($bulan as $i) {
        if ($i['bulan'] == $req) {
            $res = $i;
        } elseif ($i['angka'] == $req) {
            $res = $i;
        } elseif ($i['satuan'] == $req) {
            $res = $i;
        } elseif ($i['romawi'] == $req) {
            $res = $i;
        }
    }
    return $res;
}

function hari_dalam_bulan($month, $year)
{
    $dates = [];

    // Tentukan jumlah hari dalam bulan yang diberikan
    $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // Loop melalui setiap hari dalam bulan
    for ($day = 1; $day <= $numDays; $day++) {
        // Buat string tanggal
        $dateString = "$year-$month-$day";
        // Konversi string tanggal ke timestamp
        $timestamp = strtotime($dateString);
        // Dapatkan nama hari
        $dayOfWeek = date('l', $timestamp);

        // Tambahkan ke array
        // $dates[] = [
        //     'date' => $dateString,
        //     'day' => $dayOfWeek,
        // ];

        $dates[] = hari($dayOfWeek)['indo'] . ", " . $day . " " . bulan($month)['bulan'] . " " . $year;
    }

    return $dates;
}

function hari($req)
{
    $hari = [
        ['inggris' => 'Monday', 'indo' => 'Senin'],
        ['inggris' => 'Tuesday', 'indo' => 'Selasa'],
        ['inggris' => 'Wednesday', 'indo' => 'Rabu'],
        ['inggris' => 'Thursday', 'indo' => 'Kamis'],
        ['inggris' => 'Friday', 'indo' => 'Jumat'],
        ['inggris' => 'Saturday', 'indo' => 'Sabtu'],
        ['inggris' => 'Sunday', 'indo' => 'Minggu']
    ];

    $res = [];
    foreach ($hari as $i) {
        if ($i['inggris'] == $req) {
            $res = $i;
        } elseif ($i['indo'] == $req) {
            $res = $i;
        }
    }

    return $res;
}

function angka($angka)
{
    return number_format($angka, 0, ',', ',');
}

function upper_first($text)
{
    // $text = clear($text);
    $exp = explode(" ", $text);

    $val = [];
    foreach ($exp as $i) {
        $lower = strtolower($i);
        $val[] = ucfirst($lower);
    }

    return implode(" ", $val);
}

function options($order)
{
    $db = db('options');
    $db->where('grup', $order);
    if ($order == "Topup") {
        $db->orderBy('id', 'ASC');
    } else {
        $db->orderBy('value', 'ASC');
    }
    $q = $db->get()->getResultArray();

    return $q;
}

function csv_to_array($file = "katalog.csv")
{
    $file = fopen($file, 'r');
    $data = [];
    while (($line = fgetcsv($file)) !== FALSE) {
        //$line is an array of the csv elements
        $data[] = $line;
    }
    fclose($file);

    return $data;


    // proses selanjutnya
    // $data = csv_to_array();
    // $val = [];

    // foreach ($data as $k => $i) {
    //     if ($k > 4 && $k < 164) {
    //         $exp = explode(";", $i[0]);
    //         $val[] = ['no' => ((array_key_exists(0, $exp) ? $exp[0] : "")), 'barang' => (array_key_exists(1, $exp) ? $exp[1] : ""), 'qty' => (array_key_exists(2, $exp) ? $exp[2] : ""), 'satuan' => (array_key_exists(3, $exp) ? $exp[3] : ""), 'beli' => (array_key_exists(4, $exp) ? $exp[4] : ""), 'jual' => (array_key_exists(5, $exp) ? $exp[5] : "")];
    //     }
    // }
    // $db = db('barang');
    // foreach ($val as $i) {

    //     $data = [
    //         'barang' => $i['barang'],
    //         'qty' => $i['qty'],
    //         'satuan' => $i['satuan'],
    //         'beli' => $i['beli'],
    //         'jual' => $i['jual'],
    //         'updated_at' => time(),
    //         'petugas' => user()['nama'],
    //     ];

    //     $db->insert($data);
    // }
}

function settings($setting)
{
    $db = db('settings');
    $q = $db->where('setting', $setting)->get()->getRowArray();

    $res = '';
    if ($q) {
        $res = $q['value'];
    }

    return $res;
}

function tema($order = null)
{
    $db = db('tema');
    $q = $db->where('tema', settings('Tema'))->get()->getRowArray();

    if ($order == null) {
        return $q;
    } else {
        return $q[$order];
    }
}

function no_nota($tgl, $tabel)
{
    $db = db($tabel);
    $q = $db->orderBy('tgl', 'DESC')->get()->getRowArray();


    $nota = date('dmY', $tgl) . '-0001';

    if ($q) {
        if (date('m', $tgl) == date("m", $q['tgl']) && date('Y', $tgl) == date("Y", $q['tgl'])) {
            $last_nota = explode("-", $q['no_nota']);
            $new_nota = (int)end($last_nota) + 1;

            $temp_nota = '';
            if (strlen($new_nota) == 1) {
                $temp_nota = '000' . $new_nota;
            } elseif (strlen($new_nota) == 2) {
                $temp_nota = '00' . $new_nota;
            } elseif (strlen($new_nota) == 3) {
                $temp_nota = '0' . $new_nota;
            } else {
                $temp_nota = $new_nota;
            }

            $nota = date('dmY', $tgl) . '-' . $temp_nota;
        }
    }

    return $nota;
}

function get_tahun()
{
    $db = db('billiard');
    $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();
    $tahuns = [];

    foreach ($q as $i) {
        if (!in_array(date('Y', $i['tgl']), $tahuns)) {
            $tahuns[] = date('Y', $i['tgl']);
        }
    }

    return $tahuns;
}

function rp_to_int($rp)
{
    $r = str_replace(",", "", str_replace(".", "", $rp));
    return (int)$r;
}

function durasi_jam($start_time)
{
    // Ambil waktu saat ini dalam format UNIX timestamp
    $current_time = time();

    // Hitung selisih waktu dalam detik
    $elapsed_seconds = $current_time - $start_time;

    // Konversi detik ke jam dan menit
    $hours = floor($elapsed_seconds / 3600);
    $minutes = floor(($elapsed_seconds % 3600) / 60);

    // Format hasil
    return "{$hours} Jam : {$minutes} Menit";
}
function sisa_jam($endTime)
{
    // Ambil waktu saat ini dalam format UNIX timestamp
    $current_time = time();

    // Hitung selisih waktu dalam detik
    $elapsed_seconds =  $endTime - $current_time;

    // Konversi detik ke jam dan menit
    $hours = floor($elapsed_seconds / 3600);
    $minutes = floor(($elapsed_seconds % 3600) / 60);

    // Format hasil
    return "{$hours} Jam : {$minutes} Menit";
}

function time_hari_ini()
{
    $currentHour = date("H"); // Ambil jam saat ini
    $currentDate = date("Y-m-d"); // Ambil tanggal saat ini
    $yesterdayDate = date("Y-m-d", strtotime("-1 day")); // Ambil tanggal kemarin
    $tomorrowDate = date("Y-m-d", strtotime("+1 day")); // Ambil tanggal besok

    if ($currentHour >= 4) {
        // Jika eksekusi di atas jam 04:00 -> Rentang: Hari ini 09:00 sampai Besok 04:00
        $startTime = strtotime("$currentDate 09:00");
        $endTime = strtotime("$tomorrowDate 04:00");
    } else {
        // Jika eksekusi di bawah jam 04:00 -> Rentang: Kemarin 09:00 sampai Hari ini 04:00
        $startTime = strtotime("$yesterdayDate 09:00");
        $endTime = strtotime("$currentDate 04:00");
    }

    return [
        'start' => $startTime,
        'end' => $endTime
    ];
}
