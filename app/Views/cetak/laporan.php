<?php helper('qr_code'); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $judul; ?></title>

    <style>
        table,
        td,
        th {
            border: 0px solid #033d62;
            padding: 0px;
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
        }

        td {
            padding: 0px;
        }

        table {
            border-collapse: separate;
            border-spacing: 0px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        div {
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>

</head>

<body>
    <table style="width:100%;">
        <tr>
            <td rowspan="4" style="width: 100px;"><?= $logo; ?></td>
            <td style="font-size:14px;">KEMANDIRIAN PONPES WALISONGO SRAGEN</td>
        </tr>
        <tr>
            <th style="text-align: left;font-size:16px">CAFE WALISONGO</th>
        </tr>

        <tr>
            <td style="font-size: 12px;border-bottom:3px solid black">Menyediakan: Makanan - Minuman - Snack</td>
        </tr>
        <tr>
            <td style="font-size: 10px;font-style:italic">Alamat: Sungkul - Plumbungan - Karangmalang - Sragen - 0989789898</td>
        </tr>
    </table>
    <h3 style="text-align: center;"><?= $judul; ?></h3>
    <h4>A. RANGKUMAN</h4>
    <h5><?= angka($total_keluar) . ' - ' . angka($total_masuk) . '= ' . angka($total_keluar - $total_masuk); ?></h5>
    <h4>B. PEMASUKAN</h4>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>
            <th style="border: 1px solid grey;padding:2px">Qty</th>
            <th style="border: 1px solid grey;padding:2px">Diskon</th>
            <th style="border: 1px solid grey;padding:2px">Jml</th>

        </tr>
        <?php foreach ($masuk as $k => $i): ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px"><?= $i['barang']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['harga']); ?></td>
                <td style="text-align:center; border: 1px solid grey;padding:4px"><?= angka($i['qty']); ?></td>
                <td style="text-align: right; border: 1px solid grey;padding:4px"><?= angka($i['diskon']); ?></td>
                <td style="text-align: right; border: 1px solid grey;padding:4px"><?= angka($i['total']); ?></td>
            </tr>

        <?php endforeach; ?>
        <tr>
            <th colspan="5" style="text-align:right;border: 1px solid grey;padding:4px">TOTAL</th>
            <th style="text-align:right;border: 1px solid grey;padding:4px"><?= angka($total_masuk); ?></th>
        </tr>
    </table>

    <h4 style="margin-top: 20px;">C. PENGELUARAN</h4>

    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Ket</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>
            <th style="border: 1px solid grey;padding:2px">Qty</th>
            <th style="border: 1px solid grey;padding:2px">Diskon</th>
            <th style="border: 1px solid grey;padding:2px">Jml</th>

        </tr>
        <?php foreach ($keluar as $k => $i): ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="text-align: center;border: 1px solid grey;padding:4px"><?= ($i['kategori'] == "" ? "-" : $i['kategori']); ?></td>
                <td style="border: 1px solid grey;padding:4px"><?= $i['barang']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['harga']); ?></td>
                <td style="text-align:center; border: 1px solid grey;padding:4px"><?= angka($i['qty']); ?></td>
                <td style="text-align: right; border: 1px solid grey;padding:4px"><?= angka($i['diskon']); ?></td>
                <td style="text-align: right; border: 1px solid grey;padding:4px"><?= angka($i['total']); ?></td>
            </tr>

        <?php endforeach; ?>
        <tr>
            <th colspan="6" style="text-align:right;border: 1px solid grey;padding:4px">TOTAL</th>
            <th style="text-align:right;border: 1px solid grey;padding:4px"><?= angka($total_keluar); ?></th>
        </tr>
    </table>

    <div style="text-align: right; font-size:small;margin-top:20px"><span style="font-size: 12px;"><?= date('d/m/Y'); ?></span> - <?= user()['nama']; ?></div>
    <div style="text-align: right;">
        <img width="100" src="<?= set_qr_code(base_url('guest/laporan/') . strtolower($bulan) . "/" . $tahun, 'logo', 'CAFE'); ?>" alt="<?= $judul; ?>">

    </div>



</body>

</html>