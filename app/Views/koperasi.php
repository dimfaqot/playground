<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<h6><i class="<?= menu()['icon']; ?>"></i> <?= strtoupper(menu()['menu']); ?></h6>
<button style="font-size: small;" data-bs-toggle="modal" data-bs-target="#modal_add" class="link_main text_main rounded px-2 py-1 add_data"><i class="fa-solid fa-circle-plus"></i> Tambah Data</b></button>
<button style="font-size: small;" data-bs-toggle="modal" data-bs-target="#modal_add_pencairan" class="link_main text_main rounded px-2 py-1 add_data opacity-50"><i class="fa-solid fa-circle-plus"></i> Pencairan</b></button>
<div>
    <button style="font-size: small;" class="link_secondary mt-1 rounded px-2 py-1 mb-2 btn_detail"><?= angka($masuk); ?> - <?= angka($keluar); ?> = <?= angka($masuk - $keluar); ?></button>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_add" tabindex="-1" aria-labelledby="fullscreenLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">
            <div class="header text-center mt-5">
                <a href="" role="button" data-bs-dismiss="modal" class="text-danger fs-4"><i class="fa-solid fa-circle-xmark"></i></a>
            </div>
            <div class="modal-body modal-fullscreen">
                <div class="container">
                    <form action="<?= base_url(menu()['controller']); ?>/add" method="post">
                        <div class="mb-3">
                            <label style="font-size: 12px;">Kategori</label>
                            <select class="form-select form-select-sm mb-3" name="kategori">
                                <?php foreach (options('Divisi') as $i): ?>
                                    <option <?= ($i['value'] == 'Billiard' ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Uang</label>
                            <input placeholder="Uang" type="text" name="uang" class="form-control form-control-sm angka" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Pj</label>
                            <input placeholder="Pj" type="text" name="pj" class="form-control form-control-sm" required>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-sm btn-secondary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- pencairan -->
<div class="modal fade" id="modal_add_pencairan" tabindex="-1" aria-labelledby="fullscreenLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">
            <div class="header text-center mt-5">
                <a href="" role="button" data-bs-dismiss="modal" class="text-danger fs-4"><i class="fa-solid fa-circle-xmark"></i></a>
            </div>
            <div class="modal-body modal-fullscreen">
                <div class="container">
                    <form action="<?= base_url(menu()['controller']); ?>/add_pencairan" method="post">
                        <input type="hidden" class="add_max" name="max" value="<?= ((int)$detail['Admin']['masuk']['total']) - (int)$detail['Admin']['keluar']['total']; ?>">
                        <div class="mb-3">
                            <label style="font-size: 12px;">Kategori</label>
                            <select class="form-select form-select-sm mb-3 add_saham" name="saham">
                                <?php foreach (options('Saham') as $i): ?>
                                    <option <?= ($i['value'] == 'Admin' ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Pemberi</label>
                            <input placeholder="Pemberi" type="text" name="pemberi" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Penerima</label>
                            <input placeholder="Penerima" type="text" name="penerima" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Lokasi</label>
                            <input placeholder="Lokasi" type="text" name="lokasi" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Uang</label>
                            <input placeholder="Uang" type="text" name="uang" class="form-control form-control-sm angka add_uang" required>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-sm btn-secondary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php if (count($data) == 0): ?>
    <div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>
<?php else: ?>
    <div class="input-group input-group-sm mb-3">
        <span class="input-group-text bg_main border_main text_main">Cari Data</span>
        <input type="text" class="form-control cari bg_main border border_main text_main" placeholder="....">
    </div>
    <table class="table table-sm table-bordered bg_main text_main" style="font-size: 14px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Tgl</th>
                <th class="text-center">Kategori</th>
                <th class="text-center">Uang</th>
                <th class="text-center">Act</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <td class="text-center"><?= $k + 1; ?></td>
                    <td class="text-center"><?= date('d/m/Y', $i['tgl']); ?></td>
                    <td><?= $i['kategori']; ?></td>
                    <td class="text-end"><?= angka($i['uang']); ?></td>
                    <td class="text-center"><a href="" role="button" class="text-danger fs-6 btn_confirm btn_confirm_<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-id="<?= $i['id']; ?>"><i class="fa-solid fa-trash-can"></i></a> <a role="button" class="text-warning fs-6 btn_update" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-square-pen"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    let data = <?= json_encode($data); ?>;
    let options = <?= json_encode(options('Divisi')); ?>;
    let details = <?= json_encode($detail); ?>;
    let saham_selected = parseInt(details['Admin'].masuk.total) - parseInt(details['Admin'].keluar.total);


    $(document).on("change", ".add_saham", function(e) {
        e.preventDefault();
        let val = $(this).val();
        saham_selected = parseInt(details[val].masuk.total) - parseInt(details[val].keluar.total);
        $(".add_max").val(saham_selected);
    })

    $(document).on("keyup", ".add_uang", function(e) {
        e.preventDefault();
        let val = parseInt(str_replace(".", "", $(this).val()));

        if (val > saham_selected) {
            message("400", "Maksimal " + angka(saham_selected));
            $(this).val(angka(saham_selected));
            return;
        }

    })

    $(document).on("click", ".btn_confirm", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        popup_confirm.confirm("btn_confirm_" + id);
    })
    $(document).on("click", ".btn_update", function(e) {
        e.preventDefault();
        let id = $(this).data("id");

        let val = [];

        data.forEach(e => {
            if (e.id == id) {
                val = e;
                stop();
            }
        });
        let html = `<div class="container">
                        <form action="<?= base_url(menu()['controller']); ?>/update" method="post">
                        <input type="hidden" name="id" value="${val.id}">
                         <div class="mb-3">
                            <label style="font-size: 12px;">Tgl</label>
                            <input placeholder="Tgl" type="text" value="${time_php_to_js(val.tgl)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Kategori</label>
                            <select class="form-select form-select-sm mb-3" name="kategori">`;

        options.forEach(o => {
            html += `<option ${(o.value==val.kategori?'selected':'')} value="${o.value}">${o.value}</option>`
        });


        html += `</select>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Uang</label>
                            <input placeholder="Uang" type="text" name="uang" value="${angka(val.uang)}" class="form-control form-control-sm angka" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Pj</label>
                            <input placeholder="Pj" type="text" name="pj" value="${val.pj}" class="form-control form-control-sm" required>
                        </div> 
                        <div class="mb-3">
                            <label style="font-size: 12px;">Admin</label>
                            <input placeholder="Admin" type="text" value="${angka(val.admin)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Admin</label>
                            <input placeholder="Admin" type="text" value="${angka(val.admin)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Ceo</label>
                            <input placeholder="Ceo" type="text" value="${angka(val.ceo)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Tabungan</label>
                            <input placeholder="Tabungan" type="text" value="${angka(val.tabungan)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Yayasan</label>
                            <input placeholder="Yayasan" type="text" value="${angka(val.yayasan)}" class="form-control form-control-sm" readonly>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-sm btn-secondary"><i class="fa-solid fa-square-pen"></i> Update</button>
                        </div>
                    </form></div>`;

        popupButton.html(html);
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
                message(res.status, res.message);
            }
        })
    })
    $(document).on("click", ".btn_detail", function(e) {
        e.preventDefault();

        let sahams = <?= json_encode(options('Saham')); ?>;

        let html = "";

        html += '<div class="container">';
        html += `<table class="table table-sm bg_main text_main border_main table-bordered" style="font-size: 12px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Kategori</th>
                <th class="text-center">Masuk - Keluar</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>`;

        let total = 0;
        sahams.forEach((e, i) => {
            total += (parseInt(details[e.value].masuk.total) - parseInt(details[e.value].keluar.total));
            html += `<tr>
                        <td class="text-center">${(i+1)}</td>
                        <td class="d-grid"><a href="" data-order="${e.value}" class="link_main btn_info px-2 p-1">${e.value}</a></td>
                        <td style="vertical-align: middle" class="text-center">${angka(details[e.value].masuk.total)} - ${angka(details[e.value].keluar.total)}</td>
                        <td style="vertical-align: middle" class="text-end">${angka(parseInt(details[e.value].masuk.total)-parseInt(details[e.value].keluar.total))}</td>
                    </tr>`;
        });

        html += `<tr>
                    <th colspan="3" class="text-center">TOTAL</th>
                    <th class="text-end">${angka(total)}</th>
                </tr>`;

        html += `</tbody>
                </table>
                <div class="body_info"></div>`;
        html += '</div>';

        popupButton.html(html);
    })
    $(document).on("click", ".btn_info", function(e) {
        e.preventDefault();
        let order = $(this).data("order");
        let data = details[order];

        let html = "";

        html += '<h6>' + order + ' | ' + angka(data.masuk.total) + ' - ' + angka(data.keluar.total) + ' = ' + angka(parseInt(data.masuk.total) - parseInt(data.keluar.total)) + '</h6>';
        html += `<table class="table table-sm bg_main text_main border_main table-bordered" style="font-size: 12px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Tgl</th>
                <th class="text-center">Penerima</th>
                <th class="text-center">Lokasi</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>`;
        let total = 0;
        data.keluar.data.forEach((e, i) => {
            total += parseInt(e.uang);
            html += `<tr>
                        <td class="text-center">${(i+1)}</td>
                        <td class="text-center">${time_php_to_js(e.tgl)}</td>
                        <td>${e.penerima}</td>
                        <td>${e.lokasi}</td>
                        <td class="text-end">${angka(e.uang)}</td>
                    </tr>`;
        });
        html += `<tr>
                    <th colspan="4" class="text-center">TOTAL</th>
                    <th class="text-end">${angka(total)}</th>
                </tr>`;

        html += `</tbody></table>`;

        $(".body_info").html(html);
    })
</script>
<?= $this->endSection() ?>