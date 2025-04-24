<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<div class="input-group input-group-sm mb-2">
    <label style="width: 100px;" class="input-group-text">Tahun</label>
    <select class="form-select form-select-sm filter tahun">
        <?php foreach (get_tahun() as $i): ?>
            <option <?= ($tahun == $i ? "selected" : ""); ?> value="<?= $i; ?>"><?= $i; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="input-group input-group-sm mb-2">
    <label style="width: 100px;" class="input-group-text">Bulan</label>
    <select class="form-select form-select-sm filter bulan">
        <?php foreach (bulan() as $i): ?>
            <option <?= ($bulan == $i['angka'] ? "selected" : ""); ?> value="<?= $i['angka']; ?>"><?= $i['bulan']; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="input-group input-group-sm mb-2">
    <label style="width: 100px;" class="input-group-text">Angkatan</label>
    <select class="form-select form-select-sm filter angkatan">
        <?php foreach ($angkatans as $i): ?>
            <option <?= ($angkatan == $i['angkatan'] ? "selected" : ""); ?> value="<?= $i['angkatan']; ?>"><?= $i['angkatan']; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="d-grid text-center mb-2">
    <a href="" class="link_main btn_confirm_bayar rounded border_main">BAYAR</a>
</div>




<?php if (count($data) == 0): ?>
    <div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>
<?php else: ?>
    <div class="input-group input-group-sm mb-3">
        <span class="input-group-text bg_main border_main text_main">Cari Data</span>
        <input type="text" class="form-control cari bg_main border border_main text_main" placeholder="....">
    </div>
    <div class="body_check_all"></div>
    <table class="table table-sm table-bordered bg_main text_main" style="font-size: 12px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Check</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <td class="text-center"><?= ($k + 1); ?></td>
                    <td class="text-center"><input data-id="<?= $i['profile']['id']; ?>" class="form-check-input check" type="checkbox" value="<?= $i['total']; ?>" <?= ($i['total'] == 0 ? "disabled" : ""); ?>></td>
                    <td style="cursor: pointer;" class="detail" data-user_id="<?= $i['profile']['id']; ?>"><?= $i['profile']['nama']; ?></td>
                    <td class="text-end"><?= angka($i['total']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
    let data = <?= json_encode($data); ?>;

    let get_check = () => {
        let checkedValues = $(".check:checked").map(function() {
            return $(this).val();
        }).get();
        if (checkedValues.length > 0) {
            let html = `<div class="form-check form-switch">
                        <input class="form-check-input check_all" type="checkbox" role="switch">
                        <label class="form-check-label">Check All</label>
                    </div>`;

            $(".body_check_all").html(html);
        } else {
            $(".body_check_all").html("");
        }

    }

    $(document).on("click", ".detail", function(e) {
        e.preventDefault();
        let user_id = $(this).data("user_id");
        let val = [];
        data.forEach(e => {
            if (e.profile.id == user_id) {
                val = e;
            }
        })
        let html = `<div class="container">`;
        html += `<div>TOTAL: ${angka(val.total)}</div>`;
        html += `<table class="table table-sm table-bordered bg_main text_main" style="font-size: 12px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Tgl</th>
                <th class="text-center">Kat</th>
                <th class="text-center">Barang</th>
                <th class="text-center">Harga</th>
            </tr>
        </thead>
        <tbody>`
        val.data.forEach((e, i) => {
            html += `<tr>
                        <td class="text-center">${(i+1)}</td>
                        <td class="text-center">${time_php_to_js(e.tgl)}</td>
                        <td>${e.divisi}</td>
                        <td>${e.barang}</td>
                        <td class="text-end">${angka(e.total)}</td>
                    </tr>`;
        })

        html += `</tbody>
    </table>`;
        html += `</div>`;

        popupButton.html(html);
    })

    $(document).on("change", ".check", function(e) {
        e.preventDefault();

        get_check();
    })
    $(document).on("change", ".check_all", function(e) {
        e.preventDefault();
        $(".check:not(:disabled)").prop("checked", $(this).prop("checked"));
        if (!$(this).prop("checked")) {
            $(".body_check_all").html("");
        }
    })
    $(document).on("click", ".btn_confirm_bayar", function(e) {
        e.preventDefault();
        let checkedValues = $(".check:checked").map(function() {
            return {
                total: $(this).val(),
                user_id: $(this).data("id") // Mengambil nilai dari atribut data-id
            };

        }).get();

        if (checkedValues.length == 0) {
            message("400", "Data belum dicheck...");
            return;
        }
        let total = 0;
        checkedValues.forEach(e => {
            total += parseInt(e.total);
        })

        let html = `<div class="container text-center">`;
        html += `<div>Yakin bayar ${angka(checkedValues.length)} anak</div>`;
        html += `<div class="fw-bold">Total ${angka(total)}</div>`;
        html += `<div class="d-grid text-center mt-3">
                    <a href="" class="link_main btn_bayar rounded border_main">YAKIN BANGET</a>
                </div>`;
        html += `</div>`;

        popupButton.html(html);
    })
    $(document).on("click", ".btn_bayar", function(e) {
        e.preventDefault();
        let tahun = $(".tahun").val();
        let bulan = $(".bulan").val();

        let checkedValues = $(".check:checked").map(function() {
            return {
                total: $(this).val(),
                user_id: $(this).data("id") // Mengambil nilai dari atribut data-id
            };

        }).get();


        post("tap/bayar", {
            data: checkedValues,
            tahun,
            bulan
        }).then(res => {
            message(res.status, res.message);
            if (res.status == "200") {
                setTimeout(() => {
                    location.reload();
                }, 1200);
            }
        })

    })
    $(document).on("change", ".filter", function(e) {
        e.preventDefault();
        let tahun = $(".tahun").val();
        let bulan = $(".bulan").val();
        let angkatan = $(".angkatan").val();

        location.href = "<?= base_url('tap'); ?>/" + tahun + "/" + bulan + "/" + angkatan;
    })
</script>
<?= $this->endSection() ?>