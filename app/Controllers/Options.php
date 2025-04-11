<?php

namespace App\Controllers;

class Options extends BaseController
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

        $data = $db->orderBy('grup', 'ASC')->orderBy('value', 'ASC')->get()->getResultArray();
        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function add()
    {
        $grup = clear(upper_first($this->request->getVar('grup')));
        $value = clear(upper_first($this->request->getVar('value')));

        $db = db(menu()['tabel']);
        if ($db->where('grup', $grup)->where('value', $value)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Data sudah ada!.");
        }

        $data = [
            'grup' => $grup,
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
        $grup = clear(upper_first($this->request->getVar('grup')));
        $value = clear(upper_first($this->request->getVar('value')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        if ($db->whereNotIn('id', [$id])->where('grup', $grup)->where('value', $value)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Data sudah ada!.");
        }


        $q['grup'] = $grup;
        $q['value'] = $value;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }
}
