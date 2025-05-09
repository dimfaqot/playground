<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $judul; ?></title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?= base_url('logo.png'); ?>" sizes="16x16">

    <link href="<?= base_url(); ?>fontawesome/css/all.css" rel="stylesheet">
    <script src="<?= base_url(); ?>jquery.js"></script>
    <link rel="stylesheet" href="<?= base_url('bootstrap'); ?>/css/bootstrap.min.css">
    <script src="<?= base_url('bootstrap'); ?>/js/bootstrap.bundle.min.js"></script>

    <style>
        .lingkaran {
            width: 600px;
            height: 600px;
            border-radius: 50%;
        }

        .fullscreen-bg {
            background-size: contain;
            background-position: center;
            width: 100%;
            height: 100vh
        }

        .running-text-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            overflow: hidden;
            z-index: 9999;
        }

        .running-text {
            display: inline-block;
            white-space: nowrap;
            font-size: 100px;
            animation: runningText 15s linear infinite;
        }

        @keyframes runningText {
            0% {
                transform: translateX(150%);
            }

            100% {
                transform: translateX(-100%);
            }
        }
    </style>






    <script>
        async function post(url = '', data = {}) {
            const response = await fetch("<?= base_url(); ?>" + url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });
            return response.json(); // parses JSON response into native JavaScript objects
        }
    </script>


</head>

<body class="bg-dark">

    <div class="running-text-container">
        <span class="running-text"><?= settings('Running Text Tv'); ?></span>
    </div>
    <div class="body_iklan" style="display: none;"></div>

    <div class="modal fade bg-dark text-light" id="content" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-body body_padding">
                <div class="modal_body_content">
                    <div class="d-flex justify-content-center" style="margin-bottom: 140px;padding: 90px 0px">
                        <div style="padding: 50px 90px;font-size:100px" class="fw-bold text-center bg-success rounded-pill judul"><?= $data['judul']; ?></div>
                    </div>
                    <?php $x = 1; ?>
                    <?php foreach ($data['data'] as $i): ?>
                        <?php $exp = explode(" ", $i['perangkat']); ?>
                        <?php if ($x == 1 || $x % 5 == 1) : ?>
                            <div class="d-flex justify-content-center gap-5 mb-5">
                            <?php endif; ?>
                            <div class="lingkaran <?= ($i['metode'] == "Over" ? "bg-danger opacity-75" : ""); ?> border border border-light text-center p-5">
                                <div style="font-size: 40px;"><?= $exp[0]; ?></div>
                                <div style="font-size: 200px;margin-top:-50px"><?= $exp[1]; ?></div>
                                <div class="<?= ($i['metode'] == "Available" ? "text-success" : ""); ?>" style="font-size: 60px;margin-top:-30px"><?= $i['metode']; ?></div>
                                <div style="font-size: 50px;"><?= $i['waktu']; ?></div>
                            </div>

                            <?php if ($x % 5 == 0) : ?>
                            </div>
                        <?php endif; ?>
                        <?php $x++; ?>
                    <?php endforeach; ?>
                </div>


            </div>


            <!-- 1,5,9 %4==1 -->
            <!-- 4,8,12 %4==0 -->
        </div>





    </div>

    <script>
        let settings = <?= json_encode($settings); ?>;
        $(document).ready(function() {
            $('.running-text').each(function(index) {
                let delay = index * 2000; // Delay setiap teks 2 detik
                $(this).css({
                    'display': 'inline-block',
                    'visibility': 'visible'
                }); // Pastikan teks terlihat
                $(this).animate({
                    left: '-100%'
                }, 10000, 'linear');
            });
        });



        let myModal = document.getElementById("content");
        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        modal.show();
        let urutan = settings['urutan'];
        let index = 0;
        let content = (order) => {
            let timestamp = new Date().getTime();
            if (index == (urutan.length - 1)) {
                index = 0;
            } else {
                index++;
            }
            // <div style="background-image: url('http://localhost:8080/files/iklan.jpg'?1744396978151);" class="fullscreen-bg"></div>

            if (order == "iklan") {
                let html = `<div style="background-image: url('<?= base_url('files/iklan.jpg'); ?>?${timestamp}');" class="fullscreen-bg"></div>`;
                $(".body_iklan").html(html);
                setTimeout(() => {
                    $(".body_iklan").fadeIn();
                }, 300);
            } else {
                $(".body_iklan").fadeOut();
                $(".body_iklan").html("");
                post('landing/status_tv', {
                    order
                }).then(res => {
                    let x = 1;
                    let html = '';
                    html += `<div class="d-flex justify-content-center" style="margin-bottom: 140px;padding: 90px 0px">
                    <div style="padding: 50px 90px;font-size:100px" class="fw-bold text-center bg-success rounded-pill judul">${res.data.judul}</div>
                </div>`;
                    res.data.data.forEach(e => {
                        let exp = e.perangkat.split(" ");
                        if (x == 1 || x % 5 == 1) {
                            html += `<div class="d-flex justify-content-center gap-5 mb-5">`;
                        }
                        html += `<div class="lingkaran ${(e.metode == "Over" ? "bg-danger opacity-75" : "")} border border border-light text-center p-5">
                        <div style="font-size: 40px;">${exp[0]}</div>
                        <div style="font-size: 200px;margin-top:-50px">${exp[1]}</div>
                        <div class="${(e.metode == "Available" ? "text-success" : "")}" style="font-size: 60px;margin-top:-30px">${e.metode}</div>
                        <div style="font-size: 50px;">${e.waktu}</div>
                    </div>`;

                        if (x % 5 == 0) {
                            html += `</div>`;
                        }
                        x++;
                    });

                    $(".modal_body_content").html(html);
                    setTimeout(() => {
                        let myModal = document.getElementById("content");
                        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                        modal.show();

                    }, 300);


                })
            }
        }

        // let index = 0;
        // setInterval(() => {
        //     if (index == 3) {
        //         index = 0;
        //     }
        //     if (urutan[index] == 'iklan') {
        //         $(".fullscreen-bg").fadeIn();
        //     } else {
        //         $(".fullscreen-bg").fadeOut();

        //     }

        //     modal.hide();
        //     content(index);

        //     index++;
        // }, (urutan[index] == "iklan" ? 2000 : 7000));


        // let urutan = ['ps', 'iklan', 'billiard'];

        function loopInterval() {
            modal.hide();
            let order = urutan[index];
            let delay = (order === "iklan") ? parseInt(settings['interval'][0]) : parseInt(settings['interval'][1]); // Atur waktu sesuai jenis konten
            // if (order === 'iklan') {
            //     $(".fullscreen-bg").fadeIn();
            //     setTimeout(() => {
            //         $(".fullscreen-bg").attr("src", "<?= base_url('files/'); ?>iklan.jpg?" + timestamp);
            //     }, 300);
            // } else {
            //     $(".fullscreen-bg").fadeOut();
            // }

            content(order);

            // let myModal = document.getElementById("content");
            // let modal = bootstrap.Modal.getOrCreateInstance(myModal);

            setTimeout(loopInterval, delay); // Jalankan ulang dengan waktu baru
        }

        // Mulai pertama kali
        setTimeout(() => {
            loopInterval();
        }, 7000);
    </script>
</body>

</html>