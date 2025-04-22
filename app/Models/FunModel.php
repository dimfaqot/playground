<?php

namespace App\Models;

use CodeIgniter\Model;

class FunModel extends Model
{
    function meja($kategori, $grup)
    {
        $db = db("perangkat");
        $q = $db->where('grup', $grup)->where('kategori', $kategori)->orderBy('urutan', 'ASC')->get()->getResultArray();

        return $q;
    }
    function perangkat($grup)
    {
        $db = db("perangkat");
        $q = $db->where('grup', $grup)->orderBy('urutan', 'ASC')->get()->getResultArray();

        return $q;
    }
    function status($grup)
    {
        $db = db("perangkat");
        $q = $db->where('grup', $grup)->orderBy('urutan', 'ASC')->get()->getResultArray();

        $data = [];
        $expm = explode(" ", $grup);
        if ($expm[0] == "billiard" || $expm[0] == "ps") {

            $dbm = db(strtolower($expm[0]));
            foreach ($q as $i) {
                $status = $dbm->where("perangkat", $i['perangkat'])->where('status', 1)->get()->getRowArray();
                if ($status) {
                    $i['status'] = $status['status'];
                    $i['dari'] = $status['dari'];
                    $i['ke'] = $status['ke'];
                    $i['durasi'] = $status['durasi'];
                    $i['diskon'] = $status['diskon'];
                    $i['total'] = $status['total'];
                    $i['pembeli'] = $status['pembeli'];
                    $i['user_id'] = $status['user_id'];
                    $i['metode'] = $status['metode'];
                    $i['petugas'] = $status['petugas'];
                } else {
                    $i['dari'] = 0;
                    $i['ke'] = 0;
                    $i['durasi'] = 0;
                    $i['biaya'] = 0;
                    $i['diskon'] = 0;
                    $i['total'] = 0;
                    $i['pembeli'] = "";
                    $i['user_id'] = 0;
                    $i['metode'] = "";
                    $i['petugas'] = "";
                }
            }
            $data[] = $i;
        } else {
            foreach ($q as $i) {

                $i['dari'] = 0;
                $i['ke'] = 0;
                $i['durasi'] = 0;
                $i['biaya'] = 0;
                $i['diskon'] = 0;
                $i['total'] = 0;
                $i['pembeli'] = "";
                $i['user_id'] = 0;
                $i['metode'] = "";
                $i['petugas'] = "";

                $data[] = $i;
            }
        }

        return $data;
    }
    function Allstatus($kategori)
    {
        $db = db("perangkat");
        $q = $db->where('kategori', $kategori)->orderBy('urutan', 'ASC')->get()->getResultArray();
        $data = [];
        $expm = explode(" ", $kategori);

        if ($expm[0] == "Billiard" || $expm[0] == "Ps") {

            $dbm = db(strtolower($expm[0]));
            foreach ($q as $i) {

                $over = $dbm->where("perangkat", $i['perangkat'])->whereIn('metode', ["Play", "Over"])->get()->getRowArray();
                if ($over) {
                    $i['id'] = $over['id'];
                    $i['status'] = $over['status'];
                    $i['dari'] = $over['dari'];
                    $i['ke'] = $over['ke'];
                    $i['durasi'] = $over['durasi'];
                    $i['jam'] = (round($over['durasi'] / 60));
                    $i['diskon'] = $over['diskon'];
                    $i['total'] = $over['total'];
                    $i['pembeli'] = $over['pembeli'];
                    $i['user_id'] = $over['user_id'];
                    $i['metode'] = $over['metode'];
                    $i['petugas'] = $over['petugas'];
                } else {
                    $done = $dbm->where("perangkat", $i['perangkat'])->where('status', 1)->where('metode', "Play")->get()->getRowArray();

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
                }
                $data[] = $i;
            }
        } else {
            foreach ($q as $i) {

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

                $data[] = $i;
            }
        }
        return $data;
    }

