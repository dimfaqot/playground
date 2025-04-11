<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>
<div class="map_lokasi_saya" style="padding: 0% 5%;"></div>

<div class="text-center mb-3" style="margin-top: 80px;">WELCOME <b><?= strtoupper(user()['nama']); ?></b></div>
<div class="mb-2">
    <a class="link_main px-2 pb-1 rounded btn_header" data-order="listrik" style="font-size: small;">Listrik</a>
    <a class="link_main px-2 pb-1 rounded btn_header" data-order="poin" style="font-size: small;">Poin</a>
    <a class="link_main px-2 pb-1 rounded btn_header" data-order="bisyaroh" style="font-size: small;"><?= bulan(date('m'))['bulan']; ?></a>
    <a class="link_main px-2 pb-1 rounded btn_iklan" style="font-size: small;">Iklan</a>
</div>

<div class="row g-2">
    <?php foreach (options('Divisi') as $i): ?>
        <div class="col-md-6">
            <div class="d-flex justify-content-between bg_secondary p-2 head_<?= $i['value']; ?>" style="border-radius:10px 10px 0px 0px">

            </div>
            <div class="p-2 border_main" style="border-radius: 0px 0px 10px 10px;">
                <canvas id="chart_<?= $i['value']; ?>" style="width:90%;"></canvas>
            </div>
        </div>
    <?php endforeach; ?>
</div>



