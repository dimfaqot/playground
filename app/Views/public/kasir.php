<?= $this->extend('templates/kasir') ?>

<?= $this->section('content') ?>

<div class="p-4 content">
    <?php if (!$absen): ?>
        <div class="text-center mb-3" style="margin-top: 80px;">SILAHKAN <b>ABSEN</b></div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-3">
                <h6>Transaksi Hari Ini</h6>
                <div class="table-container" style="font-size:small;max-height: 600px;overflow-y: auto;display: block;">
                    <table class="table table-dark">
                        <thead style="position: sticky;top: 0;background: #212529;color: white;z-index: 10;">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Kat.</th>
                                <th class="text-center">Masuk</th>
                                <th class="text-center">Hutang</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="transaksi_hari_ini">
                            <?php
                            $masuk = 0;
                            $hutang = 0;
                            $total = 0;
                            ?>
                            <?php foreach (options('Divisi') as $k => $i): ?>
                                <?php
                                $masuk += (int)$today[$i['value']]['masuk']['total'];
                                $hutang += (int)$today[$i['value']]['hutang']['total'];
                                $total += (int)$today[$i['value']]['masuk']['total'] - (int)$today[$i['value']]['hutang']['total'];
                                ?>
                                <tr style="cursor: pointer;" class="btn_transaksi_hari_ini" data-divisi="<?= $i['value']; ?>">
                                    <td class="text-center"><?= ($k + 1); ?></td>
                                    <td><?= $i['value']; ?></td>
                                    <td class="text-end"><?= angka($today[$i['value']]['masuk']['total']); ?></td>
                                    <td class="text-end"><?= angka($today[$i['value']]['hutang']['total']); ?></td>
                                    <td class="text-end"><?= angka(($today[$i['value']]['masuk']['total'] - $today[$i['value']]['hutang']['total'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th class="text-center" colspan="2">TOTAL</th>
                                <th class="text-end"><?= angka($masuk); ?></th>
                                <th class="text-end"><?= angka($hutang); ?></th>
                                <th class="text-end"><?= angka($total); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-5">

                <div class="text-danger is_absen"><i class="fa-solid fa-circle"></i></div>
                <!-- <div class="d-flex gap-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input mode_bayar" type="checkbox" role="switch">
                        <label>Mode Bayar Di Awal</label>
                    </div>
                </div> -->

                <label>Pembeli <span class="saldo_pembeli"></span></label>
                <div class="d-flex gap-2">
                    <div class="w-100">
                        <div class="mb-2 position-relative">
                            <input type="text" class="form-control form-control cari_user" placeholder="Pembeli" autofocus>
                            <div class="data_list data_user" style="font-size:20px;"></div>
                        </div>

                    </div>
                    <div class="flex-shrink-1 body_btn_del_user">

                    </div>
                </div>
                <div class="body_transaksi"></div>
            </div>
            <div class="col-4">
                <h6>Nota Hari Ini</h6>
                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text bg_main border_main text_main">Cari Data</span>
                    <input type="text" class="form-control cari_nota bg_main border border_main text_main" placeholder="....">
                </div>
                <div class="table-container" style="font-size:small;max-height: 600px;overflow-y: auto;display: block;">
                    <table class="table table-dark">
                        <thead style="position: sticky;top: 0;background: #212529;color: white;z-index: 10;">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Tgl</th>
                                <th class="text-center">Pembeli</th>
                            </tr>
                        </thead>
                        <tbody class="nota_hari_ini">
                            <?php foreach ($data as $k => $i): ?>
                                <tr style="cursor: pointer;" class="btn_cetak_nota" data-link="<?= $i['jwt']; ?>">
                                    <td class="text-center"><?= ($k + 1); ?></td>
                                    <td class="text-center"><?= date('d/m/y H:i', $i['tgl']); ?></td>
                                    <td><?= $i['pembeli']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>


<script>
    let barang_selected = {};
    let pembeli = {};
    let data_transaksi = [];
    let data_transaksi_hari_ini = <?= json_encode($today); ?>;
    let petugas = <?= json_encode($absen); ?>;
    // let mode_bayar = false;

    let jwt = "<?= $jwt; ?>";


    // $(document).on('change', '.mode_bayar', function(e) {
    //     e.preventDefault();

    //     mode_bayar = $(this).is(':checked');
    //     $('input[placeholder="Barang"]').focus();
    // });

    let cek_absen = () => {
        post("kasir/cek_absen", {
            jwt
        }).then(res => {
            if (res.status == "200") {
                if (jwt == "") {
                    message("200", "Absen berhasil...");
                } else {
                    message("400", "Shift berakhir...");
                }

                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                if (parseInt(res.data) > 0) {
                    let limit = 10; // Pastikan limit dideklarasikan di luar agar bisa diperbarui
                    let interval = 2000; // Sesuaikan interval waktu dalam milidetik

                    let cd = () => {
                        message("400", `Shift akan berakhir dalam: ${limit}`);
                        limit--; // Kurangi limit setelah digunakan

                        if (limit === 0) { // Gunakan perbandingan yang benar
                            clearInterval(count);
                        }
                    };

                    let count = setInterval(cd, interval);
                }
            }
        })
    }

    setInterval(() => {
        cek_absen();
        if (petugas.user_id == undefined) {
            $(".is_absen").addClass("text-danger");
            $(".is_absen").removeClass("text-success");
        } else {
            $(".is_absen").removeClass("text-danger");
            $(".is_absen").addClass("text-success");
        }
    }, 60000);

    let body_transaksi = () => {
        let html = `
            <div class="mb-2 position-relative">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="kategori" value="Kantin" checked>
                    <label class="form-check-label">
                       Kantin
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="kategori" value="Barber">
                    <label class="form-check-label">
                       Barber
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="kategori" value="Billiard">
                    <label class="form-check-label">
                       Billiard
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="kategori" value="Ps">
                    <label class="form-check-label">
                       Ps
                    </label>
                </div>
                <input type="text" class="form-control form-control cari_barang" placeholder="Barang">
                <div class="data_list data_barang" style="font-size:20px;"></div>
            </div>
            <div class="mb-2">
                <label>Qty</label>
                <input type="text" value="1" class="form-control form-control" placeholder="Qty">
            </div>

            <div class="mb-3">
                <label>Diskon</label>
                <input type="text" value="0" class="form-control form-control angka" placeholder="Diskon">
            </div>

            <div class="d-grid">
                    <button type="button" class="btn btn btn-success btn_tambah_data_transaksi"></button>
            </div>
           
          <div class="body_tabel_transaksi"></div>`;

        if (pembeli.user_id !== undefined) {
            $(".body_transaksi").html(html);
        } else {
            $(".body_btn_del_user").html("");
            $(".body_transaksi").html("");
        }
    }

    let body_tabel_transaksi = () => {
        let html = `<div class="d-grid mt-3">
                    <button type="button" class="btn btn btn-primary btn_proses"></button>
            </div>
            <table class="table table-dark">
                <thead>
                    <tr>
                        <td class="text-center">#</td>
                        <td class="text-center">Kat</td>
                        <td class="text-center">Barang</td>
                        <td class="text-center">Harga</td>
                        <td class="text-center">Qty</td>
                        <td class="text-center">Diskon</td>
                        <td class="text-center">Total</td>
                        <td class="text-center">Del</td>
                    </tr>
                </thead>
                <tbody>`;
        let total = 0;
        let temp_data_transaksi = [];
        data_transaksi.forEach((e, i) => {
            if (e.metode == undefined) {
                e.metode = 'Bayar';
            }
            temp_data_transaksi.push(e);
            total += parseInt(e.total);
            html += `<tr>
                        <td class="text-center">${(i+1)}</td>
                        <td>${e.kategori}</td>
                        <td>${e.barang}</td>
                        <td class="text-end">${angka(e.harga)}</td>
                        <td class="text-center">${e.qty}</td>
                        <td class="text-end">${angka(e.diskon)}</td>
                        <td class="text-end">${angka(e.total)}</td>
                        <td class="text-center text-danger btn_del_barang_selected" data-i="${i}"><i class="fa-solid fa-circle-xmark"></i></td>
                    </tr>`;
        })


        // html += `<tr>
        //             <th class="text-center" colspan="6">TOTAL</th>
        //             <td class="text-end">${angka(total)}</td>
        //             <td class="text-center"><i class="fa-solid fa-ban"></i></td>
        //         </tr>`;

        html += `</tbody>
            </table>`;
        data_transaksi = temp_data_transaksi;
        if (pembeli.user_id !== undefined && data_transaksi.length > 0) {
            $(".body_tabel_transaksi").html(html);
            $(".btn_proses").html('<i class="fa-solid fa-hourglass-end"></i>' + " PROSES [ " + angka(total) + " ]");
        } else {
            $(".body_tabel_transaksi").html("");
        }
    }


    let nota_hari_ini = (data) => {
        let dataArray = Object.values(data).filter(item => typeof item === 'object' && item !== null);

        let html = '';
        dataArray.forEach((e, i) => {
            html += `<tr style="cursor: pointer;" class="btn_cetak_nota" data-link="${e.jwt}">
                        <td class="text-center">${(i+1)}</td>
                        <td class="text-center">${time_php_to_js(e.tgl,'full')}</td>
                        <td>${e.pembeli}</td>
                    </tr>`;

        })

        $(".nota_hari_ini").html(html);
    }

    $(document).on('keyup', '.cari', function(e) {
        e.preventDefault();
        let value = $(this).val().toLowerCase();
        $('.nota_hari_ini tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });

    });


    let transaksi_hari_ini = (divisi = "all") => {
        post("kasir/transaksi_hari_ini", {
            divisi
        }).then(res => {
            let html = '';
            let masuk = 0;
            let hutang = 0;
            let total = 0;

            res.data2.forEach((e, i) => {
                data_transaksi_hari_ini = res.data;
                masuk += parseInt(res.data[e.value]['masuk']['total']);
                hutang += parseInt(res.data[e.value]['hutang']['total']);
                total += parseInt(res.data[e.value]['masuk']['total'] - res.data[e.value]['hutang']['total']);

                html += `<tr style="cursor: pointer;" class="btn_transaksi_hari_ini" data-divisi="${e.value}">
                                <td class="text-center">${(i+1)}</td>
                                <td>${e.value}</td>
                                <td class="text-end">${(angka(res.data[e.value]['masuk']['total']))}</td>
                                <td class="text-end">${(angka(res.data[e.value]['hutang']['total']))}</td>
                                <td class="text-end">${(angka(res.data[e.value]['masuk']['total']-res.data[e.value]['hutang']['total']))}</td>
                            </tr>`;
            })

            html += `<tr>
                    <th class="text-center" colspan="2">TOTAL</th>
                    <th class="text-end">${angka(masuk)}</th>
                    <th class="text-end">${angka(hutang)}</th>
                    <th class="text-end">${angka(total)}</th>
                </tr>`;

            $(".transaksi_hari_ini").html(html);
        })
    }

    let tabel_transaksi_hari_ini = (divisi, data) => {
        let html = "";
        data.data.forEach((e, i) => {
            html += `<tr>
                        <td class="text-center">${(i+1)}</td>
                        <td class="text-center">${time_php_to_js(e.tgl)}</td>
                        <td>${divisi}</td>
                        <td>${e.barang}</td>
                        <td class="text-end">${angka(e.harga)}</td>
                        <td class="text-center">${e.qty}</td>
                        <td class="text-end">${angka(e.diskon)}</td>
                        <td class="text-end">${angka(e.total)}</td>
                    </tr>`;
        })

        return html;
    }
    $(document).on('click', '.btn_transaksi_hari_ini', function(e) {
        e.preventDefault();
        let divisi = $(this).data("divisi");
        let data = data_transaksi_hari_ini[divisi].masuk;
        let html = '<div class="container">';
        html += `<h6 class="judul_hutang"></h6>`;
        html += `<div class="d-flex gap-2 mb-2">
                        <a style="font-size:12px" data-order="masuk" data-divisi="${divisi}" class="filter btn btn-sm btn-primary">Masuk</a>
                        <a style="font-size:12px" data-order="hutang" data-divisi="${divisi}" class="filter btn btn-sm btn-secondary">Hutang</a>
                    </div>`;
        html += `<table class="table table-dark">
                <thead>
                    <tr>
                        <td class="text-center">Id</td>
                        <td class="text-center">Tgl</td>
                        <td class="text-center">Kat</td>
                        <td class="text-center">Barang</td>
                        <td class="text-center">Harga</td>
                        <td class="text-center">Qty</td>
                        <td class="text-center">Diskon</td>
                        <td class="text-center">Total</td>
                    </tr>
                </thead>
                <tbody class="data_hutang">`;
        html += `</tbody>
                </table>
                </div>`
        popupButton.html(html);

        $(".data_hutang").html(tabel_transaksi_hari_ini(divisi, data));

    });
    $(document).on('click', '.filter', function(e) {
        e.preventDefault();
        let divisi = $(this).data("divisi");
        let order = $(this).data("order");

        let data = data_transaksi_hari_ini[divisi][order];
        if (order == "masuk") {
            $(".filter[data-order='masuk']").removeClass("btn-secondary");
            $(".filter[data-order='masuk']").addClass("btn-primary");
            $(".filter[data-order='hutang']").addClass("btn-secondary");
            $(".filter[data-order='hutang']").removeClass("btn-primary");
        } else {
            $(".filter[data-order='masuk']").removeClass("btn-primary");
            $(".filter[data-order='masuk']").addClass("btn-secondary");
            $(".filter[data-order='hutang']").addClass("btn-primary");
            $(".filter[data-order='hutang']").removeClass("btn-secondary");
        }
        $(".data_hutang").html(tabel_transaksi_hari_ini(divisi, data));

    });

    $(document).on('keyup', '.cari_nota', function(e) {
        e.preventDefault();
        let value = $(this).val().toLowerCase();
        $('.nota_hari_ini tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });

    });


    $(document).on('keyup', '.cari_barang', function(e) {
        e.preventDefault();
        // console.log(mode_bayar);
        let value = $(this).val();
        let order = $('input[name="kategori"]:checked').val();
        let kategori = $('input[name="kategori"]:checked').val();

        post("kasir/cari_barang", {
            value,
            divisi: "<?= $divisi; ?>",
            order
            // mode_bayar
        }).then(res => {
            if (res.status == "200") {
                let html = '';

                if (res.data.length == 0) {
                    html += '<div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>';
                } else {
                    if (order == "Kantin" || order == "Barber") {
                        res.data.forEach(e => {
                            html += '<div class="select_barang" data-barang="' + e.barang + '" data-id="' + e.id + '" data-qty="' + e.qty + '" data-harga="' + e.harga + '">' + e.barang + (kategori == "Kantin" ? '/' + e.qty : "") + '</div>';
                        })

                    } else {
                        res.data.forEach(e => {
                            // if (mode_bayar) {
                            //     html += '<div class="select_barang" data-biaya="' + e.biaya + '" data-barang="' + e.perangkat + '" data-id="' + e.id + '" data-qty="' + e.durasi + '" data-harga="' + e.harga + '">' + e.perangkat + '</div>';
                            // } else {
                            html += '<div class="select_barang" data-biaya="' + e.biaya + '" data-barang="' + e.perangkat + '" data-id="' + e.id + '" data-qty="' + e.durasi + '" data-harga="' + e.harga + '">' + e.perangkat + ' - ' + e.mulai + ' - ' + e.jenis + '</div>';

                            // }
                        })

                    }

                }
                $(".data_barang").html(html);

            } else {
                message(res.status, res.message);
            }
        })


    });
    $(document).on('change', 'input[name="kategori"]', function(e) {
        e.preventDefault();
        let val = $(this).val();
        $('input[placeholder="Barang"]').focus();
        $('input[placeholder="Qty"]').val(1);
        $('input[placeholder="Diskon"]').val(0);
        $('input[placeholder="Barang"]').val("");
        if (val == "Kantin" || val == "Barber") {
            $('input[placeholder="Qty"]').removeAttr("disabled");
        } else {
            $('input[placeholder="Qty"]').prop("disabled", 'true');
        }
    });



    $(document).on('click', '.select_barang', function(e) {
        e.preventDefault();
        let barang = $(this).data("barang");
        let id = $(this).data("id");
        let qty = parseInt($(this).data("qty"));
        let harga = parseInt($(this).data("harga"));
        let biaya = parseInt($(this).data("biaya"));
        let kategori = $('input[name="kategori"]:checked').val();

        let sama = "";
        temp_data_transaksi = [];
        data_transaksi.forEach(e => {
            if (e.id == id && kategori == e.kategori) {
                e.qty += 1;
                sama = e.id;
            }
            temp_data_transaksi.push(e);
        })

        if (sama != "") {
            if (kategori == "Barber" || kategori == "Kantin") {
                data_transaksi = temp_data_transaksi;
                message("200", "Barang sudah ada, qty ditambah 1");
                body_tabel_transaksi();

            } else {
                message("400", "Barang sudah ada...");
            }
            $('input[placeholder="Qty"]').val(1);
            $('input[placeholder="Diskon"]').val(0);
            $(".cari_barang").val("");
            $(".data_barang").html("");
            return;
        }

        if (kategori == "Ps" || kategori == "Billiard") {
            // if (mode_bayar) {
            //     let html = `<div class="container">
            //     <h6 class="text-center">PILIH DURASI</h6>
            //         <div class="input-group d-flex justify-content-center">
            //             <select class="form-select durasi" style="max-width:200px">
            //                 <option selected value="">Durasi...</option>`;
            //     for (let i = 1; i < 10; i++) {
            //         html += `<option value="${i}">${i} Jam</option>`;

            //     }
            //     html += `</select>
            //             <button data-kategori="${kategori}" data-id="${id}" data-barang="${barang}" data-harga="${harga}" class="btn btn-outline-warning btn_durasi" type="button">Ok</button>
            //         </div>
            //     </div>`;
            //     popupButton.html(html);
            //     $(".data_barang").html("");
            //     return;
            // } else {
            barang_selected['total'] = $(this).data("biaya");
            barang_selected['qty'] = $(this).data("qty");
            $('input[placeholder="Qty"]').val(qty);
            $('input[placeholder="Qty"]').prop("disabled", 'true');
            $('input[placeholder="Diskon"]').focus();
            // }

        }

        barang_selected['kategori'] = kategori;
        barang_selected['id'] = id;
        barang_selected['barang'] = barang;
        barang_selected['harga'] = harga;


        $('input[placeholder="Qty"]').removeAttr("disabled");
        $('input[placeholder="Qty"]').focus();
        $('input[placeholder="Qty"]').val(1);
        if (kategori == "Kantin") {
            if (qty <= 0) {
                message('400', "Stok barang: " + angka(qty));
                return;
            }

        }

        $(".btn_tambah_data_transaksi").html('<i class="fa-solid fa-square-plus"></i> Tambahkan ' + "( " + angka(harga) + " )")
        $(".cari_barang").val(barang);
        $(".data_barang").html("");


    });

    // $(document).on('click', '.btn_durasi', function(e) {
    //     e.preventDefault();
    //     let durasi = $(".durasi").val();
    //     if (durasi == "" || durasi == "0") {
    //         message("400", "Durasi belum dipilih...");
    //         return;
    //     }
    //     durasi = parseInt(durasi) * 60;

    //     let total = parseInt($(this).data("harga")) * (parseInt(durasi) / 60);
    //     barang_selected['kategori'] = $(this).data("kategori");
    //     barang_selected['id'] = $(this).data("id");
    //     barang_selected['barang'] = $(this).data("barang");
    //     barang_selected['harga'] = $(this).data("harga");
    //     barang_selected['total'] = total;
    //     barang_selected['qty'] = durasi;
    //     barang_selected['tipe'] = mode_bayar;

    //     $('input[placeholder="Diskon"]').focus();
    //     $('input[placeholder="Qty"]').val(durasi);
    //     $('input[placeholder="Qty"]').prop("disabled", 'true');
    //     $('input[placeholder="Barang"]').val(barang_selected.barang);

    //     $(".btn_tambah_data_transaksi").html('<i class="fa-solid fa-square-plus"></i> Tambahkan ' + "( " + angka(total) + " )");
    //     let myModal = document.getElementById("fullscreen");
    //     let modal = bootstrap.Modal.getOrCreateInstance(myModal);
    //     modal.hide();
    // });
    $(document).on('keyup', '.cari_user', function(e) {
        e.preventDefault();
        let val = $(this).val();

        post("kasir/cari_user", {
            val
        }).then(res => {
            let html = "";
            if (res.data.length == 0) {
                html += '<div>Data tidak ditemukan!.</div>';
            }
            res.data.forEach(e => {
                html += '<div data-user_id="' + e.id + '" data-saldo="' + e.saldo + '"  data-role="' + e.role + '" class="select_user">' + e.nama + '</div>';
            })

            $(".data_user").html(html);
        })
    });


    $(document).on('click', '.select_user', function(e) {
        e.preventDefault();
        data_transaksi = [];
        pembeli = {};
        let nama = $(this).text();
        let user_id = $(this).data("user_id");
        let saldo = $(this).data("saldo");
        let role = $(this).data("role");

        $(".body_btn_del_user").html('<button class="btn btn btn-danger btn_del_user"><i class="fa-solid fa-circle-xmark"></i></button>');
        pembeli["user_id"] = user_id;
        pembeli["nama"] = nama;
        pembeli["saldo"] = saldo;
        pembeli["role"] = role;

        post("kasir/hutang", {
            pembeli
        }).then(res => {
            if (res.data[pembeli.user_id].length > 0) {
                let html = '<div class="container">';
                html += `<h6 class="judul_hutang"></h6>
                 <div class="d-grid">
                    <button class="btn btn btn-success btn_bayar_hutang"><i class="fa-solid fa-wallet"></i> BAYAR HUTANG</button>
                            </div>
                     <table class="table table-dark">
                <thead>
                    <tr>
                        <td class="text-center">Id</td>
                        <td class="text-center">Tgl</td>
                        <td class="text-center">Kat</td>
                        <td class="text-center">Barang</td>
                        <td class="text-center">Harga</td>
                        <td class="text-center">Qty</td>
                        <td class="text-center">Diskon</td>
                        <td class="text-center">Total</td>
                    </tr>
                </thead>
                <tbody class="data_hutang">`;
                let total = 0;
                res.data[pembeli.user_id].forEach((e, i) => {
                    total += parseInt(e.total);
                    html += `<tr>
                        <td class="text-center">${e.id}</td>
                        <td class="text-center">${time_php_to_js(e.tgl)}</td>
                        <td>${e.kategori}</td>
                        <td>${e.barang}</td>
                        <td class="text-end">${angka(e.harga)}</td>
                        <td class="text-center">${e.qty}</td>
                        <td class="text-end">${angka(e.diskon)}</td>
                        <td class="text-end">${angka(e.total)}</td>
                    </tr>`;
                })
                html += `</tbody>
                    </table>
                </div>`

                popupButton.html(html);
                $(".judul_hutang").text(pembeli.nama.toUpperCase() + " PUNYA HUTANG " + angka(total));
                $(".saldo_pembeli").text(" | " + angka(pembeli['saldo']) + " | HUTANG: " + angka(total));
                $(".cari_user").val(nama);
                $(".data_user").html("");
                body_transaksi();
                $('input[placeholder="Barang"]').focus();
            } else {
                $(".saldo_pembeli").text(" | " + angka(pembeli['saldo']));
                $(".cari_user").val(nama);
                $(".data_user").html("");
                body_transaksi();
                $('input[placeholder="Barang"]').focus();

            }
        })

    });

    $(document).on('click', '.btn_del_user', function(e) {
        e.preventDefault();
        pembeli = {};
        barang_selected = {};
        data_transaksi = [];
        $(".saldo_pembeli").text("");
        $('input[placeholder="Pembeli"]').val("");
        $('input[placeholder="Pembeli"]').focus();
        body_transaksi();
    });
    $(document).on('click', '.btn_tambah_data_transaksi', function(e) {
        e.preventDefault();

        if (pembeli.user_id == undefined) {
            message("400", "Pembeli kosong...");
            return;
        }
        if (barang_selected.id == undefined) {
            message("400", "Barang belum dipilih...");
            return;
        }
        let diskon = parseInt(str_replace(".", "", $('input[placeholder="Diskon"]').val()));
        let qty = parseInt(str_replace(".", "", $('input[placeholder="Qty"]').val()));

        barang_selected['diskon'] = diskon;
        if (barang_selected.kategori == "Kantin" || barang_selected.kategori == "Barber") {
            barang_selected['qty'] = qty;
            barang_selected['total'] = (parseInt(barang_selected['harga']) * qty) - diskon;
        } else {
            barang_selected['total'] = (parseInt(barang_selected['total'])) - diskon;
        }

        if (barang_selected['total'] < 0) {
            message("400", "Diskon terlalu besar...");
            $('input[placeholder="Diskon"]').val(angka(barang_selected.total));
            return;
        }

        barang_selected['tipe'] = "new";
        data_transaksi.push(barang_selected);

        $('input[placeholder="Barang"]').focus();
        $(".cari_barang").val("");
        $('input[placeholder="Qty"]').val(1);
        $('input[placeholder="Diskon"]').val(0);
        body_tabel_transaksi();
        barang_selected = {};
    });

    $(document).on('click', '.btn_del_barang_selected', function(e) {
        e.preventDefault();
        let index = $(this).data("i");
        let temp_data_transaksi = [];
        data_transaksi.forEach((e, i) => {
            if (i != index) {
                temp_data_transaksi.push(e);
            }
        })
        data_transaksi = temp_data_transaksi;
        body_tabel_transaksi();
    });

    $(document).on('click', '.btn_proses', function(e) {
        e.preventDefault();
        let html = "";
        html += `
        <div class="container">
            <div class="row">
                <div class="col-6">
                            <div class="text-center mb-3 border-bottom">
                                    <div class="fs-6">TOTAL</div>
                                    <h3 class="fw-bold total_pembayaran"></h3>
                                </div>`;
        html += `<div class="mb-2 text-center">
                            <label>Pembeli</label>
                            <input type="text" class="form-control text-center form-control" value="${pembeli.nama}" placeholder="Pembeli" readonly>
                        </div>`;
        html += `<div class="text-center mb-3">
                                    <span class="text_main">Uang Pembayaran</span>
                                    <input type="text" class="form-control text-center form-control uang_pembayaran angka" value="" placeholder="Uang pembayaran">
                                </div>
                                <div class="mt-2 text-center">
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
                                <button class="btn btn btn-success btn_transaksi"><i class="fa-solid fa-wallet"></i> TRANSAKSI</button>
                            </div>
                </div>
                <div class="col-6">
                       <h6>DETAIL</h6>
                     <table class="table table-dark">
                <thead>
                    <tr>
                        <td class="text-center">#</td>
                        <td class="text-center">Kat</td>
                        <td class="text-center">Barang</td>
                        <td class="text-center">Harga</td>
                        <td class="text-center">Qty</td>
                        <td class="text-center">Diskon</td>
                        <td class="text-center">Total</td>
                    </tr>
                </thead>
                <tbody>`;
        let total = 0;
        let hutang_exist = 0;
        data_transaksi.forEach((e, i) => {
            if (e.metode == "Hutang") {
                hutang_exist = 1;
            }
            total += parseInt(e.total);
            html += `<tr>
                        <td class="text-center">${(i+1)}</td>
                        <td>${e.kategori}</td>
                        <td>${e.barang}</td>
                        <td class="text-end">${angka(e.harga)}</td>
                        <td class="text-center">${e.qty}</td>
                        <td class="text-end">${angka(e.diskon)}</td>
                        <td class="text-end">${angka(e.total)}</td>
                    </tr>`;
        })

        // html += `<tr>
        //             <th class="text-center" colspan="6">TOTAL</th>
        //             <td class="text-end">${angka(total)}</td>
        //             <td class="text-center"><i class="fa-solid fa-ban"></i></td>
        //         </tr>`;

        html += `</tbody>
            </table>
                </div>
            </div>
        </div>`
        popupButton.html(html);
        if (total > pembeli.saldo) {
            $("input[name='metode_bayar'][value='Tap']").prop("disabled", true);
        }
        if (hutang_exist == 1) {
            $("input[name='metode_bayar'][value='Hutang']").removeAttr("checked");
            $("input[name='metode_bayar'][value='Hutang']").prop("disabled", true);
            $("input[name='metode_bayar'][value='Cash']").prop("checked", true);
        }

        $(".total_pembayaran").text(angka(total));
        $(".total_pembayaran").attr("data-total", total);
        $(".btn_transaksi").attr("data-total", total);
        $(".uang_lunas").val(angka(total));
        $(".uang_pembayaran").val(angka(total));

        $('#fullscreen').on('shown.bs.modal', function() {
            $('.uang_pembayaran').focus(); // Fokus ke input uang_pembayaran
        });


    });

    $(document).on('click', '.btn_bayar_hutang', function(e) {
        e.preventDefault();

        let rows = $(".data_hutang tr").map(function() {
            let rowArray = [];
            $(this).find("td").each(function() {
                rowArray.push($(this).text().trim()); // Tambahkan setiap <td> ke dalam array
            });
            return [rowArray]; // Kembalikan sebagai array per baris <tr>
        }).get();

        rows.forEach(e => {
            val = {
                id: e[0],
                kategori: e[2],
                metode: "Hutang",
                tipe: "exist",
                barang: e[3],
                harga: parseInt(str_replace(".", "", e[4])),
                qty: parseInt(str_replace(".", "", e[5])),
                diskon: parseInt(str_replace(".", "", e[6])),
                total: parseInt(str_replace(".", "", e[7]))
            }
            data_transaksi.push(val);

        })

        let myModal = document.getElementById("fullscreen");
        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        modal.hide();
        $('input[placeholder="Barang"]').focus();
        body_tabel_transaksi();
    })

    $(document).on('click', '.btn_transaksi', function(e) {
        e.preventDefault();
        let uang_pembayaran = parseInt(str_replace(".", "", $(".uang_pembayaran").val()));
        let total = parseInt($(this).data("total"));
        let metode = $('input[name="metode_bayar"]:checked').val();
        let lokasi = $(".latitude").val() + ',' + $(".longitude").val();

        if (uang_pembayaran < total) {
            message("400", "Uang pembayaran kurang...");
            $(".uang_pembayaran").val(angka(total));
            return;
        }

        if (petugas.user_id == undefined) {
            message("400", "Absen dulu...");
            return;
        }
        post("kasir/transaksi", {
            uang_pembayaran,
            data_transaksi,
            pembeli,
            metode,
            lokasi,
            jwt,
            // mode_bayar,
            petugas: petugas.user
        }).then(res => {
            message(res.status, res.message);
            if (res.status = "200") {
                let myModal = document.getElementById("fullscreen");
                let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.hide();
                if (metode != "Hutang") {

                    let html = "";
                    html += `<div class="container border border-light rounded p-2">
                                <div class="text-center mb-3">
                                    <span class="text_main fs-5">UANG KEMBALIAN</span>
                                    <div class="fw-bold fs-1 total_pembayaran">${angka(res.data)}</div>
                                </div>
                                <hr>`;
                    if (metode == "Tap") {
                        html += `<div class="text-center mb-3">
                        <span class="text_main fs-5">SALDO</span>
                        <div class="fw-bold fs-1">${angka(res.data3)}</div>
                    </div>
                    <hr>`;

                    }
                    html += `<div class="d-grid">
                            <a target="_blank" href="${res.data2}" class="btn fs-3 btn-sm btn-success"><i class="fa-regular fa-file-pdf"></i> Cetak Nota</a>
                            </div>
                    </div>`;

                    popupButton.html(html);

                    nota_hari_ini(res.data4);
                }

            }

            pembeli = {};
            barang_selected = {};
            data_transaksi = [];
            $(".saldo_pembeli").text("");
            $('input[placeholder="Pembeli"]').val("");
            $('input[placeholder="Pembeli"]').focus();
            body_transaksi();

            transaksi_hari_ini();

        })
    });

    $(document).on('click', '.btn_cetak_nota', function(e) {
        e.preventDefault();
        window.open($(this).data("link"), "_blank");
    });
</script>
<?= $this->endSection() ?>