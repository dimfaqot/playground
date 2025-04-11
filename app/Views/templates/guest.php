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
        .bg_main {
            background-color: #212529;
        }

        .bg_secondary {
            background-color: #3A98B9;
        }

        .text_main {
            color: #f8f9fa;
        }


        .border_main {
            color: #f8f9fa;
        }

        .border_bottom {
            border-bottom: 1px solid #3A98B9;
        }

        .link_main {
            text-decoration: none;
            background-color: #E8D5C4;
            color: #f8f9fa
        }

        .link_main:hover {
            text-decoration: none;
            background-color: #E8D5C4;
            color: #3A98B9;
        }

        .link_secondary {
            text-decoration: none;
            background-color: #3A98B9;
            color: #EEEEEE
        }

        .link_secondary:hover {
            text-decoration: none;
            background-color: #EEEEEE;
            color: #3A98B9;
            border: 1px solid #3A98B9;
        }

        .data_list {
            position: absolute;
            top: 100%;
            width: 100%;
            left: 0;
            z-index: 99999;
            background-color: rgb(56, 56, 57);
            color: #f8f9fa;

        }

        .data_list div {
            padding: 5px 10px;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
        }
    </style>

    <script>
        const message = (status = "200", message) => {
            let html = `<div class="d-flex justify-content-center">
                            <div class="bg-opacity-25 ${(status=="200"?"bg-success border border-success":"bg-danger border border-danger")} px-5 py-2 rounded" style="font-size: small;">${message}</div>
                        </div>`;

            $(".message").html(html);
            setTimeout(() => {
                $(".message").html("");
            }, 1000);

        }
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