    function akhiri($data)
    {
        $res = [];
        $harga = $data['harga'];
        $durasi = (round($data['durasi'] / 60)) . " Jam : 00 Menit";
        if ($data['durasi'] == 0) {
            $harga_per_menit = ceil($harga / 60);
            $durasi = durasi_jam($data['dari']);
            $exp = explode(" ", $durasi);
            if ($exp[0] == "00" || $exp[0] == "0") {
                $jml_menit = ($exp[3] == "0" || $exp[3] == "00" ? 0 : (int)$exp[3]);
            } else {
                $jml_menit = (((int)$exp[0] * 60) + ($exp[3] == "0" || $exp[3] == "00" ? 0 : (int)$exp[3]));
            }

            $biaya = $harga_per_menit * (int)$jml_menit;
            $res['harga'] = ceil($biaya / 1000) * 1000;
            $res['durasi'] = $jml_menit;
        } else {
            $exp = explode(" ", $durasi);
            $res['harga'] = (int)$harga * (int)$exp[0];
            $res['durasi'] = $data['durasi'];
        }

        return $res;
    }

    public function statistik($tahun, $kategori)
    {
        $divisi = options('Divisi');
        $AllData = [];
        foreach ($divisi as $x) {
            if ($kategori !== "All" && $kategori !== $x['value']) {
                continue;
            } else {
                $db = db(strtolower($x['value']));
                $masuk = $db->orderBy('tgl', 'ASC')->get()->getResultArray();

                $masuk_per_bulan = [];
                $tap_per_bulan = [];
                $hutang_per_bulan = [];
                $qris_per_bulan = [];
                foreach (bulan() as $b) {
                    $temp_masuk = 0;
                    $temp_tap = 0;
                    $temp_hutang = 0;
                    $temp_qris = 0;
                    foreach ($masuk as $i) {
                        if ($tahun == "All") {
                            if (date('m', $i['tgl']) == $b['angka']) {
                                if ($i['metode'] == "Hutang") {
                                    $temp_hutang += (int)$i['total'];
                                } elseif ($i['metode'] == "Tap") {
                                    $temp_tap += (int)$i['total'];
                                } elseif ($i['metode'] == "Qris") {
                                    $temp_qris += (int)$i['total'];
                                } else {
                                    $temp_masuk += (int)$i['total'];
                                }
                            }
                        } else {
                            if (date('m', $i['tgl']) == $b['angka'] && date('Y', $i['tgl']) == $tahun) {
                                if ($i['metode'] == "Hutang") {
                                    $temp_hutang += (int)$i['total'];
                                } elseif ($i['metode'] == "Tap") {
                                    $temp_tap += (int)$i['total'];
                                } elseif ($i['metode'] == "Qris") {
                                    $temp_qris += (int)$i['total'];
                                } else {
                                    $temp_masuk += (int)$i['total'];
                                }
                            }
                        }
                    }
                    $masuk_per_bulan[] = $temp_masuk;
                    $tap_per_bulan[] = $temp_tap;
                    $hutang_per_bulan[] = $temp_hutang;
                    $qris_per_bulan[] = $temp_qris;
                }

                $dbk = db("pengeluaran");
                $keluar = $dbk->where('kategori', $x['value'])->orderBy('tgl', 'ASC')->get()->getResultArray();

                $keluar_per_bulan = [];
                foreach (bulan() as $b) {
                    $temp_keluar = 0;
                    foreach ($keluar as $i) {
                        if ($tahun == "All") {
                            if (date('m', $i['tgl']) == $b['angka']) {
                                $temp_keluar += (int)$i['total'];
                            }
                        } else {
                            if (date('m', $i['tgl']) == $b['angka'] && date('Y', $i['tgl']) == $tahun) {
                                $temp_keluar += (int)$i['total'];
                            }
                        }
                    }
                    $keluar_per_bulan[] = $temp_keluar;
                }

                $dbkop = db("koperasi");
                $kop = $dbkop->where('kategori', $x['value'])->orderBy('tgl', 'ASC')->get()->getResultArray();

                $kop_per_bulan = [];
                foreach (bulan() as $b) {
                    $temp_kop = 0;
                    foreach ($kop as $i) {
                        if ($tahun == "All") {
                            if (date('m', $i['tgl']) == $b['angka']) {
                                $temp_kop += (int)$i['uang'];
                            }
                        } else {
                            if (date('m', $i['tgl']) == $b['angka'] && date('Y', $i['tgl']) == $tahun) {
                                $temp_kop += (int)$i['uang'];
                            }
                        }
                    }
                    $kop_per_bulan[] = $temp_kop;
                }

                $data = [];
                $all_masuk = 0;
                $all_keluar = 0;
                $all_kop = 0;
                $all_tap = 0;
                $all_hutang = 0;
                $all_qris = 0;
                $total_data_keuangan = 0;
                for ($i = 0; $i < 12; $i++) {
                    $total = $masuk_per_bulan[$i] - ($keluar_per_bulan[$i] + $qris_per_bulan[$i] + $tap_per_bulan[$i] + $hutang_per_bulan[$i]);
                    $total_data_keuangan += $total;
                    $data[] = ($total - $kop_per_bulan[$i]);
                    $all_masuk += (int)$masuk_per_bulan[$i];
                    $all_keluar += (int)$keluar_per_bulan[$i];
                    $all_kop += (int)$kop_per_bulan[$i];
                    $all_tap += (int)$tap_per_bulan[$i];
                    $all_hutang += (int)$hutang_per_bulan[$i];
                    $all_qris += (int)$qris_per_bulan[$i];
                }
                $AllData[$x['value']] = ['tap' => $all_tap, 'hutang' => $all_hutang, 'masuk' => $all_masuk, 'keluar' => $all_keluar, 'koperasi' => $all_kop, 'qris' => $all_qris, 'data' => $data, 'total_data_keuangan' => ($total_data_keuangan - $all_kop)];
            }
        }
        return $AllData;
    }
    public function statistik_bulanan($tahun, $bulan, $kategori)
    {
        $db = db(strtolower($kategori));
        $masuk = $db->orderBy('tgl', 'ASC')->get()->getResultArray();

        $temp_masuk = 0;
        $temp_tap = 0;
        $temp_hutang = 0;
        $temp_qris = 0;
        $data_masuk = [];
        $data_tap = [];
        $data_hutang = [];
        $data_qris = [];

        foreach ($masuk as $i) {
            if ($kategori == "Ps" || $kategori == "Billiard") {
                $i['qty'] = $i['durasi'];
                $i['barang'] = $i['perangkat'];
            }
            if ($tahun == "All") {
                if (date('n', $i['tgl']) == $bulan) {
                    if ($i['metode'] == "Hutang") {
                        $temp_hutang += (int)$i['total'];
                        $data_hutang[] = $i;
                    } elseif ($i['metode'] == "Tap") {
                        $temp_tap += (int)$i['total'];
                        $data_tap[] = $i;
                    } elseif ($i['metode'] == "Qris") {
                        $temp_qris += (int)$i['total'];
                        $data_qris[] = $i;
                    } else {
                        $temp_masuk += (int)$i['total'];
                        $data_masuk[] = $i;
                    }
                }
            } else {
                if (date('n', $i['tgl']) == $bulan && date('Y', $i['tgl']) == $tahun) {
                    if ($i['metode'] == "Hutang") {
                        $temp_hutang += (int)$i['total'];
                        $data_hutang[] = $i;
                    } elseif ($i['metode'] == "Tap") {
                        $temp_tap += (int)$i['total'];
                        $data_tap[] = $i;
                    } elseif ($i['metode'] == "Qris") {
                        $temp_qris += (int)$i['total'];
                        $data_qris[] = $i;
                    } else {
                        $temp_masuk += (int)$i['total'];
                        $data_masuk[] = $i;
                    }
                }
            }
        }

        $masuk_per_bulan = ["total" => $temp_masuk, "data" => $data_masuk];
        $tap_per_bulan = ["total" => $temp_tap, "data" => $data_tap];
        $hutang_per_bulan = ["total" => $temp_hutang, "data" => $data_hutang];
        $qris_per_bulan = ["total" => $temp_qris, "data" => $data_qris];


        $dbk = db("pengeluaran");
        $keluar = $dbk->where('kategori', $kategori)->orderBy('tgl', 'ASC')->get()->getResultArray();

        $temp_keluar = 0;
        $data_keluar = [];
        foreach ($keluar as $i) {
            if ($tahun == "All") {
                if (date('n', $i['tgl']) == $bulan) {
                    $temp_keluar += (int)$i['total'];
                    $data_keluar[] = $i;
                }
            } else {
                if (date('n', $i['tgl']) == $bulan && date('Y', $i['tgl']) == $tahun) {
                    $temp_keluar += (int)$i['total'];
                    $data_keluar[] = $i;
                }
            }
        }
        $keluar_per_bulan = ["total" => $temp_keluar, "data" => $data_keluar];


        $data = ['tap' => $tap_per_bulan, 'hutang' => $hutang_per_bulan, 'masuk' => $masuk_per_bulan, 'keluar' => $keluar_per_bulan, 'qris' => $qris_per_bulan];

        return $data;
    }
    public function laporan($tahun, $bulan, $kategori)
    {
        $bulan = bulan(upper_first($bulan))['satuan'];
        $db = db(strtolower($kategori));
        $masuk = $db->orderBy('tgl', 'ASC')->get()->getResultArray();

        $data_masuk = [];
        $total_masuk = 0;
        foreach ($masuk as $i) {
            if ($kategori == "Ps" || $kategori == "Billiard") {
                $i['qty'] = $i['durasi'];
                $i['barang'] = $i['perangkat'];
            }
            if ($tahun == "All") {
                if (date('n', $i['tgl']) == $bulan) {
                    $data_masuk[] = $i;
                    $total_masuk += (int)$i['total'];
                }
            } else {
                if (date('n', $i['tgl']) == $bulan && date('Y', $i['tgl']) == $tahun) {
                    $data_masuk[] = $i;
                    $total_masuk += (int)$i['total'];
                }
            }
        }

        $dbk = db("pengeluaran");
        $keluar = $dbk->where('kategori', $kategori)->orderBy('tgl', 'ASC')->get()->getResultArray();

        $data_keluar = [];
        $total_keluar = 0;
        foreach ($keluar as $i) {
            if ($tahun == "All") {
                if (date('n', $i['tgl']) == $bulan) {
                    $data_keluar[] = $i;
                    $total_keluar += (int)$i['total'];
                }
            } else {
                if (date('n', $i['tgl']) == $bulan && date('Y', $i['tgl']) == $tahun) {
                    $data_keluar[] = $i;
                    $total_keluar += (int)$i['total'];
                }
            }
        }
        $dbkop = db("koperasi");
        $koperasi = $dbkop->where('kategori', $kategori)->orderBy('tgl', 'ASC')->get()->getResultArray();

        $data_koperasi = [];
        $total_koperasi = 0;
        foreach ($koperasi as $i) {
            $i['total'] = $i['uang'];
            $data_koperasi[] = $i;
            $total_koperasi += (int)$i['total'];
        }

        $jwt = base_url('guest/laporan/') . encode_jwt(['tahun' => $tahun, "bulan" => $bulan]);
        $data = ['jwt' => $jwt, 'bulan' => $bulan, 'tahun' => $tahun, 'masuk' => ["total" => $total_masuk, "data" => $data_masuk], 'keluar' => ["total" => $total_keluar, "data" => $data_keluar], 'koperasi' => ["total" => $total_koperasi, "data" => $data_koperasi]];

        return $data;
    }