<script>
    const bulans = <?= json_encode(bulan()); ?>;
    const tahuns = <?= json_encode(get_tahun()); ?>;
    const divisi = <?= json_encode(options('Divisi')); ?>;


    let data_bulanan = {};
    const bulanan = (tahun, bulan, kategori) => {
        let bln = bulan;
        bulans.forEach(e => {
            if (e.satuan == bulan) {
                bln = e.bulan.toUpperCase();
            }
        })
        post("home/statistik_bulanan", {
            tahun,
            bulan,
            kategori
        }).then(res => {
            data_bulanan = res.data;
            let html = '<div class="container tabel_bulanan">';
            html += tabel("masuk", tahun, bln, kategori);
            html += '</div>';
            popupButton.html(html);
        })
    }

    const tabel = (order, tahun, bulan, kategori) => {
        let html = '';
        html += `<div class="text-center mb-3">
                        <h6>${kategori.toUpperCase()} BULAN ${bulan} TAHUN ${tahun}</h6>
                        <span class="text_main" style="font-size: small;">TOTAL</span>
                        <div class="fw-bold">${angka(data_bulanan[order].total)}</div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="masuk" class="filter btn btn-sm ${(order=="masuk"?"link_secondary":"link_main")}">Masuk</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="keluar" class="filter btn btn-sm ${(order=="keluar"?"link_secondary":"link_main")}">Keluar</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="tap" class="filter btn btn-sm ${(order=="tap"?"link_secondary":"link_main")}">Tap</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="qris" class="filter btn btn-sm ${(order=="qris"?"link_secondary":"link_main")}">Qris</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="hutang" class="filter btn btn-sm ${(order=="hutang"?"link_secondary":"link_main")}">Hutang</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="laporan" class="filter btn btn-sm ${(order=="laporan"?"link_secondary":"link_main")}">Laporan</a>
                    </div>`;

        html += `<div class="input-group input-group-sm mb-3">
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
                <th class="text-center">Qty</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody class="tabel_search">`;
        data_bulanan[order].data.forEach((e, i) => {
            html += `<tr>
            <td class="text-center">${(i+1)}</td>
                <td class="text-center">${time_php_to_js(e.tgl,"d")}</td>
            <td>${e.barang}</td>
            <td class="text-end">${angka(e.harga)}</td>
            <td class="text-center">${angka(e.qty)}</td>
            <td class="text-end">${angka(e.total)}</td>
            </tr>`;
        })

        html += `</tbody>
        </table>`;
        return html;
    }

    const head = (data, kategori, tahun) => {
        let html = '';
        html += `<div style="font-size: 10px;">
                    <div style="font-size:16px"><i class="fa-regular fa-file-lines"></i> ${kategori}</div>
                    <div style="font-weight: normal;"></div>
                </div>
                <div class="d-flex gap-1">
                <div><a href="" data-masuk="${data[kategori].masuk}" data-keluar="${data[kategori].keluar}" data-qris="${data[kategori].qris}" data-tap="${data[kategori].tap}" data-hutang="${data[kategori].hutang}" data-koperasi="${data[kategori].koperasi}" class="link_main rounded btn btn-sm btn_detail_keuangan">${angka(data[kategori].total_data_keuangan)}</a></div>
                <div>
                    <select style="font-size: small;" class="form-select form-select-sm tahun" data-kategori="${kategori}">`;
        tahuns.forEach(t => {
            html += `<option ${(t==tahun?"selected":"")} value="${t}">${t}</option>`;
        })
        html += `<option ${(tahun=="All"?"selected":"")} value="All">All</option>`;
        html += `</select>`;
        html += `</div>
                </div>`;

        return html;
    }
    const chart_html = (data, tahun) => {
        let valueY = [];
        bulans.forEach(e => {
            valueY.push(e.satuan);
        });

        divisi.forEach(div => {
            if (data[div.value] !== undefined) {

                $(".head_" + div.value).html(head(data, div.value, tahun));

                new Chart("chart_" + div.value, {
                    type: "line",
                    data: {
                        labels: valueY,
                        datasets: [{
                            fill: false,
                            lineTension: 0,
                            backgroundColor: "white",
                            borderColor: "grey",
                            data: data[div.value].data
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        onClick: (e, values) => {

                            let bulan = values[0]['_index'] + 1;
                            bulanan(tahun, bulan, div.value);
                        }
                    }
                })

            }
        })
    }

    $(document).on('click', '.filter', function(e) {
        e.preventDefault();

        $(this).closest('div').find('.filter').removeClass('link_secondary').addClass('link_main');
        // Add the desired class to the clicked button
        $(this).removeClass('link_main').addClass('link_secondary');

        let tahun = $(this).data("tahun");
        let bulan = $(this).data("bulan");
        let kategori = $(this).data("kategori");
        let order = $(this).data("order");

        if (order == "laporan") {
            laporan(tahun, bulan, kategori);
            return;
        }
        $(".tabel_bulanan").html(tabel(order, tahun, bulan, kategori));

    })
    $(document).on('change', '.tahun', function(e) {
        e.preventDefault();
        let tahun = $(this).val();
        let kategori = $(this).data("kategori");

        statistik(tahun, kategori);
    })

    let data_laporan = [];
    const tabel_laporan = (order, tahun, bulan, kategori) => {
        let html = "";
        html += `<div class="text-center mb-3">
                        <h6>${kategori.toUpperCase()} BULAN ${bulan} TAHUN ${tahun}</h6>
                        <span class="text_main" style="font-size: small;">TOTAL</span>
                        <div class="fw-bold">${angka(data_laporan[order].total)}</div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="masuk" class="filter btn btn-sm link_main">Masuk</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="keluar" class="filter btn btn-sm link_main">Keluar</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="tap" class="filter btn btn-sm link_main">Tap</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="qris" class="filter btn btn-sm link_main">Qris</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="hutang" class="filter btn btn-sm link_main">Hutang</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="laporan" class="filter btn btn-sm link_main">Laporan</a>
                    </div>`;

        html += `<div class="d-flex gap-2 mb-3">
                            <div>
                                <select class="form-select form-select-sm divisi laporan laporan_tahun" data-kategori="${kategori}" style="font-size: small;">`;
        tahuns.forEach(e => {
            html += '<option value="' + e + '" ' + (e == tahun ? "selected" : "") + '>' + e + '</option>';

        })
        html += '<option value="All" ' + (tahun == "All" ? "selected" : "") + '>All</option>';
        html += `</select>
                            </div>
                            <div>
                                <select class="form-select form-select-sm divisi laporan laporan_bulan" data-kategori="${kategori}" style="font-size: small;">`;
        bulans.forEach(e => {
            html += '<option value="' + e.bulan.toUpperCase() + '" ' + (bulan == e.bulan.toUpperCase() ? "selected" : "") + '>' + e.bulan + '</option>';
        })
        html += `</select>
        </div>
        <div><a target="_blank" class="form-control form-control-sm text_main link_main" href="${data_laporan.jwt}"><i class="fa-solid fa-file-pdf"></i> Print</a></div>

                        </div>`;

        html += ` <div class="d-flex gap-2 mb-2">
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="masuk" class="filter_laporan btn btn-sm ${(order=="masuk"?"link_secondary":"link_main")}">Masuk</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="keluar" class="filter_laporan btn btn-sm ${(order=="keluar"?"link_secondary":"link_main")}">Keluar</a>
                        <a style="font-size:12px" data-tahun="${tahun}" data-bulan="${bulan}" data-kategori="${kategori}" data-order="koperasi" class="filter_laporan btn btn-sm ${(order=="koperasi"?"link_secondary":"link_main")}">Koperasi</a>
                    </div>`;

        html += `<div class="input-group input-group-sm mb-3">
                    <span class="input-group-text bg_main border_main text_main">Cari Data</span>
                    <input type="text" class="form-control cari bg_main border border_main text_main" placeholder="....">
                </div>
        <table class="table table-sm bg_main text_main border_main table-bordered" style="font-size: 12px;">
        <thead>
            <tr>
                <th class="text-center">#</th>  
                <th class="text-center">Tgl</th>`;
        if (order === "koperasi") {
            html += `<th class="text-center">Kategori</th>`;
        } else {
            html += `<th class="text-center">Barang</th>`;
            html += `<th class="text-center">Qty</th>`;
        }
        html += `<th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody class="tabel_search">`;
        data_laporan[order].data.forEach((e, i) => {
            html += `<tr>
            <td class="text-center">${(i+1)}</td>
            <td class="text-center">${time_php_to_js(e.tgl,"d")}</td>`;
            if (order === "koperasi") {
                html += `<td>${e.kategori}</td>`;
            } else {
                html += `<td>${e.barang}</td>`;
                html += `<td class="text-center">${angka(e.qty)}</td>`;
            }
            html += `<td class="text-end">${angka(e.total)}</td>
            </tr>`;
        })
        html += `<tr>
                <th class="text-center" colspan="${(order=="koperasi"?"3":"4")}">TOTAL</th>
                <th class="text-end">${angka(data_laporan[order].total)}</th>
                </tr>`;

        html += `</tbody>
        </table>`;

        $(".tabel_bulanan").html(html);
    }

    const statistik = (tahun, kategori) => {
        post("home/statistik", {
            tahun,
            kategori
        }).then(res => {
            chart_html(res.data, tahun);
        })
    }
    const laporan = (tahun, bulan, kategori) => {
        post("home/laporan", {
            tahun,
            bulan,
            kategori
        }).then(res => {
            if (res.status == "200") {
                data_laporan = res.data;
                tabel_laporan("masuk", tahun, bulan, kategori);
            }
        })
    }

    $(document).on("click", ".filter_laporan", function(e) {
        e.preventDefault();
        $(this).closest('div').find('.filter_laporan').removeClass('link_secondary').addClass('link_main');
        // Add the desired class to the clicked button
        $(this).removeClass('link_main').addClass('link_secondary');



        let tahun = $(this).data("tahun");
        let bulan = $(this).data("bulan");
        let kategori = $(this).data("kategori");
        let order = $(this).data("order");
        tabel_laporan(order, tahun, bulan, kategori);
    })
    $(document).on("change", ".laporan", function(e) {
        e.preventDefault();

        let tahun = $(".laporan_tahun").val();
        let kategori = $(".laporan_tahun").data("kategori");
        let bulan = $(".laporan_bulan").val();
        laporan(tahun, bulan, kategori);
    })
    let data_poin = (data, data2, data3) => {
        let html = '';

        if (data2) {
            html += `<div>
                            <select style="font-size: small;" class="form-select form-select-sm change_poin_user">`;
            data2.forEach(e => {
                html += `<option ${(e.id==data3?"selected":"")} value="${e.id}">${e.nama}</option>`;
            })
            html += `</select>`;
            html += `</div>`;

        }

        html += `<a class="link_main px-2 rounded btn_header" style="font-size: small;">POIN: ${angka(data.total)}</a>
                            <div class="input-group input-group-sm mt-1 mb-3">
                                <span class="input-group-text bg_main border_main text_main">Cari Data</span>
                                <input type="text" class="form-control cari bg_main border border_main text_main" placeholder="....">
                            </div>
                            <table class="table table-sm bg_main text_main border_main table-bordered" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Tgl</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Poin</th>
                                    <th class="text-center">Ket</th>
                                </tr>
                            </thead>
                            <tbody class="tabel_search">`;
        data.data.forEach((e, i) => {
            html += `<tr>
                                <td class="text-center">${(i+1)}</td>
                                    <td class="text-center">${time_php_to_js(e.tgl,"d")}</td>
                                <td>${e.kategori}</td>
                                <td class="text-end">${angka(e.poin)}</td>
                                <td>${e.disiplin}</td>
                                </tr>`;
        })

        html += `</tbody>
                            </table>`;

        return html;
    }
    let data_bisyaroh = (data, data2, data3) => {

        let html = '';

        html += `<div class="d-flex gap-2 mb-3">
                    <div>
                            <select style="font-size: small;" class="form-select form-select-sm change_bisyaroh tahun_bisyaroh">`;
        tahuns.forEach(e => {
            html += `<option ${(e==data3?"selected":"")} value="${e}">${e}</option>`;
        })
        html += `</select>
        </div>
        <div>
                            <select style="font-size: small;" class="form-select form-select-sm change_bisyaroh bulan_bisyaroh">`;
        bulans.forEach(e => {
            html += `<option ${(e.angka==data2?"selected":"")} value="${e.angka}">${e.bulan}</option>`;
        })
        html += `</select>
        </div>`;

        html += `</div>`;



        html += `<table class="table table-sm bg_main text_main border_main table-bordered" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Menit</th>
                                    <th class="text-center">Jam</th>
                                    <th class="text-center">Jml</th>
                                </tr>
                            </thead>
                            <tbody class="tabel_search">`;

        html += `<tr>
                    <td class="text-center">1</td>
                    <td>Billiard</td>
                    <td class="text-end">${angka(data.billiard.minutes)}</td>
                    <td class="text-end">${angka(data.billiard.hours)}</td>
                    <td class="text-end">${angka(data.billiard.total)}</td>
                    </tr>
                    
                    <tr>
                    <td class="text-center">2</td>
                    <td>Ps</td>
                    <td class="text-end">${angka(data.ps.minutes)}</td>
                    <td class="text-end">${angka(data.ps.hours)}</td>
                    <td class="text-end">${angka(data.ps.total)}</td>
                    </tr>
                    
                    <tr>
                    <th class="text-center" colspan="2">TOTAL</th>
                    <th class="text-end">${angka(data.ps.minutes+data.billiard.minutes)}</th>
                    <th class="text-end">${angka(data.ps.hours + data.billiard.hours)}</th>
                    <th class="text-end">${angka(data.ps.total +data.billiard.total)}</th>
                    </tr>`;

        html += `</tbody>
                            </table>`;

        return html;
    }
    $(document).on("click", ".btn_header", function(e) {
        e.preventDefault();
        let order = $(this).data("order");
        post("home/header", {
            order,
            user_id: "",
            bulan: "<?= date('m'); ?>",
            tahun: "<?= date('Y'); ?>",
        }).then(res => {
            if (res.status == "200") {
                if (order == "listrik") {
                    let html = `<div class="container">
                                    <div class="mb-2" style="font-size:12px">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="header_bersih" type="radio" value="Kotor" ${(res.data.bersih=="Kotor"?"checked":"")}>
                                            <label class="form-check-label">Kotor</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="header_bersih" type="radio" value="Bersih" ${(res.data.bersih=="Bersih"?"checked":"")}>
                                            <label class="form-check-label">Bersih</label>
                                        </div>
                                    </div>
                                     <div class="mb-2">
                                        <label style="font-size: 12px;">Listrik</label>
                                        <input placeholder="Sisa listrik" type="text" value="${(res.data.listrik)}" class="form-control form-control-sm header_listrik">
                                    </div>

                                     <div class="d-grid">
                                        <a class="link_main text-center rounded py-1 btn_update_header"><i class="fa-solid fa-floppy-disk"></i> Save</a>
                                    </div>
                                </div>`;
                    popupButton.html(html);
                }
                if (order == "poin") {
                    let html = `<div class="container body_poin">`;
                    html += data_poin(res.data, res.data2, res.data3);
                    html += '</div>';
                    popupButton.html(html);
                }
                if (order == "bisyaroh") {
                    let html = `<div class="container body_bisyaroh">`;
                    html += data_bisyaroh(res.data, res.data2, res.data3);
                    html += '</div>';
                    popupButton.html(html);
                }
            }
        })
    })
    $(document).on("change", ".change_poin_user", function(e) {
        e.preventDefault();
        let user_id = $(this).val();
        post("home/header", {
            order: "poin",
            user_id
        }).then(res => {
            if (res.status == "200") {
                $(".body_poin").html(data_poin(res.data, res.data2, res.data3));
            }
        })
    })
    $(document).on("change", ".change_bisyaroh", function(e) {
        e.preventDefault();
        let tahun = $(".tahun_bisyaroh").val();
        let bulan = $(".bulan_bisyaroh").val();
        post("home/header", {
            order: "bisyaroh",
            tahun,
            bulan
        }).then(res => {
            if (res.status == "200") {
                $(".body_bisyaroh").html(data_bisyaroh(res.data, res.data2, res.data3));
            }
        })
    })
    $(document).on("click", ".btn_update_header", function(e) {
        e.preventDefault();
        let bersih = $('input[name="header_bersih"]:checked').val();
        let listrik = $(".header_listrik").val();

        if (bersih == "Kotor") {
            message("400", "Harus bersih...");
            return;
        }
        if (listrik == "") {
            message("400", "Listrik harus diisi...");
            return;
        }

        if (!listrik.includes('/')) {
            message("400", 'Harus dipisah: "/"');
            return;
        }

        post("home/update_header", {
            bersih,
            listrik
        }).then(res => {
            message(res.status, res.message);
        })
    })

    $(document).on("click", ".btn_iklan", function(e) {
        e.preventDefault();
        let order = $(this).data("order");

        let html = `<div class="container">
                    <img src="<?= base_url('files/iklan.jpg'); ?>" class="img-fluid" alt="Iklan">
        
                    <form class="mt-2" action="<?= base_url('home/upload_file'); ?>" method="post" enctype="multipart/form-data">
                        <div class="input-group input-group-sm">
                        <input name="file" type="file" class="form-control bg_main text_main border_main" aria-label="Upload">
                        <button class="link_main rounded border_main" type="submit">Save</button>
                        </div>
                    </form>
                    </div>`;
        popupButton.html(html);
    })

    $(document).on("click", ".btn_detail_keuangan", function(e) {
        e.preventDefault();
        let masuk = parseInt($(this).data("masuk"));
        let keluar = parseInt($(this).data("keluar"));
        let hutang = parseInt($(this).data("hutang"));
        let tap = parseInt($(this).data("tap"));
        let qris = parseInt($(this).data("qris"));
        let koperasi = parseInt($(this).data("koperasi"));
        let html = `<div class="container">
                    <div class="mb-2">
                        <label style="font-size:12px">Masuk</label>
                        <input type="text" class="form-control form-control-sm" value="${angka(masuk)}" readonly>
                    </div>
                    <div class="mb-2">
                        <label style="font-size:12px">Keluar</label>
                        <input type="text" class="form-control form-control-sm" value="${angka(keluar)}" readonly>
                    </div>
                    <div class="mb-2">
                        <label style="font-size:12px">Koperasi</label>
                        <input type="text" class="form-control form-control-sm" value="${angka(koperasi)}" readonly>
                    </div>
                    <div class="mb-2">
                        <label style="font-size:12px">Hutang</label>
                        <input type="text" class="form-control form-control-sm" value="${angka(hutang)}" readonly>
                    </div>
                    <div class="mb-2">
                        <label style="font-size:12px">Tap</label>
                        <input type="text" class="form-control form-control-sm" value="${angka(tap)}" readonly>
                    </div>
                    <div class="mb-2">
                        <label style="font-size:12px">Qris</label>
                        <input type="text" class="form-control form-control-sm" value="${angka(qris)}" readonly>
                    </div>
                    <div class="mb-2 bg_secondary rounded p-1 mt-2">
                         <label style="font-size:12px">Masuk - Keluar</label>
                         <div>${angka(masuk-keluar)}</div>
                    </div>
                    <div class="mb-2 bg_secondary rounded p-1 mt-2">
                         <label style="font-size:12px">Masuk - Keluar - Koperasi</label>
                         <div>${angka(masuk-keluar-koperasi)}</div>
                    </div>
                    <div class="mb-2 bg_secondary rounded p-1 mt-2">
                         <label style="font-size:12px">(Masuk + Tap + Qris) - Keluar</label>
                         <div>${angka((masuk+tap+qris)-keluar)}</div>
                    </div>
                    <div class="mb-2 bg_secondary rounded p-1 mt-2">
                         <label style="font-size:12px">(Masuk + Hutang + Tap + Qris) - Keluar</label>
                         <div>${angka((masuk+hutang+tap+qris)-keluar)}</div>
                    </div>
                    <div class="mb-2 bg_secondary rounded p-1">
                         <label style="font-size:12px">(Masuk + Hutang + Tap + Qris + Koperasi) - Keluar</label>
                         <div>${angka((masuk+hutang+tap+qris+koperasi)-keluar)}</div>
                    </div>
                    </div>`;
        popupButton.html(html);
    })

    statistik(<?= date('Y'); ?>, "All");
</script>
<?= $this->endSection() ?>