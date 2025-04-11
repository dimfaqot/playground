<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>
<h6><i class="<?= menu()['icon']; ?>"></i> <?= strtoupper(menu()['menu']); ?></h6>
<div class="d-flex gap-2 mb-3">
    <button data-bs-toggle="modal" data-bs-target="#modal_add" class="btn btn-sm btn-light"><i class="fa-solid fa-cash-register"></i> TRANSAKSI</b></button>
    <div class="bg-success rounded px-2 pt-1" style="font-size:small;"><?= angka($total); ?></div>
</div>
<div class="d-flex gap-2 mb-3">
    <div>
        <?php if (user()['role'] == "Root"): ?>
            <select class="form-select form-select-sm filter divisi" style="font-size: small;">
                <?php foreach (options('Divisi') as $i) : ?>
                    <option value="<?= $i['value']; ?>" <?= ($kategori == $i['value'] ? "selected" : ""); ?>><?= $i['value']; ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <?= user()['role']; ?>
        <?php endif; ?>
    </div>
    <div>
        <select class="form-select form-select-sm divisi filter tahun" style="font-size: small;">
            <?php foreach (get_tahun() as $i) : ?>
                <option value="<?= $i; ?>" <?= ($tahun == $i ? "selected" : ""); ?>><?= $i; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <select class="form-select form-select-sm divisi filter bulan" style="font-size: small;">
            <?php foreach (bulan() as $i) : ?>
                <option value="<?= $i['angka']; ?>" <?= ($bulan == $i['angka'] ? "selected" : ""); ?>><?= $i['bulan']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

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
                        <input type="hidden" name="kategori" value="<?= $kategori; ?>">
                        <?php if ($kategori == "Kantin"): ?>
                            <div class="mt-2 text-center" style="font-size:12px">
                                <?php foreach (options('Pengeluaran') as $i): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input ket" name="ket" type="radio" value="<?= $i['value']; ?>" <?= ($i['value'] == "Belanja" ? "checked" : ""); ?>>
                                        <label class="form-check-label"><?= $i['value']; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php endif; ?>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Penjual</label>
                            <input placeholder="Penjual" type="text" name="penjual" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3 barang">
                            <div class="mb-2 position-relative">
                                <label style="font-size: 12px;">Barang</label>
                                <input type="text" name="barang" class="form-control form-control-sm cari_barang add_barang" placeholder="Barang">
                                <div class="data_list data_barang" style="font-size: small;">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Harga</label>
                            <input placeholder="Harga" type="text" name="harga" class="form-control form-control-sm angka" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">qty</label>
                            <input placeholder="qty" type="text" name="qty" class="form-control form-control-sm angka" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Diskon</label>
                            <input placeholder="Diskon" type="text" value="0" name="diskon" class="form-control form-control-sm angka" required>
                        </div>
                        <?php if ($kategori == "Barber"): ?>
                            <div class="mt-2 text-center" style="font-size:12px">
                                <?php foreach (options('Pengeluaran') as $i): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" name="ket" type="radio" value="<?= $i['value']; ?>" <?= ($i['value'] == "Belanja" ? "checked" : ""); ?>>
                                        <label class="form-check-label"><?= $i['value']; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php endif; ?>

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
    <table class="table table-sm table-dark table-bordered" style="font-size: 14px;">
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
                    <td class="text-center"><?= $k + 1; ?></td>
                    <td class="text-center"><?= date('d', $i['tgl']); ?></td>
                    <td><?= $i['barang']; ?></td>
                    <td class="text-end"><?= angka($i['total']); ?></td>
                    <td class="text-center"><a href="" role="button" class="text-danger fs-6 btn_confirm btn_confirm_<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-id="<?= $i['id']; ?>"><i class="fa-solid fa-trash-can"></i></a> <a role="button" class="text-warning fs-6 btn_update" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-square-pen"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    let data = <?= json_encode($data); ?>;
    let data_selected = {};
    let options = '<?= json_encode(Options("Pengeluaran")); ?>';
    let kategori = "<?= $kategori; ?>";

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

        let html = `<form class="container" action="<?= base_url(menu()['controller']); ?>/update" method="post">
                        <input type="hidden" name="id" value="${val.id}">
                        <div class="mb-3">
                            <label style="font-size: 12px;">Tgl.</label>
                            <input type="text" value="${time_php_to_js(val.tgl)}" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Kategori</label>
                            <input type="text" value="${val.kategori}" class="form-control form-control-sm" readonly>
                        </div>

       <div class="mt-2 text-center" style="font-size:12px">`;
        options.forEach(x => {
            html += `<div class="form-check form-check-inline">
                                <input class="form-check-input" name="ket" type="radio" value="${x.value}" ${(x.value==val.ket?"checked":"")}>
                                <label class="form-check-label">${x.value}</label>
                            </div>`;

        })

        html += `</div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Penjual</label>
                            <input type="text" value="${val.penjual}" name="penjual" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Barang</label>
                            <input type="text" value="${val.barang}" name="barang" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Harga</label>
                            <input type="text" value="${angka(val.harga)}" name="harga" class="form-control form-control-sm angka" required>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Qty</label>
                            <input type="text" value="${angka(val.qty)}" name="qty" class="form-control form-control-sm angka" required>
                        </div>
                        <div class="mb-3">
                            <label style="font-size: 12px;">Diskon</label>
                            <input type="text" value="${angka(val.diskon)}" name="diskon" class="form-control form-control-sm angka" required>
                        </div>
                         <div class="mb-3">
                            <label style="font-size: 12px;">Total</label>
                            <input type="text" value="${angka(val.total)}" class="form-control form-control-sm" readonly>
                        </div>
                         <div class="mb-3">
                            <label style="font-size: 12px;">Pj</label>
                            <input type="text" value="${val.petugas}" class="form-control form-control-sm" readonly>
                        </div>
                         <div class="d-grid">
                            <button class="btn btn-sm btn-secondary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                        </div>
                    </div>`;

        popupButton.html(html);
    })

    $(document).on('change', '.filter', function(e) {
        e.preventDefault();
        let divisi = $(".divisi").val();
        let tahun = $(".tahun").val();
        let bulan = $(".bulan").val();
        window.location.href = '<?= base_url(menu()['controller']); ?>/' + divisi + "/" + tahun + "/" + bulan;
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


    $(document).on('change', '.ket', function(e) {
        e.preventDefault();
        let val = $(this).val();
        let html = '';
        if (kategori == "Kantin" && val == "Belanja") {
            html += `<div class="mb-2 position-relative">
            <label style="font-size: 12px;">Barang</label>
            <input type="text" name="barang" class="form-control form-control-sm cari_barang add_barang" placeholder="Barang">
            <div class="data_list data_barang" style="font-size: small;">

            </div>
        </div>`;

        } else {
            html += `<label style="font-size: 12px;">Barang</label>
                            <input placeholder="Barang" type="text" name="barang" class="form-control form-control-sm" required>`;

            $('input[name="barang_id"]').remove();
        }

        $(".barang").html(html);
    });


    $(document).on('keyup', '.cari_barang', function(e) {
        e.preventDefault();
        let value = $(this).val();

        post("pengeluaran/cari_barang", {
            value,
            kategori
        }).then(res => {
            if (res.status == "200") {
                let html = '';

                if (res.data.length == 0) {
                    html += '<div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>';
                } else {
                    res.data.forEach(e => {
                        html += '<div class="select_barang" data-barang="' + e.barang + '" data-id="' + e.id + '" data-qty="' + e.qty + '" data-harga="' + e.harga + '">' + e.barang + '/' + e.qty + '</div>';
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
        $('input[name="barang_id"]').remove();
        let barang = $(this).data("barang");
        let id = $(this).data("id");
        // data_selected['id'] = id;

        $(".cari_barang").val(barang);

        $(".barang").after('<input type="hidden" name="barang_id" value="' + id + '">')
        $(".data_barang").html("");
    });
</script>
<?= $this->endSection() ?>