<?php

namespace App\Controllers;

use App\Models\FunModel;

class Tap extends BaseController
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


    public function index($tahun = null, $bulan = null, $angkatan = null): string
    {

        $tahun = ($tahun == null ? date('Y') : $tahun);
        $bulan = ($bulan == null ? date('m') : $bulan);
        $angkatan = ($angkatan == null ? (int)date('Y') - 1 : $angkatan);


        $dbu = db('user');
        $users = $dbu->where('angkatan', $angkatan)->orderBy('nama', 'ASC')->get()->getResultArray();

        $angkatans = $dbu->whereNotIn('angkatan', [0])->orderBy('angkatan', 'ASC')->groupBy('angkatan')->get()->getResultArray();

        $data = [];
        foreach ($users as $a) {
            $temp_data = [];
            $total = 0;
            foreach (options('Divisi') as $d) {
                $dbd = db(strtolower($d['value']));

                $dbd->where("user_id", $a['id']);
                $dbd->where("metode", "Tap");
                $dbd->where("YEAR(FROM_UNIXTIME(tgl))", $tahun);
                $dbd->where("LPAD(MONTH(FROM_UNIXTIME(tgl)), 2, '0')", $bulan);
                $query = $dbd->get();
                $result = $query->getResultArray();

                foreach ($result as $i) {
                    $i['divisi'] = $d['value'];
                    if ($d['value'] == "Billiard" || $d['value'] == "Ps") {
                        $i['qty'] = $i['durasi'];
                        $i['barang'] = $i['perangkat'];
                    }
                    $total += (int)$i['total'];
                    $temp_data[] = $i;
                }
            }
            $data[] = ['profile' => $a, 'total' => $total, 'data' => $temp_data];
        }


        // Pastikan variabel $perangkat, $total, dan $hutang sudah dideklarasikan sebelumnya
        return view(menu()['controller'], [
            'judul' => menu()['menu'],
            'data' => $data,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'angkatan' => $angkatan,
            'angkatans' => $angkatans
        ]);
    }

    public function bayar()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $tahun = clear($this->request->getVar("tahun"));
        $bulan = clear($this->request->getVar("bulan"));

        $err = 0;
        foreach ($data as $a) {
            $temp_data = [];
            $total = 0;
            foreach (options('Divisi') as $d) {
                $dbd = db(strtolower($d['value']));

                $dbd->where("user_id", $a['user_id']);
                $dbd->where("metode", "Tap");
                $dbd->where("YEAR(FROM_UNIXTIME(tgl))", $tahun);
                $dbd->where("LPAD(MONTH(FROM_UNIXTIME(tgl)), 2, '0')", $bulan);
                $query = $dbd->get();
                $result = $query->getResultArray();

                foreach ($result as $i) {
                    $i['divisi'] = $d['value'];
                    if ($d['value'] == "Billiard" || $d['value'] == "Ps") {
                        $i['qty'] = $i['durasi'];
                        $i['barang'] = $i['perangkat'];
                    }
                    $total += (int)$i['total'];
                    $temp_data[] = $i;
                }
            }

            if ($total == $a['total']) {
                foreach ($temp_data as $i) {
                    $db = db(strtolower($i['divisi']));
                    $q = $db->where('id', $i['id'])->get()->getRowArray();
                    if ($q) {
                        $q['metode'] = 'Done';
                        $q['petugas'] = user()['nama'];
                        $db->where('id', $q['id']);
                        if (!$db->update($q)) {
                            $err++;
                        }
                    }
                }
            } else {
                gagal_js($a['user_id'] . ": Total tidak sama (" . angka($total) . " / " . angka($a['total'] . ")"));
            }
        }

        if ($err == 0) {
            sukses_js("Sukses...");
        } else {
            gagal_js(angka($err) . " data gagal diproses...");
        }
    }
}
