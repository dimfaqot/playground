<?php
$db = db('user');
$user = $db->orderBy('nama', 'ASC')->get()->getResultArray();

?>
<?= $this->extend('templates/guest') ?>

<?= $this->section('content') ?>
<h1 class="text-center" style="margin-top: 200px;">MENYALA!!!</h1>

<div class="body_absen">

</div>

<div class="body_bayar">

</div>


<form class="mb-5" action="<?= base_url('iot/tapping'); ?>" method="post">
    <select class="form-select form-select-sm" name="data">
        <?php foreach ($user as $i): ?>
            <option value="<?= $i['uid']; ?>"><?= $i['nama']; ?>/<?= $i['role']; ?></option>
        <?php endforeach; ?>
    </select>
    <select class="form-select form-select-sm" name="data2">
        <?php foreach (options('Grup') as $i): ?>
            <option value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">TAPPING</button>
</form>

<select class="form-select form-select-sm grup">
    <?php foreach (options('Grup') as $i): ?>
        <option value="<?= str_replace(" ", "-", $i['value']); ?>"><?= $i['value']; ?></option>
    <?php endforeach; ?>
</select>
<select class="form-select form-select-sm uid">
    <?php foreach ($user as $i): ?>
        <option value="<?= $i['uid']; ?>"><?= $i['nama']; ?>/<?= $i['role']; ?></option>
    <?php endforeach; ?>
</select>

<button type="button" class="esp">ESP</button>


<table class="table table-sm table-bordered bg_main text_main mt-4" style="font-size: 12px;">
    <thead>
        <tr>
            <th class="text-center">Pin</th>
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody class="tabel_search">

    </tbody>
</table>
<script>
    let jwt = "<?= $jwt; ?>";

    const esp = () => {
        post("iot/esp", {
            jwt
        }).then(res => {
            if (res.status == "200") {
                let html = "";
                res.data.forEach(e => {
                    html += `<tr>
                                <td>${e.pin}</td>
                                <td>${e.status}</td>
                            </tr>`;
                });
                $('.tabel_search').html(html);

                if (res.data2 == "") {
                    $(".body_absen").html("");
                } else {
                    let html = '';
                    html += `<select class="form-select form-select-sm uid_absen">
                                    <?php foreach ($user as $i): ?>
                                        <option value="<?= $i['uid']; ?>"><?= $i['nama']; ?>/<?= $i['role']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                               <input class="grup_absen" value="<?= str_replace("-", " ", url(3)); ?>">
                        <button type="button" class="btn_absen" data-link="iot/${res.data2}">Absen</button>`;
                    $(".body_absen").html(html);
                }
                if (res.data3 == "") {
                    $(".body_bayar").html("");
                } else {
                    let html = '';
                    html += `<select class="form-select form-select-sm uid_bayar">
                                    <?php foreach ($user as $i): ?>
                                        <option value="<?= $i['uid']; ?>"><?= $i['nama']; ?>/<?= $i['role']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                               <input class="grup_bayar" value="<?= str_replace("-", " ", url(3)); ?>">
                        <button type="button" class="btn_bayar" data-link="iot/${res.data3}">BAYAR</button>`;
                    $(".body_bayar").html(html);
                }

            }
        })
    }

    $(document).on('click', ".esp", function(e) {
        let grup = $(".grup").val();
        let uid = $(".uid").val();
        window.location.href = "<?= base_url(); ?>" + grup + "/" + uid;
    })
    $(document).on('click', ".btn_absen", function(e) {
        let grup = $(".grup_absen").val();
        let uid = $(".uid_absen").val();
        let link = $(this).data("link");
        post("landing/encode_jwt", {
            data: {
                data: uid,
                data2: grup
            }
        }).then(res => {
            if (res.status == "200") {
                post(link, {
                    jwt: res.data
                }).then(res => {
                    if (res.status == "200") {

                    }
                })
            }
        })
    })
    $(document).on('click', ".btn_bayar", function(e) {
        let grup = $(".grup_bayar").val();
        let uid = $(".uid_bayar").val();
        let link = $(this).data("link");
        post("landing/encode_jwt", {
            data: {
                data: uid,
                data2: grup
            }
        }).then(res => {
            if (res.status == "200") {
                post(link, {
                    jwt: res.data
                }).then(res => {
                    if (res.status == "200") {

                    }
                })
            }
        })
    })

    setInterval(() => {
        esp();
    }, 5000);
</script>
<?= $this->endSection() ?>