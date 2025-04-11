<?php

namespace App\Controllers;

class Barang extends BaseController
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


    public function index($kategori = "Kantin"): string
    {
        $kategori = (user()['role'] !== "Root" ? user()['role'] : $kategori);
        $db = db(menu()['tabel']);

        $data = $db->where('kategori', $kategori)->orderBy('barang', 'ASC')->get()->getResultArray();
        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data, 'kategori' => $kategori]);
    }

    public function add()
    {
        $kategori = upper_first(clear($this->request->getVar('kategori')));
        $barang = upper_first(clear($this->request->getVar('barang')));
        $ket = upper_first(clear($this->request->getVar('ket')));
        $qty = rp_to_int(clear($this->request->getVar('qty')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));

        $db = db(menu()['tabel']);
        if ($db->where('kategori', $kategori)->where('barang', $barang)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Barang sudah ada!.");
        }

        $data = [
            'tgl' => time(),
            'barang' => $barang,
            'kategori' => $kategori,
            'qty' => $qty,
            'harga' => $harga,
            'ket' => $ket,
            'petugas' => user()['nama']
        ];

        if ($db->insert($data)) {
            sukses(base_url(menu()['controller']), "Tambah data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Tambah data gagal!.");
        }
    }
    public function update()
    {
        $id = clear($this->request->getVar('id'));
        $kategori = upper_first(clear($this->request->getVar('kategori')));
        $barang = upper_first(clear($this->request->getVar('barang')));
        $ket = upper_first(clear($this->request->getVar('ket')));
        $qty = rp_to_int(clear($this->request->getVar('qty')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));


        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        if ($db->whereNotIn('id', [$id])->where('kategori', $kategori)->where('barang', $barang)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Barang sudah ada!.");
        }

        $q['kategori'] = $kategori;
        $q['ket'] = $ket;
        $q['barang'] = $barang;
        $q['qty'] =  $qty;
        $q['harga'] =  $harga;
        $q['petugas'] = user()['nama'];

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }
}
