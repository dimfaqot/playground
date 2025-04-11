<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>


<?php if (count($data) == 0): ?>
    <div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>
<?php else: ?>
    <div class="input-group input-group-sm mb-3">
        <span class="input-group-text bg_main border_main text_main">Cari Data</span>
        <input type="text" class="form-control cari bg_main border border_main text_main" placeholder="....">
    </div>
    <table class="table table-sm table-bordered bg_main text_main" style="font-size: 12px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">No. Nota</th>
                <th class="text-center">Pembeli</th>
                <th class="text-center">Lokasi</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <td class="text-center"><?= $k + 1; ?></td>
                    <td class="text-center">
                        <a style="text-decoration: none;" data-no_nota="<?= $i['profile']['no_nota']; ?>" data-metode="<?= $i['profile']['metode']; ?>" class="d-grid btn_detail text_main bg_secondary"><?= $i['profile']['no_nota']; ?>
                        </a>
                    </td>
                    <td><?= $i['profile']['pembeli']; ?></td>
                    <td><?= $i['profile']['lokasi']; ?></td>
                    <td class="text-center"><?= $i['profile']['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="body_detail mt-3"></div>

<?php endif; ?>

<script>
    let data = <?= json_encode($data); ?>;

    $(document).on('click', '.status', function(e) {
        e.preventDefault();
        let status = $(this).data("status");
        if (status == "Kamu Hutang...huks") {
            message("400", status);
        } else {
            message("200", status);
        }
    });

    $(document).on("click", ".btn_detail", function(e) {
        let no_nota = $(this).data("no_nota");
        let metode = $(this).data("metode");
        let val = [];
        data.forEach(e => {
            if (e.profile.no_nota == no_nota) {
                val = e.data;
            }
        });

        let html = `<div class="mt-3 bg_secondary py-1 px-2" style="font-size: 11px;">DAFTAR PESANAN</div>
                    <table class="table table-sm bg_main text_main table-bordered" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Barang</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="data_pesanan">`;
        let total = 0;
        val.forEach((e, i) => {
            total += parseInt(e.total);
            html += `<tr>
                                <td class="text-center">${(i+1)}</td>
                                <td>${e.barang}</td>
                                <td class="text-center">${angka(e.qty)}</td>
                                <td class="text-end">${angka(e.total)}</td>
                            </tr>`;
        })


        html += `<tr>
                            <th class="text-center" colspan="3">TOTAL</th>
                            <th class="text-end">${angka(total)}</th>
                        </tr>
                        </tbody>
                    </table>`;

        html += `<div class="d-grid">
                    <button class="btn btn-sm btn_transaksi link_main" data-no_nota="${no_nota}" data-metode="${metode}">${(metode=="Barcode"?"Proses":"Masukkan Hutang")}</button>
                </div>`;

        $(".body_detail").html(html);
    })

    $(document).on("click", ".btn_transaksi", function(e) {
        e.preventDefault();
        let no_nota = $(this).data("no_nota");
        let metode = $(this).data("metode");

        post("cafe/update_metode", {
            no_nota,
            metode: (metode == "Barcode" ? "Proses" : "Hutang")
        }).then(res => {
            if (res.status == "200") {
                message(res.status, res.message);
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                message(res.status, res.message);
            }
        })
    })
</script>
<?= $this->endSection() ?>