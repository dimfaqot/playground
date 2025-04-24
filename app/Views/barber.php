<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>
<div class="sticky-top bg_main">
    <h6 style="font-size: small;" class="bg_secondary rounded pt-2 d-flex gap-3 px-3">
        <div>
            <i class="<?= menu()['icon']; ?>"></i> <?= angka($total) . " - " . angka($hutang) . " = " . angka($total - $hutang); ?>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input show_hide" type="checkbox" checked>
            <label class="form-check-label">Show/hide</label>
        </div>
    </h6>

    <div class="main_content">
        <div class="d-flex gap-2">
            <div style="font-size:small;" class="border_main rounded px-2 py-1">No. Nota: <?= $no_nota; ?></div>
            <div style="font-size:small;" class="total border_main bg_secondary rounded px-2 py-1">0</div>
        </div>

        <div class="mb-2 position-relative">
            <label style="font-size: 12px;">Barang</label>
            <input type="text" class="form-control form-control-sm cari_barang add_barang" placeholder="Barang">
            <div class="data_list data_barang" style="font-size: small;">

            </div>
        </div>
        <div class="mb-2">
            <label style="font-size: 12px;">Qty</label>
            <input type="text" class="form-control form-control-sm angka add_qty" value="0" placeholder="Qty">
        </div>
        <div class="mb-2">
            <label style="font-size: 12px;">Diskon</label>
            <input type="text" class="form-control form-control-sm angka add_diskon" value="0" placeholder="Diskon">
        </div>
        <div class="mb-2">
            <label style="font-size: 12px;">Harga</label>
            <input type="text" class="form-control form-control-sm add_total" value="0" placeholder="Harga" readonly>
        </div>
        <div class="d-grid">
            <button class="btn btn-sm link_secondary btn_data_transaksi"><i class="fa-solid fa-square-up-right"></i> Add</button>
        </div>

        <div class="data_transaksi mt-2"></div>
    </div>
</div>

<?php if (count($data) == 0): ?>
    <div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>
