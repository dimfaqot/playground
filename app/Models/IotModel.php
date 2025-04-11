<?php

namespace App\Models;

use CodeIgniter\Model;

class IotModel extends Model
{
    function all_perangkat($grup)
    {
        $exp = explode("-", $grup);
        $lokasi = str_replace("-", " ", $grup);
        $divisi = $exp[0];

        $db = db("perangkat");
        $q = $db->where('grup', $lokasi)->orderBy('urutan', 'ASC')->get()->getResultArray();
        $data = [];

        $dbm = db(strtolower($divisi));
        foreach ($q as $i) {

            $expm = explode(" ", $i['kategori']);
            if ($expm[0] == "Billiard" || $expm[0] == "Ps") {
                $status = $dbm->where("perangkat", $i['perangkat'])->whereIn('metode', ['Play', 'Over'])->get()->getRowArray();
                $fulus = 0;
                $role = $expm[0];
                if ($status) {
                    $dbu = db('user');
                    $user = $dbu->where("id", $status['user_id'])->get()->getRowArray();
                    if ($user) {
                        $fun = new \App\Models\FunModel();
                        $fulus = $fun->dekripsi_fulus($user, $user['fulus']);
                        if (is_null($fulus)) {
                            gagal_js("Dekripsi gagal...");
                        }
                        $role = $user['role'];
                    }
                    $i['id'] = $status['id'];
                    $i['status'] = $status['status'];
                    $i['dari'] = $status['dari'];
                    $i['ke'] = $status['ke'];
                    $i['durasi'] = $status['durasi'];
                    $i['jam'] = (round($status['durasi'] / 60));
                    $i['diskon'] = $status['diskon'];
                    $i['total'] = $status['total'];
                    $i['pembeli'] = $status['pembeli'];
                    $i['user_id'] = $status['user_id'];
                    $i['metode'] = $status['metode'];
                    $i['petugas'] = $status['petugas'];
                    $i['waktu'] = durasi_jam($status['dari']);
                    $i['role'] = $role;
                    $i['saldo'] = $fulus;
                } else {
                    $i['dari'] = 0;
                    $i['ke'] = 0;
                    $i['durasi'] = 0;
                    $i['jam'] = 0;
                    $i['diskon'] = 0;
                    $i['total'] = 0;
                    $i['pembeli'] = "";
                    $i['user_id'] = 0;
                    $i['metode'] = "";
                    $i['petugas'] = "";
                    $i['waktu'] = "-";
                    $i['role'] = $role;
                    $i['saldo'] = $fulus;
                }
                $data['rental'][] = $i;
            } else {
                $data['perangkat'][] = $i;
            }
        }

        $res = ['rental' => $data['rental'], 'perangkat' => $data['perangkat'], 'lokasi' => $lokasi, 'grup' => $grup, 'judul' => str_replace("-", " ", $grup)];
        return $res;
    }
    function tv($order = "billiard")
    {

        $data = [];

        $dbp = db('perangkat');
        $perangkat = $dbp->where('kategori', upper_first($order))->orderBy('urutan', 'ASC')->get()->getResultArray();

        $val = [];
        foreach ($perangkat as $i) {
            $db = db($order);
            $status = $db->where("perangkat", $i['perangkat'])->whereIn('metode', ['Play', 'Over'])->get()->getRowArray();

            if ($status) {
                $i['id'] = $status['id'];
                $i['status'] = $status['status'];
                $i['durasi'] = $status['durasi'];
                $i['metode'] = ($status['durasi'] == 0 && $status['metode'] == "Play" ? "Open" : $status['metode']);
                $i['waktu'] = ($status['durasi'] == 0 ? durasi_jam($status['dari']) : sisa_jam($status['ke']));
            } else {
                $i['durasi'] = 0;
                $i['metode'] = "Available";
                $i['waktu'] = "-";
            }
            $val[] = $i;
        }

        $data = ['judul' => strtoupper($order), 'data' => $val];

        return $data;
    }