    function enkripsi_fulus($user, $saldo, $key = '')
    {
        if ($key == "") {
            $key = getenv("MIFTAHULJANNAH");
        }
        $method = 'AES-256-CBC';
        $iv = substr(hash('sha256', $key), 0, 16); // Membuat IV (Initialization Vector)
        $encrypted = openssl_encrypt($saldo, $method, $key, 0, $iv);
        $encryp = encode_jwt(['fulus' => base64_encode($encrypted), 'user_id' => $user['id']]);
        return  $encryp; // Encoding hasil enkripsi
    }
    function dekripsi_fulus($user, $encryptedData, $key = '')
    {
        if ($key == "") {
            $key = getenv("MIFTAHULJANNAH");
        }
        $res = null;
        $method = 'AES-256-CBC';
        $iv = substr(hash('sha256', $key), 0, 16); // Membuat IV (Initialization Vector)
        $jwt = decode_jwt($encryptedData);
        if ($jwt['user_id'] == $user['id']) {
            $decoded = base64_decode($jwt['fulus']); // Decoding data terenkripsi
            $res = (int)openssl_decrypt($decoded, $method, $key, 0, $iv);
        }

        if (is_null($res)) {
            gagal_js('Dekrip fulus gagal...');
        }

        return $res;
    }

    function enkripsi($data, $key = '')
    {
        if ($key == "") {
            $key = getenv("MIFTAHULJANNAH");
        }
        $method = 'AES-256-CBC';
        $iv = substr(hash('sha256', $key), 0, 16); // Membuat IV (Initialization Vector)
        $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
        $encryp = encode_jwt(['data' => base64_encode($encrypted)]);
        return  $encryp; // Encoding hasil enkripsi
    }