<?php else: ?>
    <div class="input-group input-group-sm mb-3">
        <span class="input-group-text bg_main border_main text_main">Cari Data</span>
        <input type="text" class="form-control cari bg_main border border_main text_main" placeholder="....">
    </div>
    <table class="table table-sm bg_main text_main border_main table-bordered" style="font-size: 12px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Tgl</th>
                <th class="text-center">Barang</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Act</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <?php if ($i['metode'] == "Hutang"): ?>
                        <td class="bg-danger bg-opacity-10 text-center"><?= $k + 1; ?></td>
                    <?php else: ?>
                        <td class="text-center"><a style="text-decoration: none;" class="text_main" target="_blank" href="<?= base_url("guest/nota/") . encode_jwt(['tabel' => menu()['tabel'], 'no_nota' => $i['no_nota']]); ?>"><?= $k + 1; ?></a></td>
                    <?php endif; ?>
                    <td class="<?= ($i['metode'] == "Hutang" ? "bg-danger bg-opacity-10" : ""); ?> text-center"><?= date("d", $i['tgl']); ?></td>
                    <td class="<?= ($i['metode'] == "Hutang" ? "bg-danger bg-opacity-10" : ""); ?>"><?= $i['barang']; ?></td>
                    <td class="<?= ($i['metode'] == "Hutang" ? "bg-danger bg-opacity-10" : ""); ?> text-end"><?= angka($i['total']); ?></td>
                    <td class=" <?= ($i['metode'] == "Hutang" ? "bg-danger bg-opacity-10" : ""); ?> text-center"><a data-no_nota="<?= $i['no_nota']; ?>" data-id="<?= $i['id']; ?>" href="" class="text_main btn_detail"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    $('input[placeholder="Barang"]').focus();
    let data_transaksi = [];

    let data = <?= json_encode($data); ?>;
    let data_selected = {};
    let no_nota = "<?= $no_nota; ?>";

    $(document).on('change', '.show_hide', function(e) {
        e.preventDefault();
        if ($(".main_content").css('display') === 'none') {
            $(".main_content").show();
            $(".cari_barang").focus();
        } else {
            $(".main_content").hide();
        }

    });



    $(document).on("click", ".btn_confirm", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        popup_confirm.confirm("btn_confirm_" + id);
    })
    $(document).on("click", ".btn_delete", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let tabel = $(this).data("tabel");

        post("home/delete", {
            tabel,
            id
        }).then(res => {
            if (res.status == "200") {
                message(res.status, res.message);
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                message("400", res.message);
            }
        })
    })

    $(document).on('keyup', '.cari_barang', function(e) {
        e.preventDefault();
        let value = $(this).val();

        post("barber/cari_barang", {
            value,
            kategori: "<?= $kategori; ?>"
        }).then(res => {
            if (res.status == "200") {
                let html = '';

                if (res.data.length == 0) {
                    html += '<div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>';
                } else {
                    res.data.forEach(e => {
                        html += '<div class="select_barang" data-barang="' + e.barang + '" data-id="' + e.id + '" data-qty="' + e.qty + '" data-harga="' + e.harga + '">' + e.barang + '</div>';
                    })

                }
                $(".data_barang").html(html);

            } else {
                popup_confirm.message(res.status, res.message);
            }
        })


    });

    $(document).on('click', '.select_barang', function(e) {
        e.preventDefault();
        let barang = $(this).data("barang");
        let id = $(this).data("id");
        let qty = parseInt($(this).data("qty"));
        let harga = parseInt($(this).data("harga"));
        // data_selected['id'] = id;
        data_selected['id'] = id;
        data_selected['barang'] = barang;
        data_selected['qty'] = qty;
        data_selected['harga'] = harga;

        $(".cari_barang").val(barang);
        $(".data_barang").html("");
        $(".add_total").val(angka(harga));
        $(".add_qty").val(1);
        $('input[placeholder="Qty"]').focus();

    });

    $(document).on('keyup', '.add_diskon', function(e) {
        e.preventDefault();
        if (data_selected.barang == undefined) {
            message("400", "Barang kosong!.");
            $(this).val(0);
            return;
        }

        let diskon = 0;
        if ($(".add_diskon").val() !== "") {
            diskon = parseInt(str_replace(".", "", $(this).val()))
        }

        let qty = 1;
        if ($(".add_qty").val() !== "") {
            qty = parseInt(str_replace(".", "", $(".add_qty").val()));
        }

        let total = (parseInt(data_selected.harga) * qty) - diskon;

        if (diskon > total) {
            message("400", "Diskon gagal!.");
            $(this).val(angka(data_selected.harga));
            return;
        }

        $(".add_total").val(angka(total));
    });


    const tabel = (cls = "btn_daftar_transaksi", order) => {
        let total = 0;
        let html = '';
        // html += `<div class="container">`;
        if (cls == "btn_transaksi") {
            html += `<div class="mt-2 text-center" style="font-size:12px">
                            <div class="form-check form-check-inline hutang">
                                <input class="form-check-input" name="metode_bayar" type="radio" value="Hutang" checked>
                                <label class="form-check-label">Hutang</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="metode_bayar" type="radio" value="Cash">
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
                        </div>`;
        }

        html += `<div class="d-grid">`;
        if (cls == "btn_daftar_transaksi") {
            html += '<button class="btn btn-sm link_main ' + cls + '"><i class="fa-solid fa-cash-register"></i> BAYAR</button>';
        }
        if (cls == "btn_transaksi") {
            html += '<button data-order="' + (order == undefined ? "Bayar" : "Hutang") + '" class="btn btn-sm link_main ' + cls + '"><i class="fa-solid fa-cash-register"></i> TRANSAKSI</button>';
        }

        html += `</div>`;

        if (cls == "btn_lunas") {
            html += `<div class="d-flex gap-2 mb-2">
            <div style="font-size:small;" class="border_main rounded px-2 py-1">${data_transaksi[0].pembeli}</div>
            <div style="font-size:small;" class="border_main bg_secondary rounded px-2 py-1">${data_transaksi[0].no_nota}</div>
        </div>`;
        }
        html += `<table class="table table-sm bg_main text_main border_main table-bordered" style="font-size: 14px;">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Barang</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Total</th>`;
        if (cls == "btn_daftar_transaksi") {
            html += '<th class="text-center">Act</th>';
        }
        html += `</tr>
                </thead>
                <tbody class="tabel_search">`;

        data_transaksi.forEach((e, i) => {
            total += parseInt(e.total);
            html += `<tr>
                            <td class="text-center">${(i+1)}</td>
                            <td>${e.barang}</td>
                            <td class="text-end">${angka(e.harga)}</td>
                            <td class="text-end">${angka(e.qty)}</td>
                            <td class="text-end">${angka(e.total)}</td>`;
            if (cls == "btn_daftar_transaksi") {
                html += `<td class="text-center"><a href="" role="button" class="text-danger fs-6 btn_remove_data_transaksi" data-i="${i}"><i class="fa-solid fa-trash-can"></i></a></td>`;

            }
            html += `</tr>`;

        })
        if (cls == "btn_lunas") {
            html += '<tr><th colspan="4" class="text-center">TOTAL</th><th class="text-end">' + angka(total) + '</th></tr>';
        }


        html += `</tbody>
            </table>`;
        if (cls == "btn_lunas") {
            html += '<div class="d-grid"><button data-nama="' + data_transaksi[0].pembeli + '" data-user_id="' + data_transaksi[0].user_id + '" data-total="' + total + '" data-no_nota="' + data_transaksi[0].no_nota + '" class="btn btn-sm link_main ' + cls + '"><i class="fa-solid fa-cash-register"></i> LUNASI</button></div>';
        }
        // html += '</div>';
        if (cls !== "btn_lunas") {
            $(".total").html(angka(total));
        }
        return html;

    }

    $(document).on('keyup', '.add_qty', function(e) {
        e.preventDefault();
        if (data_selected.barang == undefined) {
            message("400", "Barang kosong!.");
            $(this).val(0);
            return;
        }

        let diskon = 0;
        if ($(".add_diskon").val() !== "") {
            diskon = parseInt(str_replace(".", "", $(".add_diskon").val()))
        }

        if ($(this).val() == 0) {
            message("400", "Qty gagal!.");
            $(this).val(1);
        }

        let qty = 1;
        if ($(this).val() !== "") {
            qty = parseInt(str_replace(".", "", $(this).val()));
        }


        let total = (parseInt(data_selected.harga) * qty) - diskon;

        $(".add_total").val(angka(total));

    });

    $(document).on('click', '.btn_data_transaksi', function(e) {
        e.preventDefault();
        if (data_selected.barang == undefined) {
            message("400", "Barang kosong!.");
            $(this).val(0);
            return;
        }
        let id = data_selected.id;
        let barang = $(".add_barang").val();
        let harga = parseInt(data_selected.harga);
        let qty = parseInt(str_replace(".", "", $(".add_qty").val()));
        let diskon = parseInt(str_replace(".", "", $(".add_diskon").val()));
        let total = parseInt(str_replace(".", "", $(".add_total").val()));

        data_transaksi.push({
            barang,
            id,
            harga,
            qty,
            total,
            no_nota,
            diskon
        });

        let html = tabel();
        $(".data_transaksi").html(html);

        $(".add_barang").val("");
        $(".add_harga").val(0);
        $(".add_qty").val(0);
        $(".add_diskon").val(0);
        $(".cari_barang").focus();
    });

    $(document).on('click', '.btn_remove_data_transaksi', function(e) {
        e.preventDefault();
        let i = $(this).data("i");
        let data = [];
        data_transaksi.forEach((e, x) => {
            if (x !== i) {
                data.push(e);
            }
        })

        data_transaksi = data;
        let html = tabel();

        $(".data_transaksi").html(html);
    });

    $(document).on('keyup', '.cari_user', function(e) {
        e.preventDefault();
        let val = $(this).val();

        post("barber/cari_user", {
            val
        }).then(res => {
            let html = "";
            if (res.data.length == 0) {
                html += '<div>Data tidak ditemukan!.</div>';
            }
            res.data.forEach(e => {
                html += '<div data-user_id="' + e.id + '" class="select_user">' + e.nama + '</div>';
            })

            $(".data_user").html(html);
        })
    });

    let pembeli = {};
    $(document).on('click', '.select_user', function(e) {
        e.preventDefault();
        let nama = $(this).text();
        let user_id = $(this).data("user_id");
        pembeli["user_id"] = user_id;
        pembeli["nama"] = nama;

        $(".cari_user").val(nama);
        $(".data_user").html("");
        $('input[placeholder="Barang"]').focus();
    });
    $(document).on('click', '.btn_daftar_transaksi', function(e) {
        e.preventDefault();
        let total = parseInt(str_replace(".", "", $(".total").text()));
        let html = '<div class="container">';
        html += `<div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">TOTAL</span>
                            <div data-total="${total}" class="fw-bold total_pembayaran">${angka(total)}</div>
                        </div>`;
        html += '<div>NOTA: ' + no_nota + '</div>';
        html += `<div class="mb-2 position-relative">
                    <label style="font-size: 12px;">Pembeli</label>
                    <input type="text" class="form-control form-control-sm cari_user" placeholder="Pembeli">
                    <div class="data_list data_user" style="font-size: small;">

                    </div>
                </div>`;
        html += `<div class="text-center mb-3">
                            <span class="text_main" style="font-size: 12px;">Uang Pembayaran</span>
                            <input type="text" class="mt-2 form-control form-control-sm uang_pembayaran text-center angka" value="${angka(total)}" placeholder="Uang pembayaran">
                        </div>`;
        html += tabel("btn_transaksi");
        html += '</div>';
        popupButton.html(html);
        setTimeout(() => {
            $(".cari_user").focus();
        }, 1000);
    });
    $(document).on('click', '.btn_transaksi', function(e) {
        e.preventDefault();
        let total = parseInt($(".total_pembayaran").data("total"));
        let uang_pembayaran = parseInt(str_replace(".", "", $(".uang_pembayaran").val()));
        let metode = $('input[name="metode_bayar"]:checked').val();
        let order = $(this).data("order");
        if (pembeli.user_id == undefined) {
            message("400", "Pembeli kosong!.");
            return;
        }

        if (uang_pembayaran < total) {
            message("400", "Uang kurang!.");
            return;
        }

        if (metode == "Tap") {
            let new_data_transaksi = [];
            data_transaksi.forEach(e => {
                e['user_id'] = pembeli.user_id;
                e['pembeli'] = pembeli.nama;
                new_data_transaksi.push(e);
            })
            metode_tap(new_data_transaksi, order);
            return;
        }

        post("barber/transaksi", {
            data_transaksi,
            metode,
            pembeli,
            no_nota,
            uang_pembayaran
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
                    html += '<div class="bg-opacity-25 bg-danger border border-danger mb-2" px-5 pb-1 rounded text-center" style="font-size: medium;">GAGAL: ' + res.data2.join(", ") + '</div>';
                }
                if (metode !== "Hutang") {
                    html += `<div class="d-grid">
                                        <a target="_blank" href="${res.data3}" class="btn btn-sm btn-success"><i class="fa-regular fa-file-pdf"></i> Cetak Nota</a>
                                        </div>
                                </div>`;
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

    $(document).on("click", ".btn_detail", function(e) {
        e.preventDefault();
        let no_nota = $(this).data("no_nota");

        let val = [];

        data.forEach(e => {
            if (e.no_nota == no_nota) {
                val.push(e);
            }
        });

        data_transaksi = val;
        let html = '';
        if (data_transaksi[0].metode == "Hutang") {
            html += tabel("btn_lunas");
        } else {
            let id = $(this).data("id");
            data.forEach(e => {
                if (e.id == id) {
                    val = e;
                    stop();
                }
            });
            html += `<div class="container">
                        <div class="mb-3">
                            <label style="font-size: 12px;">Tgl</label>
                            <input type="text" value="${time_php_to_js(val.tgl)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">No. Nota</label>
                            <input type="text" value="${(val.no_nota)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">User Id</label>
                            <input type="text" value="${(val.user_id)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Pembeli</label>
                            <input type="text" value="${(val.pembeli)}" class="form-control form-control-sm" readonly>
                        </div>
                         <div class="mb-3">
                            <label style="font-size: 12px;">Barang</label>
                            <input type="text" value="${(val.barang)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Qty</label>
                            <input type="text" value="${angka(val.qty)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Harga</label>
                            <input type="text" value="${angka(val.harga)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Diskon</label>
                            <input type="text" value="${angka(val.diskon)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Total</label>
                            <input type="text" value="${angka(val.total)}" class="form-control form-control-sm" readonly>
                        </div>
                    
                        <div class="mb-3">
                            <label style="font-size: 12px;">Metode</label>
                            <input type="text" value="${val.metode}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Petugas</label>
                            <input type="text" value="${val.petugas}" class="form-control form-control-sm" readonly>
                        </div>
                    </div>`;
        }

        popupButton.html(html);

        setTimeout(() => {
            $('#fullscreen').on('hidden.bs.modal', function() {
                data_transaksi = [];
                $(".add_barang").focus();
            });
        }, 300);

    })

    $(document).on('click', '.btn_lunas', function(e) {
        e.preventDefault();
        let total = parseInt($(this).data("total"));
        no_nota = $(this).data("no_nota");
        let nama = $(this).data("nama");
        let user_id = $(this).data("user_id");

        pembeli['nama'] = nama;
        pembeli['user_id'] = user_id;

        let val = [];

        data.forEach(e => {
            if (e.no_nota == no_nota) {
                val.push(e);
            }
        });

        data_transaksi = val;

        let myModal = document.getElementById("fullscreen");
        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        modal.hide();

        let html = '<div class="container">';
        html += `<div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">TOTAL</span>
                            <div data-total="${total}" class="fw-bold total_pembayaran">${angka(total)}</div>
                        </div>`;
        html += '<div>NOTA: ' + no_nota + '</div>';
        html += `<div class="mb-2 position-relative">
                    <label style="font-size: 12px;">Pembeli</label>
                    <input type="text" class="form-control form-control-sm" value="${pembeli.nama}" placeholder="Pembeli" readonly>
                </div>`;
        html += `<div class="text-center mb-3">
                            <span class="text_main" style="font-size: 12px;">Uang Pembayaran</span>
                            <input type="text" class="mt-2 form-control form-control-sm uang_pembayaran text-center angka" value="${angka(total)}" placeholder="Uang pembayaran">
                        </div>`;
        html += '</div>';
        html += tabel("btn_transaksi", "Hutang");
        popupButton.html(html);

        setTimeout(() => {
            $('input[name="metode_bayar"][value="Cash"]').prop('checked', true);
            $(".hutang").remove();
        }, 600);
        setTimeout(() => {
            $('#fullscreen').on('hidden.bs.modal', function() {
                data_transaksi = [];
                pembeli = {};
                $(".add_barang").focus();
            });
        }, 300);
    });
</script>
<?= $this->endSection() ?>