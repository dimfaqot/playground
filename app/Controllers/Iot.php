<?php

namespace App\Controllers;

use \App\Models\IotModel;

class Iot extends BaseController
{
    public function grup_iot($grup)
    {
        // $fun = new \App\Models\FunModel();
        // $db = db('user');
        // $q = $db->orderBy('id')->get()->getResultArray();

        // foreach ($q as $i) {
        //     $i['fulus'] = $fun->enkripsi_fulus($i, 0);
        //     $db->where('id', $i['id']);
        //     $db->update($i);
        // }


        $fun = new IotModel();
        $data = $fun->all_perangkat($grup);

        return view("public/iot", ['judul' => str_replace("-", " ", $grup), 'data' => $data, 'divisi' => explode("-", $grup)[0]]);
    }

    public function cek_absen()
    {
        $divisi = clear($this->request->getVar('divisi'));
        $grup = clear($this->request->getVar('grup'));
        $fun = new IotModel();
        $fun->cek_absen_tap($divisi, $grup);
    }
    public function play()
    {
        $user = json_decode(json_encode($this->request->getVar('user_tap')), true);
        $shift = json_decode(json_encode($this->request->getVar('shift')), true);
        $transaksi = json_decode(json_encode($this->request->getVar('transaksi')), true);
        $csrf = clear($this->request->getVar("csrf"));
        $grup = clear($this->request->getVar('grup'));
        $order = clear($this->request->getVar('order'));
        $map = clear($this->request->getVar('map'));

        $dec = new \App\Models\FunModel();
        $dekripsi = $dec->dekripsi($csrf);
        $exp = explode(",", $dekripsi);

        if ((time() - (int)$exp[0]) > 15) {
            gagal_js("Time over");
        }

        if ($user['user_id'] != $exp[1]) {
            gagal_js("User invalid");
        }

        if ($transaksi['durasi'] == 0 && $user['role'] == "Member") {
            gagal_js("Main open harus Admin");
        }

        if ($user['role'] == "Member") {
            $hutang = $dec->hutangs($user['user_id']);

            if ($hutang['total'] > 0) {
                gagal_js("Lunasi dulu hutangmu: " . angka($hutang['total']));
            }
        }


        $exp = explode("-", $grup);
        $lokasi = str_replace("-", " ", $grup);
        $divisi = $exp[0];

        $dbp = db('perangkat');
        $perangkat = $dbp->where('id', $transaksi['id'])->get()->getRowArray();

        if (!$perangkat) {
            gagal_js("Id perangkat not found");
        }

        $time_now = time();
        $dbx = \Config\Database::connect();
        $db = $dbx->table(strtolower($divisi));

        $data = [];
        if ($order == "Reguler") {
            $data = [
                'tgl' => $time_now,
                'perangkat' => $perangkat['perangkat'],
                'dari' => $time_now,
                'ke' => $time_now + ((60 * 60) * (int)$transaksi['durasi']),
                'status' => 1,
                'harga' => $perangkat['harga'],
                'durasi' => (int)$transaksi['durasi'] * 60,
                'pembeli' => $user['nama'],
                'user_id' => $user['user_id'],
                'metode' => "Play"
            ];
            if ($user['role'] == "Member") {
                $data['petugas'] = $shift['user'];
                $data['diskon'] = 0;
                $data['total'] = (int)$perangkat['harga'] * (int)$transaksi['durasi'];
            } elseif ($user['role'] == "Root" || $user['role'] == "Ceo") {
                $data['diskon'] = (int)$perangkat['harga'] * (int)$transaksi['durasi'];
                $data['total'] = 0;
                $data['petugas'] = $shift['user'];
            } else {
                // admin
                $data['petugas'] = $user['nama'];
                $data['diskon'] = 0;
                $data['total'] = (int)$perangkat['harga'] * (int)$transaksi['durasi'];
            }
        }
        if ($order == "Open") {
            $data = [
                'tgl' => $time_now,
                'perangkat' => $perangkat['perangkat'],
                'dari' => $time_now,
                'ke' => 0,
                'harga' => $perangkat['harga'],
                'durasi' => 0,
                'pembeli' => $user['nama'],
                'user_id' => $user['user_id'],
                'status' => 1,
                'metode' => "Play",
                'diskon' => 0,
                'total' => 0,
                'petugas' => $user['nama']
            ];
        }

        if ($db->insert($data)) {
            $new_id = $dbx->insertID();
            // member n reguler ada update saldo
            if ($user['role'] == "Member" && $order == "Reguler") {
                $dbu = db('user');
                $q_user = $dbu->where('id', $data['user_id'])->get()->getRowArray();
                if ($q_user) {
                    $fulus = $dec->dekripsi_fulus($user, $user['fulus']);
                    if (is_null($fulus)) {
                        gagal_js("Dekripsi gagal...");
                    }
                    $saldo = $fulus - (int)$data['total'];
                    $data_tap = [
                        "tgl" => time(),
                        "lokasi" => $map,
                        "user" => $q_user['nama'],
                        "user_id" => $q_user['id'],
                        "saldo" => $fulus,
                        "uang" => $data['total'],
                        "total" => $saldo,
                        "kategori" => "Bayar",
                        "petugas" => $shift['user'],
                        'ket' => substr($divisi, 0, 1) . "-" . $data['perangkat'],
                        'no_nota' => substr($divisi, 0, 1) . date('dmY') . "-" . $new_id,
                        "metode" => $lokasi
                    ];
                    $dbt = db('tap');
                    if ($dbt->insert($data_tap)) {
                        $q_user['fulus'] = $dec->enkripsi($saldo);

                        $dbu->where('id', $q_user['id']);

                        if ($dbu->update($q_user)) {
                            $db_iot = db('iot');
                            $iot = $db_iot->where('kategori', 'Tap')->where('grup', str_replace("-", " ", $grup))->get()->getRowArray();
                            if ($iot) {
                                $db_iot->where('id', $iot['id']);
                                $db_iot->delete();
                            }
                            sukses_js("Transaksi berhasil.", angka($saldo));
                        } else {
                            gagal_js("Update saldo gagal");
                        }
                    } else {
                        gagal_js("Insert riwayat gagal!.");
                    }
                } else {
                    gagal_js("User not found");
                }

                sukses_js("Berhasil");
            } else {
                // selain member tidak ada update saldo
                $db_iot = db('iot');
                $iot = $db_iot->where('kategori', 'Tap')->where('grup', str_replace("-", " ", $grup))->get()->getRowArray();
                if ($iot) {
                    $db_iot->where('id', $iot['id']);
                    $db_iot->delete();
                }
                sukses_js("Berhasil");
            }
        } else {
            gagal_js("Gagal");
        }
    }


