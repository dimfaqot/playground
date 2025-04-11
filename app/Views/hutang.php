<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>
<h6><i class="<?= menu()['icon']; ?>"></i> <?= strtoupper(menu()['menu']); ?></h6>

<div class="mb-2 d-flex gap-2">
    <?php if (user()['role'] == "Root"): ?>
        <select class="form-select form-select-sm filter divisi" style="font-size: small;">
            <?php foreach (options('Divisi') as $i) : ?>
                <option value="<?= $i['value']; ?>" <?= ($kategori == $i['value'] ? "selected" : ""); ?>><?= $i['value']; ?></option>
            <?php endforeach; ?>
        </select>
    <?php else: ?>
        <?= user()['role']; ?>
    <?php endif; ?>

    <div class="form-check form-switch">
        <input class="form-check-input btn_all_kategori" type="checkbox" role="switch">
        <label class="form-check-label">All</label>
    </div>
</div>

<?php if (count($data) == 0): ?>
    <div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>
<?php else: ?>
    <div class="input-group input-group-sm mb-3">
        <span class="input-group-text bg_main border_main text_main">Cari Data</span>
        <input type="text" class="form-control cari bg_main border border_main text_main" placeholder="....">
    </div>
    <table class="table table-sm bg_main text_main border_main table-bordered" style="font-size: 14px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Hp</th>
                <th class="text-center">Act</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <td class="<?= ($i['metode'] == "Hutang" ? "bg-danger bg-opacity-25" : ""); ?> text-center"><?= ($k + 1); ?></td>
                    <td class="<?= ($i['metode'] == "Hutang" ? "bg-danger bg-opacity-25" : ""); ?>"><?= $i['nama']; ?></td>
                    <td class="<?= ($i['metode'] == "Hutang" ? "bg-danger bg-opacity-25" : ""); ?> text-center"><?= $i['hp']; ?></td>
                    <td class=" <?= ($i['metode'] == "Hutang" ? "bg-danger bg-opacity-25" : ""); ?> text-center"><a data-user_id="<?= $i['id']; ?>" data-tabel="<?= strtolower($kategori); ?>" data-order="<?= ($i['metode'] == "Hutang" ? "Hutang" : "Lunas"); ?>" href="" class="text_main btn_detail"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    $(document).on('change', '.filter', function(e) {
        e.preventDefault();
        let divisi = $(".divisi").val();
        window.location.href = '<?= base_url(menu()['controller']); ?>/' + divisi;
    });

    let data_transaksi = [];
    $(document).on('click', '.btn_detail', function(e) {
        e.preventDefault();


        let user_id = $(this).data("user_id");
        let tabel = $(this).data("tabel");
        let order = $(this).data("order");
        let kategori = "<?= $kategori; ?>";

        if ($('.btn_all_kategori').is(':checked')) {
            kategori = "All";
        }

        post("hutang/detail", {
            user_id,
            tabel,
            kategori,
            order
        }).then(res => {
            data_transaksi = res.data;

            if (res.status == "200") {
                let html = "";
                html += '<div class="container">';
                html += '<h6 style="font-size: small;" class="bg_secondary rounded p-2">' + res.data[0].nama + " " + angka(res.data2) + '</h6>';
                html += `<table class="table table-sm bg_main text_main border_main table-bordered" style="font-size: 12px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Tgl</th>
                <th class="text-center">Kategori</th>
                <th class="text-center">Barang</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Harga</th>
            </tr>
        </thead>
        <tbody class="tabel_search">`;
                res.data.forEach((e, i) => {
                    html += `<tr>
                        <td class="text-center ${(e.metode=="Hutang"?"bg-opacity-25 bg-danger":"")}">${(i+1)}</td>
                       <td class="text-center ${(e.metode=="Hutang"?"bg-opacity-25 bg-danger":"")}">${time_php_to_js(e.tgl)}</td>
                       <td class="${(e.metode=="Hutang"?"bg-opacity-25 bg-danger":"")}">${e.kategori}</td>
                       <td class="${(e.metode=="Hutang"?"bg-opacity-25 bg-danger":"")}">${e.barang}</td>
                       <td class="text-center ${(e.metode=="Hutang"?"bg-opacity-25 bg-danger":"")}">${angka(e.qty)}</td>
                       <td class="text-end ${(e.metode=="Hutang"?"bg-opacity-25 bg-danger":"")}">${angka(e.harga)}</td>
                    </tr>`;

                });
                html += `</tbody>
    </table>`;
                if (order == "Hutang") {
                    html += `<div class="d-grid">
                                <a href="" class="btn btn-sm bg_secondary text_main btn_bayar mb-2"><i class="fa-solid fa-cash-register"></i> Bayar</a>
                                <a target="_blank" href="${res.data3}" class="btn btn-sm btn-success btn_whatsapp" data-jwt="${res.data3}" data-hp="${res.data[0].hp}" data-nama="${res.data[0].nama}"><i class="fa-brands fa-whatsapp"></i> Whatsapp</a>
                                </div>
                        </div>`;

                    html += '</div>'

                }


                popupButton.html(html);

            } else {
                message("400", res.message);
            }
        })
    });

    $(document).on('click', '.btn_whatsapp', function(e) {
        e.preventDefault();
        let nama = $(this).data('nama');
        let jwt = $(this).data('jwt');
        let no_hp = "62";
        no_hp += $(this).data('hp').substring(1);

        let text = "_Assalamualaikum Wr. Wb._%0a";
        text += "Yth. *" + nama + '*%0a%0a';
        text += 'Tagihan Anda di Hayu Playground:%0a%0a';
        text += '*No. -- Tgl -- Kategori -- Barang -- Qty -- Harga*%0a'

        let x = 1;
        let total = 0;
        data_transaksi.forEach((e, i) => {
            total += e.total;
            text += (x++) + '. ' + time_php_to_js(e.tgl) + ' - ' + e.kategori + ' - ' + e.barang + ' - ' + e.qty + ' - ' + angka(e.harga) + '%0a';

        })
        text += '%0a';
        text += "*TOTAL: " + angka(total) + "*%0a%0a";
        text += "*_Mohon segera dibayar njihhh..._*%0a";
        text += "_Wassalamualaikum Wr. Wb._%0a%0a";
        text += 'Petugas%0a%0a';
        text += '<?= user()['nama']; ?>';
        text += "%0a%0a";
        text += "_(*)Pesan ini dikirim oleh sistem, jadi mohon maklum dan ampun tersinggung njih._";
        text += "%0a%0a";
        text += "Info lebih lengkap klik: %0a%0a";
        text += jwt;


        // let url = "https://api.whatsapp.com/send/?phone=" + no_hp + "&text=" + text;
        let url = "whatsapp://send/?phone=" + no_hp + "&text=" + text;

        location.href = url;
        // window.open(url);
    });

    $(document).on('click', '.btn_bayar', function(e) {
        e.preventDefault();
        let myModal = document.getElementById("fullscreen");
        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        modal.hide();

        let total = 0;
        data_transaksi.forEach(e => {
            total += parseInt(e.harga);
        })

        let html = "";

        html += `<div class="container">

                <div class="d-flex gap-2 mb-2">
                    <div style="font-size:small;" class="border_main rounded px-2 py-1">${data_transaksi[0].nama}</div>
                    <div style="font-size:small;" class="border_main bg_secondary rounded px-2 py-1">${angka(total)}</div>
                </div>
                
                <div class="text-center mb-3">
                    <span class="text_main" style="font-size: 12px;">Uang Pembayaran</span>
                    <input type="text" class="mt-2 form-control form-control-sm uang_lunas text-center angka" value="${angka(total)}" placeholder="Uang Lunas">
                </div>
                <div class="mt-2 text-center" style="font-size:12px">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" name="metode_bayar" type="radio" value="Cash" checked>
                        <label class="form-check-label">Cash</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" name="metode_bayar" type="radio" value="Tap">
                        <label class="form-check-label">Tap</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" name="metode_bayar" type="radio" value="Qris">
                        <label class="form-check-label">Qris</label>
                    </div>
                </div>
                <div class="d-grid mb-1">
                    <button class="btn btn-sm btn-success btn_lunas" data-total="${total}"><i class="fa-solid fa-wallet"></i> LUNASI</button>
                </div>`;

        html += `<table class="table table-sm bg_main text_main table-bordered" style="font-size: 14px;">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Tgl</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Barang</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Harga</th>
                    </tr>
                </thead>
                <tbody class="tabel_search">`;

        data_transaksi.forEach((e, i) => {
            html += `<tr>
                            <td class="text-center">${(i+1)}</td>
                            <td class="text-center">${time_php_to_js(e.tgl)}</td>
                            <td>${e.kategori}</td>
                            <td>${e.barang}</td>
                            <td class="text-center">${angka(e.qty)}</td>
                            <td class="text-end">${angka(e.harga)}</td>
                    </tr>`;

        });
        html += '<tr><th colspan="5" class="text-center">TOTAL</th><th class="text-end">' + angka(total) + '</th></tr>';



        html += `</tbody>
            </table>`;

        html += '</div>';

        popupButton.html(html);

        setTimeout(() => {
            $(".uang_lunas").focus();
        }, 500);
    });

    $(document).on('click', '.btn_lunas', function(e) {
        e.preventDefault();
        let total = parseInt($(this).data("total"));
        let uang_lunas = parseInt(str_replace(".", "", $(".uang_lunas").val()));
        let metode = $('input[name="metode_bayar"]:checked').val();

        if (uang_lunas < total) {
            message("400", "Uang kurang!.");
            $(".uang_lunas").val(angka(total));
            return;
        }

        if (metode == "Tap") {
            metode_tap(data_transaksi, "Hutang");
            return;
        }

        post("hutang/lunas", {
            data: data_transaksi,
            uang_lunas,
            total,
            metode
        }).then(res => {
            if (res.status == "200") {
                let myModal = document.getElementById("fullscreen");
                let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.hide();

                let html = "";
                html += `<div class="container border border-light rounded p-2">
                                <div class="text-center mb-3">
                                    <span class="text_main" style="font-size: small;">UANG KEMBALIAN</span>
                                    <div class="fw-bold total_pembayaran">${angka(res.data)}</div>
                                </div>
                                <hr>`;
                if (res.data2.length > 0) {
                    html += '<div class="bg-opacity-25 bg-danger border border-danger mb-2" px-5 pb-1 rounded text-center" style="font-size: medium;">GAGAL: ' + res.data + '</div>';
                }

                popupButton.html(html);

                setTimeout(() => {
                    $('#fullscreen').on('hidden.bs.modal', function() {
                        location.reload();
                    });
                }, 300);
            } else {
                message("400", res.message);
            }
        })
    });
</script>
<?= $this->endSection() ?>