<?php

namespace App\Controllers;

use App\Models\FunModel;

class Home extends BaseController
{

    function __construct()
    {
        helper('functions');
        if (!session('id')) {
            gagal(base_url(), "Kamu belum login!.");
            die;
        }
        if (url() !== 'logout') {
            menu();
        }
    }



    public function index(): string
    {
        return view('home', ['judul' => "Home"]);
    }


    public function delete()
    {
        $tabel = clear($this->request->getVar('tabel'));
        $id = clear($this->request->getVar('id'));

        $db = db($tabel);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js("Id tidak ditemukan!.");
        }

        $db->where('id', $id);
        if ($db->delete()) {
            sukses_js("Delete data sukses.");
        } else {
            sukses_js("Delete data gagal!.");
        }
    }

    public function logout()
    {
        session()->remove('id');

        sukses(base_url(), 'Logout sukses!.');
    }
    public function switch_tema()
    {
        $db = db('settings');
        $q = $db->where('setting', 'Tema')->get()->getRowArray();
        $q['value'] = ($q['value'] == 'dark' ? 'light' : 'dark');

        $db->where('id', $q['id']);
        if ($db->update($q)) {
            sukses_js('Update tema berhasil.');
        } else {
            gagal_js('Update tema gagal!.');
        }
    }

    public function statistik()
    {
        $fun = new FunModel();
        sukses_js("Sukses", $fun->statistik(clear($this->request->getVar('tahun')), clear($this->request->getVar('kategori'))));
    }
    public function statistik_bulanan()
    {
        $fun = new FunModel();
        sukses_js("Sukses", $fun->statistik_bulanan(clear($this->request->getVar('tahun')), clear($this->request->getVar('bulan')), clear($this->request->getVar('kategori'))));
    }
    public function laporan()
    {
        $fun = new FunModel();
        sukses_js("Sukses", $fun->laporan(clear($this->request->getVar('tahun')), clear($this->request->getVar('bulan')), clear($this->request->getVar('kategori'))));
    }
    public function csrf()
    {
        $fun = new FunModel();
        sukses_js("Sukses", $fun->enkripsi(user()['role'] . "," . time() . "," . clear($this->request->getVar('id'))));
    }

    public function cek_metode_tap()
    {
        $id = $this->request->getVar('newId');
        $jenis = clear($this->request->getVar('jenis'));
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $controller = clear($this->request->getVar('controller'));

        $dbx = \Config\Database::connect();
        $dbm = $dbx->table('metode');
        $q = $dbm->get()->getRowArray();

        $fun = new FunModel();

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
                        "user_id" => $user['id'],
                        "pembeli" => $user['nama'],
                        "status" => "200",
                        "message" => "Menunggu tap...",
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
            $db->where('id', $id);
            if ($db->delete()) {
                sukses_js("Sukses");
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
        $new_id = clear($this->request->getVar('newId'));
        $lokasi = clear($this->request->getVar('lokasi'));

        $dbm = db('metode');
        $q = $dbm->where('id', $new_id)->get()->getRowArray();
        $fun = new FunModel();
        if ($q) {
            $db = db('user');
            $user = $db->where('id', $q['user_id'])->get()->getRowArray();
            // jika ada data metode
            $eksekusi = $this->update_hutang($data, $q['jenis'], $q['controller'], $user);
            $fun->tap($user, $eksekusi, $q, $new_id, $lokasi); //proses terakhir
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
            $fun = new FunModel();
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
                    $not = upper_first(substr($controller, 0, 1)) . date('dmY', $q['tgl']) . "-" . $q['id'];
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

    public function notif()
    {
        $db = db('kantin');
        $q = $db->where('metode', 'Barcode')->groupBy('no_nota')->orderBy('tgl', 'DESC')->get()->getResultArray();
        $message = 0;

        if ($q) {
            $message = count($q);
        }

        if ($message !== 0) {
            $message = "Ada " . angka($message) . " pesanan masuk!.";
            sukses_js($message, "Kantin", base_url('home/kantin/notif'));
        }

        $dbp = db('poin');
        $qs = $dbp->where("kategori", "Sos")->where('notif', "")->orderBy("tgl", "ASC")->get()->getResultArray();

        $sos = null;
        foreach ($qs as $i) {
            if (date('d', $i['tgl']) == date('d')) {
                $sos = $i;
            }
        }

        if ($sos) {
            sukses_js("Panggilan ke " . $sos['divisi'], "Sos", base_url('home/poin/notif'), $sos);
        }

        $q_litrik = $dbp->where("kategori", "Listrik")->orderBy("tgl", "ASC")->get()->getResultArray();

        $listrik = null;
        foreach ($q_litrik as $i) {
            if (date('d', $i['tgl']) == date('d')) {
                $exp = explode(",", $i['notif']);
                if (!in_array(user()['id'], $exp)) {
                    $listrik = $i;
                }
            }
        }

        if ($listrik) {
            sukses_js($listrik['petugas'] . " bersih-bersih dan mencatat listrik dengan poin: " . $listrik['poin'], "Listrik & Kebersihan", base_url('home/poin/notif'), $listrik);
        }

        $poin = $dbp->whereNotIn("kategori", ['Sos', 'Listrik'])->orderBy('tgl', 'ASC')->get()->getResultArray();

        $notif = null;
        foreach ($poin as $i) {
            if (date('d', $i['tgl']) == date('d')) {
                $admins = explode(",", $i['notif']);
                if (!in_array(user()['id'], $admins)) {
                    $notif = $i;
                    break;
                }
            }
        }

        if ($notif) {
            $admins = explode(",", $notif['notif']);
            if (!in_array(user()['id'], $admins)) {
                $notif = $i;
                if ($notif['kategori'] == "Absen") {
                    $message = $notif['petugas'] . " [" . $notif['divisi'] . "] " . ((int)$notif['poin'] < 0 ? "terlambat dengan poin: " : "tepat waktu dengan poin: ") . $notif['poin'];
                } elseif ($notif['kategori'] == "Disiplin") {
                    $message = $notif['petugas'] . " [" . $notif['divisi'] . "] melanggar " . $notif['disiplin'] . " dan poin dikurangi: " . $notif['poin'];
                } elseif ($notif['kategori'] == "Pujian") {
                    $message = $notif['petugas'] . " [" . $notif['divisi'] . "] patut dipuji karena " . $notif['disiplin'] . " dan poin ditambah: " . $notif['poin'];
                }
            }

            sukses_js($message, "Disiplin", base_url('home/poin/notif'), $notif);
        }

        gagal_js("Nol");
    }
    public function notif_kantin()
    {
        $db = db('kantin');

        $q = $db->whereIn('metode', ['Barcode', 'Proses'])->groupBy('no_nota')->orderBy('tgl', 'DESC')->get()->getResultArray();

        $data = [];
        foreach ($q as $i) {
            if ($i['metode'] == "Barcode") {
                $i['status'] = '<div data-status="Menunggu" class="status text_main"><i class="fa-solid fa-clock-rotate-left"></i></div>';
            } elseif ($i['metode'] == "Proses") {
                $i['status'] = '<div data-status="Sedang dimasak" class="status text_main"><i class="fa-solid fa-fire-burner"></i></div>';
            }
            $val = $db->where('no_nota', $i['no_nota'])->orderBy('barang', 'ASC')->get()->getResultArray();

            $data[] = ['profile' => $i, 'data' => $val];
        }
        return view('notif/kantin', ['judul' => "Notif Kantin", 'data' => $data]);
    }
    public function notif_poin()
    {
        $db = db('poin');

        $db;
        if (user()['role'] !== "Root") {
            $db->where('user_id', user()['id']);
        }
        $q = $db->orderBy('tgl', 'ASC')->get()->getResultArray();
        $total = 0;
        foreach ($q as $i) {
            $total += (int)$i['poin'];
        }
        return view('notif/poin', ['judul' => "Notif poin", 'data' => $q, 'total' => $total]);
    }
    public function read_notif()
    {
        $db = db('poin');

        $q = $db->where('id', clear($this->request->getVar('id')))->get()->getRowArray();

        if ($q) {
            $admins = explode(",", $q['notif']);
            if (!in_array(user()['id'], $admins)) {
                if ($q['notif'] == "") {
                    $q['notif'] = user()['id'];
                } else {

                    $q['notif'] .= "," . user()['id'];
                }
                if ($q['kategori'] == "Sos") {
                    $q['user_id'] = user()['id'];
                    $q['petugas'] = user()['nama'];
                    $q['poin'] = 1;
                }

                $db->where('id', $q['id']);
                if ($db->update($q)) {
                    sukses_js("Sukses.");
                }
            } else {
                sukses_js("Sukses.");
            }
        }
    }

    public function header()
    {
        $order = clear($this->request->getVar('order'));
        $user_id = clear($this->request->getVar('user_id'));
        $bulan = clear($this->request->getVar('bulan'));
        $tahun = clear($this->request->getVar('tahun'));

        $db = db('poin');
        $dbu = db('user');
        if ($order == "listrik") {
            $q = $db->where('kategori', "Listrik")->get()->getResultArray();

            $res = ['bersih' => 'Kotor', 'listrik' => ''];
            foreach ($q as $i) {
                if (date('d') == date('d', $i['tgl']) && date('m') == date('m', $i['tgl']) && date('Y') == date('Y', $i['tgl'])) {
                    $res['bersih'] = "Bersih";
                    $res['listrik'] = $i['disiplin'];
                }
            }

            sukses_js("Sukses", $res);
        }
        if ($order == "poin") {
            $users = $dbu->whereNotIn('role', ["Member", "Root", "Ceo"])->orderBy('nama', 'ASC')->get()->getResultArray();
            if (user()['role'] !== "Root" && user()['role'] !== "Ceo" && user()['role'] !== "Member") {
                $user_id = user()['id'];
            } else {
                if ($user_id == "") {
                    $user_id = $users[0]['id'];
                }
            }
            $res = [];
            foreach ($users as $u) {
                $total = 0;
                $data = [];
                $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();
                foreach ($q as $i) {
                    if ($i['user_id'] == $u['id'] && date('m') == date('m', $i['tgl']) && date('Y') == date('Y', $i['tgl'])) {
                        $total += (int)$i['poin'];
                        $data[] = $i;
                    }
                }

                $res[$u['id']] = ['total' => $total, 'data' => $data];
            }

            sukses_js("Sukses", $res[$user_id], (user()['role'] == "Root" || user()['role'] == "Ceo" ? $users : null), $user_id);
        }

        if ($order == "bisyaroh") {
            $bisy_billiard = 1000;
            $data = ['billiard', 'ps'];
            $user = $dbu->where('role', 'Billiard')->get()->getRowArray();

            $res = [];
            foreach ($data as $i) {
                $db = db($i);
                $q = $db->where('petugas', $user['nama'])->whereNotIn('metode', ['Hutang', 'Play', 'Over'])->whereNotIn('total', [0])->get()->getResultArray();
                $data = [];
                $minutes = 0;
                $total = 0;
                foreach ($q as $b) {
                    if ($bulan == date('m', $b['tgl']) && date('Y', $b['tgl']) == $tahun) {
                        if ($i == "billiard" && $b['total'] > 15000) {
                            $minutes += (int)$b['durasi'];
                            $data[] = $b;
                        }
                        if ($i == "ps" && $b['total'] > 3000) {
                            $minutes += (int)$b['durasi'];
                            $data[] = $b;
                        }
                    }
                }
                $hours = floor($minutes / 60);
                if ($i == "billiard") {
                    $total += $bisy_billiard * (int)$hours;
                }
                if ($i == "ps") {
                    $total += round($bisy_billiard / 2) * (int)$hours;
                }

                $res[$i] = ['minutes' => $minutes, "hours" => $hours, 'total' => $total, 'data' => $data];
            }
            sukses_js("Sukses", $res, $bulan, $tahun);
        }
    }
    public function update_header()
    {
        $listrik = clear($this->request->getVar('listrik'));
        $bersih = clear($this->request->getVar('bersih'));
        if ($bersih == "Bersih") {
            $db_poin = db('poin');
            $q_poin = $db_poin->where('kategori', "Listrik")->get()->getResultArray();
            $res = null;
            foreach ($q_poin as $i) {
                if (date('d') == date('d', $i['tgl']) && date('m') == date('m', $i['tgl']) && date('Y') == date('Y', $i['tgl'])) {
                    $res = $i;
                }
            }

            $db = db('poin');

            if ($res) {
                $res['disiplin'] = $listrik;
                $db_poin->where('id', $res['id']);
                if ($db_poin->update($res)) {
                    sukses_js('Sukses...');
                } else {
                    gagal_js('Gagal...');
                }
            } else {
                $data = [
                    'tgl' => time(),
                    'kategori' => "Listrik",
                    'divisi' => "",
                    'grup' => "",
                    'disiplin' => $listrik,
                    'shift' => "",
                    'user_id' => user()['id'],
                    'petugas' => user()['nama'],
                    'poin' => 5
                ];

                if ($db->insert($data)) {
                    sukses_js('Sukses...');
                } else {
                    gagal_js('Gagal...');
                }
            }
        } else {
            gagal_js("Harus bersih...");
        }
    }

    public function upload_file()
    {
        $file = $_FILES['file'];
        if ($file['error'] == 4) {
            gagal(base_url('home'), "File belum dipilih");
        }
        if ($file['size'] > 2000000) {
            gagal(base_url('home'), "Ukuran max. 2MB");
        }

        $ext = explode(".", $file['name']);

        if (strtolower(end($ext)) !== "jpg") {
            gagal(base_url('home'), "File harus jpg");
        }

        $nama = "iklan.jpg";
        $dir = 'files/' . $nama;
        if (!unlink($dir)) {
            gagal(base_url('home'), 'File lama gagal dihapus.');
        }
        if (!move_uploaded_file($file['tmp_name'], $dir)) {
            gagal(base_url('home'), 'File gagal diupload');
        }
        sukses(base_url('home'), "Sukses");
    }
}
