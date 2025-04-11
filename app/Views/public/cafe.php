<?= $this->extend('templates/cafe') ?>

<?= $this->section('content') ?>
<div>
    <h1 class=" text-center mt-5" style="font-family: 'Tangerine', serif;color:#d78926;font-size:40px">
        Hayu Food Court
    </h1>
    <h1 class="text-center menu" style="font-size:30px">
        MENU
    </h1>

</div>
<div class="d-none d-md-block">
    <div class="ps-1 pe-1" style="padding-bottom: 80px;">
        <div class="row g-2">
            <?php foreach (options('Cafe') as $i): ?>
                <div class="col-6">
                    <h1 class="text-center mb-4" style="font-family: 'Barrio';color:#fffefd;font-size:30px">
                        <?= $i['value']; ?>
                    </h1>
                    <div style="border-left: 4px solid #db8600;">
                        <div class="row g-1">
                            <?php foreach ($data[$i['value']] as $d): ?>
                                <div class="col-6">
                                    <h6 data-qty="<?= $d['qty']; ?>" class="menu ps-2 <?= ($d['qty'] > 0 ? 'select_menu' : ''); ?>" data-ket="<?= $i['value']; ?>" data-id="<?= $d['id']; ?>" data-barang="<?= $d['barang']; ?>" data-harga="<?= $d['harga']; ?>" style="cursor: pointer"><?= ($d['qty'] <= 0 ? '<del style="color:red">' . $d['barang'] . '</del>' : $d['barang']); ?></h6>
                                </div>
                                <div class="list_<?= $d['id']; ?> col-2"></div>
                                <div class="col-4 menu fw-bold text-end pe-1" style="background-color: #db8600;"><?= angka($d['harga']); ?></div>

                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-5">
                        <img class="img-fluid rounded-circle" width="20%" src="<?= base_url('files/cafe'); ?>/<?= strtolower($i['value']); ?>.jpg" alt="<?= $i['value']; ?>">
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="d-block d-md-none d-sm-block">
    <div class="ps-1 pe-1" style="padding-bottom: 80px;">
        <div class="row g-2">
            <?php foreach (options('Cafe') as $i): ?>
                <div class="col-6">
                    <h1 class="text-center" style="font-family: 'Barrio';color:#fffefd;font-size:20px">
                        <?= $i['value']; ?>
                    </h1>
                    <div style="border-left: 4px solid #db8600;">
                        <div class="row g-1">
                            <?php foreach ($data[$i['value']] as $d): ?>
                                <div class="col-6">
                                    <h6 data-qty="<?= $d['qty']; ?>" class="menu ps-2 <?= ($d['qty'] > 0 ? 'select_menu' : 'text-decoration-line-through'); ?>" data-ket="<?= $i['value']; ?>" data-id="<?= $d['id']; ?>" data-barang="<?= $d['barang']; ?>" data-harga="<?= $d['harga']; ?>" style="cursor: pointer"><?= $d['barang']; ?></h6>
                                </div>
                                <div class="list_<?= $d['id']; ?> col-2"></div>
                                <div class="col-4 menu fw-bold text-end pe-1" style="background-color: #db8600;"><?= angka($d['harga']); ?></div>

                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-5">
                        <img class="img-fluid rounded-circle" width="53%" src="<?= base_url('files/cafe'); ?>/<?= strtolower($i['value']); ?>.jpg" alt="<?= $i['value']; ?>">

                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>

</div>

