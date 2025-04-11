<?php

namespace App\Controllers;

class Pengeluaran extends BaseController
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


    public function index($kategori = "Billiard", $tahun = "", $bulan = ""): string
    {
        $tahun = ($tahun == "" ? date('Y') : $tahun);
        $bulan = ($bulan == "" ? date('m') : $bulan);

        $kategori = (user()['role'] !== "Root" ? user()['role'] : $kategori);
        $db = db(menu()['tabel']);

        $q = $db->where('kategori', $kategori)->orderBy('tgl', 'DESC')->get()->getResultArray();

        $data = [];
        $total = 0;
        foreach ($q as $i) {
            if (date('m', $i['tgl']) == $bulan && date('Y', $i['tgl']) == $tahun) {
                $total += (int)$i['total'];
                $data[] = $i;
            }
        }

        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data, 'kategori' => $kategori, 'tahun' => $tahun, 'bulan' => $bulan, 'total' => $total]);
    }

    public function add()
    {
        $penjual = upper_first(clear($this->request->getVar('penjual')));
        $ket = upper_first(clear($this->request->getVar('ket')));
        $barang = upper_first(clear($this->request->getVar('barang')));
        $kategori = upper_first(clear($this->request->getVar('kategori')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $qty = rp_to_int(clear($this->request->getVar('qty')));
        $diskon = rp_to_int(clear($this->request->getVar('diskon')));
        $total = ($harga * $qty) - $diskon;



        $data = [
            'tgl' => time(),
            'penjual' => $penjual,
            'kategori' => $kategori,
            'barang' => $barang,
            'harga' => $harga,
            'qty' => $qty,
            'diskon' => $diskon,
            'total' => $total,
            'petugas' => user()['nama'],
            'ket' => $ket
        ];

        $db = db(menu()['tabel']);
        if ($db->insert($data)) {
            if ($kategori == "Kantin") {
                $barang_id = clear($this->request->getVar('barang_id'));
                $dbb = db('barang');
                $q = $dbb->where('id', $barang_id)->get()->getRowArray();

                if ($q) {
                    $q['qty'] += (int)$qty;
                    $dbb->where('id', $barang_id);
                    if ($dbb->update($q)) {
                        sukses(base_url(menu()['controller']), "Tambah data berhasil.");
                    } else {
                        gagal(base_url(menu()['controller']), "Update stok gagal!.");
                    }
                }
            } else {
                sukses(base_url(menu()['controller']), "Tambah data berhasil.");
            }
        } else {
            gagal(base_url(menu()['controller']), "Tambah data gagal!.");
        }
    }
    public function update()
    {
        $id = clear($this->request->getVar('id'));
        $barang = upper_first(clear($this->request->getVar('barang')));
        $ket = upper_first(clear($this->request->getVar('ket')));
        $penjual = upper_first(clear($this->request->getVar('penjual')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $qty = rp_to_int(clear($this->request->getVar('qty')));
        $diskon = rp_to_int(clear($this->request->getVar('diskon')));
        $total = ($harga * $qty) - $diskon;

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        $q['penjual'] = $penjual;
        $q['barang'] = $barang;
        $q['ket'] = $ket;
        $q['harga'] = $harga;
        $q['qty'] = $qty;
        $q['diskon'] = $diskon;
        $q['total'] = $total;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }

    public function cari_barang()
    {
        $value = clear($this->request->getVar('value'));
        $kategori = clear($this->request->getVar('kategori'));
        $db = db('barang');
        $q = $db->whereIn('kategori', [$kategori])->like('barang', $value, 'both')->orderBy('barang', 'ASC')->limit(8)->get()->getResultArray();

        sukses_js("Sukses", $q);
    }
}
