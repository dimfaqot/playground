<?= $this->extend('templates/guest') ?>

<?= $this->section('content') ?>

<!-- Modal notif-->
<div class="modal fade" id="notif" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-5">
        <div class="modal-content">
            <div class="modal-body border text-center border-secondary bg-dark rounded modal_body_notif" style="margin-top: -100px;">
            </div>
        </div>
    </div>
</div>

<div style="margin-top: 60px;"></div>

<?php $x = 1; ?>
<div class="d-flex justify-content-center gap-2 border-bottom border-light my-4">
    <div>
        <a href="" class="text-danger menu"><i class="fa-regular fa-circle-dot"></i></a>
    </div>
    <div class="text-center"><?= strtoupper($judul); ?> <span class="shift"></span></div>
</div>


<div class="row g-2 body_rental text-center" style="font-size: small;">
    <?php foreach ($data['rental'] as $i): ?>
        <div class="col-3">
            <div data-id="<?= $i['id']; ?>" data-harga="<?= $i['harga']; ?>" data-perangkat="<?= $i['perangkat']; ?>" class="border rounded-circle <?= ($i['status'] == 1 ? "bg-secondary opacity-75 border-light btn_pembayaran" : ($i['metode'] == "Over" ? "bg-danger opacity-50 border-danger btn_pembayaran" :  "border-warning btn_rental")); ?>" style="padding:7px 0px;font-size:x-small">

                <?php $exp = explode(" ", $i['perangkat']); ?>
                <div style="margin-top: -3px;"><?= $exp[0]; ?></div>
                <div style="margin-top: -5px;" class="fs-3 fw-bold"><?= $exp[1]; ?></div>

                <div style="margin-top: -4px;"><?= ($i['status'] == 1 ? ($i['durasi'] == 0 ? "OPEN" : $i['jam'] . " H") : ($i['metode'] == "Over" ? "OVER" : "AVAILABLE")); ?></div>
                <div style="margin-top: -2px;"><?= str_replace("Jam", "H", str_replace("Menit", "M", $i['waktu'])); ?></div>
                <?php if ($i['status'] == 1 && $i['durasi'] == 0): ?>
                    <div style="font-style: italic;margin-top:-5px">passed</div>
                <?php elseif ($i['status'] == 1 && $i['durasi'] > 0): ?>
                    <div style="font-style: italic;margin-top:-5px">left</div>
                <?php else: ?>
                    <div style="font-style: italic;margin-top:-5px">...</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>
<div class="body_sos text-center" style="margin-top: 50px;">
    <div class="d-flex justify-content-center">
        <a href="" class="d-block btn_sos border text-danger border-danger rounded-circle" style="width: 55px;height:55px;text-decoration:none;padding-top:8px">
            <div style="font-size: large;"><i class="fa-solid fa-bullhorn"></i></div>
            <div style="font-size: x-small;">SOS</div>
        </a>
    </div>
</div>


<div class="fw-bold timer text-center" style="z-index: 999999999999;position:absolute;left:42%;margin-top:160px"></div>