<script>
    let data_pesanan = [];
    let pembeli = {};

    const tabel = () => {
        if (data_pesanan.length == 0) {
            $(".div_top").html("");
            return;
        }

        let total = 0;

        let html = '';
        if ($('.div_top').is(':empty')) {
            html += `<div class="fixed-top px-5 mt-3" style="z-index: 99999;">
                            <div class="rounded bg-dark px-2 border border-secondary shadow shadow-sm">
                                <div class="text-center mb-1 expand_show">
    
                                </div>
    
                                <div class="main_content">
                                    <div class="div_nama mb-1">
    
                                    </div>
                                    <div class="mb-1">
                                        <span class="text_main" style="font-size: 11px;">No. Whatsapp</span>
                                        <input type="text" style="font-size: x-small;" class="form-control form-control-sm hp" value="" placeholder="No. whatsapp">
                                    </div>
                                    <div class="mb-1">
                                        <span class="text_main" style="font-size: 11px;">No. Meja</span>
                                        <input type="text" style="font-size: x-small;" class="form-control form-control-sm no_meja" value="1" placeholder="No. meja">
                                    </div>
                                    <div style="font-size: 12px;">DAFTAR PESANAN</div>
                                    <table class="table table-sm table-dark text_main table-bordered" style="font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Barang</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody class="data_pesanan">`;
            data_pesanan.forEach((e, i) => {
                total += parseInt(e.harga);
                html += `<tr>
                                <td class="text-center">${(i+1)}</td>
                                <td>${e.barang}</td>
                                <td class="text-center">
                                    <a href="" class="text-light qty_count" data-max="${e.max}" data-id="${e.id}" data-order="minus"><i class="fa-solid fa-circle-minus"></i></a><span class="mx-2">${angka(e.qty)}</span><a href="" class="text-light qty_count" data-max="${e.max}" data-id="${e.id}" data-order="plus"><i class="fa-solid fa-circle-plus"></i></a>
                                </td>
                                <td class="text-end">${angka(e.harga)}</td>
                            </tr>`;

            })
            html += `<tr>
                            <th class="text-center" colspan="3">TOTAL</th>
                            <th class="text-end">${angka(total)}</th>
                        </tr>`;
            html += `</tbody>
                                    </table>
    
    
                                    <div class="d-grid mb-3">
                                        <button class="btn btn-sm btn-warning btn_transaksi" style="font-size: x-small;">TRANSAKSI</button>
                                    </div>
                                    <div class="body_pendaftaran"></div>
                                </div>
    
                                <div class="text-center expand_hide" style="margin-bottom: 2px;">
                                    <a href="" class="text-light btn_expand" data-order="hide" style="font-size: xx-large;"><i class="fa-solid fa-caret-up"></i></a>
                                    <div class="text-center" style="font-size: x-small;margin-top:-19px">Hide</div>
                                </div>
                            </div>
                        </div>`;

            $(".div_top").html(html);
        } else {
            data_pesanan.forEach((e, i) => {
                total += parseInt(e.harga);
                html += `<tr>
                    <td class="text-center">${(i+1)}</td>
                    <td>${e.barang}</td>
                    <td class="text-center">
                    <a href="" class="text-light qty_count" data-max="${e.max}" data-id="${e.id}" data-order="minus"><i class="fa-solid fa-circle-minus"></i></a><span class="mx-2">${angka(e.qty)}</span><a href="" class="text-light qty_count" data-max="${e.max}" data-id="${e.id}" data-order="plus"><i class="fa-solid fa-circle-plus"></i></a>
                    </td>
                    <td class="text-end">${angka(e.harga)}</td>
                    </tr>`;

            })
            html += `<tr>
                <th class="text-center" colspan="3">TOTAL</th>
                <th class="text-end">${angka(total)}</th>
                </tr>`;

            $(".data_pesanan").html(html);
        }

    }

    $(document).on("click", ".select_menu", function(e) {
        e.preventDefault();

        let id = $(this).data("id");
        let ket = $(this).data("ket");
        let barang = $(this).data("barang");
        let qty = $(this).data("qty");
        let harga = $(this).data("harga");

        let id_exist = undefined;
        let new_data = [];
        let qty_now = 1;
        let max = undefined;
        data_pesanan.forEach(e => {
            if (e.id == id) {
                id_exist = id;
                e.qty += 1;
                qty_now = e.qty;
                if (e.qty > qty) {
                    max = qty;
                    e.qty = qty;
                    qty_now = qty;
                }
            }
            new_data.push(e);
        })

        if (id_exist) {
            if (max) {
                message("400", barang + " maksimal " + angka(max));
                return;
            } else {
                data_pesanan = new_data;
            }
        } else {
            data_pesanan.push({
                id,
                ket,
                barang,
                max: qty,
                qty: 1,
                harga
            })
        }

        tabel();

        message("200", barang + " " + angka(qty_now));
    })
    $(document).on("click", ".qty_count", function(e) {
        e.preventDefault();

        let id = $(this).data("id");
        let order = $(this).data("order");
        let max = parseInt($(this).data("max"));

        let new_data = [];
        let barang = "";
        let qty_now = 0;

        data_pesanan.forEach(e => {
            if (e.id == id) {
                barang = e.barang;
                if (order == "plus") {
                    e.qty += 1;
                } else {
                    e.qty -= 1;
                }
                qty_now = e.qty;

                if (e.qty > max) {
                    e.qty = max;
                }
            }



            if (e.qty > 0) {
                new_data.push(e);
            }
        })

        if (qty_now > max) {
            message("400", barang + " maksimal " + angka(max));
            return;
        } else if (qty_now == 0) {
            message("400", barang + " dihapus!.");
            data_pesanan = new_data;
        }

        tabel();
    })

    $(document).on('click', '.btn_expand', function(e) {
        e.preventDefault();
        let order = $(this).data("order");

        if (order == "hide") {
            let html = "";
            html += `<div class="text-center" style="font-size: x-small;margin-bottom:-27px">Show</div>
                            <a href="" class="text-light btn_expand" data-order="show" style="font-size: xx-large;"><i class="fa-solid fa-sort-down"></i></a>`;
            $(".expand_show").html(html);
            $(".expand_hide").html("");
            $(".main_content").hide();
        } else {
            let html = "";
            html += `<a href="" class="text-light btn_expand" data-order="hide" style="font-size: xx-large;"><i class="fa-solid fa-caret-up"></i></a>
                <div class="text-center" style="font-size: x-small;margin-top:-19px">Hide</div>`;
            $(".expand_show").html("");
            $(".expand_hide").html(html);
            $(".main_content").show();
        }
    });




    $(document).on('click', '.daftar', function(e) {
        e.preventDefault();
        if ($(".body_daftar").css('display') === 'none') {
            $(".body_daftar").show();
        } else {
            $(".body_daftar").hide();
        }

    });
    $(document).on('click', '.btn_daftar', function(e) {
        e.preventDefault();
        let nama = $(".add_nama").val();
        let hp = $(".add_hp").val();
        if (nama == "") {
            message("400", "Nama harus diisi!.");
            return;
        }
        if (hp == "") {
            message("400", "Hp harus diisi!.");
            return;
        }

        post("cafe/add_user", {
            nama,
            hp
        }).then(res => {
            message(res.status, res.message);
            if (res.status == "200") {
                $(".body_pendaftaran").remove();
            }
        })

    });

    $(document).on('click', '.btn_transaksi', function(e) {
        e.preventDefault();
        let hp = $(".hp").val();
        let no_meja = $(".no_meja").val();
        if (hp == "") {
            message("400", "No whatsapp harus diisi!.");
            return;
        }
        if (no_meja == "") {
            message("400", "No. meja harus diisi!.");
            return;
        }

        if (pembeli.user_id === undefined) {
            post("landing/cek_user", {
                hp,
                no_meja
            }).then(res => {
                message(res.status, res.message);
                if (res.status == "200") {
                    pembeli["user_id"] = res.data.id;
                    pembeli["nama"] = res.data.nama;
                    let html = `<span class="text_main" style="font-size: 11px;">Nama Pemesan</span>
                                <input type="text" style="font-size: x-small;" class="form-control border-success bg-success text-light fw-bold form-control-sm" value="${res.data.nama}" placeholder="" readonly>`;
                    $(".div_nama").html(html);
                } else {
                    let html = `<section class="bg-dark">
                                <div class="d-flex justify-content-between">
                                    <div style="font-size:12px" class="text-danger pt-1">Kamu belum terdaftar. Mau daftar?</div>
                                    <div><a style="font-size:x-small" href="" class="btn btn-sm btn-danger close_pendaftaran"><i class="fa-solid fa-circle-xmark"></i> Close</a></div>
                                </div>
                                <section class="mb-1">
                                    <span class="text_main" style="font-size: 11px;">Nama</span>
                                    <input type="text" style="font-size: x-small;" class="form-control form-control-sm add_nama" value="" placeholder="Nama">
                                </section>
                                <section class="mb-1">
                                    <span class="text_main" style="font-size: 11px;">No. Whatsapp</span>
                                    <input type="text" style="font-size: x-small;" class="form-control form-control-sm add_hp" value="" placeholder="No. Whatsapp">
                                </section>
                                <section class="d-grid mt-3">
                                <button style="font-size:x-small" class="btn btn-sm btn-warning btn_daftar">DAFTAR</button></section>
                            </section>
                            `;

                    $(".body_pendaftaran").html(html);
                }
            })
        } else {
            transaksi();
        }

    });

    const transaksi = () => {
        if (pembeli.user_id == undefined) {
            message("400", "Pembeli kosong!.");
            return;
        }
        if (data_pesanan.length == 0) {
            message("400", "Pesanan kosong!.");
            return;
        }

        post("cafe/transaksi", {
            pembeli,
            data_pesanan
        }).then(res => {
            if (res.status == "200") {
                message("200", "Sukses.");
                setTimeout(() => {
                    window.location.href = res.data;
                }, 1300);
            } else {
                message("400", res.message);
            }
        })
    }

    $(document).on('click', '.close_pendaftaran', function(e) {
        e.preventDefault();
        $(".body_pendaftaran").html("");

    });
</script>
<?= $this->endSection() ?>