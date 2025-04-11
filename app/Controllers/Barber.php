<?php

namespace App\Controllers;

class Barber extends BaseController
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

        $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();

        $data = [];
        $total = 0;
        $hutang = 0;
        foreach ($q as $i) {
            if ($i['metode'] == "Hutang") {
                $data[] = $i;
                $total += (int)$i['total'];
                $hutang += (int)$i['total'];
            } else {
                if (date('m', $i['tgl']) == date('m') && date('Y', $i['tgl']) == date('Y') && date('d', $i['tgl']) == date('d')) {
                    $data[] = $i;
                    $total += (int)$i['total'];
                }
            }
        }

        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data, 'total' => $total, 'hutang' => $hutang, 'no_nota' => "K" . no_nota(time(), 'barber'), 'kategori' => 'Barber']);
    }


    public function cari_barang()
    {
        $value = clear($this->request->getVar('value'));
        $kategori = clear($this->request->getVar('kategori'));
        $db = db('barang');
        $q = $db->whereIn('kategori', [$kategori])->like('barang', $value, 'both')->orderBy('barang', 'ASC')->limit(8)->get()->getResultArray();

        sukses_js("Sukses", $q);
    }

    public function cari_user()
    {
        $val = clear($this->request->getVar('val'));

        $db = db('user');
        $q = $db->like('nama', $val, 'both')->orderBy('nama', 'ASC')->limit(10)->get()->getResultArray();

        sukses_js("Ok", $q);
    }
    public function transaksi()
    {
        $data = json_decode(json_encode($this->request->getVar("data_transaksi")), true);
        $pembeli = json_decode(json_encode($this->request->getVar("pembeli")), true);
        $metode = upper_first(clear($this->request->getVar("metode")));
        $uang_pembayaran = rp_to_int(clear($this->request->getVar("uang_pembayaran")));
        $no_nota = clear($this->request->getVar("no_nota"));

        $db = db(menu()['tabel']);

        $nota_exist = $db->where('no_nota', $no_nota)->get()->getResultArray();

        if ($nota_exist) {
            $total2 = 0;
            $err = [];
            foreach ($data as $i) {
                $i['metode'] = $metode;
                $i['petugas'] = user()['nama'];

                $db->where('id', $i['id']);
                if ($db->update($i)) {
                    $total2 += (int)$i['total'];
                } else {
                    $err[] = $i['barang'];
                }
            }

            $jwt = base_url("guest/nota/") . encode_jwt(['tabel' => menu()['tabel'], 'no_nota' => $no_nota]);
            sukses_js("Transaksi sukses.", ($uang_pembayaran - $total2), $err, $jwt);
        } else {
            $total2 = 0;
            $err = [];
            foreach ($data as $i) {
                unset($i["id"]);
                $i['tgl'] = time();
                $i['no_nota'] = $no_nota;
                $i['metode'] = $metode;
                $i['pembeli'] = $pembeli['nama'];
                $i['user_id'] = $pembeli['user_id'];
                $i['petugas'] = user()['nama'];

                if ($db->insert($i)) {
                    $total2 += (int)$i['total'];
                } else {
                    $err[] = $i['barang'];
                }
            }
            $jwt = base_url("guest/nota/") . encode_jwt(['tabel' => menu()['tabel'], 'no_nota' => $no_nota]);
            sukses_js("Transaksi sukses.", ($uang_pembayaran - $total2), $err, $jwt);
        }
    }
}
