<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<div class="sticky-top bg_main mb-2" style="top: 55px;padding-top:10px">
    <h6 style="font-size: small;" class="bg_secondary rounded pt-2 d-flex gap-3 px-3">
        <div>
            <i class="<?= menu()['icon']; ?>"></i> <?= angka($total) . " - " . angka($hutang) . " = " . angka($total - $hutang); ?>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input show_hide" type="checkbox" checked>
            <label class="form-check-label">Show/hide</label>
        </div>
    </h6>
    <?php foreach ($perangkat as $i): ?>
        <div class="<?= ($i['status'] == 0 && $i['metode'] !== "Over" ? "bg_main" : "bg-danger bg-opacity-10"); ?> text_main border_main rounded px-3 py-2 mb-2 main_content">
            <form class="d-flex justify-content-between gap-3" method="post" action="<?= base_url('billiard/add'); ?>">
                <input type="hidden" name="perangkat" value="<?= $i['perangkat']; ?>">
                <input type="hidden" name="harga" value="<?= $i['harga']; ?>">
                <!-- info meja -->
                <div style="width: 40%;">
                    <?= $i['perangkat']; ?>
                    <?php if ($i['status'] == 0): ?>
                        <?php if ($i['metode'] == "Over"): ?>
                            <!-- jika sudah dimatikan iot tapi belum bayar -->
                            <div class="text-warning" style="font-size: x-small;">Wait for pay</div>
                        <?php else: ?>
                            <!-- jika kosong -->
                            <div class="text-success" style="font-size: x-small;">Available</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($i['durasi'] == 0): ?>
                            <!-- dipakai dan open -->
                            <div class="text-warning" style="font-size: x-small;">Open | <?= durasi_jam($i['dari']); ?></div>
                        <?php else: ?>
                            <?php if (time() <= $i['ke']): ?>
                                <!-- dipakai waktu masih -->
                                <div class="text-warning" style="font-size: x-small;"><?= $i['jam']; ?> Jam | <?= date('h', $i['ke']); ?> : <?= date('i', $i['ke']); ?></div>

                            <?php else: ?>
                                <!-- dipakai waktu habis -->
                                <div class="text-danger fw-bold" style="font-size: x-small;">HABIS | <?= date('h', $i['ke']); ?> : <?= date('i', $i['ke']); ?></div>

                            <?php endif; ?>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
                <!-- select durasi -->
                <div style="width: 25%;">
                    <?php if ($i['status'] == 0): ?>
                        <?php if ($i['metode'] == "Over"): ?>
                            <select class="form-control px-1 durasi_<?= $i['id']; ?>" style="font-size: 13px; padding-top:6px;padding-bottom:6px" name="durasi">
                                <option value="Durasi">Open</option>
                                <?php for ($x = 1; $x < 11; $x++): ?>
                                    <option <?= ($i["jam"] == $x ? "selected" : ""); ?> value="<?= $x; ?>"><?= $x; ?> Jam</option>
                                <?php endfor; ?>
                            </select>
                        <?php else: ?>
                            <select class="form-control px-1 durasi_<?= $i['id']; ?>" style="font-size: 13px; padding-top:6px;padding-bottom:6px" name="durasi">
                                <option value="Durasi" selected>Durasi</option>
                                <option value="0">Open</option>
                                <?php for ($x = 1; $x < 11; $x++): ?>
                                    <option value="<?= $x; ?>"><?= $x; ?> Jam</option>
                                <?php endfor; ?>
                            </select>

                        <?php endif; ?>
                    <?php else: ?>

                        <select class="form-control px-1 durasi_<?= $i['id']; ?>" style="font-size: 13px; padding-top:6px;padding-bottom:6px" name="durasi">
                            <option value="Durasi">Open</option>
                            <?php for ($x = 1; $x < 11; $x++): ?>
                                <option <?= ($i["jam"] == $x ? "selected" : ""); ?> value="<?= $x; ?>"><?= $x; ?> Jam</option>
                            <?php endfor; ?>
                        </select>


                    <?php endif; ?>
                </div>
                <!-- tombol submit -->
                <div style="width: 35%;">
                    <?php if ($i['status'] == 1): ?>
                        <?php if ($i['durasi'] == 0): ?>
                            <button type="button" href="" class="bg-secondary opacity-50 btn btn-sm" style="font-size: medium;"><i class="fa-solid fa-ban"></i></button>
                        <?php else: ?>
                            <!-- jika waktu kurang 10 menit munculkan tombol tambah durasi -->
                            <?php if (round(($i['ke'] - time()) / 60) <= 10): ?>
                                <button type="button" href="" class="bg-warning opacity-50 btn btn-sm tambah_durasi" data-id="<?= $i['id']; ?>" style="font-size: medium;"><i class="fa-solid fa-clock"></i></button>
                            <?php else: ?>
                                <button type="button" href="" class="bg-secondary opacity-50 btn btn-sm" style="font-size: medium;"><i class="fa-solid fa-ban"></i></button>
                            <?php endif; ?>
                            <!-- jika tidak kosong -->
                        <?php endif; ?>
                        <button type="submit" href="" class="link_secondary btn btn-sm akhiri akhiri_<?= $i['id']; ?>" data-id="<?= $i['id']; ?>" style="font-size: medium;"><i class="fa-solid fa-cash-register"></i> PAY</button>
                    <?php else: ?>
                        <?php if ($i['metode'] == "Over"): ?>
                            <button type="button" href="" class="bg-warning opacity-50 btn btn-sm tambah_durasi" data-id="<?= $i['id']; ?>" style="font-size: medium;"><i class="fa-solid fa-clock"></i></button>
                            <button type="submit" href="" class="link_secondary btn btn-sm akhiri akhiri_<?= $i['id']; ?>" data-id="<?= $i['id']; ?>" style="font-size: medium;"><i class="fa-solid fa-cash-register"></i> PAY</button>
                        <?php else: ?>
                            <button type="button" href="" class="bg-secondary opacity-50 btn btn-sm" style="font-size: medium;"><i class="fa-solid fa-ban"></i></button>
                            <!-- jika kosong -->
                            <button type="submit" href="" class="link_main btn btn-sm" style="font-size: medium;width:65px"><i class="fa-solid fa-angles-up"></i> OK</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </form>
        </div>

    <?php endforeach; ?>