    // set interval dari web iot
    function cek_notif($grup)
    {
        $exp = explode("-", $grup);
        $lokasi = str_replace("-", " ", $grup);
        $divisi = $exp[0];

        $db = db('iot');
        $q_absen = $db->where('kategori', 'Absen')->get()->getResultArray();

        $absen = null;
        $dbp = db('poin');
        $dbu = db('user');

        $absen_iot = null;
        if ($lokasi == "Billiard 1") {
            // hapus absen saat waktu tap habis
            if ($q_absen) {
                foreach ($q_absen as $i) {
                    if (time() > (int) $i['end']) {
                        $db->where('id', $i['id']);
                        $db->delete();
                    } else {
                        if ($i['grup'] == $lokasi) {
                            $absen = $i;
                        }
                    }
                }
            }

            // ghoib absen otomatis iot
            $abs_iot = $this->absen_iot();
            if ($abs_iot['status'] == "400") {
                $absen_iot = $abs_iot['message'];
            }

            // tidak mencatat listrik dan bersih2
            $q_poin = $dbp->orderBy('tgl', "DESC")->where('kategori', "Listrik")->get()->getResultArray();
            $res = null;
            foreach ($q_poin as $i) {
                if (date('d') == date('d', $i['tgl']) && date('m') == date('m', $i['tgl']) && date('Y') == date('Y', $i['tgl'])) {
                    $res = $i;
                }
            }
            if (!is_null($res)) {
                if ((int)date('H') >= 22) {
                    $users = $dbu->whereNotIn('role', ["Member", "Root", "Ceo"])->get()->getResultArray();
                    foreach ($users as $i) {
                        $data = [
                            'tgl' => time(),
                            'kategori' => "Disiplin",
                            'divisi' => $i['role'],
                            'grup' => "",
                            'disiplin' => "Tidak bersih-bersih dan mencatat listrik dengan poin: -10",
                            'shift' => "",
                            'user_id' => $i['id'],
                            'petugas' => $i['nama'],
                            'poin' => -10
                        ];
                        $dbp->insert($data);
                    }
                }
            }
        } else {
            foreach ($q_absen as $i) {
                if ($i['divisi'] == $divisi) {
                    if (time() > (int) $i['end']) {
                        $db->where('id', $i['id']);
                        $db->delete();
                    } else {
                        if ($i['grup'] == $lokasi) {
                            $absen = $i;
                        }
                    }
                }
            }
        }

        $q = $db->where('grup', $lokasi)->whereNotIn('kategori', ['Absen'])->get()->getRowArray();
        // hapus user tap
        if ($q) {
            if ($q['kategori'] == "Tap") {
                if (time() > (int)$q['end']) {
                    $db->where('id', $q['id']);
                    $db->delete();
                }
            }
        }

        $sos = null;

        // Sos
        $q_sos = $dbp->where('kategori', 'Sos')->where('grup', $lokasi)->where('notif', '')->get()->getRowArray();
        if ($q_sos) {
            $sos = $q_sos['petugas'];
        }

        $perangkats = $this->all_perangkat($grup);
        if ($lokasi == "Billiard 1") {
            $divisions = [];
            foreach (options('Divisi') as $i) {
                $divisions[] = $i['value'];
            }
            $divisions[] = "Root";


            foreach ($perangkats['rental'] as $i) {
                if ((int)$i['durasi'] > 0 && $i['status'] == 1) {
                    if (time() > (int)$i['ke']) {
                        $dbper = db(strtolower($divisi));
                        $per = $dbper->where('id', $i['id'])->get()->getRowArray();

                        if ($per) {
                            $per['status'] = 0;
                            $per['metode'] = "Tap";


                            $user = $dbu->where('id', $per['user_id'])->get()->getRowArray();

                            if ($user) {
                                if (in_array($user['role'], $divisions)) {
                                    $per['metode'] = "Over";
                                }
                            }

                            $dbper->where('id', $per['id']);
                            $dbper->update($per);
                        }
                    }
                }
            }
        }

        sukses_js('Sukses', $perangkats, $sos, $q, $absen, $absen_iot);
    }
    function cek_absen_tap($divisi, $grup)
    {
        // mencari shift saat ini
        // $now_time = strtotime(date("18:30:00"));
        $now_time = time();

        $db = db('shift');
        $q_all = $db->where('divisi', $divisi)->get()->getResultArray();

        $shift_now = [];
        $selisihTerdekat = PHP_INT_MAX;
        foreach ($q_all as $i) {
            // Memecah rentang waktu, misalnya "13:00-18:00"
            $rentang = explode('-', $i['pukul']);
            $mulai = strtotime($rentang[0] . ":00"); // Waktu mulai
            $akhir = strtotime($rentang[1] . ":00"); // Waktu akhir
            $i['start'] = $mulai;
            $i['end'] = $akhir;

            $waktu_akhir = 0;
            if ($rentang[1] == "04:00") {
                if ($rentang[0] == "00:00") {
                    $waktu_akhir = strtotime($rentang[1] . ":00"); // Waktu akhir
                } else {
                    $tgl = ((int)date('d') + 1) . "-" . date('m') . "-" . date("Y") . " " . $rentang[1] . ":00";
                    $waktu_akhir = strtotime($tgl); // Waktu akhir
                }
            } else {
                $waktu_akhir = strtotime($rentang[1] . ":00"); // Waktu akhir
            }
            $i['waktu_akhir'] = $waktu_akhir;

            // Periksa apakah waktu saat ini ada dalam rentang
            if ($mulai <= $now_time && $now_time <= $akhir) {
                $shift_now = $i; // Langsung mengembalikan rentang waktu jika cocok
                break;
            }

            // Bandingkan selisih waktu mulai, baik positif atau negatif
            $selisihMulai = abs($mulai - $now_time);
            if ($selisihMulai < $selisihTerdekat) {
                $selisihTerdekat = $selisihMulai;
                $shift_now = $i;
            }
        }

        $db_iot = db('iot');
        $data_iot = $db_iot->where('kategori', 'Absen')->get()->getResultArray();
        if ($data_iot) {
            foreach ($data_iot as $i) {
                if ($i['status'] == "Tap" && $divisi == $i['divisi']) {
                    gagal_js("Shift belum berakhir...");
                }
                if ($i['status'] == "200") {
                    gagal_js("Tunggu, proses lain berlangsung...");
                }
            }
        }

        $dbu = db('user');
        $user = $dbu->where('id', $shift_now['user_id'])->get()->getRowArray();
        $fun = new \App\Models\FunModel();
        $fulus = $fun->dekripsi_fulus($user, $user['fulus']);
        if (is_null($fulus)) {
            gagal_js("Dekripsi gagal...");
        }

        // apakah sudah absen
        $db_poin = db('poin');
        $poin = $db_poin->where('kategori', 'Absen')->where('divisi', $divisi)->where('user_id', $shift_now['user_id'])->where('shift', $shift_now['shift'])->orderBy('tgl', 'DESC')->get()->getRowArray();

        if ($poin) {
            if (date('d', $poin['tgl']) == date('d')) {
                if ($poin['poin'] >= 0) {
                    $message = $poin['petugas'] . " sudah absen dengan poin: " . $poin['poin'];
                    gagal_js($message);
                } else {
                    $message = "Absen ditutup: " . $poin['poin'];
                    $data_absen = $db_iot->where('kategori', 'Absen')->where('divisi', $divisi)->get()->getRowArray();

                    if (!$data_absen) {
                        $data = [
                            'kategori' => "Absen",
                            'divisi' => $shift_now['divisi'],
                            'grup' => str_replace("-", " ", $grup),
                            'user_id' => $shift_now['user_id'],
                            'user' => $shift_now['petugas'],
                            'status' => "Tap",
                            'message' => "By click",
                            'saldo' => $fulus,
                            'start' => $shift_now['start'],
                            'end' => $shift_now['waktu_akhir'],
                            'role' => $user['role']
                        ];
                        $db_iot->insert($data);
                    }

                    gagal_js($message, $shift_now);
                }
            }
        }


        // belum waktunya absen
        // if ($now_time < $shift_now['start']) {
        //     $message = "Belum waktunya absen...";
        //     gagal_js($message);
        // }


        $data = [
            'kategori' => "Absen",
            'divisi' => $shift_now['divisi'],
            'grup' => str_replace("-", " ", $grup),
            'user_id' => $shift_now['user_id'],
            'user' => $shift_now['petugas'],
            'status' => "200",
            'message' => "Menunggu tap...",
            'saldo' => $fulus,
            'start' => $shift_now['start'],
            'end' => time() + 15,
            'role' => $user['role']
        ];

        if ($db_iot->insert($data)) {
            sukses_js("Menunggu tap...", $data);
        } else {
            gagal_js("Insert data gagal...");
        }
    }
    function absen_tap($jwt)
    {
        $decode = decode_jwt($jwt);
        $exp = explode(" ", $decode['data2']);
        $grup = $decode['data2'];
        $divisi = $exp[0];

        $divisions = [];
        foreach (options('Divisi') as $i) {
            $divisions[] = $i['value'];
        }
        $divisions[] = "Root";
        $dbu = db('user');
        $user = $dbu->whereIn('role', $divisions)->where('uid', $decode['data'])->get()->getRowArray();

        $db_iot = db('iot');
        $data_iot = $db_iot->where('kategori', 'Absen')->where('grup', $grup)->where('status', "200")->get()->getRowArray();
        if (!$data_iot) {
            gagal_js("Data iot not found!.");
        }

        if (!$user) {
            $data_iot['message'] = "User not found...";
            $data_iot['status'] = "400";
            $db_iot->where('id', $data_iot['id']);
            if ($db_iot->update($data_iot)) {
                gagal_js($data_iot['message']);
            }
        }
        if ($user['role'] !== "Root") {
            if ($user['id'] != $data_iot['user_id']) {
                $data_iot['message'] = "User not match...";
                $data_iot['status'] = "400";
                $db_iot->where('id', $data_iot['id']);
                if ($db_iot->update($data_iot)) {
                    gagal_js($data_iot['message']);
                }
            }
        }


        // mencari shift saat ini
        // $now_time = strtotime(date("18:30:00"));
        $now_time = time();

        $db = db('shift');
        $q_all = $db->where('divisi', $divisi)->get()->getResultArray();

        $shift_now = [];
        $selisihTerdekat = PHP_INT_MAX;
        foreach ($q_all as $i) {
            // Memecah rentang waktu, misalnya "13:00-18:00"
            $rentang = explode('-', $i['pukul']);
            $mulai = strtotime($rentang[0] . ":00"); // Waktu mulai
            if ($rentang[1] == "04:00") {
                if ($rentang[0] == "00:00") {
                    $akhir = strtotime($rentang[1] . ":00"); // Waktu akhir
                } else {
                    $tgl = ((int)date('d') + 1) . "-" . date('m') . "-" . date("Y") . " " . $rentang[1] . ":00";
                    $akhir = strtotime($tgl); // Waktu akhir
                }
            } else {
                $akhir = strtotime($rentang[1] . ":00"); // Waktu akhir
            }
            $i['start'] = $mulai;
            $i['end'] = $akhir;

            // Periksa apakah waktu saat ini ada dalam rentang
            if ($mulai <= $now_time && $now_time <= $akhir) {
                $shift_now = $i; // Langsung mengembalikan rentang waktu jika cocok
                break;
            }

            // Bandingkan selisih waktu mulai, baik positif atau negatif
            $selisihMulai = abs($mulai - $now_time);
            if ($selisihMulai < $selisihTerdekat) {
                $selisihTerdekat = $selisihMulai;
                $shift_now = $i;
            }
        }



        // apakah sudah absen
        $db_poin = db('poin');
        $poin = $db_poin->where('kategori', 'Absen')->where('divisi', $divisi)->where('shift', $shift_now['shift'])->orderBy('tgl', 'DESC')->get()->getRowArray();

        if ($poin) {
            if (date('d', $poin['tgl']) == date('d')) {
                $message = $poin['petugas'] . ", sudah absen dengan poin: " . $poin['poin'];
                $data_iot['status'] = "400";
                $data_iot['message'] = $message;
                $db_iot->where('id', $data_iot['id']);
                if ($db_iot->update($data_iot)) {
                    gagal_js($data_iot['message']);
                }
            }
        }

        // belum waktunya absen
        if ($user['role'] !== "Root") {
            if ($now_time < $shift_now['start']) {
                $message = "Belum waktunya absen!.";
                $data_iot['status'] = "400";
                $data_iot['message'] = $message;
                $db_iot->where('id', $data_iot['id']);
                if ($db_iot->update($data_iot)) {
                    gagal_js($data_iot['message']);
                }
            }
        }

        // $poinmu = (round(((int)$shift_now['start'] - (int)$now_time)  / 60)) + (int)settings('Absen');
        $poinmu = round(((int)$now_time - (int)$shift_now['start'])  / 60);

        if ($poinmu <= 31) {
            $poinmu = (int)settings('Absen') - $poinmu;
        } else {
            $poinmu = ($poinmu - (int)settings('Absen')) * -2;
        }

        $data_iot['kategori'] = "Absen";
        $data_iot['divisi'] = $shift_now['divisi'];
        $data_iot['grup'] = $grup;
        $data_iot['user_id'] = $shift_now['user_id'];
        $data_iot['user'] = $shift_now['petugas'];
        $data_iot['start'] = $shift_now['start'];
        $data_iot['end'] = $shift_now['end'];



        $db_iot->where('id', $data_iot['id']);
        $db_iot->update($data_iot);


        $data_iot['status'] = "Tap";

        if ($poinmu >= 0) {
            $message = $shift_now["petugas"] . " tepat waktu dan poinmu: " . $poinmu;
            $data_iot['message'] = $message;
        } else {
            $message = $shift_now["petugas"] . " terlambat dan poinmu: " . $poinmu;
            $data_iot['message'] = $message;
        }

        $db_iot->where('id', $data_iot['id']);
        if ($db_iot->update($data_iot)) {
            if ($now_time >= $shift_now['start']) {
                $data = [
                    'tgl' => $now_time,
                    'kategori' => $data_iot['kategori'],
                    'divisi' => $data_iot['divisi'],
                    'grup' => $data_iot['grup'],
                    'disiplin' => $data_iot['message'],
                    'shift' => $shift_now['shift'],
                    'user_id' => $data_iot['user_id'],
                    'petugas' => $data_iot['user'],
                    'poin' => $poinmu
                ];
                $db_poin->insert($data);
            }
        }
    }
    function absen_iot()
    {
        $db = db('shift');
        $q_all = $db->get()->getResultArray();
        // $now_time = strtotime(date("13:46:00"));
        $now_time = time();
        $db_poin = db('poin');

        $petugas = [];
        foreach ($q_all as $i) {
            // Memecah rentang waktu, misalnya "13:00-18:00"
            $rentang = explode('-', $i['pukul']);
            $mulai = strtotime($rentang[0] . ":00"); // Waktu mulai

            $selisih = round(($mulai - $now_time) / 60);
            if ($selisih < -45) {
                $q_poin = $db_poin->where('kategori', 'Absen')->where('divisi', $i["divisi"])->where('user_id', $i['user_id'])->where('shift', $i['shift'])->orderBy('tgl', 'DESC')->get()->getRowArray();

                $poin = false;
                if ($q_poin) {
                    if (date('d', $q_poin['tgl']) == date('d') && date('m', $q_poin['tgl']) == date('m') && date('Y', $q_poin['tgl']) == date('Y')) {
                        $poin = true;
                    }
                }

                if (!$poin) {

                    $data = [
                        'tgl' => $now_time,
                        'kategori' => "Absen",
                        'divisi' => $i['divisi'],
                        'user_id' => $i['user_id'],
                        'petugas' => $i['petugas'],
                        'shift' => $i['shift'],
                        'grup' => "Billiard 1",
                        'poin' => -60,
                        'disiplin' => $i['petugas'] . " GHOIB dan poin dikurangi: -60"
                    ];

                    if ($db_poin->insert($data)) {
                        $db_iot = db('iot');
                        $grup = $i['divisi'] . " 1";

                        $absen_exist = $db_iot->where('kategori', "Absen")->where('grup', $grup)->get()->getRowArray();
                        if (!$absen_exist) {
                            $dbu = db('user');
                            $user = $dbu->where('id', $i['user_id'])->get()->getRowArray();
                            $fun = new \App\Models\FunModel();
                            $fulus = $fun->dekripsi_fulus($user, $user['fulus']);
                            if (is_null($fulus)) {
                                gagal_js("Dekripsi gagal...");
                            }

                            $exp = explode("-", $i['pukul']);
                            $data_iot = [
                                'kategori' => "Absen",
                                'divisi' => $i['divisi'],
                                'grup' => $grup,
                                'user_id' => $i['user_id'],
                                'user' => $i['petugas'],
                                'status' => "Tap",
                                'message' => "By iot",
                                'saldo' => $fulus,
                                'start' => strtotime($exp[0] . ":00"),
                                'end' => strtotime($exp[1] . ":00"),
                                'role' => $user['role']
                            ];
                            $db_iot->insert($data_iot);
                        }
                        $petugas[] = $i['petugas'];
                    }
                }
            }
        }
        $res = [];

        if (count($petugas) == 0) {
            $res['status'] = "200";
            $res['message'] = "Clear";
        } else {
            $res['status'] = "400";
            $res["message"] = implode(", ", $petugas) . " GHOIB dan poin dikurangi: -60";
        }

        return $res;
    }

