<?php

namespace App\Controllers;

class Hutang extends BaseController
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

    public function index($kategori = "Billiard"): string
    {

        $kategori = (user()['role'] !== "Root" ? user()['role'] : $kategori);
        $db_user = db("user");

        $q = $db_user->orderBy('nama', 'ASC')->get()->getResultArray();

        $data = [];

        $dbk = db(strtolower($kategori));
        foreach ($q as $i) {
            $hutang = $dbk->where('user_id', $i['id'])->where('metode', "Hutang")->get()->getRowArray();

            if ($hutang) {
                $i['metode'] = "Hutang";
            } else {
                $i['metode'] = "Cash";
            }

            $data[] = $i;
        }



        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data, 'kategori' => $kategori]);
    }

    public function detail()
    {
        $user_id = clear($this->request->getVar('user_id'));
        $tabel = clear($this->request->getVar('tabel'));
        $kategori = clear($this->request->getVar('kategori'));
        $order = clear($this->request->getVar('order'));

        $data = [];
        $total = 0;
        if ($kategori == "All") {
            $divisi = options('Divisi');
            foreach ($divisi as $x) {
                $db = db(strtolower($x['value']));
                $db->where('user_id', $user_id);
                if ($order == "Hutang") {
                    $db->where('metode', "Hutang");
                }
                $val = $db->orderBy('tgl', 'ASC')->get()->getResultArray();

                foreach ($val as $i) {
                    $total += (int)$i['total'];
                    if ($x['value'] == "Ps" || $x['value'] == "Billiard") {
                        if ($i['status'] == 1) {
                            continue;
                        }
                        $i['no_nota'] = upper_first(substr($x['value'], 0, 1)) . date('dmY', $i['tgl']) . "-" . $i['id'];
                        $i['barang'] = $i['perangkat'];
                        $i['qty'] = $i['durasi'];
                    }

                    $dbu = db('user');
                    $usr = $dbu->where('id', $i['user_id'])->get()->getRowArray();

                    $data[] = [
                        'id' => $i['id'],
                        'nama' => $i['pembeli'],
                        'no_nota' => $i['no_nota'],
                        'user_id' => $i['user_id'],
                        'hp' => ($usr ? $usr['hp'] : ""),
                        'kategori' => $x['value'],
                        'tgl' => $i['tgl'],
                        'metode' => $i['metode'],
                        'barang' => $i['barang'],
                        'qty' => $i['qty'],
                        'harga' => $i['total']
                    ];
                }
            }
        } else {
            $db = db($tabel);
            $db->where('user_id', $user_id);
            if ($order == "Hutang") {
                $db->where('metode', "Hutang");
            }
            $val = $db->orderBy('tgl', 'ASC')->get()->getResultArray();

            foreach ($val as $i) {
                $total += (int)$i['total'];
                if ($tabel == "ps" || $tabel == "billiard") {
                    if ($i['status'] == 1) {
                        continue;
                    }
                    $i['barang'] = $i['perangkat'];
                    $i['qty'] = $i['durasi'];
                    $i['no_nota'] = upper_first(substr($tabel, 0, 1)) . $i['id'];
                }

                $dbu = db('user');
                $usr = $dbu->where('id', $i['user_id'])->get()->getRowArray();

                $data[] = [
                    'id' => $i['id'],
                    'nama' => $i['pembeli'],
                    'user_id' => $i['user_id'],
                    'no_nota' => $i['no_nota'],
                    'hp' => ($usr ? $usr['hp'] : ""),
                    'kategori' => upper_first($tabel),
                    'tgl' => $i['tgl'],
                    'metode' => $i['metode'],
                    'barang' => $i['barang'],
                    'qty' => $i['qty'],
                    'harga' => $i['total']
                ];
            }
        }

        sukses_js("Sukses", $data, $total, base_url("guest/transaksi/") . encode_jwt(['id' => $user_id]));
    }

    public function lunas()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $uang_lunas = rp_to_int(clear($this->request->getVar('uang_lunas')));
        $total = rp_to_int(clear($this->request->getVar('total')));
        $metode = clear($this->request->getVar('metode'));

        if ($uang_lunas < $total) {
            gagal_js("Uang kurang!.");
        }
        $err = [];
        $total2 = 0;
        foreach ($data as $i) {
            $db = db(strtolower($i['kategori']));
            $q = $db->where('id', $i['id'])->get()->getRowArray();

            if ($q) {
                $q['metode'] = $metode;
                $db->where('id', $i['id']);
                if ($db->update($q)) {
                    $total2 += (int)$q['total'];
                } else {
                    $err[] = $i['barang'];
                }
            } else {
                $err[] = $i['barang'];
            }
        }

        sukses_js("Sukses", angka($uang_lunas - $total2), $err);
    }
}
