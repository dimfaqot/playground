<?php

namespace App\Controllers;

class Penjualan extends BaseController
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

        foreach ($q as $i) {
            if (date('m', $i['tgl']) == date('m') && date('Y', $i['tgl']) == date('Y') && date('d', $i['tgl']) == date('d')) {
                $data[] = $i;
            }
        }
        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function no_nota()
    {
        sukses_js("Sukses", no_nota(time()));
    }
    public function cari_barang()
    {
        $value = clear($this->request->getVar('value'));
        $db = db('barang');
        $q = $db->whereIn('kategori', ['barang'])->like('barang', $value, 'both')->orderBy('barang', 'ASC')->limit(8)->get()->getResultArray();

        sukses_js("Sukses", $q);
    }
    public function transaksi()
    {
        $data = json_decode(json_encode($this->request->getVar('daftar_transaksi')), true);
        $no_nota = explode(": ", clear($this->request->getVar('no_nota')));
        $uang_pembayaran = rp_to_int(clear($this->request->getVar('uang_pembayaran')));
        $nama_pembeli = upper_first(clear($this->request->getVar('nama_pembeli')));
        $ket = clear($this->request->getVar('ket'));
        $user_id = clear($this->request->getVar('user_id'));
        $order = clear($this->request->getVar('order'));

        $total = 0;


        foreach ($data as $i) {
            $total += (int)$i['total'];
        }

        if ($uang_pembayaran < $total) {
            gagal_js("Uang kurang!.");
        }

        $db = db('penjualan');
        $dbb = db('barang');

        $total_2 = 0;
        $err = [];
        foreach ($data as $i) {
            $i['qty'] = rp_to_int($i['qty']);
            $i['diskon'] = rp_to_int($i['diskon']);
            $i['harga'] = rp_to_int($i['harga']);
            $i['total'] = rp_to_int($i['total']);
            if ($order == "Lunas") {
                $i['ket'] = $ket;
                $db->where('id', $i['id']);
                if ($db->update($i)) {
                    $total_2 += $i['total'];
                } else {
                    $err[] = $i['barang'];
                }
            } else {
                $data = [
                    'no_nota' => end($no_nota),
                    'tgl' => time(),
                    'pembeli' => $nama_pembeli,
                    'petugas' => user()['nama'],
                    'barang' => $i['barang'],
                    'qty' => $i['qty'],
                    'diskon' => $i['diskon'],
                    'harga' => $i['harga'],
                    'total' => $i['total'],
                    'user_id' => $user_id,
                    'ket' => $ket
                ];


                if ($db->insert($data)) {
                    $total_2 += $i['total'];

                    $barang = $dbb->where('id', $i['id'])->get()->getRowArray();

                    if ($barang) {
                        $barang['qty'] -= $i['qty'];
                        $dbb->where('id', $barang['id']);
                        $dbb->update($barang);
                    }
                } else {
                    $err[] = $i['barang'];
                }
            }
        }

        $url_nota = base_url('guest/cetak_nota/') . encode_jwt(['no_nota' => end($no_nota)]);

        sukses_js("Sukses", $uang_pembayaran - $total_2, implode(", ", $err), $url_nota);
    }

    public function user()
    {
        $val = clear(upper_first($this->request->getVar('val')));

        $db = db('user');
        $q = $db->like('nama', $val, 'both')->orderBy('nama', 'ASC')->limit(10)->get()->getResultArray();

        sukses_js("Ok", $q);
    }
}
