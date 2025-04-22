<?php

namespace App\Controllers;


class Kasir extends BaseController
{
    public function index()
    {

        // $fun = new \App\Models\FunModel();
        // $db = db('user');
        // $q = $db->orderBy('id')->get()->getResultArray();

        // foreach ($q as $i) {
        //     $i['fulus'] = $fun->enkripsi_fulus($i, 0);
        //     $db->where('id', $i['id']);
        //     $db->update($i);
        // }


        $divisi = "Kantin";
        $judul = "Kantin 1";
        $db = db('nota');

        $hari_ini = time_hari_ini();
        $data_hari_ini = $db->where('tgl >=', $hari_ini['start'])->where('tgl <=', $hari_ini['end'])->groupBy('no_nota')->orderBy('tgl', 'DESC')->get()->getResultArray();


        $data = [];
        foreach ($data_hari_ini as $i) {
            $i['jwt'] = base_url('guest/nota/') . encode_jwt(['tabel' => 'nota', 'no_nota' => $i['no_nota']]);
            $data[] = $i;
        }
        $db = db('iot');
        $q = $db->where('kategori', 'Absen')->where('status', 'Tap')->where('divisi', "Kantin")->get()->getRowArray();

        $jwt = '';
        if ($q) {
            $jwt = encode_jwt(['time' => time(), 'controller' => 'kasir']);
        }

        $dbu = db('user');
        $q_users = $dbu->orderBy('nama', 'ASC')->get()->getResultArray();
        $users = [];

        $fun = new \App\Models\FunModel();
        foreach ($q_users as $i) {
            $i['saldo'] = $fun->dekripsi_fulus($i, $i['fulus']);
            $users[] = $i;
        }


        return view('public/kasir', ['judul' => $judul, 'data' => $data, 'divisi' => $divisi, 'users' => $users, 'today' => $fun->data_hari_ini(), 'absen' => $q, 'jwt' => $jwt]);
    }




    // kantin barber

    public function cari_barang()
    {
        $value = clear($this->request->getVar('value'));
        // $mode_bayar = clear($this->request->getVar('mode_bayar'));
        $order = clear($this->request->getVar('order'));
        $db = db(($order == "Kantin" || $order == "Barber" ? "barang" : strtolower($order)));
        $data = [];
        if ($order == "Barber" || $order == "Kantin") {
            $data = $db->whereIn('kategori', [$order])->like('barang', $value, 'both')->orderBy('barang', 'ASC')->limit(8)->get()->getResultArray();
        } else {
            $fun = new \App\Models\FunModel();
            // $q = [];
            // if ($mode_bayar) {
            //     $dbp = db('perangkat');
            //     $qp = $dbp->where('kategori', $order)->like('perangkat', $value, 'both')->get()->getResultArray();
            //     foreach ($qp as $i) {
            //         $query = $db->where('perangkat', $i['perangkat'])->whereIn('metode', ['Play', 'Over'])->get()->getRowArray();
            //         if (!$query) {
            //             $q[] = $db->where('perangkat', $i['perangkat'])->get()->getRowArray();
            //         }
            //     }
            // } else {
            $q = $db->whereIn('metode', ['Play', 'Over'])->like('perangkat', $value, 'both')->orderBy('tgl', 'DESC')->limit(10)->get()->getResultArray();
            // }


            foreach ($q as $i) {
                $akhiri = $fun->akhiri($i);
                $i['jenis'] = ($i['durasi'] == 0 ? "Open" : ($i['durasi'] / 60) . " Jam");
                $i['durasi'] = $akhiri['durasi'];
                $i['biaya'] = $akhiri['harga'];
                $i['mulai'] = date("H:i", $i['dari']);
                $data[] = $i;
            }
        }


        sukses_js("Sukses", $data);
    }