    function dekripsi($encryptedData, $key = '')
    {
        if ($key == "") {
            $key = getenv("MIFTAHULJANNAH");
        }
        $method = 'AES-256-CBC';
        $iv = substr(hash('sha256', $key), 0, 16); // Membuat IV (Initialization Vector)
        $jwt = decode_jwt($encryptedData);
        $decoded = base64_decode($jwt['data']); // Decoding data terenkripsi
        return openssl_decrypt($decoded, $method, $key, 0, $iv);
    }

    function topup($user, $topup, $kategori, $metode, $lokasi)
    {
        $saldo = $this->dekripsi_fulus($user, $user['fulus']);

        if (is_null($saldo)) {
            gagal_js("Dekripsi gagal...");
        }
        $total = (int)$saldo + (int)$topup;

        $user['fulus'] = $this->enkripsi_fulus($user, $total);

        $db = db('user');
        $db->where('id', $user['id']);
        if ($db->update($user)) {
            $data = [
                "tgl" => time(),
                "lokasi" => $lokasi,
                "user" => $user['nama'],
                "user_id" => $user['id'],
                "saldo" => $saldo,
                "uang" => $topup,
                "total" => $total,
                "kategori" => $kategori,
                "petugas" => user()['nama'],
                "metode" => $metode
            ];
            $dbt = db('tap');
            if ($dbt->insert($data)) {
                sukses_js("Topup berhasil.");
            } else {
                gagal_js("Insert riwayat gagal!.");
            }
        } else {
            gagal_js("Topup data gagal!.");
        }
    }

