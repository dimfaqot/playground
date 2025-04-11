<?php

namespace App\Controllers;

class Cafe extends BaseController
{
    public function index(): string
    {

        $db = db("barang");
        $data = [];

        foreach (options("Cafe") as $i) {
            $q = $db->where('kategori', 'kantin')->where('ket', $i['value'])->orderBy('barang', 'ASC')->get()->getResultArray();
            $data[$i['value']] = $q;
        }
        return view('public/' . menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function transaksi()
    {
        $pembeli = json_decode(json_encode($this->request->getVar('pembeli')), true);
        $data_pesanan = json_decode(json_encode($this->request->getVar('data_pesanan')), true);

        $db = db('kantin');
        $dbb = db("barang");
        $no_nota = "K" . no_nota(time(), 'kantin');
        $time = time();
        foreach ($data_pesanan as $i) {
            $q = $dbb->where('id', $i['id'])->get()->getRowArray();
            if ($q) {
                $data = [
                    'tgl' => $time,
                    'no_nota' => $no_nota,
                    'barang' => $q['barang'],
                    'harga' => $q['harga'],
                    'qty' => $i['qty'],
                    'diskon' => 0,
                    'total' => (int)$q['harga'] * (int)$i['qty'],
                    'metode' => "Barcode",
                    'pembeli' => $pembeli['nama'],
                    'user_id' => $pembeli['user_id'],
                    'petugas' => $pembeli['nama']
                ];

                $db->insert($data);
            }
        }

        $jwt = base_url("cafe/pesanan/") . encode_jwt(['no_nota' => $no_nota]);
        sukses_js("Transaksi sukses.", $jwt);
    }

    public function pesanan($jwt)
    {
        $decode = decode_jwt($jwt);
        $db = db("kantin");
        $q = $db->where('no_nota', $decode['no_nota'])->get()->getResultArray();
        $user = [];

        if ($q) {
            $dbu = db('user');
            $user = $dbu->where('id', $q[0]['user_id'])->get()->getRowArray();
        }
        return view('public/pesanan', ['judul' => "Pesanan " . $q[0]['pembeli'], 'data' => $q, 'user' => ['nama' => $user['nama'], 'hp' => $user['hp']]]);
    }
    public function cek_status()
    {
        $no_nota = clear($this->request->getVar('no_nota'));

        $db = db('kantin');

        $q = $db->where('no_nota', $no_nota)->get()->getRowArray();

        $message = "Barcode";
        if ($q) {
            $message = $q['metode'];
        }
        $jwt = '';

        if ($message !== "Barcode" && $message !== "Proses" && $message !== "Hutang") {
            $jwt = base_url('guest/nota/') . encode_jwt(['tabel' => 'kantin', 'no_nota' => $no_nota]);
        }
        sukses_js($message, $jwt);
    }
    public function update_metode()
    {
        $no_nota = clear($this->request->getVar('no_nota'));
        $metode = clear($this->request->getVar('metode'));

        $db = db('kantin');
        $dbb = db("barang");

        $q = $db->where('no_nota', $no_nota)->get()->getResultArray();

        if (!$q) {
            gagal_js("No. nota not found!.");
        } else {
            foreach ($q as $i) {
                $i['metode'] = $metode;

                $db->where('id', $i['id']);
                $db->update($i);

                if ($metode == "Proses") {
                    $qb = $dbb->where('kategori', 'Kantin')->where('barang', $i['barang'])->get()->getRowArray();
                    if ($qb) {
                        $qb['qty'] -= (int)$i['qty'];

                        $dbb->where('id', $qb['id']);
                        $dbb->update($qb);
                    }
                }
            }

            sukses_js("Sukses.");
        }
    }

    public function add_user()
    {
        $nama = clear(upper_first($this->request->getVar('nama')));
        $hp = upper_first($this->request->getVar('hp'));

        $dbx = \Config\Database::connect();
        $db = $dbx->table('user');
        $q = $db->where('nama', $nama)->get()->getRowArray();

        if ($q) {
            gagal_js("Cari nama lain!.");
        }
        $q = $db->where('hp', $hp)->get()->getRowArray();

        if ($q) {
            gagal_js("No. whatsapp sudah terdaftar!.");
        }
        $fun = new \App\Models\FunModel();
        $data = [
            'nama' => $nama,
            'hp' => $hp,
            'role' => "Member",
            'uid' => ""
        ];

        if ($db->insert($data)) {
            $new_id = $dbx->insertID();
            $q = $db->where('id', $new_id)->get()->getRowArray();
            if ($q) {
                $fun = new \App\Models\FunModel();
                $q['fulus'] = $fun->enkripsi_fulus($q, 0);
                $db->where('id', $q['id']);
                $db->update($q);
            }
            sukses_js("Sukses.");
        } else {
            gagal_js("Gagal!.");
        }
    }
}
