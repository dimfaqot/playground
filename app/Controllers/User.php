<?php

namespace App\Controllers;

use App\Models\FunModel;

class User extends BaseController
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
        // $db = db('billiard');
        // $q = $db->get()->getResultArray();

        // $total = 0;
        // foreach ($q as $i) {
        //     $total += (int)$i['total'];
        // }
        // dd($total);
        // $db = db('rental', 'new_ps');
        // $q = $db->get()->getResultArray();
        // dd(count($q));
        // $total = 0;
        // foreach ($q as $i) {
        //     $total += (int)$i['biaya'] - (int)$i['diskon'];
        // }
        // dd($total);

        // $db = db('hutang', 'new_ps');
        // $q = $db->get()->getResultArray();
        // $total = 0;
        // foreach ($q as $i) {
        //     if ($i['kategori'] == "Billiard" && $i['status'] == 0) {
        //         $total += (int)$i['total_harga'];
        //     }
        // }

        // dd($total);

        $db = db(menu()['tabel']);

        $q = $db->orderBy('role', 'ASC')->orderBy('nama', 'ASC')->get()->getResultArray();
        $data = [];

        foreach ($q as $i) {
            $i['link'] = base_url('auth/') . encode_jwt(['id' => $i['id']]);
            $data[] = $i;
        }

        return view(menu()['controller'], ['judul' => menu()['menu'], 'data' => $data]);
    }

    public function add()
    {
        $nama = clear(upper_first($this->request->getVar('nama')));
        $role = clear(upper_first($this->request->getVar('role')));
        $hp = clear(upper_first($this->request->getVar('hp')));

        if (user()['role'] == "Admin") {
            $role = "Member";
        }

        $dbx = \Config\Database::connect();
        $db = $dbx->table('user');
        if ($db->where('nama', $nama)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Nama sudah ada!.");
        }
        if ($db->where('hp', $hp)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Hp sudah ada!.");
        }

        $data = [
            'nama' => $nama,
            'hp' => $hp,
            'role' => $role,
            'status' => 1
        ];

        if ($db->insert($data)) {
            $new_id = $dbx->insertID();
            $q = $db->where('id', $new_id)->get()->getRowArray();
            if ($q) {
                $fun = new FunModel();
                $q['fulus'] = $fun->enkripsi_fulus($q, 0);
                $db->where('id', $q['id']);
                $db->update($q);
            }
            sukses(base_url(menu()['controller']), "Tambah data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Tambah data gagal!.");
        }
    }
    public function update()
    {
        $id = clear($this->request->getVar('id'));
        $nama = clear(upper_first($this->request->getVar('nama')));
        $role = clear(upper_first($this->request->getVar('role')));
        $hp = clear(upper_first($this->request->getVar('hp')));

        if (user()['role'] == "Admin") {
            $role = "Member";
        }

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        if ($db->whereNotIn('id', [$id])->where('nama', $nama)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Nama sudah ada!.");
        }
        if ($db->whereNotIn('id', [$id])->where('hp', $hp)->get()->getRowArray()) {
            gagal(base_url(menu()['controller']), "Hp sudah ada!.");
        }


        $q['nama'] = $nama;
        $q['role'] = $role;
        $q['hp'] = $hp;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), "Update data berhasil.");
        } else {
            gagal(base_url(menu()['controller']), "Update data gagal!.");
        }
    }
    public function topup()
    {

        if (user()['role'] !== "Root") {
            gagal_js("Not root!.");
        }
        $fun = new FunModel();

        $csrf = clear($this->request->getVar('csrf'));
        $latitude = clear($this->request->getVar('latitude'));
        $longitude = clear($this->request->getVar('longitude'));
        $topup = rp_to_int(clear($this->request->getVar('topup')));

        $lokasi = $latitude . "," . $longitude;

        $dec = $fun->dekripsi($csrf);
        $exp_dec = explode(",", $dec);

        if (count($exp_dec) !== 3) {
            gagal_js("Exp not 3!.");
        }

        if ($exp_dec[0] !== "Root") {
            gagal_js("Not root!.");
        }

        if (time() - (int)$exp_dec[1] > 15) {
            gagal_js("Time over!.");
        }

        $topup_options = options('Topup');

        $jml_topup = 0;
        foreach ($topup_options as $i) {
            if ($i['value'] == $topup) {
                $jml_topup = (int)$i['value'];
                break;
            }
        }

        if ($jml_topup == 0) {
            gagal_js("Illegal number!.");
        }



        $db = db(menu()['tabel']);
        $q = $db->where('id', $exp_dec[2])->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        $fun->topup($q, $jml_topup, "Topup", "Web", $lokasi);
    }
    public function update_hp()
    {


        $id = clear($this->request->getVar('id'));
        $value = clear($this->request->getVar('value'));



        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id tidak ditemukan!.");
        }

        if ($db->whereNotIn('id', [$id])->where('nama', $q['nama'])->get()->getRowArray()) {
            gagal_js("Nama sudah ada!.");
        }

        if ($db->whereNotIn('id', [$id])->where('hp', $value)->get()->getRowArray()) {
            gagal_js("Hp sudah ada!.");
        }

        $q['hp'] = $value;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses_js("Sukses...");
        } else {
            gagal_js("Gagal...");
        }
    }

    public function update_db()
    {
        $tabel = clear($this->request->getVar('tabel'));
        $fun = new FunModel();
        $db = db($tabel, "new_ps");
        $dbu = db('user');

        if ($tabel == "users") {
            $users_old = $db->orderBy('id', 'ASC')->get()->getResultArray();

            foreach ($users_old as $i) {
                $val = [
                    'nama' => $i['nama'],
                    'hp' => $i['hp'],
                    'role' => $i['role'],
                    'uid' => $i['uid'],
                    'fulus' => '',
                    'finger' => "",
                    'status' => 1
                ];
                $dbu->insert($val);
            }

            $new_user = $dbu->orderBy('id', 'ASC')->get()->getResultArray();
            foreach ($new_user as $i) {
                $i['fulus'] = $fun->enkripsi_fulus($i, 0);
                $dbu->where('id', $i['id']);
                $dbu->update($i);
            }
        } elseif ($tabel == "billiard_2") {
            $billiard_old = $db->orderBy('id', "ASC")->get()->getResultArray();
            $dbb = db('billiard');
            foreach ($billiard_old as $i) {
                $val = [
                    'tgl' => $i['tgl'],
                    'perangkat' => $i['meja'],
                    'dari' => $i['start'],
                    'ke' => $i['end'],
                    'harga' => $i['harga'],
                    'durasi' => $i['durasi'],
                    'diskon' => $i['diskon'],
                    'total' => $i['biaya'],
                    'status' => $i['is_active'],
                    'petugas' => $i['petugas'],
                    'pembeli' => $i['petugas'],
                    'user_id' => 0,
                    'metode' => $i['metode']
                ];

                $user = $dbu->where('nama', $i['petugas'])->get()->getRowArray();
                if ($user) {
                    $val['pembeli'] = $user['nama'];
                    $val['user_id'] = $user['id'];
                }
                $dbb->insert($val);
            }
        } elseif ($tabel == "billiard_2") {
            $billiard_old = $db->orderBy('id', "ASC")->get()->getResultArray();
            $dbp = db('billiard');
            $total = 0;
            $data = [];
            foreach ($billiard_old as $i) {
                $total += (int)$i['biaya'];
                $val = [
                    'tgl' => $i['tgl'],
                    'perangkat' => ($i['meja'] == "Meja 15" ? "Meja 6" : ($i['meja'] == "Meja 24" ? "Meja 7" : $i['meja'])),
                    'dari' => $i['start'],
                    'ke' => $i['end'],
                    'harga' => $i['harga'],
                    'durasi' => $i['durasi'],
                    'diskon' => $i['diskon'],
                    'total' => $i['biaya'],
                    'status' => $i['is_active'],
                    'petugas' => $i['petugas'],
                    'pembeli' => $i['petugas'],
                    'user_id' => 0,
                    'metode' => $i['metode']
                ];
                $data[] = $val;

                $user = $dbu->where('nama', $i['petugas'])->get()->getRowArray();
                if ($user) {
                    $val['pembeli'] = $user['nama'];
                    $val['user_id'] = $user['id'];
                }
            }
            foreach ($data as $i) {
                $dbp->insert($i);
            }
            dd($total);
        } elseif ($tabel == "rental") {
            $ps_old = $db->orderBy('id', "ASC")->get()->getResultArray();
            $dbp = db('ps');
            $total = 0;
            $data = [];
            foreach ($ps_old as $i) {
                $val = [
                    'tgl' => $i['tgl'],
                    'perangkat' => $i['meja'],
                    'dari' => $i['dari'],
                    'ke' => $i['ke'],
                    'harga' => $i['harga'],
                    'durasi' => $i['durasi'],
                    'diskon' => $i['diskon'],
                    'total' => (int)$i['biaya'] - (int)$i['diskon'],
                    'status' => $i['is_active'],
                    'petugas' => $i['petugas'],
                    'pembeli' => $i['petugas'],
                    'user_id' => 0,
                    'metode' => $i['metode']
                ];
                $data[] = $val;
                $total += (int)$val['total'];

                $user = $dbu->where('nama', $i['petugas'])->get()->getRowArray();
                if ($user) {
                    $val['pembeli'] = $user['nama'];
                    $val['user_id'] = $user['id'];
                }
                $dbp->insert($val);
            }
            // dd(count($data));
            dd($total);
        } elseif ($tabel == "kantin") {
            $kantin_old = $db->orderBy('id', "ASC")->get()->getResultArray();
            $dbp = db('kantin');
            foreach ($kantin_old as $i) {
                $exp = explode("/", $i['no_nota']);
                $no_nota = $exp[0] . $exp[1] .  $exp[2] . "-" . $exp[3];
                $val = [
                    'tgl' => $i['tgl'],
                    'no_nota' => $no_nota,
                    'barang' => $i['barang'],
                    'qty' => $i['qty'],
                    'harga' => $i['harga_satuan'],
                    'diskon' => $i['diskon'],
                    'total' => $i['total_harga'],
                    'petugas' => $i['petugas'],
                    'pembeli' => $i['petugas'],
                    'user_id' => 0,
                    'metode' => $i['metode'],
                    'lokasi' => ''
                ];

                $user = $dbu->where('nama', $i['petugas'])->get()->getRowArray();
                if ($user) {
                    $val['pembeli'] = $user['nama'];
                    $val['user_id'] = $user['id'];
                }
                $dbp->insert($val);
            }
        } elseif ($tabel == "barber") {
            $barber_old = $db->orderBy('id', "ASC")->get()->getResultArray();
            $dbp = db('barber');
            foreach ($barber_old as $k => $i) {
                $k += 1;
                $no_nota = "B" . date('dmY');
                if (strlen($k) == 1) {
                    $no_nota .= "-000" . $k;
                }
                if (strlen($k) == 2) {
                    $no_nota .= "-00" . $k;
                }
                if (strlen($k) == 3) {
                    $no_nota .= "-0" . $k;
                }
                if (strlen($k) == 4) {
                    $no_nota .= "-" . $k;
                }
                $val = [
                    'tgl' => $i['tgl'],
                    'no_nota' => $no_nota,
                    'barang' => $i['layanan'],
                    'qty' => $i['qty'],
                    'harga' => $i['harga'],
                    'diskon' => $i['diskon'],
                    'total' => $i['total_harga'],
                    'petugas' => $i['petugas'],
                    'pembeli' => $i['petugas'],
                    'user_id' => 0,
                    'metode' => $i['metode']
                ];

                $user = $dbu->where('nama', $i['petugas'])->get()->getRowArray();
                if ($user) {
                    $val['pembeli'] = $user['nama'];
                    $val['user_id'] = $user['id'];
                }

                $dbp->insert($val);
            }
        } elseif ($tabel == "barang") {
            $dbb = db('barang');
            $orders = ['barang', 'layanan'];
            foreach ($orders as $o) {
                $dbo = db($o, 'new_ps');
                $barang_old = $dbo->orderBy('id', 'ASC')->get()->getResultArray();

                foreach ($barang_old as $i) {
                    if ($o == 'layanan') {
                        $val = [
                            'tgl' => time(),
                            'kategori' => 'Barber',
                            'barang' => $i['layanan'],
                            'qty' => 0,
                            'harga' => $i['harga'],
                            'petugas' => 'Abdi',
                            'ket' => ''
                        ];
                    }
                    if ($o == 'barang') {
                        $val = [
                            'tgl' => time(),
                            'kategori' => 'Kantin',
                            'barang' => $i['barang'],
                            'qty' => $i['stok'],
                            'harga' => $i['harga_satuan'],
                            'petugas' => 'Abdi',
                            'ket' => ($i['jenis'] == "Cemilan" ? "Snack" : $i['jenis'])
                        ];
                    }
                    $dbb->insert($val);
                }
            }
        } elseif ($tabel == "pengeluaran") {
            $dbp = db('pengeluaran');
            $data = ['barber', 'billiard', 'kantin'];

            foreach ($data as $o) {
                $db = db('pengeluaran_' . $o, 'new_ps');
                $q = $db->orderBy('id', 'ASC')->get()->getResultArray();

                foreach ($q as $i) {
                    $val = [
                        'tgl' => $i['tgl'],
                        'kategori' => upper_first($o),
                        'penjual' => "",
                        'barang' => $i['barang'],
                        'qty' => $i['qty'],
                        'harga' => round($i['harga'] / $i['qty']),
                        'diskon' => 0,
                        'total' => $i['harga'],
                        'petugas' => $i['pj'],
                        'ket' => ($i['is_inv'] == 1 ? "Inv" : "Belanja")
                    ];
                    $dbp->insert($val);
                }
            }
            $db = db('inventaris', 'new_ps');

            $q = $db->where('jenis', "Pengeluaran")->orderBy('id', 'ASC')->get()->getResultArray();

            foreach ($q as $i) {
                $val = [
                    'tgl' => $i['tgl'],
                    'kategori' => "Ps",
                    'penjual' => "",
                    'barang' => $i['barang'],
                    'qty' => $i['qty'],
                    'harga' => round($i['harga'] / $i['qty']),
                    'diskon' => 0,
                    'total' => $i['harga'],
                    'petugas' => $i['pembeli'],
                    'ket' => "Belanja"
                ];

                $dbp->insert($val);
            }
        } elseif ($tabel == "hutang") {

            $q = $db->where('status', 0)->orderBy('id', 'ASC')->get()->getResultArray();

            $data = [];
            $k = 1;
            foreach ($q as $i) {
                $tabel = strtolower($i['kategori']);
                if ($i['kategori'] == "Billiard") {
                    $tabel = 'billiard_2';
                }
                $user = $dbu->where('id', $i['user_id'])->get()->getRowArray();
                if (!$user) {
                    if ($i['nama'] == "Abi Liwa") {
                        $usr = $dbu->where('nama', 'Sofyan')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Gus tawa") {
                        $usr = $dbu->where('nama', 'Gustawa')->where('role', "Gus")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Nofal Alumni") {
                        $usr = $dbu->where('nama', 'Nofal Alumni')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Khadafi") {
                        $usr = $dbu->where('nama', 'Khadafi')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Pak ulil") {
                        $usr = $dbu->where('nama', 'Ulil Jyb')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Tahfidz") {
                        $usr = $dbu->where('nama', 'Tahfidz')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Dewan putri") {
                        $usr = $dbu->where('nama', 'Dewan Putri')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Ardika") {
                        $usr = $dbu->where('nama', 'Ardika Ack')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Kukun") {
                        $usr = $dbu->where('nama', 'Kukun')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Dewa") {
                        $usr = $dbu->where('nama', 'Dewa')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Reza ansory") {
                        $usr = $dbu->where('nama', 'Reza Ansory')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Gus aan" || $i['nama'] == "Gus  aan") {
                        $usr = $dbu->where('nama', 'Gus Aan')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Ridho") {
                        $usr = $dbu->where('nama', 'Ridho')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Wisnu") {
                        $usr = $dbu->where('nama', 'Wisnu')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Husain") {
                        $usr = $dbu->where('nama', 'Husain Timnas')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($i['nama'] == "Ekstra") {
                        $usr = $dbu->where('nama', 'Ekstra')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    }

                    $usr = $dbu->where('nama', $i['nama'])->get()->getRowArray();

                    $i['nama'] = $usr['nama'];
                    $i['user_id'] = $usr['id'];

                    if ($i['kategori'] == "Billiard") {
                        $exp = explode(" ", $i['barang']);
                        $val = [
                            'tgl' => $i['tgl'],
                            'perangkat' => $exp[1] . " " . $exp[2],
                            'dari' => $i['tgl'],
                            'ke' => (int)$i['tgl'] + ((int)$i['qty'] * 60),
                            'harga' => $i['harga_satuan'],
                            'durasi' => $i['qty'],
                            'diskon' => 0,
                            'total' => $i['total_harga'],
                            'status' => 0,
                            'petugas' => $i['teller'],
                            'pembeli' => $i['nama'],
                            'user_id' => $i['user_id'],
                            'metode' => 'Hutang'
                        ];
                        $data[$i['kategori']][] = $val;
                    } else {
                        $k++;
                        $no_nota = upper_first(substr($i['kategori'], 0, 1)) . date('dmY');
                        if (strlen($k) == 1) {
                            $no_nota .= "-000" . $k;
                        }
                        if (strlen($k) == 2) {
                            $no_nota .= "-00" . $k;
                        }
                        if (strlen($k) == 3) {
                            $no_nota .= "-0" . $k;
                        }
                        if (strlen($k) == 4) {
                            $no_nota .= "-" . $k;
                        }
                        $val = [
                            'tgl' => $i['tgl'],
                            'no_nota' => $no_nota,
                            'barang' => $i['barang'],
                            'qty' => $i['qty'],
                            'harga' => $i['harga_satuan'],
                            'diskon' => 0,
                            'total' => $i['total_harga'],
                            'petugas' => $i['teller'],
                            'pembeli' => $i['nama'],
                            'user_id' => $i['user_id'],
                            'metode' => "Hutang"
                        ];
                        $data[$i['kategori']][] = $val;
                    }
                    //  else {
                    //     $data['kosong'][] = $i;
                    // }
                } else {
                    if ($i['nama'] == "Husain") {
                        $usr = $dbu->where('nama', 'Husain Timnas')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    }
                    if ($user['nama'] == "Gus Tawa") {
                        $usr = $dbu->where('nama', 'Gustawa')->where('role', "Gus")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($user['nama'] == "Husain") {
                        $usr = $dbu->where('nama', 'Husain Timnas')->where('role', "Member")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($user['nama'] == "Mbahdim") {
                        $usr = $dbu->where('nama', 'Dimyati')->where('role', "Root")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($user['nama'] == "Abdi" && $user['role'] == "Member") {
                        $usr = $dbu->where('nama', 'Abdi')->where('role', "Admin Bill")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($user['nama'] == "Akbar kantin" && $user['role'] == "Member") {
                        $usr = $dbu->where('nama', 'Akbar')->where('role', "Admin Kant")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($user['nama'] == "Aziz kantin" && $user['role'] == "Member") {
                        $usr = $dbu->where('nama', 'Aziz')->where('role', "Admin Kant")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    } elseif ($user['nama'] == "Adi kantin" && $user['role'] == "Member") {
                        $usr = $dbu->where('nama', 'Adi Prabowo')->where('role', "Admin Kant")->get()->getRowArray();
                        $i['nama'] = $usr['nama'];
                        $i['user_id'] = $usr['id'];
                    }

                    $usr = $dbu->where('nama', $i['nama'])->get()->getRowArray();

                    $i['nama'] = $usr['nama'];
                    $i['user_id'] = $usr['id'];

                    if ($i['kategori'] == "Billiard") {
                        $exp = explode(" ", $i['barang']);
                        $val = [
                            'tgl' => $i['tgl'],
                            'perangkat' => $exp[1] . " " . $exp[2],
                            'dari' => $i['tgl'],
                            'ke' => (int)$i['tgl'] + ((int)$i['qty'] * 60),
                            'harga' => $i['harga_satuan'],
                            'durasi' => $i['qty'],
                            'diskon' => 0,
                            'total' => $i['total_harga'],
                            'status' => 0,
                            'petugas' => $i['teller'],
                            'pembeli' => $i['nama'],
                            'user_id' => $i['user_id'],
                            'metode' => 'Hutang'
                        ];
                        $data[$i['kategori']][] = $val;
                    } else {
                        $k++;
                        $no_nota = upper_first(substr($i['kategori'], 0, 1)) . date('dmY');
                        if (strlen($k) == 1) {
                            $no_nota .= "-000" . $k;
                        }
                        if (strlen($k) == 2) {
                            $no_nota .= "-00" . $k;
                        }
                        if (strlen($k) == 3) {
                            $no_nota .= "-0" . $k;
                        }
                        if (strlen($k) == 4) {
                            $no_nota .= "-" . $k;
                        }
                        $val = [
                            'tgl' => $i['tgl'],
                            'no_nota' => $no_nota,
                            'barang' => $i['barang'],
                            'qty' => $i['qty'],
                            'harga' => $i['harga_satuan'],
                            'diskon' => 0,
                            'total' => $i['total_harga'],
                            'petugas' => $i['teller'],
                            'pembeli' => $i['nama'],
                            'user_id' => $i['user_id'],
                            'metode' => "Hutang"
                        ];
                        $data[$i['kategori']][] = $val;
                    }
                }
            }

            // if (count($data['kosong']) > 0) {
            //     $dbx = \Config\Database::connect();
            //     $db = $dbx->table('user');
            //     $user_inserted = [];
            //     foreach ($data['kosong'] as $i) {
            //         if (!in_array($i['nama'], $user_inserted)) {
            //             if ($i['nama'] == "Pak ulil") {
            //                 continue;
            //             }
            //             $val = [
            //                 'nama' => $i['nama'],
            //                 'hp' => '',
            //                 'role' => "Member"
            //             ];

            //             if ($db->insert($val)) {
            //                 $new_id = $dbx->insertID();
            //                 $q = $db->where('id', $new_id)->get()->getRowArray();
            //                 if ($q) {
            //                     $fun = new FunModel();
            //                     $q['fulus'] = $fun->enkripsi_fulus($q, 0);
            //                     $db->where('id', $q['id']);
            //                     $db->update($q);
            //                     $user_inserted[] = $i['nama'];
            //                 }
            //             }
            //         }
            //     }
            // }

            // $loop = ['Kantin', 'Billiard'];
            // $total = 0;
            // foreach ($loop as $l) {
            //     $db = db(strtolower($l));
            //     foreach ($data[$l] as $i) {
            //         $total += (int)$i['total'];
            //         $db->insert($i);
            //     }
            // }

            // dd($total);
        } elseif ($tabel == "koperasi") {
            $koperasi_old = $db->orderBy('id')->get()->getResultArray();

            $db_kop = db('koperasi');
            foreach ($koperasi_old as $i) {

                $data = [
                    'tgl' => $i['tgl'],
                    'kategori' => $i['usaha'],
                    'uang' => $i['tabungan'],
                    'pj' => "Mbachdim"
                ];

                foreach (options("Saham") as $v) {
                    $data[strtolower($v['value'])] = ((int)settings($v['value']) / 100) * (int)$data['uang'];
                }

                $db_kop->insert($data);
            }
        }
    }
}