    public function insert_sos($grup)
    {
        $exp = explode("-", $grup);
        $lokasi = str_replace("-", " ", $grup);
        $divisi = $exp[0];



        $db = db('poin');
        $q = $db->where('kategori', 'Sos')->where('divisi', $divisi)->where('notif', "")->get()->getRowArray();

        if ($q) {
            gagal_js("Sos in progress...");
        }


        $now_time = time();

        $dbs = db('shift');
        $q_all = $dbs->where('divisi', $divisi)->get()->getResultArray();

        $shift_now = [];
        $selisihTerdekat = PHP_INT_MAX;
        foreach ($q_all as $i) {
            // Memecah rentang waktu, misalnya "13:00-18:00"
            $rentang = explode('-', $i['pukul']);
            $mulai = strtotime($rentang[0] . ":00"); // Waktu mulai
            $akhir = strtotime($rentang[1] . ":00"); // Waktu akhir
            $i['start'] = $mulai;
            $i['end'] = $akhir;

            // Periksa apakah waktu saat ini ada dalam rentang
            if ($mulai <= $now_time && $now_time <= $akhir) {
                $shift_now = $i; // Langsung mengembalikan rentang waktu jika cocok
                break;
            }

            // Bandingkan selisih waktu mulai, baik positif atau negatif
            $selisihMulai = abs($mulai - $now_time);
            if ($selisihMulai < $selisihTerdekat) {
                $selisihTerdekat = $selisihMulai;
                $shift_now = $i;
            }
        }

        $data = [
            'tgl' => time(),
            'kategori' => "Sos",
            'divisi' => $divisi,
            'user_id' => $shift_now['user_id'],
            'grup' => $lokasi,
            'petugas' => $shift_now['petugas'],
            'poin' => -2,
            'shift' => $shift_now['shift'],
            'notif' => "",
            "disiplin" => "Panggilan ke " . strtoupper($lokasi)
        ];

        if ($db->insert($data)) {
            sukses_js("Waiting...");
        } else {
            gagal_js("Sos failed!.");
        }
    }
}
