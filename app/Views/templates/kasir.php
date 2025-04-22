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
        .data_list {
            position: absolute;
            top: 100%;
            width: 100%;
            left: 0;
            z-index: 99999;
            background-color: rgb(48, 51, 54);
            color: #f8f9fa;

        }

        .data_list div {
            padding: 10px;
            border-bottom: 1px solid rgb(93, 94, 95);
            cursor: pointer;
        }

        .body_tabel_transaksi {
            max-height: 400px;
            /* Batasi tinggi maksimum */
            overflow-y: auto;
            /* Aktifkan scroll hanya jika konten tabel melebihi batas */
            display: block;
            /* Memastikan elemen dapat di-scroll */
        }

        .body_tabel_transaksi table {
            width: 100%;
            /* Pastikan tabel memenuhi container */
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
    <input type="hidden" class="latitude">
    <input type="hidden" class="longitude">
    <div class="div_top"></div>

    <div id="modal_fullscreen"></div>


    <?= $this->renderSection('content') ?>
    <div class="fixed-bottom message" style="margin-bottom: 90px;z-index:999999"></div>


    <script>
        const successCb = (position) => {
            latitude = position.coords.latitude;
            longitude = position.coords.longitude;
            $(".latitude").val(latitude);
            $(".longitude").val(longitude);
            // $(".map").html('<iframe width="100%" height="600" src="https://maps.google.com/maps?q=-7.441695779343123,111.03870220698958&amp;z=15&amp;output=embed"></iframe>');
        }

        const errorCb = (error) => {
            console.error(error);
        }

        navigator.geolocation.getCurrentPosition(successCb, errorCb, {
            enableHighAccuracy: true,
            maximumAge: 0
        });

        const upper_first = (str) => {
            let arr = str.split(" ");
            for (var i = 0; i < arr.length; i++) {
                arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);

            }

            let res = arr.join(" ");

            return res;
        }

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
                    return `<div class="modal-content bg-dark text-light">
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
            let year = d.getFullYear().toString();
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
                if (order === "full") {
                    res = `${day}/${month}/${year.slice(-2)} ${hours}:${minutes}`; // Format jam:menit
                }

            }
            return res;
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