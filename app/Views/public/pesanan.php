<?= $this->extend('templates/cafe') ?>

<?= $this->section('content') ?>
<div class="main_content">
    <div>
        <h1 class=" text-center mt-5" style="font-family: 'Tangerine', serif;color:#d78926;font-size:60px">
            Hayu Food Court
        </h1>
        <h1 class="text-center menu" style="font-size:30px">
            PESANAN
        </h1>

        <div class="d-flex gap-2 justify-content-center body_status">
            <div data-status="Menunggu" class="status text-success"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div data-status="Sedang dimasak" class="status text-secondary"><i class="fa-solid fa-fire-burner"></i></div>
            <div data-status="Kamu Hutang...huks" class="status text-secondary"><i class="fa-solid fa-face-tired"></i></div>
            <div data-status="Selesai kakak baikk..." class="status text-secondary"><i class="fa-solid fa-face-smile-wink"></i></div>
        </div>

    </div>



    <div class="mb-1">
        <span class="text-secondary" style="font-size: 11px;font-style:italic">Nama Pemesan</span>
        <input type="text" style="font-size: x-small;" class="form-control form-control-sm" value="<?= $user['nama']; ?>" readonly>
    </div>
    <div class="mb-1">
        <span class="text-secondary" style="font-size: 11px;font-style:italic">Lokasi</span>
        <input type="text" style="font-size: x-small;" class="form-control form-control-sm" value="<?= $data[0]['lokasi']; ?>" readonly>
    </div>
    <div class="mb-1">
        <span class="text-secondary" style="font-size: 11px;font-style:italic">No. Whatsapp</span>
        <input type="text" style="font-size: x-small;" class="form-control form-control-sm" value="<?= $user['hp']; ?>" readonly>
    </div>
    <div class="mt-3" style="font-size: 12px;">DAFTAR PESANAN</div>
    <table class="table table-sm table-dark text_main table-bordered" style="font-size: 12px;">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Barang</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Harga</th>
            </tr>
        </thead>
        <tbody class="data_pesanan">
            <?php $total = 0; ?>
            <?php foreach ($data as $k => $i): ?>
                <?php $total += (int)$i["total"]; ?>
                <tr>
                    <td class="text-center"><?= ($k + 1); ?></td>
                    <td><?= $i['barang']; ?></td>
                    <td class="text-center"><?= angka($i['qty']); ?></td>
                    <td class="text-end"><?= angka($i['total']); ?></td>
                </tr>

            <?php endforeach; ?>
            <tr>
                <th class="text-center" colspan="3">TOTAL</th>
                <th class="text-end"><?= angka($total); ?></th>
            </tr>
        </tbody>
    </table>
</div>

<script>
    $(document).on('click', '.status', function(e) {
        e.preventDefault();
        let status = $(this).data("status");
        if (status == "Kamu Hutang...huks") {
            message("400", status);
        } else {
            message("200", status);
        }
    });

    let x = 0;

    function cek_status() {

        const intervalId = setInterval(() => {
            if (x == 0 || (x % 8) == 0) {
                post("cafe/cek_status", {
                    no_nota: "<?= $data[0]['no_nota']; ?>"
                }).then(res => {
                    if (res.status == "200") {
                        let html = "";
                        if (res.message == "Barcode" || res.message == "Proses" || res.message == "Hutang") {
                            html = `<div data-status="Menunggu" class="status ${(res.message=="Barcode"?"text-success":"text-secondary")}"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <div data-status="Sedang dimasak" class="status ${(res.message=="Proses"?"text-success":"text-secondary")}"><i class="fa-solid fa-fire-burner"></i></div>`;
                            html += '<div data-status="Kamu Hutang...huks" class="status ' + (res.message == "Hutang" ? "text-danger" : "text-secondary") + '"><i class="fa-solid fa-face-tired"></i></div>';
                            html += '<div data-status="Selesai kakak baikk..." class="status text-secondary"><i class="fa-solid fa-face-smile-wink"></i></div>';
                            $(".body_status").html(html);
                        } else {
                            html += `<div data-status="Menunggu" class="status text-secondary"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            <div data-status="Sedang dimasak" class="status text-secondary"><i class="fa-solid fa-fire-burner"></i></div>`;
                            html += '<div data-status="Kamu Hutang...huks" class="status text-secondary"><i class="fa-solid fa-face-tired"></i></div>';
                            html += '<div data-status="Selesai kakak baikk..." class="status text-success"><i class="fa-solid fa-face-smile-wink"></i></div>';
                            $(".body_status").html(html);
                            clearInterval(intervalId);
                            setTimeout(() => {
                                let main = ` <h1 class="text-center text-success" style="margin-top:120px;font-family: 'Barrio', serif;color:#d78926;font-size:60px">
                                                <div><i class="fa-solid fa-circle-check"></i></div>
                                                <div>SELESAI</div>
                                                <div>THANKS KAKA...</div>
                                                <a href="${res.data}" class="btn btn-warning"><i class="fa-solid fa-file-pdf"></i> DOWNLOAD NOTA</a>
                                            </h1>`;

                                $(".main_content").html(main);
                            }, 2000);
                        }


                    } else {
                        message("400", res.message);
                    }
                })
            }

            x++;
        }, 1000); // setiap detik

    }

    cek_status();
</script>
<?= $this->endSection() ?>