<body class="bg-dark text-light">
    <div class="div_top"></div>
    <input type="hidden" class="latitude">
    <input type="hidden" class="longitude">
    <?= view('templates/navbar'); ?>
    <div id="modal_fullscreen"></div>

    <div class="container" style="margin-top: 70px;">
        <?= $this->renderSection('content') ?>

    </div>
    <div class="fixed-bottom message" style="margin-bottom: 90px;z-index:999999"></div>

    <nav class="fixed-bottom border-top border_main">
        <div class="m-auto text-center p-3">
            <a target="_blank" href="https://www.instagram.com/djanasragen/">
                <svg width="98" height="17" viewBox="0 0 98 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.02734 2.80078V1.48828L9.90234 0.550781V2.80078V3.73828V7.39453C9.90234 8.23828 9.69531 9.01172 9.28125 9.71484C8.86719 10.4102 8.3125 10.9648 7.61719 11.3789C6.92188 11.793 6.14844 12 5.29688 12C4.45312 12 3.68359 11.793 2.98828 11.3789C2.29297 10.9648 1.73828 10.4102 1.32422 9.71484C0.910156 9.01953 0.703125 8.25 0.703125 7.40625C0.703125 6.55469 0.910156 5.78125 1.32422 5.08594C1.73828 4.39062 2.29297 3.83594 2.98828 3.42188C3.69141 3.00781 4.46484 2.80078 5.30859 2.80078H8.02734ZM8.02734 7.39453V4.67578H5.30859C4.55859 4.67578 3.91406 4.94141 3.375 5.47266C2.84375 6.00391 2.57812 6.64844 2.57812 7.40625C2.57812 8.15625 2.84375 8.79688 3.375 9.32812C3.90625 9.85938 4.54688 10.125 5.29688 10.125C6.05469 10.125 6.69922 9.85938 7.23047 9.32812C7.76172 8.78906 8.02734 8.14453 8.02734 7.39453Z" fill="#80DEEA" />
                    <path d="M14.9766 5.08594V12.3281H14.9648H14.9766C14.9766 13.1719 14.7695 13.9414 14.3555 14.6367C13.9414 15.3398 13.3828 15.8984 12.6797 16.3125C11.9844 16.7266 11.2148 16.9336 10.3711 16.9336V15.0586C11.1211 15.0586 11.7617 14.7891 12.293 14.25C12.832 13.7188 13.1016 13.0781 13.1016 12.3281V5.08594H14.9766ZM14.9766 1.42969V3.30469H13.1016V2.36719L14.9766 1.42969Z" fill="#80DEEA" />
                    <path d="M20.9766 2.80078C21.8281 2.80078 22.6016 3.00781 23.2969 3.42188C23.9922 3.83594 24.5469 4.39062 24.9609 5.08594C25.375 5.78125 25.582 6.55469 25.582 7.40625V12H20.9883C20.1445 12 19.3711 11.793 18.668 11.3789C17.9727 10.9648 17.418 10.4102 17.0039 9.71484C16.5898 9.01172 16.3828 8.23828 16.3828 7.39453C16.3828 6.55078 16.5898 5.78125 17.0039 5.08594C17.418 4.39062 17.9727 3.83594 18.668 3.42188C19.3633 3.00781 20.1328 2.80078 20.9766 2.80078ZM23.707 10.125V7.40625C23.707 6.64844 23.4414 6.00391 22.9102 5.47266C22.3789 4.94141 21.7344 4.67578 20.9766 4.67578C20.2266 4.67578 19.5859 4.94141 19.0547 5.47266C18.5234 6.00391 18.2578 6.64453 18.2578 7.39453C18.2578 8.14453 18.5234 8.78906 19.0547 9.32812C19.5938 9.85938 20.2383 10.125 20.9883 10.125H23.707Z" fill="#80DEEA" />
                    <path d="M31.1133 2.8125C31.8633 2.8125 32.5469 3 33.1641 3.375C33.7891 3.74219 34.2852 4.23828 34.6523 4.86328C35.0273 5.48047 35.2148 6.16797 35.2148 6.92578V12H33.3398V6.92578C33.3398 6.30859 33.1211 5.78125 32.6836 5.34375C32.2461 4.90625 31.7227 4.6875 31.1133 4.6875C30.4961 4.6875 29.9688 4.90625 29.5312 5.34375C29.0938 5.78125 28.875 6.30859 28.875 6.92578V12H27V6.92578C27 6.16797 27.1836 5.48047 27.5508 4.86328C27.9258 4.23828 28.4219 3.74219 29.0391 3.375C29.6641 3 30.3555 2.8125 31.1133 2.8125Z" fill="#80DEEA" />
                    <path d="M41.2266 2.80078C42.0781 2.80078 42.8516 3.00781 43.5469 3.42188C44.2422 3.83594 44.7969 4.39062 45.2109 5.08594C45.625 5.78125 45.832 6.55469 45.832 7.40625V12H41.2383C40.3945 12 39.6211 11.793 38.918 11.3789C38.2227 10.9648 37.668 10.4102 37.2539 9.71484C36.8398 9.01172 36.6328 8.23828 36.6328 7.39453C36.6328 6.55078 36.8398 5.78125 37.2539 5.08594C37.668 4.39062 38.2227 3.83594 38.918 3.42188C39.6133 3.00781 40.3828 2.80078 41.2266 2.80078ZM43.957 10.125V7.40625C43.957 6.64844 43.6914 6.00391 43.1602 5.47266C42.6289 4.94141 41.9844 4.67578 41.2266 4.67578C40.4766 4.67578 39.8359 4.94141 39.3047 5.47266C38.7734 6.00391 38.5078 6.64453 38.5078 7.39453C38.5078 8.14453 38.7734 8.78906 39.3047 9.32812C39.8438 9.85938 40.4883 10.125 41.2383 10.125H43.957Z" fill="#80DEEA" />
                    <path d="M53.6133 6.45703C54.3789 6.45703 55.0312 6.73047 55.5703 7.27734C56.1172 7.81641 56.3906 8.46875 56.3906 9.23438C56.3906 9.99219 56.1172 10.6445 55.5703 11.1914C55.0312 11.7305 54.3789 12 53.6133 12H47.625V10.125H53.6133C53.8633 10.125 54.0742 10.0391 54.2461 9.86719C54.4258 9.6875 54.5156 9.47656 54.5156 9.23438C54.5156 8.98438 54.4258 8.77344 54.2461 8.60156C54.0742 8.42188 53.8633 8.33203 53.6133 8.33203H52.9336H52.5586H50.0156C49.2578 8.33203 48.6055 8.0625 48.0586 7.52344C47.5195 6.98438 47.25 6.33203 47.25 5.56641C47.25 4.80078 47.5195 4.14844 48.0586 3.60938C48.6055 3.07031 49.2578 2.80078 50.0156 2.80078H56.0156V4.67578H50.0156C49.7734 4.67578 49.5625 4.76172 49.3828 4.93359C49.2109 5.10547 49.125 5.31641 49.125 5.56641C49.125 5.81641 49.2109 6.02734 49.3828 6.19922C49.5625 6.37109 49.7734 6.45703 50.0156 6.45703H52.5586H52.9336H53.6133Z" fill="#00BCD4" />
                    <path d="M62.3906 2.78906C63.2422 2.78906 64.0156 3 64.7109 3.42188C65.4062 3.83594 65.9609 4.39062 66.375 5.08594C66.7891 5.78125 66.9961 6.55078 66.9961 7.39453V11.0625V12V14.2383L65.1211 13.3008V12H62.4023C61.5586 12 60.7852 11.793 60.082 11.3789C59.3867 10.957 58.832 10.3984 58.418 9.70312C58.0039 9.00781 57.7969 8.23828 57.7969 7.39453C57.7969 6.55078 58.0039 5.78125 58.418 5.08594C58.832 4.38281 59.3867 3.82422 60.082 3.41016C60.7773 2.99609 61.5469 2.78906 62.3906 2.78906ZM62.4023 10.125H65.1211V7.39453C65.1211 6.64453 64.8555 6.00391 64.3242 5.47266C63.793 4.93359 63.1484 4.66406 62.3906 4.66406C61.6406 4.66406 61 4.93359 60.4688 5.47266C59.9375 6.00391 59.6719 6.64453 59.6719 7.39453C59.6719 8.14453 59.9375 8.78906 60.4688 9.32812C61.0078 9.85938 61.6523 10.125 62.4023 10.125Z" fill="#00BCD4" />
                    <path d="M74.7539 7.93359V2.78906H76.6289V7.93359C76.6289 8.68359 76.4414 9.36719 76.0664 9.98438C75.6992 10.5938 75.2031 11.082 74.5781 11.4492C73.9609 11.8164 73.2773 12 72.5273 12C71.7695 12 71.0781 11.8164 70.4531 11.4492C69.8359 11.082 69.3398 10.5938 68.9648 9.98438C68.5977 9.36719 68.4141 8.68359 68.4141 7.93359V2.78906H70.2891V7.93359C70.2891 8.54297 70.5078 9.06641 70.9453 9.50391C71.3828 9.93359 71.9102 10.1484 72.5273 10.1484C73.1367 10.1484 73.6602 9.93359 74.0977 9.50391C74.5352 9.06641 74.7539 8.54297 74.7539 7.93359Z" fill="#00BCD4" />
                    <path d="M82.6406 2.80078C83.4922 2.80078 84.2656 3.00781 84.9609 3.42188C85.6562 3.83594 86.2109 4.39062 86.625 5.08594C87.0391 5.78125 87.2461 6.55469 87.2461 7.40625V12H82.6523C81.8086 12 81.0352 11.793 80.332 11.3789C79.6367 10.9648 79.082 10.4102 78.668 9.71484C78.2539 9.01172 78.0469 8.23828 78.0469 7.39453C78.0469 6.55078 78.2539 5.78125 78.668 5.08594C79.082 4.39062 79.6367 3.83594 80.332 3.42188C81.0273 3.00781 81.7969 2.80078 82.6406 2.80078ZM85.3711 10.125V7.40625C85.3711 6.64844 85.1055 6.00391 84.5742 5.47266C84.043 4.94141 83.3984 4.67578 82.6406 4.67578C81.8906 4.67578 81.25 4.94141 80.7188 5.47266C80.1875 6.00391 79.9219 6.64453 79.9219 7.39453C79.9219 8.14453 80.1875 8.78906 80.7188 9.32812C81.2578 9.85938 81.9023 10.125 82.6523 10.125H85.3711Z" fill="#00BCD4" />
                    <path d="M95.9883 2.80078V1.48828L97.8633 0.550781V2.80078V3.73828V7.39453C97.8633 8.23828 97.6562 9.01172 97.2422 9.71484C96.8281 10.4102 96.2734 10.9648 95.5781 11.3789C94.8828 11.793 94.1094 12 93.2578 12C92.4141 12 91.6445 11.793 90.9492 11.3789C90.2539 10.9648 89.6992 10.4102 89.2852 9.71484C88.8711 9.01953 88.6641 8.25 88.6641 7.40625C88.6641 6.55469 88.8711 5.78125 89.2852 5.08594C89.6992 4.39062 90.2539 3.83594 90.9492 3.42188C91.6523 3.00781 92.4258 2.80078 93.2695 2.80078H95.9883ZM95.9883 7.39453V4.67578H93.2695C92.5195 4.67578 91.875 4.94141 91.3359 5.47266C90.8047 6.00391 90.5391 6.64844 90.5391 7.40625C90.5391 8.15625 90.8047 8.79688 91.3359 9.32812C91.8672 9.85938 92.5078 10.125 93.2578 10.125C94.0156 10.125 94.6602 9.85938 95.1914 9.32812C95.7227 8.78906 95.9883 8.14453 95.9883 7.39453Z" fill="#00BCD4" />
                </svg>
            </a>
        </div>
    </nav>

    <script>
        // let myModal = document.getElementById("fullscreen");
        // let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        // modal.show();
        // Mendefinisikan kelas
        class Modal {
            // Konstruktor untuk menginisialisasi properti
            constructor(header = "noButton", bg = "bg_main") {
                this.header = header;
                this.bg = bg;
            }

            // Properti statis
            headers = {
                noButton: '',
                confirm: '',
                button: '<a href="" role="button" data-bs-dismiss="modal" class="text-danger fs-4 mt-4"><i class="fa-solid fa-circle-xmark"></i></a>',
            };


            struktur = {
                start: `<div class="modal fade" id="fullscreen" tabindex="-1" aria-labelledby="fullscreenLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">`,
                header: () => {
                    return `<div class="modal-content ${this.bg}">
                <div class="header text-center mt-5">
                    ${this.headers[this.header]}
                </div>
                <div class="modal-body modal-fullscreen">`;
                },
                body: '',
                end: `</div></div></div></div>`
            };

            message(status = "200", message) {
                let html = '';
                if (status == "200") {
                    html = `<div class="d-flex justify-content-center mt-5">
                                <div class="rounded text-center text-success border bg-success bg-opacity-10 border-success px-4 py-2"><i class="fa-solid fa-circle-check"></i> ${message}</div>
                            </div>`;
                } else {
                    html = `<div class="d-flex justify-content-center mt-5">
                                <div class="rounded text-center text-danger border bg-danger bg-opacity-10 border-danger px-4 py-2"><i class="fa-solid fa-triangle-exclamation"></i> ${message}</div>
                            </div>`;
                }
                this.struktur.body = html;

                this.execute();
            }



            html(html) {
                this.struktur.body = html;
                this.execute();
            }

            execute(order = "show") {
                let html = this.struktur.start;
                html += this.struktur.header(); // Memanggil fungsi header
                html += this.struktur.body; // Memanggil fungsi body
                html += this.struktur.end;
                $('#modal_fullscreen').html(html);

                let myModal = document.getElementById("fullscreen");
                let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                if (order == "show") {
                    modal.show();
                } else {
                    modal.hide();
                }

                if (this.header == "noButton") {
                    setTimeout(() => {
                        modal.hide();
                    }, 1200);
                }
            }
        }

        // Membuat instance dari kelas Modal
        const popup = new Modal();
        const popupButton = new Modal("button");


        $(document).on('keyup', '.angka', function(e) {
            e.preventDefault();
            let val = $(this).val();

            $(this).val(angka(val));
        });


        function angka(a, prefix) {
            let angka = a.toString();
            let isNegative = angka[0] === '-'; // Check if the number is negative
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;

            // Add negative sign back if the number is negative
            if (isNegative) {
                rupiah = '-' + rupiah;
            }

            return prefix == undefined ? rupiah : (rupiah ? (isNegative ? '-Rp. ' : 'Rp. ') + rupiah : '');
        }

        const str_replace = (search, replace, subject) => {
            return subject.split(search).join(replace);
        }
        // Menampilkan pesan sukses dan memanggil metode execute
        const time_php_to_js = (date, order = undefined) => {
            let d = new Date(date * 1000);
            let month = (d.getMonth() + 1).toString().padStart(2, '0'); // Bulan dimulai dari 0, sehingga harus ditambah 1
            let day = d.getDate().toString().padStart(2, '0');
            let year = d.getFullYear();
            let hours = d.getHours().toString().padStart(2, '0'); // Format jam (dua digit)
            let minutes = d.getMinutes().toString().padStart(2, '0')

            let res = `${day}/${month}/${year}`;
            if (order !== undefined) {
                if (order == "d") {
                    res = day;
                }
                if (order === "jm") {
                    res = `${hours}:${minutes}`; // Format jam:menit
                }

            }
            return res;
        }

        function metode_tap(data, jenis, limit = 15) {
            let html = `
                    <div class="fixed-top bg_main border_main rounded text_main px-2 py-5" style="z-index: 99999;height:100%;padding-top:11px">
                        <div class="text-center">
                            <div class="position-relative" style="display: inline-block;">
                                <div class="spinner-border p-1" style="width:50px;height:50px" role="status"></div>
                                <div class="position-absolute top-50 start-50 translate-middle durasi" style="animation: none;font-size:20px;margin-top:-3px;">${limit}</div>
                            </div>
                            <div class="text-center mt-3">
                                <div class="pembeli border_bottom fs-2"></div>
                                <div class="fs-6">
                                    <div style="font-size: 10px;">DANA TERSIMPAN</div>
                                    <div class="fw-bold fulus"></div>
                                </div>
                                <div class="fs-6 mt-3 px-4">
                                    <div class="bg_secondary rounded p-2">
                                        <div style="font-size: 10px;">TOTAL TRANSAKSI</div>
                                        <div class="fw-bold total"></div>
                                    </div>
                                </div>
                                <div class="pesan mt-5">Proses...</div>
                                <div class="fs-6 saldo"></div>
                            </div>
                        </div>
                    </div>`;
            let lokasi = $(".latitude").val() + ',' + $(".longitude").val();
            $(".div_top").html(html);
            let newId = 0;
            let langkah = "";
            const checkLopp = async () => {
                if (limit <= 0) {
                    clearInterval(loopReq);
                    $('.durasi').text(0);
                    $(".pesan").html('<span class="text-danger fw-bold">Waktu Habis!.</span>');

                    post("iot/delete_id_metode_tap", {
                        newId
                    }).then(res => {
                        if (res.status == "400") {
                            message("400", res.message);
                        }
                        $(".div_top").html("");
                        user_tap = {};
                        temp_user = {};

                    })
                } else {
                    $('.durasi').text(limit--);

                }
                // Melakukan request ke server
                try {
                    const res = await post("iot/cek_metode_tap", {
                        newId,
                        langkah,
                        jenis,
                        user_tap,
                        judul,
                        data,
                        controller: div.toLowerCase()
                    });

                    if (res.status == "200") {
                        newId = res.data;
                        langkah = res.data2;
                        $(".pesan").text(res.message);
                        if (res.data3 !== null) {
                            $(".pembeli").text(res.data3.pembeli);
                            $(".fulus").text(res.data3.fulus);
                            $(".total").text(angka(res.data3.total));

                        }

                        let lokasi = $(".latitude").val() + ',' + $(".longitude").val();

                        if (res.data2 == "Tap") {
                            const exeLoop = async () => {
                                clearInterval(loopReq);
                                try {
                                    const respond = await post("iot/metode_tap", {
                                        newId,
                                        data,
                                        shift,
                                        lokasi,
                                        judul
                                    });

                                    if (respond.status) {
                                        if (respond.data.status == "400") {
                                            $(".pesan").text("text-danger");
                                        }
                                        if (respond.status == "400") {
                                            $(".pesan").text("text-danger");
                                        }
                                        $(".pesan").text(respond.message);

                                        if (respond.status == "200") {
                                            let html = `<div style="font-size: 10px;">SALDO</div>
                                                        <div class="fw-bold">${angka(respond.data.saldo)}</div>`;

                                            if (respond.data.err.length > 0) {
                                                html += `<div class="my-2" style="font-size: 11px;">Gagal: ${respond.data.err.join(", ")}</div>`;
                                            }

                                            $(".saldo").html(html);

                                        }
                                        post("iot/delete_id_metode_tap", {
                                            newId
                                        }).then(res => {
                                            if (res.status == "400") {
                                                message("400", res.message);
                                            }
                                            $(".div_top").html("");
                                            setTimeout(() => {
                                                post("iot/delete_id_metode_tap", {
                                                    newId
                                                }).then(res => {
                                                    if (res.status == "400") {
                                                        message("400", res.message);
                                                    }
                                                    $(".div_top").html("");
                                                    user_tap = {};
                                                    temp_user = {};

                                                })
                                            }, 2000);

                                        })
                                    }


                                } catch (error) {
                                    // $(".pesan").text("Terjadi kesalahan dalam proses.");
                                    console.error(error);
                                }
                            }
                            exeLoop();
                        }
                    } else {
                        // console.log("masuk");
                        clearInterval(loopReq);
                        $(".pesan").addClass("text-danger");
                        $(".pesan").text(res.message);
                        $(".fulus").text(res.data);
                        $(".total").text(res.data2);
                        if (res.message == "Saldo tidak cukup!") {
                            let x = 0;
                            setInterval(() => {
                                x++;
                                if (x == 3) {
                                    $(".pesan").text("Proses Batal!");
                                }
                                if (x == 5) {
                                    $(".div_top").html("");
                                    user_tap = {};
                                    temp_user = {};
                                }
                            }, 1000);

                        } else {
                            setTimeout(() => {
                                post("iot/delete_id_metode_tap", {
                                    newId
                                }).then(res => {
                                    if (res.status == "400") {
                                        message("400", res.message);
                                    }
                                    $(".div_top").html("");
                                    user_tap = {};
                                    temp_user = {};

                                })
                            }, 2000);

                        }
                    }


                } catch (error) {
                    $(".pesan").text("Terjadi kesalahan dalam proses.");
                    user_tap = {};
                    temp_user = {};
                    console.error(error);
                }
            }

            const loopReq = setInterval(checkLopp, 1000);

        }

        <?php if (session()->getFlashdata('gagal')) : ?>
            let msg = "<?= session()->getFlashdata('gagal'); ?>";
            message("400", msg);
        <?php endif; ?>
        <?php if (session()->getFlashdata('sukses')) : ?>
            let msg = "<?= session()->getFlashdata('sukses'); ?>";
            message("200", msg);
        <?php endif; ?>
    </script>
</body>

</html>