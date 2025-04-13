<?php

namespace App\Controllers;

class Settings extends BaseController
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

        $data = $db->orderBy('setting', 'ASC')->orderBy('value', 'ASC')->get()->getResultArray();
        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function add()
    {
        $setting = clear(upper_first($this->request->getVar('setting')));
        $value = clear(upper_first($this->request->getVar('value')));

        $db = db(menu()['tabel']);
        if ($db->where('setting', $setting)->where('value', $value)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Data sudah ada!.");
        }

        $data = [
            'setting' => $setting,
            'value' => $value
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
        $setting = clear(upper_first($this->request->getVar('setting')));
        $value = clear(upper_first($this->request->getVar('value')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        if ($db->whereNotIn('id', [$id])->where('setting', $setting)->where('value', $value)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Data sudah ada!.");
        }


        $q['setting'] = $setting;
        $q['value'] = $value;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }
}