    public function cari_user()
    {
        $val = clear($this->request->getVar('val'));

        $db = db('user');
        $q = $db->whereIn('role', ['Member', 'Ceo', 'Root'])->like('nama', $val, 'both')->orderBy('nama', 'ASC')->limit(10)->get()->getResultArray();

        $res = [];

        $fun = new \App\Models\FunModel();
        foreach ($q as $i) {
            $i['saldo'] = $fun->dekripsi_fulus($i, $i['fulus']);
            $res[] = $i;
        }


        sukses_js("Sukses", $res);
    }

    public function transaksi()
    {
        $uang_pembayaran = rp_to_int($this->request->getVar('uang_pembayaran'));
        $metode = clear($this->request->getVar('metode'));
        $jwt = decode_jwt(clear($this->request->getVar('jwt')));
        $petugas = clear($this->request->getVar('petugas'));
        $lokasi = clear($this->request->getVar('lokasi'));
        $data = json_decode(json_encode($this->request->getVar('data_transaksi')), true);
        $pembeli = json_decode(json_encode($this->request->getVar('pembeli')), true);

        $tgl = time();
        $dbu = db('user');
        $user = $dbu->where('id', $pembeli['user_id'])->get()->getRowArray();
        if (!$user) {
            gagal_js("User id not found...");
        }

        if ($jwt['controller'] != "kasir") {
            gagal_js("Failed jwt...");
        }
        if (time() > ($jwt['time'] + (60 * 20))) {
            gagal_js("Time over...");
        }
        $val = [];
        foreach ($data as $i) {
            if ($i['kategori'] == "Ps" || $i['kategori'] == "Billiard") {
                $db = db(strtolower($i['kategori']));
                $q = $db->where('id', $i['id'])->get()->getRowArray();
                if ($q) {
                    $val[] = $i;
                } else {
                    $val['err'][] = $i['barang'];
                }
            } else {
                $val[] = $i;
            }
        }

        if (count($val) !== count($data)) {
            gagal_js(implode(", ", $val['err']));
        }

        $total = 0;

        foreach ($data as $i) {
            $total += (int)$i['total'];
        }

        if ($total > $uang_pembayaran) {
            gagal_js("Uang pembayaran kurang...");
        }

        if ($metode == "Tap") {
            $fun = new \App\Models\FunModel();
            $fulus = $fun->dekripsi_fulus($user, $user['fulus']);

            if ($fulus < $total) {
                gagal_js("Saldo kurang...");
            }
        }


        $dbx = \Config\Database::connect();

        $total2 = 0;
        $nota = [];

        foreach ($val as $i) {
            $db = db(strtolower(($i['kategori'] == "Ps" || $i['kategori'] == "Billiard" ? $i['kategori'] : "barang")));
            $q = $db->where('id', $i['id'])->get()->getRowArray();

            if ($i['kategori'] == "Ps" || $i['kategori'] == "Billiard") {
                if ($q) {

                    // if ($i['tipe'] === true) {
                    //     unset($q["id"]);
                    //     $q['durasi'] = $i['qty'];
                    //     $q['diskon'] = $i['diskon'];
                    //     $q['total'] = $i['total'];
                    //     $q['metode'] = $metode;
                    //     $q['dari'] = $tgl;
                    //     $q['ke'] = (int)$tgl + ((int)$i['qty'] * 60);
                    //     $q['status'] = 1;
                    //     $q['metode'] = "Play";
                    //     $q['user_id'] = $pembeli['user_id'];
                    //     $q['pembeli'] = $pembeli['nama'];
                    //     $q['petugas'] = $petugas;
                    //     $total2 += (int)$q['total'];
                    //     $db_bayar_dulu = $dbx->table(strtolower($i['kategori']));
                    //     if ($db_bayar_dulu->insert($q)) {
                    //         $new_id = $dbx->insertID();
                    //         $q['id'] = $new_id;
                    //         $q['tabel'] = strtolower($i['kategori']);
                    //         $q['barang'] = $q['perangkat'];
                    //         $q['qty'] = $q['durasi'];
                    //         $q['metode'] = $metode;

                    //         $nota[] = $q;
                    //     }
                    // } else {

                    $q['metode'] = $metode;
                    $q['status'] = 0;
                    $q['diskon'] = $i['diskon'];
                    if ($q['durasi'] == 0) {
                        $q['total'] = $i['total'];
                        $q['ke'] = time();
                        $q['durasi'] = $i['qty'];
                    }

                    $q['pembeli'] = $pembeli['nama'];
                    $q['user_id'] = $pembeli['user_id'];
                    $q['petugas'] = $petugas;

                    $db->where('id', $q['id']);
                    if ($db->update($q)) {
                        $total2 += (int)$q['total'];
                        $temp_nota = [
                            'tabel' => strtolower($i['kategori']),
                            'tgl' => $tgl,
                            'id' => $q['id'],
                            'barang' => $q['perangkat'],
                            'harga' => $q['harga'],
                            'qty' => $q['durasi'],
                            'diskon' => $q['diskon'],
                            'total' => $q['total'],
                            'petugas' => $petugas,
                            'pembeli' => $pembeli['nama'],
                            'metode' => $metode,
                            'user_id' => $pembeli['user_id']
                        ];
                        $nota[] = $temp_nota;
                    }
                    // }
                }
            } else {
                if ($i['tipe'] == "exist") {
                    $db = db(strtolower($i['kategori']));
                    $q = $db->where('id', $i['id'])->get()->getRowArray();

                    if ($q) {
                        $q['metode'] = $metode;
                        $q['petugas'] = $petugas;

                        $db->where('id', $i['id']);
                        $db->update($q);
                        $total2 += (int)$q['total'];
                        $q['tgl'] = $tgl;
                        $q['tabel'] = strtolower($i['kategori']);

                        $nota[] = $q;
                    }
                } else {
                    if ($q) {
                        $no_nota = substr($i['kategori'], 0, 1) . no_nota($tgl, strtolower($i['kategori']));
                        $insert = [];
                        $insert['tgl'] = $tgl;
                        $insert['no_nota'] = $no_nota;
                        $insert['metode'] = $metode;
                        $insert['pembeli'] = $pembeli['nama'];
                        $insert['user_id'] = $pembeli['user_id'];
                        $insert['petugas'] = $petugas;
                        $insert['barang'] = $q['barang'];
                        $insert['qty'] = $i['qty'];
                        $insert['diskon'] = $i['diskon'];
                        $insert['harga'] = $q['harga'];
                        $insert['total'] = (((int)$q['harga'] * (int)$i['qty']) - (int)$i['diskon']);

                        $db_insert = $dbx->table(strtolower($i['kategori']));
                        if ($db_insert->insert($insert)) {
                            $new_id = $dbx->insertID();

                            $q['qty'] -= (int)$insert['qty'];
                            $db->where('id', $i['id']);
                            $db->update($q);
                            $total2 += (int)$insert['total'];
                            $insert['tabel'] = strtolower($i['kategori']);
                            $insert['tgl'] = $tgl;
                            $insert['id'] = $new_id;

                            $nota[] = $insert;
                        }
                    }
                }
            }
        }


        $link = null;
        $saldo = null;
        $data_nota = [];
        if ($metode !== "Hutang") {
            $no_nota = "KSR-" . no_nota($tgl, 'nota');
            $db = db('nota');
            foreach ($nota as $i) {

                $data_nota = [
                    'no_nota' => $no_nota,
                    'tgl' => $tgl,
                    'tabel' => $i['tabel'],
                    'barang_id' => $i['id'],
                    'barang' => $i['barang'],
                    'harga' => $i['harga'],
                    'qty' => $i['qty'],
                    'diskon' => $i['diskon'],
                    'total' => $i['total'],
                    'user_id' => $i['user_id'],
                    'pembeli' => $i['pembeli'],
                    'petugas' => $i['petugas'],
                    'metode' => $i['metode']
                ];

                $db->insert($data_nota);
            }
            $jwt = encode_jwt(['tabel' => 'nota', 'no_nota' => $no_nota]);
            $link = base_url('guest/nota/') . $jwt;


            if ($metode == "Tap") {
                $fun = new \App\Models\FunModel();
                $fulus = $fun->dekripsi_fulus($user, $user['fulus']);
                if ($fulus < $total2) {
                    gagal_js("Saldo kurang...");
                }
                $saldo = $fun->update_saldo($user, $total2, "Bayar", $lokasi, $petugas, "Kasir", $no_nota);
            }
            $hari_ini = time_hari_ini();
            $nota_hari_ini = $db->where('tgl >=', $hari_ini['start'])->where('tgl <=', $hari_ini['end'])->groupBy('no_nota')->orderBy('tgl', 'DESC')->get()->getResultArray();

            foreach ($nota_hari_ini as $i) {
                $i['jwt'] = base_url('guest/nota/') . encode_jwt(['tabel' => 'nota', 'no_nota' => $i['no_nota']]);
                $data_nota[] = $i;
            }
        }

        sukses_js("Sukses...", angka($uang_pembayaran - $total2), $link, $saldo, $data_nota);
    }


