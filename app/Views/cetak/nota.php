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
            <td rowspan="6" style="width: 55px;"><?= $logo; ?></td>
            <td style="font-size:14px;">HAYU <b>PLAYGROUND</b></td>
        </tr>

        <tr>
            <td style="font-size: 12px;border-bottom:3px solid black">Billiard - Ps - Barber - Cafe</td>
        </tr>
        <tr>
            <td style="font-size: 10px;font-style:italic">Alamat: Sungkul - Plumbungan - Karangmalang - Sragen - 0989789898</td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td></td>
        </tr>
    </table>

    <table style="margin-top: 20px;width:100%">
        <tr>
            <td style="width: 80px;padding:2px">Tgl</td>
            <td style="width: 10px;padding:2px">: </td>
            <td style="padding: 2px;"><?= $tgl; ?></td>
            <td style="width: 100px;padding:2px"></td>
            <td style="width: 80px;padding:2px">Pembeli</td>
            <td style="width: 10px;padding:2px">: </td>
            <td style="padding: 2px;"><?= $pembeli; ?></td>

        </tr>
        <tr>
            <td style="width: 80px;padding:2px">No. Nota</td>
            <td style="width: 10px;padding:2px">: </td>
            <td style="padding: 2px;"><?= $no_nota; ?></td>
            <td style="width: 100px;padding:2px"></td>
            <td style="width: 80px;padding:2px">teller</td>
            <td style="width: 10px;padding:2px">: </td>
            <td style="padding: 2px;"><?= $teller; ?></td>
        </tr>
    </table>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px"><?= ($tabel == "billiard" || $tabel == "ps" ? "Meja" : "Barang"); ?></th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>
            <th style="border: 1px solid grey;padding:2px"><?= ($tabel == "billiard" || $tabel == "ps" ? "Durasi" : "Qty"); ?></th>
            <th style="border: 1px solid grey;padding:2px">Diskon</th>
            <th style="border: 1px solid grey;padding:2px">Biaya</th>

        </tr>
        <?php $total = 0; ?>
        <?php foreach ($data as $k => $i): ?>
            <?php $total += $i['total']; ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px"><?= ($tabel == "billiard" || $tabel == "ps" ? $i['perangkat'] : $i['barang']); ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['harga']); ?></td>
                <td style="text-align:center; border: 1px solid grey;padding:4px"><?= ($tabel == "billiard" || $tabel == "ps" ? angka($i['durasi']) : angka($i['qty'])); ?></td>
                <td style="text-align: right; border: 1px solid grey;padding:4px"><?= angka($i['diskon']); ?></td>
                <td style="text-align: right; border: 1px solid grey;padding:4px"><?= angka($i['total']); ?></td>
            </tr>

        <?php endforeach; ?>
        <tr>
            <th colspan="5" style="text-align:right;border: 1px solid grey;padding:4px">TOTAL</th>
            <th style="text-align:right;border: 1px solid grey;padding:4px"><?= angka($total); ?></th>
        </tr>
    </table>

    <div style="text-align: right; font-size:small;margin-top:20px"><span style="font-size: 12px;"><?= date('d/m/Y'); ?></span> - <?= $teller; ?></div>
    <div style="text-align: right;">
        <img width="100" src="<?= set_qr_code($jwt, 'logo', 'PLAYGROUND'); ?>" alt="NOTA <?= $no_nota; ?>">

    </div>



</body>

</html>