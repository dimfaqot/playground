<?php

namespace App\Controllers;

class Guest extends BaseController
{
    public function nota($jwt)
    {
        $decode = decode_jwt($jwt);

        $db = db($decode['tabel']);
        $no_nota = '';
        $db;
        if ($decode['tabel'] == "ps" || $decode['tabel'] == "billiard") {
            $db->where('id', $decode['id']);
        } else {
            $db->where('no_nota', $decode['no_nota']);
        }
        $q = $db->orderBy('tgl', 'ASC')->get()->getResultArray();


        if ($decode['tabel'] == "ps" || $decode['tabel'] == "billiard") {
            foreach ($q as $i) {
                $no_nota = upper_first(substr($decode['tabel'], 0, 1)) . date('dmY', $i['tgl']) . $i['id'];
            }
        } else {
            $no_nota = $decode['no_nota'];
        }

        // Buat instance mPDF
        $set = [
            'mode' => 'utf-8',
            'orientation' => 'P',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 8,
            'margin_bottom' => 8
        ];

        $mpdf = new \Mpdf\Mpdf($set);

        $judul = "Transaksi " . upper_first($decode['tabel']) . " No. Nota: " . $no_nota;
        // Dapatkan konten HTML
        $logo = '<img width="50" src="logo.png" alt="KOP"/>';
        $html = view('cetak/nota', ['judul' => $judul, 'jwt' => base_url('guest/nota/') . $jwt, 'data' => $q, 'logo' => $logo, 'no_nota' => $no_nota, 'tgl' => date('d/m/Y', $q[0]['tgl']), 'teller' => $q[0]['petugas'], 'pembeli' => $q[0]['pembeli'], 'tabel' => $decode['tabel']]); // view('pdf_template') mengacu pada file view yang akan dirender menjadi PDF

        // Setel konten HTML ke mPDF
        $mpdf->WriteHTML($html);

        // Output PDF ke browser
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf->Output($judul . '.pdf', 'I');
    }

    public function laporan($bulan, $tahun)
    {
        $bl = bulan(upper_first($bulan))['angka'];

        $db_masuk = db("penjualan");
        $db_keluar = db("pengeluaran");


        $masuk = $db_masuk->orderBy('tgl', 'ASC')->orderBy('barang', 'ASC')->get()->getResultArray();
        $keluar = $db_keluar->orderBy('tgl', 'ASC')->orderBy('barang', 'ASC')->get()->getResultArray();

        $data_masuk = [];
        $data_keluar = [];


        // mencari tahun pemasukan
        $total_masuk = 0;
        foreach ($masuk as $i) {

            if ($tahun == date('Y', $i['tgl']) && $bl == date('m', $i['tgl'])) {
                $data_masuk[] = $i;
                $total_masuk += (int)$i['total'];
            }
        }

        // mencari tahun pengeluaran
        $total_keluar = 0;
        foreach ($keluar as $i) {
            if ($tahun == date('Y', $i['tgl']) && $bl == date('m', $i['tgl'])) {
                $data_keluar[] = $i;
                $total_keluar += (int)$i['total'];
            }
        }

        $set = [
            'mode' => 'utf-8',
            'format' => [210, 330],
            'orientation' => 'P',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5
        ];

        $mpdf = new \Mpdf\Mpdf($set);

        $judul = "LAPORAN CAFE BULAN " . strtoupper($bulan) . " TAHUN " . $tahun;
        // Dapatkan konten HTML
        $logo = '<img width="90" src="logo.png" alt="KOP"/>';
        $html = view('cetak/laporan', ['judul' => $judul, 'logo' => $logo, 'tahun' => $tahun, 'bulan' => $bulan, 'masuk' => $data_masuk, 'keluar' => $data_keluar, 'total_masuk' => $total_masuk, 'total_keluar' => $total_keluar]); // view('pdf_template') mengacu pada file view yang akan dirender menjadi PDF

        // Setel konten HTML ke mPDF
        $mpdf->WriteHTML($html);

        // Output PDF ke browser
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf->Output($judul . '.pdf', 'I');
    }
}