    public function hutang()
    {
        $pembeli = json_decode(json_encode($this->request->getVar('pembeli')), true);
        $order = clear($this->request->getVar('order'));
        $divisi = options('Divisi');

        $dbu = db('user');
        $dbu;
        if ($order !== "All") {
            $dbu->where('id', $pembeli['user_id']);
        }
        $users = $dbu->orderBy('nama', "ASC")->get()->getResultArray();

        $data = [];

        foreach ($users as $u) {
            $temp_data = [];
            foreach ($divisi as $d) {
                $db = db(strtolower($d['value']));
                $q = $db->where('user_id', $u['id'])->where('metode', "Hutang")->get()->getResultArray();

                if ($q) {
                    foreach ($q as $i) {
                        if ($d['value'] == "Ps" || $d['value'] == "Billiard") {
                            if ($i['metode'] == "Play" || $i['metode'] == "Over") {
                                continue;
                            }
                            $i['barang'] = $i['perangkat'];
                            $i['qty'] = $i['durasi'];
                        }
                        $temp_data[] = [
                            'id' => $i['id'],
                            'total' => $i['total'],
                            'kategori' => $d['value'],
                            'tgl' => $i['tgl'],
                            'barang' => $i['barang'],
                            'diskon' => $i['diskon'],
                            'qty' => $i['qty'],
                            'harga' => $i['harga']
                        ];
                    }
                }
            }

            $data[$u['id']] = $temp_data;
        }

        sukses_js("Sukses", $data);
    }

    public function transaksi_hari_ini()
    {
        $fun = new \App\Models\FunModel();
        $data = $fun->data_hari_ini();

        sukses_js("Sukses", $data, options('Divisi'));
    }
    public function cek_absen()
    {
        $jwt = clear($this->request->getVar('jwt'));
        $db = db('iot');
        $q = $db->where('Kategori', 'Absen')->where('divisi', 'Kantin')->where('status', 'Tap')->get()->getRowArray();
        $countdown = 0;
        if ($jwt == "" && $q !== null) {
            sukses_js("Sukses", $countdown);
        } elseif ($jwt !== "" && $q == null) {
            sukses_js("Sukses", $countdown);
        } else {
            if ($q) {
                $sisa = ((int)$q['end'] - (int)time());
                if ($sisa < 100) {
                    $countdown = $sisa;
                }
            }
            gagal_js("Gagal", $countdown);
        }
    }

    public function absen()
    {
        $jwt = clear($this->request->getVar('jwt'));
        $decode = decode_jwt($jwt);
        $fun = new \App\Models\IotModel();
        $fun->cek_absen_tap("Kantin", "Kantin 1", "Kasir");
    }
}
