<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<h6><i class="<?= menu()['icon']; ?>"></i> <?= strtoupper(menu()['menu']); ?></h6>
<button data-bs-toggle="modal" data-bs-target="#modal_add" class="btn btn-sm btn-light my-3"><i class="fa-solid fa-circle-plus"></i> Tambah Data</b></button>


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
                        <div class="body_user_id_add"></div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Role</label>
                            <select class="form-select form-select-sm mb-3" name="divisi">
                                <?php foreach (options('Divisi') as $i): ?>
                                    <option <?= ($i['value'] == 'Barber' ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Shift</label>
                            <input placeholder="Shift" type="number" name="shift" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Pukul (*dipisah strip -)</label>
                            <input placeholder="Pukul" type="text" name="pukul" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-2 position-relative">
                            <label style="font-size: 12px;">Petugas</label>
                            <input type="text" class="form-control form-control-sm cari_user" data-order="add" name="petugas" placeholder="Petugas">
                            <div class="data_list data_user_add" style="font-size: small;">

                            </div>
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
                <th class="text-center">Divisi</th>
                <th class="text-center">Shift</th>
                <th class="text-center">Pukul</th>
                <th class="text-center">Petugas</th>
                <th class="text-center">Act</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <td class="text-center"><?= $k + 1; ?></td>
                    <td><?= $i['divisi']; ?></td>
                    <td class="text-center"><?= $i['shift']; ?></td>
                    <td class="text-center"><?= $i['pukul']; ?></td>
                    <td><?= $i['petugas']; ?></td>
                    <td class="text-center"><a href="" role="button" class="text-danger fs-6 btn_confirm btn_confirm_<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-id="<?= $i['id']; ?>"><i class="fa-solid fa-trash-can"></i></a> <a role="button" class="text-warning fs-6 btn_update" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-square-pen"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    let data = <?= json_encode($data); ?>;
    let options = <?= json_encode(options('Divisi')); ?>;

    $(document).on('keyup', '.cari_user', function(e) {
        e.preventDefault();
        let val = $(this).val();
        let order = $(this).data("order");

        post("shift/cari_user", {
            val
        }).then(res => {
            let html = "";
            if (res.data.length == 0) {
                html += '<div>Data tidak ditemukan!.</div>';
            }
            res.data.forEach(e => {
                html += '<div data-order="' + order + '" data-user_id="' + e.id + '" class="select_user">' + e.nama + '</div>';
            })

            $(".data_user_" + order).html(html);
        })
    });

    $(document).on('click', '.select_user', function(e) {
        e.preventDefault();
        let nama = $(this).text();
        let user_id = $(this).data("user_id");
        let order = $(this).data("order");

        $(".cari_user").val(nama);
        $(".data_user_" + order).html("");
        $(".body_user_id_" + order).html('<input type="hidden" value="' + user_id + '" name="user_id" class="form-control form-control-sm">');
    });

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
                        <div class="body_user_id_update"> <input type="hidden" name="user_id" value="${val.user_id}"></div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Divisi</label>
                            <select class="form-select form-select-sm mb-3" name="divisi">`;

        options.forEach(o => {
            html += `<option ${(o.value==val.divisi?'selected':'')} value="${o.value}">${o.value}</option>`
        });


        html += `</select>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Shift</label>
                            <input placeholder="Shift" type="number" name="shift" value="${val.shift}" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                             <label style="font-size: 12px;">Pukul (*dipisah strip -)</label>
                            <input placeholder="Pukul" type="text" name="pukul" value="${val.pukul}" class="form-control form-control-sm" required>
                        </div>

                       <div class="mb-2 position-relative">
                            <label style="font-size: 12px;">Petugas</label>
                            <input type="text" class="form-control form-control-sm cari_user" value="${val.petugas}" data-order="update" name="petugas" placeholder="Petugas">
                            <div class="data_list data_user_update" style="font-size: small;">

                            </div>
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
</script>
<?= $this->endSection() ?>