</div>

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
                <th class="text-center">Tgl</th>
                <th class="text-center">Meja</th>
                <th class="text-center">Biaya</th>
                <th class="text-center">User</th>
                <th class="text-center">Act</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <?php if ($i['metode'] == "Hutang" || $i['metode'] == "Over"): ?>
                        <td class="bg-danger bg-opacity-10 text-center"><?= $k + 1; ?></td>
                    <?php else: ?>
                        <td class="text-center"><a style="text-decoration: none;" class="text_main" target="_blank" href="<?= base_url("guest/nota/") . encode_jwt(['tabel' => menu()['tabel'], 'id' => $i['id']]); ?>"><?= $k + 1; ?></a></td>
                    <?php endif; ?>
                    <td class="<?= ($i['metode'] == "Hutang" || $i['metode'] == "Over" ? "bg-danger bg-opacity-10" : ""); ?> text-center"><?= date("d", $i['tgl']); ?></td>
                    <td class="<?= ($i['metode'] == "Hutang" || $i['metode'] == "Over" ? "bg-danger bg-opacity-10" : ""); ?>"><?= $i['perangkat']; ?></td>
                    <td class="<?= ($i['metode'] == "Hutang" || $i['metode'] == "Over" ? "bg-danger bg-opacity-10" : ""); ?> text-end"><?= angka($i['total']); ?></td>
                    <td class="<?= ($i['metode'] == "Hutang" || $i['metode'] == "Over" ? "bg-danger bg-opacity-10" : ""); ?>"><?= $i['pembeli']; ?></td>
                    <td class=" <?= ($i['metode'] == "Hutang" || $i['metode'] == "Over" ? "bg-danger bg-opacity-10" : ""); ?> text-center"><a data-metode="<?= $i['metode']; ?>" data-id="<?= $i['id']; ?>" href="" class="text_main btn_detail"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    let data = <?= json_encode($data); ?>;

    $(document).on("click", ".btn_detail", function(e) {
        e.preventDefault();
        let id = $(this).data("id");

        let val = [];

        data.forEach(e => {
            if (e.id == id) {
                val = e;
                stop();
            }
        });
        let html = '<div class="container">';
        if (val.metode == "Hutang") {
            html += `<div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">TOTAL</span>
                            <div data-total="${val.total}" class="fw-bold total_pembayaran">${angka(val.total)}</div>
                        </div>`;
            html += '<div>NOTA: B' + str_replace("/", "", time_php_to_js(val.tgl)) + "-" + val.id + '</div>';
            html += `<div class="mb-2 position-relative">
                    <label style="font-size: 12px;">Pembeli</label>
                    <input type="text" class="form-control form-control-sm" value="${val.pembeli}" placeholder="Pembeli" readonly>
                </div>`;
            html += `<div class="text-center mb-3">
                            <span class="text_main" style="font-size: 12px;">Uang Pembayaran</span>
                            <input type="text" class="mt-2 form-control form-control-sm uang_lunas text-center angka" value="${angka(val.total)}" placeholder="Uang pembayaran">
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
                    <div class="d-grid">
                        <button class="btn btn-sm btn-success btn_lunas" data-total="${val.total}" data-id="${val.id}"><i class="fa-solid fa-wallet"></i> LUNASI</button>
                    </div>`;
        } else {
            html += `<div class="mb-3">
                        <label style="font-size: 12px;">Tgl</label>
                        <input type="text" value="${time_php_to_js(val.tgl)}" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 12px;">Pembeli</label>
                        <input type="text" value="${(val.pembeli)}" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 12px;">User Id</label>
                        <input type="text" value="${(val.user_id)}" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 12px;">Meja</label>
                        <input type="text" value="${val.perangkat}" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 12px;">Dari</label>
                        <input type="text" value="${time_php_to_js(val.dari, "jm")}" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 12px;">Ke</label>
                        <input type="text" value="${time_php_to_js(val.ke, "jm")}" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 12px;">Durasi</label>
                        <input type="text" value="${angka(val.durasi)}" class="form-control form-control-sm" readonly>
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
                        <label style="font-size: 12px;">Status</label>
                        <input type="text" value="${val.status}" class="form-control form-control-sm" readonly>
                    </div>
                
                    <div class="mb-3">
                        <label style="font-size: 12px;">Metode</label>
                        <input type="text" value="${val.metode}" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 12px;">Petugas</label>
                        <input type="text" value="${val.petugas}" class="form-control form-control-sm" readonly>
                    </div>`;



        }
        html += '</div>';

        popupButton.html(html);
    })


    $(document).on("click", ".tambah_durasi", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let durasi = $(".durasi_" + id).val();
        if (durasi == 0) {
            message("400", "Tidak boleh open!.");
            return;
        }

        popup_confirm.durasi("tambah_durasi", durasi);
    })
    $(document).on("click", ".btn_tambah_durasi", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let durasi = $(".durasi_" + id).val();
        if (durasi == 0) {
            message("400", "Tidak boleh open!.");
            return;
        }

        post("billiard/add_durasi", {
            durasi,
            id
        }).then(res => {
            if (res.status == "200") {
                message("200", res.message);
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                message("400", res.message);
            }
        })
    })
    $(document).on("click", ".akhiri", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        popup_confirm.akhiri("akhiri_" + id);
    })

    $(document).on("click", ".btn_akhiri", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        post("billiard/akhiri", {
            id
        }).then(res => {
            if (res.status == "200") {
                let html = "";
                html += `<div class="container border border-light rounded p-2">
                        <div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">TOTAL</span>
                            <div data-total="${res.data.harga}" class="fw-bold total_pembayaran">${angka(res.data.harga)}</div>
                            <div class="fw-bold" style="font-size:12px">${res.data.durasi}</div>
                        </div>
                        <hr>
                        <div class="text-center mb-3" style="position: relative;">
                            <span class="text_main" style="font-size: small;">Pembeli</span>
                            <input type="text" class="mb-2 form-control users text-center" value="" placeholder="Nama pembeli">
                            <div class="data_list"></div>
                        </div>
                        <div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">Diskon</span>
                            <input type="text" class="mt-2 form-control diskon text-center angka" value="0" placeholder="Diskon">
                        </div>
                        <div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">Uang Pembayaran</span>
                            <input type="text" class="mt-2 form-control uang_pembayaran text-center angka" value="${angka(res.data.harga)}" placeholder="Uang pembayaran">
                        </div>
                        <div class="mb-3 text-center" style="font-size:12px">
                            <div class="form-check form-check-inline">
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
                        </div>
                    <div class="d-grid">
                        <button class="btn btn-sm btn-success btn_transaksi" data-total="${res.data.harga}" data-id="${id}"><i class="fa-solid fa-wallet"></i> OK</button>
                    </div>`;
                html += '</div>';


                popupButton.html(html);

                setTimeout(() => {
                    $('#fullscreen').on('shown.bs.modal', function() {
                        let input = $('.users');
                        input.focus();

                        // Pindahkan kursor ke akhir teks di input
                        let inputElement = input[0];
                        let length = inputElement.value.length;
                        inputElement.setSelectionRange(length, length);
                    });
                }, 300);

                const modal = document.getElementById('fullscreen');
                modal.addEventListener('hidden.bs.modal', function() {
                    // Reload the page when the modal is hidden
                    location.reload();
                });
            } else {
                message("400", res.message);
            }
        })
    })

    $(document).on('keyup', '.users', function(e) {
        e.preventDefault();
        let val = $(this).val();

        post("billiard/users", {
            val
        }).then(res => {
            let html = "";
            if (res.data.length == 0) {
                html += '<div>Data tidak ditemukan!.</div>';
            }
            res.data.forEach(e => {
                html += '<div data-user_id="' + e.id + '" class="select_user">' + e.nama + '</div>';
            })

            $(".data_list").html(html);
        })
    });


    let temp_user_id = 0;
    $(document).on('click', '.select_user', function(e) {
        e.preventDefault();
        let nama = $(this).text();
        let user_id = $(this).data("user_id");
        temp_user_id = user_id;

        $(".users").val(nama);
        $(".data_list").html("");
    });

    $(document).on('keyup', '.diskon', function(e) {
        e.preventDefault();
        let total = parseInt($(".total_pembayaran").data("total"));
        let dsk = $(this).val();

        let diskon = 0;
        if (dsk !== "") {
            diskon = parseInt(str_replace(".", "", dsk));
        }

        if (diskon > total) {
            message("400", "Diskon maksimal " + angka(total));
            $(".total_pembayaran").text(angka(total));
            $(this).val(angka(total));
            return;
        }
        if (diskon == 0) {
            $(".total_pembayaran").text(angka(total));
        } else {
            $(".total_pembayaran").text(angka(total) + " - " + angka(diskon) + " = " + angka(total - diskon));
        }

    });


    $(document).on('click', '.btn_transaksi', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let jml = parseInt($(this).data("total"));
        let pembeli = $(".users").val();
        let diskon = parseInt(str_replace(".", "", $(".diskon").val()));
        let user_id = temp_user_id;
        let uang_pembayaran = str_replace(".", "", $(".uang_pembayaran").val());
        let total = jml - diskon;
        let metode = $('input[name="metode_bayar"]:checked').val();
        if (pembeli == "") {
            message("400", "Pembeli kosong!.");
            return;
        }
        if (user_id == "" || user_id == 0) {
            message("400", "User id kosong!.");
            return;
        }
        if (uang_pembayaran == "" || uang_pembayaran == 0) {
            message("400", "Uang kosong!.");
            return;
        }
        if (jml == "" || jml == 0) {
            message("400", "Jml kosong!.");
            return;
        }
        if (total == "" || total == 0) {
            message("400", "Total kosong!.");
            return;
        }
        if (id == "" || total == 0) {
            message("400", "Id kosong!.");
            return;
        }
        if (uang_pembayaran < total) {
            message("400", "Uang kurang!.");
            $(".uang_pembayaran").val(angka(total));
            return;
        }
        if (metode == "Tap") {
            let data_transaksi = {};
            data_transaksi["user_id"] = user_id;
            data_transaksi["pembeli"] = pembeli;
            data_transaksi["diskon"] = diskon;
            data_transaksi["id"] = id;
            metode_tap(data_transaksi, "Bayar");
            return;
        }
        post("billiard/transaksi", {
            id,
            diskon,
            user_id,
            pembeli,
            biaya: jml,
            uang_pembayaran,
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
                }, 500);
            } else {
                message("400", res.message);
            }

        })

    });
    $(document).on('click', '.btn_lunas', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let total = parseInt($(this).data("total"));
        let uang_lunas = str_replace(".", "", $(".uang_lunas").val());
        let metode = $('input[name="metode_bayar"]:checked').val();

        if (uang_lunas == "" || uang_lunas == 0) {
            message("400", "Uang kosong!.");
            return;
        }

        if (uang_lunas < total) {
            message("400", "Uang kurang!.");
            $(".uang_lunas").val(angka(total));
            return;
        }
        if (metode == "Tap") {
            metode_tap({
                id
            }, "Hutang");
            return;
        }
        post("billiard/lunas", {
            id,
            uang_lunas,
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

    $(document).on('change', '.show_hide', function(e) {
        e.preventDefault();
        if ($(".main_content").css('display') === 'none') {
            $(".main_content").show();
        } else {
            $(".main_content").hide();
        }

    });
</script>
<?= $this->endSection() ?>