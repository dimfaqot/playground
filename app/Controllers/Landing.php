<?php

namespace App\Controllers;

class Landing extends BaseController
{
    function __construct()
    {
        if (session('id')) {
            header("Location: " . base_url('home'));
            die;
        }
    }
    public function index($grup = "Billiard-1", $uid = "c3144c15"): string
    {
        dd(encode_jwt(['id' => 1]));
        // $db = db('billiard');
        // $q = $db->whereIn('perangkat', ['Meja 15', "Meja 24"])->get()->getResultArray();
        // foreach ($q as $i) {
        //     $i['perangkat'] = ($i['perangkat'] == "Meja 15" ? "Meja 6" : "Meja 7");
        //     $db->where('id', $i['id']);
        //     $db->update($i);
        // }
        $jwt = encode_jwt(['data' => $uid, 'data2' => str_replace("-", " ", $grup)]);
        return view('public/landing', ['judul' => "Landing", 'jwt' => $jwt]);
    }


    //https://playground.walisongosragen.com/auth/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MX0.r0JOc_nfhR-fZA-rBwH82fppMpqPh3tD6eMVAUWFpGU
    //http://localhost:8080/auth/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MX0.r0JOc_nfhR-fZA-rBwH82fppMpqPh3tD6eMVAUWFpGU
    public function auth($jwt)
    {
        $decode = decode_jwt($jwt);

        $db = db('user');

        $q = $db->where('id', $decode['id'])->where('status', 1)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(), "Id tidak ditemukan!.");
        }

        $data = [
            'id' => $decode['id']
        ];

        session()->set($data);
        sukses(base_url('home'), 'Login sukses.');
    }

    public function encode_jwt()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        sukses_js("Sukses", encode_jwt($data));
    }


    public function cek_user()
    {
        $hp = clear(upper_first($this->request->getVar('hp')));

        $db = db('user');
        $q = $db->where('hp', $hp)->get()->getRowArray();

        if (!$q) {
            gagal_js("No. whatsapp belum terdaftar!.");
        }
        if ($q['status'] == 0) {
            gagal_js("You are banned...");
        }

        sukses_js("Sukses.", $q);
    }

    public function tv()
    {
        $fun = new \App\Models\IotModel();

        return view('public/tv', ['judul' => "HAYU PLAYGROUND", 'data' => $fun->tv()]);
    }
    public function status_tv()
    {
        $fun = new \App\Models\IotModel();
        sukses_js("Sukses", $fun->tv($this->request->getVar('order')));
    }
}
