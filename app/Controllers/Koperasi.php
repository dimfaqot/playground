<?php

namespace App\Controllers;

use App\Models\FunModel;

class Koperasi extends BaseController
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
        $fun = new FunModel();
        $data = $fun->koperasi();
        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data['data'], "masuk" => $data['total_masuk'], "keluar" => $data['total_keluar'], "detail" => $data['detail']]);
    }

    public function add()
    {
        $kategori = clear($this->request->getVar('kategori'));
        $uang = rp_to_int(clear($this->request->getVar('uang')));
        $pj = upper_first(clear($this->request->getVar('pj')));

        $db = db(menu()['tabel']);

        $data = [
            'tgl' => time(),
            'kategori' => $kategori,
            'uang' => $uang,
            'pj' => $pj
        ];

        foreach (options("Saham") as $i) {
            $data[strtolower($i['value'])] = ((int)settings($i['value']) / 100) * $uang;
        }

        if ($db->insert($data)) {
            sukses(base_url(menu()['controller']), "Tambah data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Tambah data gagal!.");
        }
    }
    public function add_pencairan()
    {
        $saham = clear($this->request->getVar('saham'));
        $pemberi = upper_first(clear($this->request->getVar('pemberi')));
        $penerima = upper_first(clear($this->request->getVar('penerima')));
        $lokasi = upper_first(clear($this->request->getVar('lokasi')));
        $uang = rp_to_int(clear($this->request->getVar('uang')));
        $max = rp_to_int(clear($this->request->getVar('max')));

        if ($uang > $max) {
            gagal(base_url('koperasi'), "Maksimal " . angka($max));
        }

        $db = db("pencairan");

        $data = [
            'tgl' => time(),
            'saham' => $saham,
            'pemberi' => $pemberi,
            'penerima' => $penerima,
            'lokasi' => $lokasi,
            'uang' => $uang,
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
        $role = clear($this->request->getVar('role'));
        $menu = clear(upper_first($this->request->getVar('menu')));
        $tabel = clear(strtolower($this->request->getVar('tabel')));
        $controller = clear(strtolower($this->request->getVar('controller')));
        $icon = clear(strtolower($this->request->getVar('icon')));
        $grup = clear(upper_first($this->request->getVar('grup')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        if ($db->whereNotIn('id', [$id])->where('menu', $menu)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Menu sudah ada!.");
        }


        $q['role'] = $role;
        $q['menu'] = $menu;
        $q['tabel'] = $tabel;
        $q['controller'] = $controller;
        $q['icon'] = $icon;
        $q['grup'] = $grup;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }
}
