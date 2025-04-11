<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>
<?php
$dbs = [
    ['text' => "Billiard", 'tabel' => 'billiard_2'],
    ['text' => "Ps", 'tabel' => 'rental'],
    ['text' => "Kantin", 'tabel' => 'kantin'],
    ['text' => "Barber", 'tabel' => 'barber'],
    ['text' => "User", 'tabel' => 'users'],
    ['text' => "Pengeluaran", 'tabel' => 'pengeluaran'],
    ['text' => "Barang", 'tabel' => 'barang'],
    ['text' => "Hutang", 'tabel' => 'hutang'],
    ['text' => "Koperasi", 'tabel' => 'koperasi']
]
?>
<h6 style="color: <?= tema('link_secondary'); ?>;"><i class="<?= menu()['icon']; ?>"></i> <?= strtoupper(menu()['menu']); ?></h6>

<form action="<?= base_url(menu()['controller']); ?>/update_db" method="post">
    <select class="form-select form-select-sm" name="tabel">
        <?php foreach ($dbs as $i): ?>
            <option value="<?= $i['tabel']; ?>"><?= $i['text']; ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-sm btn-danger">Update</button>
</form>
<button data-bs-toggle="modal" data-bs-target="#modal_add" class="btn btn-sm link_secondary my-3 add_data"><i class="fa-solid fa-circle-plus"></i> Tambah Data</b></button>

<!-- Modal -->
<div class="modal fade" id="modal_add" tabindex="-1" aria-labelledby="fullscreenLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg_main">
            <div class="header text-center mt-5">
                <a href="" role="button" data-bs-dismiss="modal" class="text-danger fs-4"><i class="fa-solid fa-circle-xmark"></i></a>
            </div>
            <div class="modal-body modal-fullscreen">
                <div class="container">
                    <form action="<?= base_url(menu()['controller']); ?>/add" method="post">

                        <div class="mb-3">
                            <label style="font-size: 12px;">Nama</label>
                            <input placeholder="Nama" type="text" name="nama" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Hp</label>
                            <input placeholder="Hp" type="text" name="hp" class="form-control form-control-sm" required>
                        </div>
                        <?php if (user()['role'] == "Root"): ?>
                            <div class="mb-3">
                                <label style="font-size: 12px;">Role</label>
                                <select class="form-select form-select-sm mb-3" name="role">
                                    <?php foreach (options('role') as $i): ?>
                                        <?php if (user()['role'] == "Advisor" && $i['value'] !== "Root" && $i['value'] !== "Advisor"): ?>
                                            <option <?= ($i['value'] == 'Member' ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        <?php endif; ?>

                        <div class="d-grid">
                            <button class="btn btn-sm link_secondary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
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
                <th class="text-center">Nama</th>
                <th class="text-center">Role</th>
                <?php if (user()['role'] == "Root"): ?>
                    <th class="text-center">Link</th>
                <?php endif; ?>
                <th class="text-center">Act</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <th><?= $k + 1; ?></th>
                    <td><?= $i['nama']; ?></td>
                    <td><?= $i['role']; ?></td>
                    <?php if (user()['role'] == "Root"): ?>
                        <td class="text-center"><a role="button" class="copy_text link_main rounded py-1 px-2" data-text="<?= $i['link']; ?>" href=""><i class="fa-solid fa-link"></i> Link</a></td>
                        <td class="text-center"><a style="font-size: x-small;" href="" role="button" class="link_secondary topup px-2 py-1 rounded" data-id="<?= $i['id']; ?>">TOPUP</a> <a href="" role="button" class="text-danger fs-6 btn_confirm btn_confirm_<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-id="<?= $i['id']; ?>"><i class="fa-solid fa-trash-can"></i></a> <a role="button" class="text-warning fs-6 btn_update" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-square-pen"></i></a></td>

                    <?php elseif (user()['role'] == "Admin" && $i['role'] == "Member"): ?>
                        <td class="text-center"><a href="" role="button" class="text-danger fs-6 btn_confirm btn_confirm_<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-id="<?= $i['id']; ?>"><i class="fa-solid fa-trash-can"></i></a> <a role="button" class="text-warning fs-6 btn_update" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-square-pen"></i></a></td>
                    <?php else: ?>
                        <td class="text-center"><i class="fa-solid fa-ban"></i></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    let data = <?= json_encode($data); ?>;
    let options = <?= json_encode(options('Role')); ?>;
    let topup = <?= json_encode(options('Topup')); ?>;
    let role = "<?= user()['role']; ?>";

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
                            <label style="font-size: 12px;">Nama</label>
                            <input placeholder="Nama" type="text" name="nama" value="${val.nama}" class="form-control form-control-sm" required>
                        </div>
                          <div class="mb-3">
                            <label style="font-size: 12px;">Hp</label>
                            <input placeholder="Hp" type="text" name="hp" value="${val.hp}" class="form-control form-control-sm" required>
                        </div>`;
        if (role == "Root") {
            html += `<div class="mb-3">
            <label style="font-size: 12px;">Role</label>
            <select class="form-select form-select-sm mb-3" name="role">`;

            options.forEach(o => {

                html += `<option ${(o.value==val.role?'selected':'')} value="${o.value}">${o.value}</option>`

            });


            html += `</select>
                    </div>`;

        }

        html += `<div class="d-grid">
                            <button class="btn btn-sm link_secondary"><i class="fa-solid fa-square-pen"></i> Update</button>
                        </div>
                    </form></div>`;

        popupButton.html(html);
    })
    $(document).on("click", ".topup", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        post("home/csrf", {
            id
        }).then(res => {
            if (res.status == "200") {
                let html = '<div class="container">';
                html += `<div class="row g-2">`;
                topup.forEach(e => {
                    html += `<div class="col-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" name="topup" type="radio" value="${e.value}" role="switch">
                                    <label class="form-check-label">${angka(e.value)}</label>
                                </div>
                            </div>`;

                })
                html += '</div>';
                html += `<input type="hidden" value="${res.data}" class="csrf">`;
                html += `<div class="d-grid mt-3">
                            <button class="btn btn-sm link_secondary btn_topup"><i class="fa-solid fa-wallet"></i> TOPUP</button>
                        </div>`;
                html += '</div>';
                popupButton.html(html);
            } else {
                message("400", res.message);
            }
        })
    })


    $(document).on("click", ".btn_topup", function(e) {
        e.preventDefault();
        let csrf = $(".csrf").val();
        if (csrf == undefined || csrf == "") {
            message("400", "Gagal!.");
        }
        let topup = $('input[name="topup"]:checked').val();
        if (topup == undefined || topup == "") {
            message("400", "Gagal!.");
        }
        post("user/topup", {
            csrf,
            latitude: $(".latitude").val(),
            longitude: $(".longitude").val(),
            topup
        }).then(res => {
            message(res.status, res.message);
            setTimeout(() => {
                location.reload();
            }, 1200);
        })

    })

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
</script>
<?= $this->endSection() ?>