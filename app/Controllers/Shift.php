<?php

namespace App\Controllers;

use App\Models\AbsenModel;

class Shift extends BaseController
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

        $q = $db->orderBy('divisi', 'ASC')->orderBy('shift', 'ASC')->get()->getResultArray();

        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $q]);
    }


    public function cari_user()
    {
        $val = clear($this->request->getVar('val'));

        $db = db('user');
        $q = $db->whereNotIn('role', ['Member'])->like('nama', $val, 'both')->orderBy('nama', 'ASC')->limit(10)->get()->getResultArray();

        sukses_js("Ok", $q);
    }

    public function add()
    {
        $divisi = clear($this->request->getVar('divisi'));
        $shift = rp_to_int(clear($this->request->getVar('shift')));
        $pukul = clear($this->request->getVar('pukul'));
        $user_id = rp_to_int(clear($this->request->getVar('user_id')));


        if (strpos($pukul, '-') == false) {
            gagal(base_url(menu()['controller']), "Format pukul salah (-)!.");
        }
        $dbu = db('user');
        $user = $dbu->where('id', $user_id)->whereNotIn('role', ['Member'])->get()->getRowArray();
        if (!$user) {
            gagal(base_url(menu()['controller']), "Admin not found!.");
        }


        $db = db(menu()['tabel']);
        if ($db->where('divisi', $divisi)->where('shift', $shift)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Shift sudah ada!.");
        }
        if ($db->where('divisi', $divisi)->where('pukul', $pukul)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Pukul sudah ada!.");
        }

        $data = [
            'divisi' => $divisi,
            'shift' => $shift,
            'pukul' => $pukul,
            'petugas' => $user['nama'],
            'user_id' => $user['id']
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
        $divisi = clear($this->request->getVar('divisi'));
        $shift = rp_to_int(clear($this->request->getVar('shift')));
        $pukul = clear($this->request->getVar('pukul'));
        $user_id = rp_to_int(clear($this->request->getVar('user_id')));


        if (strpos($pukul, '-') == false) {
            gagal(base_url(menu()['controller']), "Format pukul salah (-)!.");
        }
        $dbu = db('user');
        $user = $dbu->where('id', $user_id)->whereNotIn('role', ['Member'])->get()->getRowArray();
        if (!$user) {
            gagal(base_url(menu()['controller']), "Admin not found!.");
        }


        $db = db(menu()['tabel']);
        if ($db->whereNotIn('id', [$id])->where('divisi', $divisi)->where('shift', $shift)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Shift sudah ada!.");
        }
        if ($db->whereNotIn('id', [$id])->where('divisi', $divisi)->where('pukul', $pukul)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Pukul sudah ada!.");
        }
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), "Id not found!.");
        }


        $q['divisi'] = $divisi;
        $q['shift'] = $shift;
        $q['pukul'] = $pukul;
        $q['petugas'] = $user['nama'];
        $q['user_id'] = $user['id'];

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }

    public function tes() {}
}
