<?php

namespace App\Controllers;

class Perangkat extends BaseController
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


    public function index(): string
    {

        $db = db(menu()['tabel']);

        $data = $db->orderBy('grup', 'ASC')->orderBy('urutan', 'ASC')->get()->getResultArray();
        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function add()
    {
        $kategori = upper_first(clear($this->request->getVar('kategori')));
        $perangkat = upper_first(clear($this->request->getVar('perangkat')));
        $pin = clear($this->request->getVar('pin'));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $grup = clear(upper_first($this->request->getVar('grup')));
        $urutan = upper_first($this->request->getVar('urutan'));
        $desc = upper_first($this->request->getVar('desc'));

        $db = db(menu()['tabel']);
        if ($db->where('grup', $grup)->where('perangkat', $perangkat)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Perangkat sudah ada!.");
        }

        $urutan = 0;
        $q = $db->orderBy('urutan', 'DESC')->get()->getRowArray();

        if ($q) {
            $urutan = (int)$q['urutan'] + 1;
        }

        $data = [
            'kategori' => $kategori,
            'perangkat' => $perangkat,
            'status' => 0,
            'harga' => $harga,
            'pin' => $pin,
            'grup' => $grup,
            'urutan' => $urutan,
            'desc' => $desc
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
        $perangkat = upper_first(clear($this->request->getVar('perangkat')));
        $pin = clear($this->request->getVar('pin'));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $grup = clear(upper_first($this->request->getVar('grup')));
        $urutan = upper_first($this->request->getVar('urutan'));
        $desc = upper_first($this->request->getVar('desc'));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        if ($db->whereNotIn('id', [$id])->where('grup', $grup)->where('perangkat', $perangkat)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Perangkat sudah ada!.");
        }


        $q['kategori'] = $kategori;
        $q['perangkat'] = $perangkat;
        $q['pin'] = $pin;
        $q['harga'] = $harga;
        $q['grup'] = $grup;
        $q['urutan'] = $urutan;
        $q['desc'] = $desc;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }
    public function update_blur()
    {
        $id = clear($this->request->getVar('id'));
        $val = rp_to_int(clear($this->request->getVar('val')));
        $col = strtolower(clear($this->request->getVar('col')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js("Id tidak ditemukan!.");
        }


        $q[$col] = $val;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }
}
