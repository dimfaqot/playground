<?php

namespace App\Controllers;

class Menu extends BaseController
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
        $cols = ['id', 'role', 'menu', 'tabel', 'controller', 'icon', 'urutan', 'grup'];

        $db = db(menu()['tabel']);

        $data = $db->select(implode(",", $cols))->orderBy('urutan', 'ASC')->get()->getResultArray();
        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function add()
    {
        $role = clear($this->request->getVar('role'));
        $menu = clear(upper_first($this->request->getVar('menu')));
        $tabel = clear(strtolower($this->request->getVar('tabel')));
        $controller = clear(strtolower($this->request->getVar('controller')));
        $icon = clear(strtolower($this->request->getVar('icon')));
        $grup = clear(upper_first($this->request->getVar('grup')));

        $db = db(menu()['tabel']);
        if ($db->where('menu', $menu)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Menu sudah ada!.");
        }

        $urutan = 0;
        $q = $db->where('role', $role)->orderBy('urutan', 'DESC')->get()->getRowArray();

        if ($q) {
            $urutan = (int)$q['urutan'] + 1;
        }

        $data = [
            'role' => $role,
            'menu' => $menu,
            'tabel' => $tabel,
            'controller' => $controller,
            'icon' => $icon,
            'urutan' => $urutan,
            'grup' => $grup
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