<script>
    let divisions = <?= json_encode(options('Divisi')); ?>;
    let perangkats = <?= json_encode($data['perangkat']); ?>;
    let rentals = <?= json_encode($data['rental']); ?>;
    let div = "<?= $divisi; ?>";
    let judul = "<?= $judul; ?>";
    let user_tap = {};
    let shift = {};


    const successCb = (position) => {
        latitude = position.coords.latitude;
        longitude = position.coords.longitude;
        $(".latitude").val(latitude);
        $(".longitude").val(longitude);
        // $(".map").html('<iframe width="100%" height="600" src="https://maps.google.com/maps?q=-7.441695779343123,111.03870220698958&amp;z=15&amp;output=embed"></iframe>');
    }

    const errorCb = (error) => {
        console.error(error);
    }

    navigator.geolocation.getCurrentPosition(successCb, errorCb, {
        enableHighAccuracy: true,
        maximumAge: 0
    });

    let data_tapping = {};
    const timer = (limit = 30) => {
        let content = `<div>
                            <div class="position-relative mb-2" style="display: inline-block;">
                                <div class="spinner-border p-1 text_timer" style="width:55px;height:55px" role="status"></div>
                                <div class="position-absolute text_limit top-50 start-50 translate-middle" style="animation: none;font-size:18px;margin-top:-3px;">${limit}</div>
                            </div>
                            <div>${user_tap.nama}</div>
                            <div>${angka(user_tap.saldo)}</div>
                        </div>`;

        $(".timer").html(content);

        const looping = () => {
            if (limit >= 0) {
                if (data_tapping == null) {
                    clearInterval(duration);
                    $(".message_notif").addClass("text-danger");
                    $(".text_timer").addClass("text-danger");
                    $(".text_limit").addClass("text-danger");
                    $(".text_limit").text("Over");
                    user_tap = {}

                    setTimeout(() => {
                        $(".timer").html("");
                    }, 5000);
                    setTimeout(() => {
                        let myModal = document.getElementById("fullscreen");
                        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                        modal.hide();
                    }, 2000);
                } else {
                    $(".text_limit").text(limit--);
                }
            } else {
                clearInterval(duration);
                $(".message_notif").addClass("text-danger");
                $(".text_timer").addClass("text-danger");
                $(".text_limit").addClass("text-danger");
                $(".text_limit").text("Over");
                user_tap = {}

                setTimeout(() => {
                    $(".timer").html("");
                }, 5000);
                setTimeout(() => {
                    let myModal = document.getElementById("fullscreen");
                    let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                    modal.hide();
                }, 2000);
            }
        }

        const duration = setInterval(looping, 1000);
    }

    let status_absen = "";
    const main_timer = (limit = 15) => {

        const looping = () => {
            if (limit >= 0) {
                if (status_absen == "400") {
                    clearInterval(duration);
                    $(".message_notif").addClass("text-danger");
                    $(".text_timer").addClass("text-danger");
                    $(".text_limit").addClass("text-danger");
                    $(".text_limit").text("Over");
                    user_tap = {}
                    setTimeout(() => {
                        $(".modal_body_notif").html("");
                    }, 3000);
                    setTimeout(() => {
                        let myModal = document.getElementById("notif");
                        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                        modal.hide();
                    }, 2000);
                } else {
                    $(".text_limit").text(limit--);
                }
            } else {
                clearInterval(duration);
                $(".message_notif").addClass("text-danger");
                $(".text_timer").addClass("text-danger");
                $(".text_limit").addClass("text-danger");
                $(".text_limit").text("Over");
                user_tap = {}
                setTimeout(() => {
                    $(".modal_body_notif").html("");
                }, 5000);
                setTimeout(() => {
                    let myModal = document.getElementById("notif");
                    let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                    modal.hide();
                }, 2000);
            }
        }

        const duration = setInterval(looping, 1000);
    }

    const update_status_rentals = () => {
        let html = "";
        rentals.forEach(e => {
            let exp = e.perangkat.split(" ");
            html += `<div class="col-3">
                        <div data-id="${e.id}" data-harga="${e.harga}" data-perangkat="${e.perangkat}" class="border rounded-circle ${(e.status== 1 ? "bg-secondary opacity-75 border-light btn_pembayaran" : (e.metode == "Over" ? "bg-danger opacity-50 border-danger btn_pembayaran" :  "border-warning btn_rental"))}" style="padding:7px 0px;font-size:x-small">

                            <div style="margin-top: -3px;">${exp[0]}</div>
                            <div style="margin-top: -5px;" class="fs-3 fw-bold">${exp[1]}</div>

                            <div style="margin-top: -4px;">${(e.status == 1 ? (e.durasi == 0 ? "OPEN" : e.jam + " H") : (e.metode == "Over" ? "OVER" : "AVAILABLE"))}</div>
                            <div style="margin-top: -2px;">${str_replace("Jam", "H", str_replace("Menit", "M", e.waktu))}</div>`;

            if (e.status == 1 && e.durasi == 0) {
                html += `<div style="font-style: italic;margin-top:-5px">passed</div>`;
            } else if (e.status == 1 && e.durasi > 0) {
                html += `<div style="font-style: italic;margin-top:-5px">left</div>`;
            } else {
                html += `<div style="font-style: italic;margin-top:-5px">...</div>`;

            }

            html += `</div>
                    </div>`;


        })
        $(".body_rental").html(html);
    }

    const pembayaran = (val) => {
        let html = "";
        html += `<div class="container border border-light rounded p-2">
                        <div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">TOTAL</span>
                            <div data-total="${val.total}" class="fw-bold total_pembayaran">${angka(val.total)}</div>
                            <div class="fw-bold" style="font-size:12px">${val.waktu}</div>
                        </div>
                        <hr>
                        <div class="text-center mb-3" style="position: relative;">
                            <span class="text_main" style="font-size: small;">Pembeli</span>
                            <input type="text" class="mb-2 form-control cari_user text-center" value="" placeholder="Nama pembeli">
                            <div class="data_list"></div>
                        </div>`;
        if (val.ke == 0 || val.metode == "Over") {
            html += `<div class="text-center mb-3">
                                                <span class="text_main" style="font-size: small;">Diskon</span>
                                                <input type="text" class="mt-2 form-control diskon text-center angka" value="0" placeholder="Diskon">
                                            </div>`;
        }
        html += `<div class="text-center mb-3">
                            <span class="text_main" style="font-size: small;">Uang Pembayaran</span>
                            <input type="text" class="mt-2 form-control uang_pembayaran text-center angka" value="${angka(val.total)}" placeholder="Uang pembayaran">
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
                        <button class="btn btn-sm btn-success btn_transaksi" data-total="${val.total}" data-id="${val.id}"><i class="fa-solid fa-wallet"></i> OK</button>
                    </div>`;
        html += '</div>';


        popupButton.html(html);
    }

    const cek_notif = () => {
        post("iot/cek_notif", {
            grup: "<?= $data['grup']; ?>"
        }).then(res => {
            if (res.data) {
                perangkats = res.data.perangkat;
                rentals = res.data.rental;

                update_status_rentals();
            }

            data_tapping = res.data3;
            if (res.data3) {
                if (res.data3.kategori == "Tap") {
                    // user yang ngetap
                    user_tap = res.data3;
                    user_tap['nama'] = res.data3.user;
                    let limit = $(".text_limit").html();
                    if (limit == undefined) {
                        let html = `<div class="border-bottom border-warning mb-3">WELCOME <b>${res.data3.user.toUpperCase()}</b></div>
                                        <div style="font-size: x-small;">SALDO</div>
                                        <div><b>${angka(res.data3.saldo)}</b></div>`;
                        $(".modal_body_notif").html(html);
                        let myModal = document.getElementById("notif");
                        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                        modal.show();
                        timer();
                        setTimeout(() => {
                            modal.hide();
                        }, 2000);
                    }

                } else {
                    user_tap = {};
                }
            } else {
                user_tap = {};
            }

            // absen
            if (res.data4) {
                status_absen = res.data4.status;
                if (res.data4.status == "Tap") {
                    if (shift.user_id == undefined) {
                        $(".message_notif").text(res.data4.message);
                        setTimeout(() => {
                            shift = res.data4;
                            $(".modal_body_notif").html("");
                            let myModal2 = document.getElementById("notif");
                            let modal2 = bootstrap.Modal.getOrCreateInstance(myModal2);
                            modal2.hide();
                            $(".menu").removeClass("text-danger");
                            $(".menu").addClass("text-success");
                            $(".shift").text("- " + shift.user.toUpperCase() + " -");
                        }, 2000);
                    }
                } else {
                    let text_limit = parseInt($(".text_limit").text());
                    $(".message_notif").text(res.data4.message);
                    if (text_limit == 15) {
                        main_timer();
                    }
                }
            } else {
                shift = {};
                $(".menu").removeClass("text-success");
                $(".menu").addClass("text-danger");
                $(".shift").text("");
            }
            let html = "";
            html += `<div class="container">`;
            html += `</div>`;

            let sos = '';
            if (res.data2) {
                sos = `<div class="position-relative" style="display: inline-block;">
                                <div class="spinner-border text-danger p-1" style="width:55px;height:55px" role="status"></div>
                                <div class="position-absolute top-50 start-50 translate-middle" style="animation: none;font-size:10px;margin-top:-3px;">Waiting...</div>
                            </div>`;
            } else {
                sos = `<div class="d-flex justify-content-center">
                            <a href="" class="d-block btn_sos border text-danger border-danger rounded-circle" style="width: 55px;height:55px;text-decoration:none;padding-top:8px">
                                <div style="font-size: large;"><i class="fa-solid fa-bullhorn"></i></div>
                                <div style="font-size: x-small;">SOS</div>
                            </a>
                        </div>`;
            }
            $(".body_sos").html(sos);
        })
    }

    setInterval(() => {
        cek_notif();
    }, 5000);

    //menu admin
    $(document).on("click", ".menu", function(e) {
        e.preventDefault();
        let html = "";
        html += `<div class="container">`;
        html += `<ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation" style="font-size: small;">
                        <button class="nav-link text-secondary active px-2 py-1" data-bs-toggle="tab" data-bs-target="#absen" type="button" role="tab" aria-selected="true">Home</button>
                    </li>
                    <li class="nav-item" role="presentation" style="font-size: small;">
                        <button class="nav-link text-secondary px-2 py-1" data-bs-toggle="tab" data-bs-target="#perangkat" type="button" role="tab" aria-selected="true">Perangkat</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">`;

        html += `<div class="tab-pane fade show active" id="absen" role="tabpanel" aria-labelledby="home-tab" tabindex="0">`;

        divisions.forEach(e => {
            if (judul == "Billiard 1") {
                html += `<div class="d-grid mb-1"><button class="btn-secondary btn btn-sm btn_absen" data-divisi="${e.value}">${e.value}</button></div>`;
            } else {
                if (e.value == div) {
                    html += `<div class="d-grid mb-1"><button class="btn-secondary btn btn-sm btn_absen" data-divisi="${e.value}">${e.value}</button></div>`;
                }
            }

        });
        html += `</div>`;

        html += `<div class="tab-pane fade" id="perangkat" role="tabpanel" aria-labelledby="home-tab" tabindex="0">`;

        perangkats.forEach(e => {
            html += `<div class="d-grid mb-1"><button class="btn-dark ${(e.status==1?"border-light":"border-secondary text-secondary")} btn btn-sm btn_perangkat" data-id="${e.id}">${e.perangkat}</button></div>`;

        });
        html += `</div>`;


        html += `</div>`;
        html += `</div>`;

        popupButton.html(html);
    })

    //btn_perangkat
    $(document).on("click", ".btn_perangkat", function(e) {
        e.preventDefault();
        let id = $(this).data("id");

        if (shift.user_id == undefined) {
            message("400", "Admin belum absen");
            return;
        }
        if (user_tap.user_id == undefined) {
            message("400", "Tap dulu");
            return;
        }

        if (user_tap.role == "Member") {
            message("400", "Harus admin");
            return;
        }

        post("iot/update_perangkat", {
            id
        }).then(res => {
            if (res.status == "200") {
                message(res.status, res.message);
                setTimeout(() => {
                    let myModal = document.getElementById("fullscreen");
                    let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                    modal.hide();
                }, 1200);
            }
        })
    })
    //btn_pembayaran
    $(document).on("click", ".btn_pembayaran", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let val = {};
        rentals.forEach(e => {
            if (e.id == id) {
                val = e;
                data_transaksi = e;
            }
        })

        if (shift.user_id == undefined) {
            message("400", "Admin belum absen");
            return;
        }
        if (user_tap.user_id == undefined) {
            message("400", "Tap dulu");
            return;
        }

        if (user_tap.role == "Member") {
            message("400", "Harus admin");
            return;
        }

        if (val.role == "Member" && user_tap.role !== "Member" && val.durasi > 0) {
            let html = '';
            html += `<div class="border-bottom border-warning mb-2">Yakin akhiri?</div>
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button> <button data-id="${id}" class="btn btn-sm btn-success btn_akhiri">Ya, Akhiri</button>`;
            $(".modal_body_notif").html(html);
            let myModal = document.getElementById("notif");
            let modal = bootstrap.Modal.getOrCreateInstance(myModal);
            modal.show();
            return;
        }

        post("iot/akhiri", {
            val
        }).then(res => {
            if (res.status == "200") {
                if (val.durasi == 0) {
                    val['durasi'] = res.data.durasi
                    val['total'] = res.data.harga
                } else {
                    val['total'] = res.data.harga
                    val['waktu'] = "0" + (parseInt(res.data.durasi) / 60) + " Jam : 00 Menit";
                }

                pembayaran(val);
            }
        })
    })
    $(document).on("click", ".btn_akhiri", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let val = {};
        rentals.forEach(e => {
            if (e.id == id) {
                val = e;
                data_transaksi = e;
            }
        })

        if (shift.user_id == undefined) {
            message("400", "Admin belum absen");
            return;
        }
        if (user_tap.user_id == undefined) {
            message("400", "Tap dulu");
            return;
        }

        if (user_tap.role == "Member") {
            message("400", "Harus admin");
            return;
        }


        if (val.role == "Member" && user_tap.role !== "Member" && val.durasi > 0) {

            post("iot/afk", {
                id: val.id,
                divisi: div.toLowerCase(),
                grup: judul,
                petugas: user_tap.nama,
                order: "afk"
            }).then(res => {
                if (res.status == "200") {
                    message(res.status, res.message);

                    let myModal = document.getElementById("notif");
                    let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                    modal.hide();
                }
            })
        }

    })

    //sos
    $(document).on("click", ".btn_sos", function(e) {
        e.preventDefault();

        post("iot/sos", {
            grup: "<?= $data['grup']; ?>"
        }).then(res => {
            if (res.status == "200") {
                message(res.status, "Sukses...");
            }
        })
    })
    //absen
    $(document).on("click", ".btn_absen", function(e) {
        e.preventDefault();
        let divisi = $(this).data("divisi");

        post("iot/cek_absen", {
            divisi,
            grup: "<?= $data['grup']; ?>"
        }).then(res => {
            if (res.status == "200") {
                let myModal = document.getElementById("fullscreen");
                let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.hide();
                let html = `<div>
                                <div class="position-relative mb-2" style="display: inline-block;">
                                    <div class="spinner-border p-1 text_timer" style="width:55px;height:55px" role="status"></div>
                                    <div class="position-absolute text_limit top-50 start-50 translate-middle" style="animation: none;font-size:18px;margin-top:-3px;">15</div>
                                </div>
                            </div>
                        <div class="border-bottom border-warning" style="font-size:small">ABSEN ${divisi.toUpperCase()}</div>
                                <div class="mb-3">WELCOME <b>${res.data.user.toUpperCase()}</b></div>
                                <div style="font-size: x-small;">SALDO</div>
                                <div><b>${angka(res.data.saldo)}</b></div>
                                <div class="mt-3 message_notif">${res.message}</div>`;
                $(".modal_body_notif").html(html);
                let myModal2 = document.getElementById("notif");
                let modal2 = bootstrap.Modal.getOrCreateInstance(myModal2);
                modal2.show();
            } else {
                message(res.status, res.message);
            }
        })
    })

    //rental
    let transaksi = {};
    $(document).on("click", ".btn_rental", function(e) {
        e.preventDefault();
        if (shift.user_id == undefined) {
            message("400", "Admin belum absen");
            return;
        }
        if (user_tap == null || user_tap.user_id == undefined) {
            message("400", "Tap dulu");
            return;
        }
        let id = $(this).data("id");
        let perangkat = $(this).data("perangkat");
        let harga = parseInt($(this).data("harga"));
        transaksi['id'] = id;
        transaksi['perangkat'] = perangkat;
        transaksi['harga'] = harga;

        let html = "";
        html += `<div class="container text-center mt-4">`;
        html += '<h6 class="mb-4">DURASI (JAM)</h6>'
        html += '<div class="row g-5 mb-5">';
        for (let i = 1; i < 9; i++) {
            html += `<div class="col-3"><button class="btn-info opacity-50 fs-1 px-4 btn btn-sm btn_durasi" data-durasi="${i}">${i}</button></div>`;

        }
        html += `</div>`;
        html += `<button class="btn-warning opacity-50 fs-1 px-5 btn btn-sm btn_durasi" data-durasi="0">OPEN</button>`;
        html += `</div>`;
        html += '<div class="body_transaksi mt-5"></div>';
        html += `</div>`;

        popupButton.html(html);
    })
    $(document).on("click", ".btn_durasi", function(e) {
        e.preventDefault();
        let durasi = parseInt($(this).data("durasi"));
        transaksi['durasi'] = durasi;
        let html = "";
        html += '<div class="text-center" style="font-style:italic">Main ' + transaksi["perangkat"] + (durasi !== 0 ? " durasi " + durasi + " jam " + "[" + (angka(parseInt(transaksi.harga) * parseInt(transaksi.durasi))) + "]" : " OPEN") + '</div>';
        html += `<div class="d-grid mt-3">
                    <button data-order="Reguler" class="btn btn-sm btn-success py-2 fs-2 btn_play"><i class="fa-solid fa-cash-register"></i> OK</button>

                </div>`;

        $(".body_transaksi").html(html);
    })

    $(document).on("click", ".btn_play", function(e) {
        e.preventDefault();
        if (shift.user_id == undefined) {
            message("400", "Admin belum absen");
            return;
        }
        if (transaksi.durasi == undefined) {
            message("400", "Durasi belum dipilih");
            return;
        }
        if (user_tap.user_id == undefined) {
            message("400", "Tap dulu");
            return;
        }
        if (transaksi.durasi == 0 && user_tap.role == "Member") {
            message("400", "Play open harus Admin");
            return;
        }
        if ((transaksi.harga * transaksi.durasi) > user_tap.saldo) {
            if (user_tap.role == "Member") {
                message("400", "Saldo tidak cukup");
                return;
            }
        }

        let order = $(this).data("order");

        post("iot/csrf", {
            user_id: user_tap.user_id
        }).then(respond => {
            if (respond.status == "200") {
                let map = $(".latitude").val() + ',' + $(".longitude").val();
                post("iot/play", {
                    user_tap,
                    transaksi,
                    order,
                    map,
                    shift,
                    grup: "<?= $data['grup']; ?>",
                    csrf: respond.data
                }).then(res => {
                    message(res.status, res.message);
                    transaksi = {};
                    user_tap = {};

                    let myModal = document.getElementById("fullscreen");
                    let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                    modal.hide();

                    if (res.status == "200") {
                        let html = `<div class="border-bottom border-warning mb-3"><b>${res.message}</b></div>
                                <div style="font-size: x-small;">SALDO</div>
                                <div><b>${angka(res.data)}</b></div>`;
                        $(".modal_body_notif").html(html);
                        let myModal2 = document.getElementById("notif");
                        let modal2 = bootstrap.Modal.getOrCreateInstance(myModal2);
                        modal2.show();

                        setTimeout(() => {
                            modal2.hide();
                        }, 2000);

                    }
                })
            }
        })

    })



    $(document).on('keyup', '.cari_user', function(e) {
        e.preventDefault();
        let val = $(this).val();

        post("iot/cari_user", {
            val
        }).then(res => {
            let html = "";
            if (res.data.length == 0) {
                html += '<div>Data tidak ditemukan!.</div>';
            }
            res.data.forEach(e => {
                html += '<div data-user_id="' + e.id + '" data-saldo="' + e.saldo + '" class="select_user">' + e.nama + '</div>';
            })

            $(".data_list").html(html);
        })
    });


    let temp_user = {};
    $(document).on('click', '.select_user', function(e) {
        e.preventDefault();
        let nama = $(this).text();
        let user_id = $(this).data("user_id");
        let saldo = parseInt($(this).data("saldo"));
        let total_pembayaran = parseInt($(".total_pembayaran").data("total"));

        temp_user['user_id'] = user_id;
        temp_user['saldo'] = saldo;

        if (saldo < total_pembayaran) {
            $('input[name="metode_bayar"][value="Tap"]').prop('disabled', true);
        }

        $(".cari_user").val(nama);
        $(".data_list").html("");
    });

    $(document).on('keyup', '.diskon', function(e) {
        e.preventDefault();
        let total = parseInt($(".total_pembayaran").data("total"));
        let dsk = $(this).val();

        if (temp_user.user_id == undefined) {
            message("400", "Pembeli diisi dulu");
            $(this).val(0);
            return;
        }

        let diskon = 0;
        if (dsk !== "") {
            diskon = parseInt(str_replace(".", "", dsk));
        }

        let harga_terakhir = parseInt(total) - parseInt(diskon);

        if (parseInt(temp_user.saldo) < harga_terakhir) {

            $('input[name="metode_bayar"][value="Tap"]').prop('checked', false);
            $('input[name="metode_bayar"][value="Tap"]').prop('disabled', true);
            $('input[name="metode_bayar"][value="Hutang"]').prop('checked', true);
        } else {
            $('input[name="metode_bayar"][value="Tap"]').prop('disabled', false);;
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
            $(".total_pembayaran").text(angka(total) + " - " + angka(diskon) + " = " + angka(harga_terakhir));
        }

    });


    $(document).on('click', '.btn_transaksi', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let jml = parseInt($(this).data("total"));
        let pembeli = $(".cari_user").val();
        let diskon = parseInt(str_replace(".", "", $(".diskon").val()));
        let user_id = temp_user.id;
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
        if (shift.user_id == undefined) {
            message("400", "Admin belum absen!.");
            return;
        }
        if (user_tap.user_id == undefined) {
            message("400", "Tap dulu!.");
            return;
        }
        if (user_tap.role == "Member") {
            message("400", "Harus admin!.");
            return;
        }

        if (uang_pembayaran < total) {
            message("400", "Uang kurang!.");
            $(".uang_pembayaran").val(angka(total));
            return;
        }
        if (metode == "Tap") {
            let data_transaksi = {};
            data_transaksi["user_id"] = temp_user.user_id;
            data_transaksi["pembeli"] = pembeli;
            data_transaksi["diskon"] = diskon;
            data_transaksi["id"] = id;
            metode_tap(data_transaksi, "Bayar");
            return;
        }
        post("iot/transaksi", {
            id,
            diskon,
            uang_pembayaran,
            div,
            grup: judul,
            petugas: user_tap.nama,
            user_id: temp_user.user_id,
            metode
        }).then(res => {
            user_tap = {};
            temp_user = {};
            if (res.status == "200") {
                let myModal = document.getElementById("fullscreen");
                let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.hide();

                let html = "";
                html += `<div class="container border border-light rounded p-2">
                                <div class="text-center mb-3">
                                    <span class="text_main" style="font-size: small;">UANG KEMBALIAN</span>
                                    <div data-total="${res.data}" class="fw-bold">${angka(res.data)}</div>
                                </div>
                                <hr>`;

                popupButton.html(html);
                setTimeout(() => {
                    myModal.hide();
                }, 3000);
            } else {
                message("400", res.message);
            }

        })

    });
</script>

<?= $this->endSection() ?>