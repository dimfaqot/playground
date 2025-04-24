<?php

namespace App\Controllers;

use App\Models\FunModel;

class Tap extends BaseController
{
    function __construct()
    {
        helper('functions');
        if (!session('id')) {
            gagal(base_url(), "Kamu belum login!.");
            die;
        }
        menu();
    }


    public function index($angkatan = null, $tahun = null, $bulan = null): string
    {

        $tahun = ($tahun == null ? date('Y') : $tahun);
        $bulan = ($bulan == null ? date('m') : $bulan);
        $angkatan = ($angkatan == null ? (int)date('Y') - 1 : $angkatan);


        $dbu = db('user');
        $users = $dbu->where('angkatan', $angkatan)->orderBy('nama', 'ASC')->get()->getResultArray();

        // $santris = [];
        // foreach ($angkatans as $i) {
        //     $q = $dbu->where('angkatan', $i['angkatan'])->orderBy('nama', 'ASC')->get()->getResultArray();
        //     $santris[$i['angkatan']] = $q;
        // }
        $data = [];
        foreach ($users as $a) {
            $temp_data = [];
            $total = 0;
            foreach (options('Divisi') as $d) {
                $dbd = db(strtolower($d['value']));

                $dbd->where("user_id", $a['id']);
                $dbd->where("metode", "Tap");
                $dbd->where("YEAR(FROM_UNIXTIME(tgl))", $tahun);
                $dbd->where("LPAD(MONTH(FROM_UNIXTIME(tgl)), 2, '0')", $bulan);
                $query = $dbd->get();
                $result = $query->getResultArray();

                foreach ($result as $i) {
                    $i['divisi'] = $d['value'];
                    $total += (int)$i['total'];
                    $temp_data[] = $i;
                }
            }
            $data[$a['id']] = ['profile' => $a, 'total' => $total, 'data' => $temp_data];
        }

        dd($data); // Debugging, bisa dihapus jika tidak diperlukan

        // Pastikan variabel $perangkat, $total, dan $hutang sudah dideklarasikan sebelumnya
        return view(menu()['controller'], [
            'judul' => menu()['menu'],
            'perangkat' => $perangkat ?? [],
            'data' => $data,
            'total' => $total ?? 0,
            'hutang' => $hutang ?? 0
        ]);
    }