    function tap($user, $eksekusi, $q, $new_id, $lokasi, $shift = null)
    {
        $db = db('user');
        $dbm = db('metode');
        $total = (int)$eksekusi['total'];

        $fulus = $this->dekripsi_fulus($user, $user['fulus']);
        if (is_null($fulus)) {
            gagal_js("Dekripsi gagal...");
        }

        $saldo = (int)$fulus - (int)$total;
        $user['fulus'] = $this->enkripsi($saldo);
        $db->where('id', $user['id']);
        if ($db->update($user)) {
            $data = [
                "tgl" => time(),
                "lokasi" => $lokasi,
                "user" => $user['nama'],
                "user_id" => $user['id'],
                "saldo" => $fulus,
                "uang" => $total,
                "total" => $saldo,
                "kategori" => $eksekusi['jenis'],
                'ket' => (count($eksekusi['barang']) == 0 ? '' : implode(", ", $eksekusi['barang'])),
                'no_nota' => (count($eksekusi['no_nota']) == 0 ? '' : implode(", ", $eksekusi['no_nota'])),
                "metode" => "Iot"
            ];
            if (session('id')) {
                $data['petugas'] = user()['id'];
            } else {
                $shift['user'];
            }
            $dbt = db('tap');
            if ($dbt->insert($data)) {
                $q['saldo'] = $saldo;
                $q['fulus'] = $fulus;
                $q['pembeli'] = $user['nama'];
                $q['total'] = $total;
                $q['status'] = "End";
                $q['message'] = "Transaksi berhasil.";
                $q['new_id'] = $new_id;
                $q['err'] = $eksekusi['err'];
                sukses_js("Transaksi berhasil.", $q);
            } else {
                gagal_js("Insert riwayat gagal!.");
            }
        } else {
            // jika update gagal hapus data metode
            $dbm->where('id', $new_id);
            if ($dbm->delete()) {
                gagal_js("Transaksi gagal!.");
            } else {
                gagal_js("Data gagal dihapus!.");
            }
        }
    }

    function koperasi()
    {
        $db = db(menu()['tabel']);
        $db_cair = db("pencairan");

        $data = $db->orderBy('tgl', 'DESC')->get()->getResultArray();
        $detail = [];

        $total_masuk = 0;
        $total_keluar = 0;

        foreach (options("Saham") as $i) {

            $cair = $db_cair->where('saham', $i['value'])->get()->getResultArray();
            $keluar = 0;
            $data_keluar = [];
            foreach ($cair as $c) {
                $keluar += (int)$c['uang'];
                $data_keluar[] = $c;
            }
            $total_keluar += (int)$keluar;

            $masuk = 0;
            $data_masuk = [];
            foreach ($data as $d) {
                $d['keluar'] = $keluar;
                $d['total'] = (int)$d['uang'] - (int)$keluar;
                $masuk += (int)$d[strtolower($i['value'])];
                $data_masuk[] = $d;
            }

            $total_masuk += (int)$masuk;

            $detail[$i['value']] = [
                'masuk' => ['total' => $masuk, 'data' => $data_masuk],
                'keluar' => ['total' => $keluar, 'data' => $data_keluar],
            ];
        }

        $res = ['data' => $data, 'total_masuk' => $total_masuk, 'total_keluar' => $total_keluar, 'detail' => $detail];

        return $res;
    }