    public function cek_notif()
    {
        $fun = new IotModel();
        $fun->cek_notif(clear($this->request->getVar('grup')));
    }

    public function sos()
    {
        $fun = new IotModel();

        $fun->insert_sos(clear($this->request->getVar('grup')));
    }

    public function absen_tap()
    {
        // $data = clear($this->request->getVar('data'));
        // $data2 = clear($this->request->getVar('data2'));
        // $jwt = encode_jwt(['data' => $data, 'data2' => $data2]);
        $jwt = $this->request->getVar('jwt');
        $fun = new IotModel();
        $fun->absen_tap($jwt);
    }
    public function update_perangkat()
    {
        $id = clear($this->request->getVar('id'));
        $db = db('perangkat');
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js('Id not found...');
        }

        $q['status'] = ($q['status'] == 0 ? 1 : 0);
        $db->where('id', $q['id']);
        if ($db->update($q)) {
            sukses_js("Sukses...");
        } else {
            gagal_js("Update gagal...");
        }
    }

    public function csrf()
    {
        $fun = new \App\Models\FunModel();
        sukses_js("Sukses", $fun->enkripsi(time() . "," . clear($this->request->getVar('user_id'))));
    }
    public function afk()
    {
        $id = clear($this->request->getVar('id'));
        $divisi = clear($this->request->getVar('divisi'));
        $petugas = clear($this->request->getVar('petugas'));
        $grup = clear($this->request->getVar('grup'));

        $db = db(strtolower($divisi));
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal_js("Id not found...");
        }

        $q['status'] = 0;
        $q['metode'] = "Tap";
        $q['petugas'] = $petugas;

        $db->where('id', $q['id']);
        if ($db->update($q)) {
            $db_iot = db('iot');
            $iot = $db_iot->where('kategori', "Tap")->where('grup', $grup)->get()->getRowArray();
            if ($iot) {
                $db_iot->where('id', $iot['id']);
                $db_iot->delete();
            }
            sukses_js("Berhasil...");
        }
    }
    public function akhiri()
    {
        $fun = new \App\Models\FunModel();
        sukses_js("Sukses", $fun->akhiri(json_decode(json_encode($this->request->getVar('val')), true)));
    }


    // dari tap perangkat iot

    public function iot_tapping()
    {
        // $data = clear($this->request->getVar('data'));
        // $data2 = clear($this->request->getVar('data2'));
        // $jwt = encode_jwt(['data' => $data, 'data2' => $data2]);
        $jwt = clear($this->request->getVar('jwt'));
        $decode = decode_jwt($jwt);

        $exp = explode(" ", $decode['data2']);
        $uid = $decode['data'];
        $dbu = db('user');
        $user = $dbu->where('uid', $uid)->get()->getRowArray();
        if (!$user) {
            gagal_js("Gagal");
        }
        if ($user['status'] == 0) {
            gagal_js("You are banned...");
        }


        $db = db('iot');

        $exist = $db->where('kategori', "Tap")->where('grup', $decode['data2'])->get()->getRowArray();
        if ($exist) {
            gagal_js("Tunggu proses lain selesai...");
        }

        $now_time = time();
        $fun = new \App\Models\FunModel();
        $fulus = $fun->dekripsi_fulus($user, $user['fulus']);
        if (is_null($fulus)) {
            gagal_js("Dekripsi gagal...");
        }
        $data = [
            'kategori' => "Tap",
            'divisi' => $exp[0],
            'grup' => $decode['data2'],
            'user_id' => $user['id'],
            'user' => $user['nama'],
            'start' => $now_time,
            'end' => $now_time + 30,
            'status' => "200",
            'saldo' => $fulus,
            'role' => $user['role']
        ];

        if ($db->insert($data)) {
            sukses_js("sukses");
        } else {
            gagal_js("Gagal");
        }
    }

    public function cari_user()
    {
        $val = clear($this->request->getVar('val'));

        $db = db('user');
        $q = $db->whereIn('role', ['Member'])->like('nama', $val, 'both')->orderBy('nama', 'ASC')->limit(10)->get()->getResultArray();

        $res = [];

        $fun = new \App\Models\FunModel();
        foreach ($q as $i) {
            $i['saldo'] = $fun->dekripsi_fulus($i, $i['fulus']);
            $res[] = $i;
        }
        sukses_js("Ok", $res);
    }


    public function cek_metode_tap()
    {
        $id = $this->request->getVar('newId');
        $jenis = clear($this->request->getVar('jenis'));
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $user_tap = json_decode(json_encode($this->request->getVar('user_tap')), true);
        $controller = clear($this->request->getVar('controller'));
        $judul = clear($this->request->getVar('judul')); //grup

        if ($user_tap['role'] == "Member") {
            gagal_js('Harus admin...');
        }

        $dbx = \Config\Database::connect();
        $dbm = $dbx->table('metode');
        $q = $dbm->get()->getRowArray();

        $fun = new \App\Models\FunModel();

        $db = db('user');
        $user = [];
        $total = 0;

        if ($q) {
            if ($q['id'] == $id) {
                sukses_js(($q['status'] == "Tap" ? "Proses tapping..." : $q['message']), $q['id'], $q['status']);
            } else {
                sukses_js("Transaksi lain sedang berlangsung!.", 0);
            }
        } else {
            if ($id == 0) {
                $db = db('user');
                $user = [];
                $total = 0;
                $perangkat_id = 0;
                if ($controller == "hutang") {
                    foreach ($data as $i) {
                        $dbh = db(strtolower($i['kategori']));
                        $qh = $dbh->where('id', $i['id'])->where('metode', 'Hutang')->get()->getRowArray();
                        if ($qh) {

                            $total += (int)$qh['total'];

                            $u = $db->where('id', $qh['user_id'])->get()->getRowArray();
                            if ($u) {
                                $user = $u;
                            } else {
                                gagal_js("User not found!.");
                            }
                        } else {
                            gagal_js("Id not found!.");
                        }
                    }
                } elseif ($controller == "ps" || $controller == "billiard") {
                    $dbp = db($controller);
                    $q = $dbp->where('id', $data['id'])->get()->getRowArray();
                    $perangkat_id = $q['id'];
                    if ($jenis == "Bayar") {
                        $akhiri = $fun->akhiri($q);
                        $total = (int)$akhiri['harga'] - (int)$data['diskon'];
                        $u = $db->where('id', $data['user_id'])->get()->getRowArray();
                        if ($u) {
                            $user = $u;
                        } else {
                            gagal_js("User not found!.");
                        }
                    }
                    if ($jenis == "Hutang") {
                        $total = $q['total'];
                        if ($q) {
                            $u = $db->where('id', $q['user_id'])->get()->getRowArray();
                            if ($u) {
                                $user = $u;
                            } else {
                                gagal_js("User not found!.");
                            }
                        } else {
                            gagal_js("Id not found!.");
                        }
                    }
                } else {
                    $u = $db->where('id', $data[0]['user_id'])->get()->getRowArray();

                    if ($u) {
                        $user = $u;
                    } else {
                        gagal_js("User not found!.");
                    }
                    $dbb = db('barang');
                    foreach ($data as $i) {
                        if ($jenis == "Bayar") {
                            $qb = $dbb->where('barang', $i['barang'])->where('kategori', upper_first($controller))->get()->getRowArray();
                            if ($qb) {
                                $total += ($qb['harga'] * (int)$i['qty']) - (int)$i['diskon'];
                            } else {
                                gagal_js("Barang not found!.");
                            }
                        } else {
                            $dbk = db($controller);
                            $qk = $dbk->where('id', $i['id'])->get()->getRowArray();
                            if ($qk) {
                                $total += $qk['total'];
                            } else {
                                gagal_js("Barang not found!.");
                            }
                        }
                    }
                }

                $fulus = $fun->dekripsi_fulus($user, $user['fulus']);
                if (is_null($fulus)) {
                    gagal_js("Dekripsi gagal...");
                }

                // jika saldo tidak cukup status 401
                if ($fulus < (int)$total) {
                    gagal_js("Saldo tidak cukup!", angka($fulus), angka($total));
                } else {
                    // jika saldo cukup status 200
                    $val = [
                        "total" => $total,
                        "jenis" => $jenis,
                        "controller" => $controller,
                        "grup" => $judul,
                        "user_id" => $user['id'],
                        "pembeli" => $user['nama'],
                        "status" => "200",
                        "message" => "Menunggu tap...",
                        'petugas' => $user_tap['user'],
                        'perangkat_id' => $perangkat_id
                    ];

                    if ($dbm->insert($val)) {
                        $val['fulus'] = angka($fulus);
                        $val['pembeli'] = $user['nama'];
                        sukses_js("Menunggu tap...", $dbx->insertID(), "200", $val);
                    }
                }
            }
        }
    }
    public function delete_id_metode_tap()
    {
        $id = $this->request->getVar('newId');
        $db = db('metode');
        $q = $db->where('id', $id)->get()->getRowArray();
        if ($q) {
            $db_iot = db('iot');
            $iot = $db_iot->where('kategori', "Tap")->where('grup', $q['grup'])->get()->getRowArray();
            if ($iot) {
                $db_iot->where('id', $iot['id']);
                $db_iot->delete();
            }
            $db->where('id', $id);
            if ($db->delete()) {
                sukses_js("Sukses", $iot);
            } else {
                gagal_js("Delete new id failed!.");
            }
        } else {
            gagal_js("Id not found!.");
        }
    }

    public function metode_tap()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $shift = json_decode(json_encode($this->request->getVar('shift')), true);
        $new_id = clear($this->request->getVar('newId'));
        $lokasi = clear($this->request->getVar('lokasi'));

        $dbm = db('metode');
        $q = $dbm->where('id', $new_id)->get()->getRowArray();
        $fun = new \App\Models\FunModel();
        if ($q) {
            $db = db('user');
            $user = $db->where('id', $q['user_id'])->get()->getRowArray();
            // jika ada data metode
            $eksekusi = $this->update_hutang($data, $q['jenis'], $q['controller'], $user);
            $fun->tap($user, $eksekusi, $q, $new_id, $lokasi, $shift); //proses terakhir
        } else {
            gagal_js("Id metode not found!.");
        }
    }

    function update_hutang($data, $jenis, $controller, $user)
    {
        $total = 0;
        $no_notas = [];
        $err = [];
        $barangs = [];
        if ($controller == "hutang") {
            foreach ($data as $i) {
                $db = db(strtolower($i['kategori']));
                $q = $db->where('id', $i['id'])->where('metode', 'Hutang')->get()->getRowArray();
                if ($q) {
                    $q['metode'] = "Tap";
                    $db->where('id', $q['id']);
                    if ($db->update($q)) {
                        $total += (int)$q['total'];
                        if ($i['kategori'] == "Ps" || $i['kategori'] == "Billiard") {
                            $barangs[] = upper_first(substr($i['kategori'], 0, 1)) . "-" . $i['barang'];
                        } else {
                            $barangs[] = $i['barang'];
                        }
                        if (!in_array($i['no_nota'], $no_notas)) {
                            $no_notas[] = $i['no_nota'];
                        }
                    } else {
                        $err[] = $i['barang'];
                    }
                } else {
                    $err[] = $i['barang'];
                }
            }
        } elseif ($controller == "billiard" || $controller == "ps") {
            $db = db($controller);
            $q = $db->where('id', $data['id'])->get()->getRowArray();
            $fun = new \App\Models\FunModel();
            $akhiri = $fun->akhiri($q);
            if ($q) {
                $q['metode'] = "Tap";
                if ($jenis == "Bayar") {
                    if ($q['durasi'] == 0) {
                        $q['ke'] = time();
                    }
                    $q['durasi'] = $akhiri['durasi'];
                    $q['status'] = 0;
                    $q['total'] = $akhiri['harga'] - (int)$data['diskon'];
                    $q['diskon'] = (int)$data['diskon'];
                    $q['user_id'] = $data['user_id'];
                    $q['pembeli'] = $data['pembeli'];
                }

                $db->where('id', $q['id']);
                if ($db->update($q)) {
                    $not = upper_first(substr($controller, 0, 1)) . $q['id'];
                    if (!in_array($not, $no_notas)) {
                        $no_notas[] = $not;
                    }
                    $total += (int)$q['total'];
                    $barangs[] = upper_first(substr($controller, 0, 1)) . "-" . $q['perangkat'];
                } else {
                    $err[] = $q['perangkat'];
                }
            } else {
                gagal_js("Id " . $controller . " not found!.");
            }
        } else {
            $db = db($controller);
            $dbb = db('barang');
            foreach ($data as $i) {
                $time = time();
                $no_nota = strtoupper(substr($controller, 0, 1)) . no_nota($time, $controller);
                if ($jenis == "Bayar") {
                    $q = $dbb->where('barang', $i['barang'])->where('kategori', upper_first($controller))->get()->getRowArray();
                    if ($q) {
                        $val = [];
                        $val['tgl'] = $time;
                        $val['no_nota'] = $no_nota;
                        $val['metode'] = 'Tap';
                        $val['pembeli'] = $user['nama'];
                        $val['user_id'] = $user['id'];
                        $val['barang'] = $i['barang'];
                        $val['qty'] = $i['qty'];
                        $val['harga'] = $q['harga'];
                        $val['diskon'] = $i['diskon'];
                        $val['total'] = ($q['harga'] * (int)$i['qty']) - (int)$i['diskon'];
                        $val['petugas'] = user()['nama'];

                        if ($db->insert($val)) {
                            if (!in_array($val['no_nota'], $no_notas)) {
                                $no_notas[] = $val['no_nota'];
                            }
                            $barangs[] = $q['barang'];
                            $total += (int)$val['total'];
                            $q['qty'] -= (int)$val['qty'];
                            $dbb->where('id', $q['id']);
                            $dbb->update($q);
                        } else {
                            $err[] = $i['barang'];
                        }
                    } else {
                        $err[] = $i['barang'];
                    }
                } else {
                    $q = $db->where('id', $i['id'])->get()->getRowArray();
                    if ($q) {
                        $q['metode'] = "Tap";

                        $db->where('id', $q['id']);
                        if ($db->update($q)) {
                            if (!in_array($q['no_nota'], $no_notas)) {
                                $no_notas[] = $q['no_nota'];
                            }
                            $barangs[] = $q['barang'];
                            $total += (int)$q['total'];
                        } else {
                            $err[] = $q['barang'];
                        }
                    } else {
                        $err[] = $q['barang'];
                    }
                }
            }
        }

        $res = ['total' => $total, 'err' => $err, 'no_nota' => $no_notas, 'jenis' => $jenis, 'barang' => $barangs];

        return $res;
    }

    public function bayar_tap()
    {
        // $data = clear($this->request->getVar('data'));
        // $data2 = clear($this->request->getVar('data2'));
        // $jwt = encode_jwt(['data' => $data, 'data2' => $data2]);
        $decode = decode_jwt($this->request->getVar('jwt'));
        $controller = strtolower(explode(" ", $decode['data2'])[0]);

        // mencari metode tap, data dimasukkan admin saat permainan selesai, berisi rincian pembayaran
        $db = db('metode');
        $q = $db->where("controller", $controller)->where('status', '200')->get()->getRowArray();

        if (!$q) {
            gagal_js("Data not found");
        }

        // mencocokan user dari yang ngetap dengan rincian pembayaran
        $dbu = db('user');
        $qu = $dbu->where('uid', $decode['data'])->get()->getRowArray();
        $user = null;

        if (!$qu) {
            $q['message'] = "Invalid card...";
            $db->where('id', $q['id']);
            if ($db->update($q)) {
                gagal_js($q['message']);
            }
        }

        $user = null;
        if ($qu['id'] == $q['user_id']) {
            $user = $qu;
        }

        if (!$user) {
            $q['message'] = "User not match...";
            $db->where('id', $q['id']);
            if ($db->update($q)) {
                gagal_js($q['message']);
            }
        }

        $fun = new \App\Models\FunModel();
        $fulus = $fun->dekripsi_fulus($user, $user['fulus']);
        if (is_null($fulus)) {
            gagal_js("Dekripsi gagal...");
        }
        // cek apakah saldo cukup
        if ($fulus < (int)$q['total']) {
            $q['message'] = "Saldo kurang...";
            $db->where('id', $q['id']);
            if ($db->update($q)) {
                gagal_js($q['message']);
            }
        }

        // mengupdate petugas
        $db_perangkat = db($controller);
        $perangkat = $db_perangkat->where('id', $q['perangkat_id'])->whereIn('metode', ['Play', 'Over'])->get()->getRowArray();

        $q['message'] = "Berhasil...";
        $q['status'] = "Tap";

        $db->where('id', $q['id']);
        if ($db->update($q)) {
            if ($perangkat) {
                $perangkat['petugas'] = $q['petugas'];
                $db_perangkat->where('id', $perangkat['id']);
                $db_perangkat->update($perangkat);
            }
            sukses_js($q['message']);
        }
    }
    public function transaksi()
    {
        $id = clear($this->request->getVar('id'));
        $divisi = clear($this->request->getVar('div'));
        $metode = clear($this->request->getVar('metode'));
        $grup = clear($this->request->getVar('grup'));
        $petugas = clear($this->request->getVar('petugas'));
        $user_id = clear($this->request->getVar('user_id'));
        $diskon = rp_to_int(clear($this->request->getVar('diskon')));
        $uang_pembayaran = rp_to_int(clear($this->request->getVar('uang_pembayaran')));

        $db = db(strtolower($divisi));

        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal_js('Id not found...');
        }
        $dbu = db('user');
        $user = $dbu->where('id', $user_id)->get()->getRowArray();

        if (!$q) {
            gagal_js('User not found...');
        }

        $fun = new \App\Models\FunModel();

        $akhiri = $fun->akhiri($q);
        $total = (int) $akhiri['harga'] - (int)$diskon;

        if ($uang_pembayaran < $total) {
            gagal_js('Uang kurang...');
        }

        $q['metode'] = $metode;
        $q['petugas'] = $petugas;
        $q['pembeli'] = $user['nama'];
        $q['durasi'] = $akhiri['durasi'];
        $q['user_id'] = $user['id'];
        $q['diskon'] = $diskon;
        $q['total'] = $total;
        $q['ke'] = time();
        $q['status'] = 0;
        $db->where('id', $q['id']);

        if ($db->update($q)) {
            $db_iot = db('iot');
            $iot = $db_iot->where('kategori', 'Tap')->where('grup', $grup)->get()->getRowArray();
            if ($iot) {
                $db_iot->where('id', $iot['id']);
                $db_iot->delete();
            }
            sukses_js("Sukses...", angka((int)$uang_pembayaran - (int)$total));
        } else {
            gagal_js("Gagal...");
        }
    }

    public function esp()
    {
        $jwt = clear($this->request->getVar('jwt'));
        $decode = decode_jwt($jwt);
        $absen = '';
        $perangkat = [];
        $pembayaran = '';
        // $tapping = '';

        // url yang diakses esp untuk mengecek apakah ada Absen dengan status 200 di table iot
        // Absen hanya untuk tablet Billiard 1
        if ($decode['data2'] == "Billiard 1") {
            $db = db('iot');
            $q = $db->where('kategori', 'Absen')->where('grup', "Billiard 1")->where('status', '200')->get()->getRowArray();
            if ($q) {
                $absen = "absen_tap";
            }
        }

        // url yang diakses esp untuk mengecek apakah ada data di tabel metode sesuai grup esp berada
        // Tap hanya untuk tablet Billiard 1 Billiard 2 Ps 1 Kantin 1 dan Barber 1
        // untuk pembayaran
        if ($decode['data2'] == "Billiard 1" || $decode['data2'] == "Billiard 2" || $decode['data2'] == "Kantin 1") {
            $db = db('metode');
            $q = $db->where('grup', $decode['data2'])->get()->getRowArray();
            if ($q) {
                $pembayaran = 'bayar_tap';
            }

            // untuk tapping
            // jika belum ditap status 200
            // tap mengubah 200 menjadi Tap
            // $db = db('iot');
            // $q = $db->where('kategori', "Tap")->where('grup', $decode['data2'])->where('status', '200')->get()->getRowArray();
            // if ($q) {
            //     $tapping = "tapping";
            // }
        }

        // url yang diakses esp untuk mengecek apakah perangkat status 1 untuk nyala dan 0 untuk mati
        $db = db('perangkat');
        $q_perangkat = $db->where('lokasi_esp', $decode['data2'])->get()->getResultArray();

        foreach ($q_perangkat as $i) {
            $val = ['pin' => $i['pin'], 'status' => $i['status']];
            if ($i['kategori'] == "Ps" || $i['kategori'] == "Billiard") {
                $dbp = db(strtolower($i['kategori']));
                $q = $dbp->where('perangkat', $i['perangkat'])->where('status', 1)->get()->getRowArray();
                if ($q) {
                    $val['status'] = $q['status'];
                }
            }

            $perangkat[] = $val;
        }

        sukses_js('Sukses', $perangkat, $absen, $pembayaran);
    }
    public function perangkat()
    {
        $jwt = clear($this->request->getVar('jwt'));
        $decode = decode_jwt($jwt);

        // url yang diakses esp untuk mengecek apakah perangkat status 1 untuk nyala dan 0 untuk mati
        $db = db('perangkat');
        $q_perangkat = $db->where('lokasi_esp', $decode['data'])->get()->getResultArray();

        foreach ($q_perangkat as $i) {
            $val = ['pin' => $i['pin'], 'status' => $i['status']];
            if ($i['kategori'] == "Ps" || $i['kategori'] == "Billiard") {
                $dbp = db(strtolower($i['kategori']));
                $q = $dbp->where('perangkat', $i['perangkat'])->where('status', 1)->get()->getRowArray();
                if ($q) {
                    $val['status'] = $q['status'];
                }
            }

            $perangkat[] = $val;
        }

        $fun = new IotModel();
        $perangkat[] = $fun->sos_barcode_kantin();

        sukses_js('Sukses', $perangkat);
    }
    public function billiard()
    {
        $jwt = clear($this->request->getVar('jwt'));
        $decode = decode_jwt($jwt);

        // url yang diakses esp untuk mengecek apakah perangkat status 1 untuk nyala dan 0 untuk mati
        $db = db('perangkat');
        $perangkat = $db->select('pin,status')->where('lokasi_esp', $decode['data'])->get()->getResultArray();

        sukses_js('Sukses', $perangkat);
    }


    // kantin barber

    // public function cari_barang()
    // {
    //     $value = clear($this->request->getVar('value'));
    //     $kategori = clear($this->request->getVar('kategori'));
    //     $db = db('barang');
    //     $q = $db->whereIn('kategori', [$kategori])->like('barang', $value, 'both')->orderBy('barang', 'ASC')->limit(8)->get()->getResultArray();

    //     sukses_js("Sukses", $q);
    // }
    // public function transaction()
    // {
    //     $data = json_decode(json_encode($this->request->getVar("data_transaksi")), true);
    //     $pembeli = json_decode(json_encode($this->request->getVar("pembeli")), true);
    //     $metode = upper_first(clear($this->request->getVar("metode")));
    //     $uang_pembayaran = rp_to_int(clear($this->request->getVar("uang_pembayaran")));
    //     $total = rp_to_int(clear($this->request->getVar("total")));
    //     $no_nota = clear($this->request->getVar("no_nota"));

    //     $db = db(menu()['tabel']);
    //     if ($uang_pembayaran < $total) {
    //         gagal_js("Uang kurang!.");
    //     }
    //     $nota_exist = $db->where('no_nota', $no_nota)->get()->getResultArray();

    //     if ($nota_exist) {
    //         $total2 = 0;
    //         $err = [];
    //         foreach ($data as $i) {
    //             $i['metode'] = $metode;
    //             $i['petugas'] = user()['nama'];

    //             $db->where('id', $i['id']);
    //             if ($db->update($i)) {
    //                 $total2 += (int)$i['total'];
    //             } else {
    //                 $err[] = $i['barang'];
    //             }
    //         }

    //         $jwt = base_url("guest/nota/") . encode_jwt(['tabel' => menu()['tabel'], 'no_nota' => $no_nota]);
    //         sukses_js("Transaksi sukses.", ($uang_pembayaran - $total2), $err, $jwt);
    //     } else {
    //         $dbb = db("barang");
    //         $total2 = 0;
    //         $err = [];
    //         foreach ($data as $i) {
    //             unset($i["id"]);

    //             $i['tgl'] = time();
    //             $i['no_nota'] = $no_nota;
    //             $i['metode'] = $metode;
    //             $i['pembeli'] = $pembeli['nama'];
    //             $i['user_id'] = $pembeli['user_id'];
    //             $i['petugas'] = user()['nama'];

    //             if ($db->insert($i)) {
    //                 $total2 += (int)$i['total'];
    //                 $q = $dbb->where('kategori', menu()['menu'])->where('barang', $i['barang'])->get()->getRowArray();
    //                 if ($q) {
    //                     $q['qty'] -= (int)$i['qty'];
    //                     $dbb->where('id', $q['id']);
    //                     $dbb->update($q);
    //                 }
    //             } else {
    //                 $err[] = $i['barang'];
    //             }
    //         }
    //         $jwt = base_url("guest/nota/") . encode_jwt(['tabel' => menu()['tabel'], 'no_nota' => $no_nota]);
    //         sukses_js("Transaksi sukses.", ($uang_pembayaran - $total2), $err, $jwt);
    //     }
    // }

    // public function cek_absen_kantin()
    // {
    //     $sesi = clear($this->request->getVar('status_absen'));
    //     $db = db('iot');
    //     $q = $db->where('kategori', 'Absen')->where('status', 'Tap')->where('grup', "Kantin 1")->get()->getRowArray();

    //     $sesi = ($q ? "Tap" : "Over");
    //     sukses_js("Sukses", $sesi);
    // }
}