    public function add()
    {

        $perangkat = upper_first(clear($this->request->getVar('perangkat')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $durasi = rp_to_int(clear($this->request->getVar('durasi')));

        if (clear($this->request->getVar('durasi')) == "Durasi") {
            gagal(base_url(menu()['controller']), "Durasi belum dipilih!.");
        }
        $dari = time();
        $ke = 0;
        $total = 0;
        if ($durasi > 0) {
            $ke += $dari + ((60 * 60) * $durasi);
            $total = (int)$harga * (int)$durasi;
        }

        $db = db(menu()['tabel']);
        $data = [
            'tgl' => $dari,
            'perangkat' => $perangkat,
            'dari' => $dari,
            'ke' => $ke,
            'status' => 0,
            'harga' => $harga,
            'total' => $total,
            'durasi' => $durasi * 60,
            'pembeli' => user()['nama'],
            'user_id' => user()['id'],
            'status' => 1,
            'metode' => "Play",
            'petugas' => user()['nama']
        ];

        if ($db->insert($data)) {
            sukses(base_url(menu()['controller']), "Tambah data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Tambah data gagal!.");
        }
    }
    public function add_durasi()
    {
        $id = clear($this->request->getVar('id'));
        $durasi = rp_to_int(clear($this->request->getVar('durasi')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js("Id tidak ditemukan!.");
        }


        $q['ke'] = $q['ke'] + ((60 * 60) * $durasi);
        $q['durasi'] = (int)$q['durasi'] + ($durasi * 60);
        $q['petugas'] = user()['nama'];

        $q['metode'] = "Play";
        $q['status'] = 1;


        $db->where('id', $id);
        if ($db->update($q)) {
            sukses_js("Update data berhasil.");
        } else {
            gagal_js("Update data gagal!.");
        }
    }

    public function akhiri()
    {
        $id = clear($this->request->getVar('id'));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js("Id tidak ditemukan!.");
        }
        $fun = new FunModel();
        $akhiri = $fun->akhiri($q);

        sukses_js("Ok", $akhiri);
    }
    public function transaksi()
    {
        $id = clear($this->request->getVar('id'));
        $metode = upper_first(clear($this->request->getVar('metode')));
        $diskon = rp_to_int(clear($this->request->getVar('diskon')));
        $user_id = clear($this->request->getVar('user_id'));
        $pembeli = clear($this->request->getVar('pembeli'));
        $uang_pembayaran = rp_to_int(clear($this->request->getVar('uang_pembayaran')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js("Id tidak ditemukan!.");
        }
        $fun = new FunModel();
        $akhiri = $fun->akhiri($q);

        $total_now = (int)$akhiri['harga'];
        if ($diskon > $total_now) {
            gagal_js("Diskon max " . angka($total_now) . ".");
        }
        if ($uang_pembayaran < ($total_now - $diskon)) {
            gagal_js("Uang kurang!.");
        }

        if ($q['ke'] == 0) {
            $q['ke'] = time();
            $exp = explode(" ", durasi_jam($q['dari']));
            if ($exp[0] !== "00" && $exp[0] !== "0") {
                $q['durasi'] = (((int)$exp[0] * 60) + ($exp[3] == "0" || $exp[3] == "00" ? 0 : (int)$exp[3]));
            } else {
                $q['durasi'] = ($exp[3] == "0" || $exp[3] == "00" ? 0 : (int)$exp[3]);
            }
        }
        $total_akhir = $total_now - $diskon;
        $q['diskon'] = $diskon;
        $q['total'] = $total_akhir;
        $q['status'] = 0;
        $q['pembeli'] = $pembeli;
        $q['user_id'] = $user_id;
        $q['metode'] = $metode;
        $q['petugas'] = user()['nama'];

        $jwt = base_url("guest/nota/") . encode_jwt(['tabel' => menu()['tabel'], 'id' => $id]);

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses_js("Sukses", angka($uang_pembayaran - $total_akhir), [], $jwt);
        } else {
            gagal_js("Transaksi gagal!.");
        }
    }
    public function lunas()
    {
        $id = clear($this->request->getVar('id'));
        $metode = upper_first(clear($this->request->getVar('metode')));
        $uang_lunas = rp_to_int(clear($this->request->getVar('uang_lunas')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js("Id tidak ditemukan!.");
        }


        if ($uang_lunas < (int)$q['total']) {
            gagal_js("Uang kurang!.");
        }

        $q['metode'] = $metode;
        $q['petugas'] = user()['nama'];

        $jwt = base_url("guest/nota/") . encode_jwt(['tabel' => menu()['tabel'], 'id' => $id]);

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses_js("Sukses", angka($uang_lunas - (int)$q['total']), [], $jwt);
        } else {
            gagal_js("Transaksi gagal!.");
        }
    }

    public function users()
    {
        $val = clear(upper_first($this->request->getVar('val')));

        $db = db('user');
        $q = $db->like('nama', $val, 'both')->orderBy('nama', 'ASC')->limit(10)->get()->getResultArray();

        sukses_js("Ok", $q);
    }
    public function pindah_meja()
    {
        $id = clear($this->request->getVar('id'));
        $perangkat = clear($this->request->getVar('perangkat'));

        $db = db(menu()['tabel']);
        $q = $db->where('perangkat', $perangkat)->whereIn('metode', ["Play", "Over"])->get()->getRowArray();
        if ($q) {
            gagal_js("Meja sudah digunakan...");
        }

        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal_js("Id not found...");
        }

        $q['perangkat'] = $perangkat;
        $db->where('id', $id);

        if ($db->update($q)) {
            sukses_js("Pindah meja sukses...");
        } else {
            sukses_js("Pindah meja gagal...");
        }
    }
}