    function hutangs($user_id)
    {

        $data = [];
        $total = 0;

        $divisi = options('Divisi');
        foreach ($divisi as $x) {
            $db = db(strtolower($x['value']));
            $val = $db->where('user_id', $user_id)->where('metode', "Hutang")->orderBy('tgl', 'ASC')->get()->getResultArray();

            foreach ($val as $i) {
                $total += (int)$i['total'];
                if ($x['value'] == "Ps" || $x['value'] == "Billiard") {
                    if ($i['status'] == 1) {
                        continue;
                    }
                    $i['no_nota'] = upper_first(substr($x['value'], 0, 1)) . date('dmY', $i['tgl']) . "-" . $i['id'];
                    $i['barang'] = $i['perangkat'];
                    $i['qty'] = $i['durasi'];
                }

                $dbu = db('user');
                $usr = $dbu->where('id', $i['user_id'])->get()->getRowArray();

                $data[] = [
                    'id' => $i['id'],
                    'nama' => $i['pembeli'],
                    'no_nota' => $i['no_nota'],
                    'user_id' => $i['user_id'],
                    'hp' => ($usr ? $usr['hp'] : ""),
                    'kategori' => $x['value'],
                    'tgl' => $i['tgl'],
                    'metode' => $i['metode'],
                    'barang' => $i['barang'],
                    'qty' => $i['qty'],
                    'harga' => $i['total']
                ];
            }
        }
        $res = ['total' => $total, 'data' => $data];
        return $res;
    }

    function update_saldo($user, $total_transaksi, $kategori, $lokasi, $petugas, $metode, $no_nota = '')
    {
        $saldo = $this->dekripsi_fulus($user, $user['fulus']); //saldo saat ini
        $saldo_akhir = (int)$saldo - (int)$total_transaksi;
        $dbu = db('user');
        $user['fulus'] = $this->enkripsi_fulus($user, $saldo_akhir);

        $dbu->where('id', $user['id']);
        if (!$dbu->update($user)) {
            gagal_js("Update saldo gagal...");
        }

        $db_tap = db('tap');
        $data = [
            'tgl' => time(),
            'kategori' => $kategori,
            'lokasi' => $lokasi,
            'user' => $user['nama'],
            'saldo' => $saldo, //saldo saat ini
            'uang' => $total_transaksi, //jml transaksi
            'total' => $saldo_akhir, //saldo setelah dikurangi transaksi
            'petugas' => $petugas,
            'metode' => $metode,
            'no_nota' => $no_nota

        ];

        if (!$db_tap->insert($data)) {
            gagal_js('Data gagal dimasukkan ke tabel tap...');
        }

        return $saldo_akhir;
    }

    function data_hari_ini()
    {
        $hari_ini = time_hari_ini();
        $data = [];
        foreach (options('Divisi') as $d) {
            $db = db(strtolower($d['value']));
            $data_hutang = $db->where('tgl >=', $hari_ini['start'])->where('tgl <=', $hari_ini['end'])->where('metode', 'Hutang')->orderBy('tgl', 'DESC')->get()->getResultArray();
            $data_masuk = $db->where('tgl >=', $hari_ini['start'])->where('tgl <=', $hari_ini['end'])->whereNotIn('metode', ['Hutang', 'Over', 'Play'])->orderBy('tgl', 'DESC')->get()->getResultArray();

            $masuk = 0;
            $hutang = 0;

            foreach ($data_masuk as $m) {
                $masuk += (int)$m['total'];
            }

            foreach ($data_hutang as $h) {
                $hutang += (int)$h['total'];
            }

            $data[$d['value']] = [
                'masuk' => ['total' => $masuk, 'data' => $data_masuk],
                'hutang' => ['total' => $hutang, 'data' => $data_hutang]
            ];
        }

        return $data;
    }
}
