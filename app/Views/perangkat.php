<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<h6><i class="<?= menu()['icon']; ?>"></i> <?= strtoupper(menu()['menu']); ?></h6>
<button data-bs-toggle="modal" data-bs-target="#modal_add" class="btn btn-sm btn-light my-3 add_data"><i class="fa-solid fa-circle-plus"></i> Tambah Data</b></button>

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
                            <label style="font-size: 12px;">Grup</label>
                            <select class="form-select form-select-sm mb-3" name="grup">
                                <?php foreach (options('Grup') as $i): ?>
                                    <option <?= ($i['value'] == 'Billiard 1' ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Grup Esp</label>
                            <select class="form-select form-select-sm mb-3" name="lokasi_esp">
                                <?php foreach (options('Grup') as $i): ?>
                                    <option <?= ($i['value'] == 'Billiard 1' ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Kategori</label>
                            <select class="form-select form-select-sm mb-3" name="kategori">
                                <?php foreach (options('Kategori') as $i): ?>
                                    <option <?= ($i['value'] == 'Billiard' ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Perangkat</label>
                            <input placeholder="Perangkat" type="text" name="perangkat" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Harga</label>
                            <input placeholder="Harga" type="text" name="harga" class="form-control form-control-sm angka" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Pin</label>
                            <input placeholder="Pin" type="text" name="pin" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Desc</label>
                            <input placeholder="Desc" type="text" name="desc" class="form-control form-control-sm" required>
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
                <th class="text-center">Grup</th>
                <th class="text-center">Grup Esp</th>
                <th class="text-center">Perangkat</th>
                <th class="text-center">Status</th>
                <th class="text-center">Pin</th>
                <th class="text-center">Act</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <td class="text-center"><?= $k + 1; ?></td>
                    <td><?= $i['grup']; ?></td>
                    <td><?= $i['lokasi_esp']; ?></td>
                    <td><?= $i['perangkat']; ?></td>
                    <td contenteditable="true" class="text-center update_blur" data-col="status" data-id="<?= $i['id']; ?>"><?= $i['status']; ?></td>
                    <td contenteditable="true" class="text-center update_blur" data-col="pin" data-id="<?= $i['id']; ?>"><?= $i['pin']; ?></td>
                    <td class="text-center"><a href="" role="button" class="text-danger fs-6 btn_confirm btn_confirm_<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-id="<?= $i['id']; ?>"><i class="fa-solid fa-trash-can"></i></a> <a role="button" class="text-warning fs-6 btn_update" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-square-pen"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    let data = <?= json_encode($data); ?>;
    let kategoris = <?= json_encode(options('Kategori')); ?>;
    let grups = <?= json_encode(options('Grup')); ?>;

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
        // console.log(val.grup);
        // console.log(val.kategori);
        let html = `<div class="container">
                        <form action="<?= base_url(menu()['controller']); ?>/update" method="post">
                        <input type="hidden" name="id" value="${val.id}">
                        <div class="mb-3">
                            <label style="font-size: 12px;">Grup</label>
                            <select class="form-select form-select-sm mb-3" name="grup">`;

        grups.forEach(o => {

            html += `<option ${(o.value==val.grup?'selected':'')} value="${o.value}">${o.value}</option>`
        });


        html += `</select>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Grup Esp</label>
                            <select class="form-select form-select-sm mb-3" name="lokasi_esp">`;

        grups.forEach(o => {

            html += `<option ${(o.value==val.lokasi_esp?'selected':'')} value="${o.value}">${o.value}</option>`
        });


        html += `</select>
                        </div>

                         <div class="mb-3">
                            <label style="font-size: 12px;">Kategori</label>
                            <select class="form-select form-select-sm mb-3" name="kategori">`;

        kategoris.forEach(o => {
            html += `<option ${(o.value==val.kategori?'selected':'')} value="${o.value}">${o.value}</option>`
        });

        html += `</select>
                        </div>

                         <div class="mb-3">
                            <label style="font-size: 12px;">Kategori</label>
                            <select class="form-select form-select-sm mb-3" name="kategori">`;

        kategoris.forEach(o => {
            html += `<option ${(o.value==val.kategori?'selected':'')} value="${o.value}">${o.value}</option>`
        });


        html += `</select>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Perangkat</label>
                            <input placeholder="Perangkat" type="text" name="perangkat" value="${val.perangkat}" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Status</label>
                            <input placeholder="Status" type="text" name="status" value="${val.status}" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Harga</label>
                            <input placeholder="Harga" type="text" name="harga" value="${angka(val.harga)}" class="form-control form-control-sm angka" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Pin</label>
                            <input placeholder="Pin" type="text" name="pin" value="${val.pin}" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Urutan</label>
                            <input placeholder="Urutan" type="text" name="urutan" value="${val.urutan}" class="form-control form-control-sm">
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Desc</label>
                            <input placeholder="Desc" type="text" name="desc" value="${val.desc}" class="form-control form-control-sm">
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-sm btn-secondary"><i class="fa-solid fa-square-pen"></i> Update</button>
                        </div>
                    </form></div>`;

        popupButton.html(html);
    })

    $(document).on("blur", ".update_blur", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let col = $(this).data("col");
        let val = $(this).text();

        post("perangkat/update_blur", {
            id,
            col,
            val
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
    $(document).on("click", ".btn_delete", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let tabel = $(this).data("tabel");

        post("home/delete", {
            tabel,
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
</script>
<?= $this->endSection() ?>