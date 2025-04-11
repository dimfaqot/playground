    <!-- Modal -->
    <div class="modal fade" id="fullscreen" tabindex="-1" aria-labelledby="fullscreenLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark">
                <div class="header text-center mt-5">
                    <a href="" class="text-danger fs-4"><i class="fa-solid fa-circle-xmark"></i></a>
                </div>
                <div class="modal-body modal-fullscreen">

                </div>

            </div>
        </div>
    </div>

    <!-- cari -->
    <div class="input-group input-group-sm mb-3">
        <span class="input-group-text bg-secondary border border-light">Cari Data</span>
        <input type="text" class="form-control cari bg-secondary border border-light text-light" placeholder="....">
    </div>


    <!-- table -->
    <table class="table table-sm table-dark table-striped" style="font-size: 12px;">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Link</th>
                <th scope="col">Last</th>
                <th scope="col">Handle</th>
            </tr>
        </thead>
        <tbody class="tabel_search">
            <?php foreach ($data as $k => $i): ?>
                <tr>
                    <th><?= $k + 1; ?></th>
                    <td><?= $i['nama']; ?></td>
                    <td><?= $i['role']; ?></td>
                    <td><a role="button" class="copy_text" data-text="<?= $i['link']; ?>" href=""><i class="fa-solid fa-link"></i> Link</a></td>
                    <td><a href="" role="button" class="text-danger fs-6 btn_confirm btn_confirm_<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-id="<?= $i['id']; ?>"><i class="fa-solid fa-trash-can"></i></a> <a role="button" class="text-warning fs-6 btn_update" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-square-pen"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- form -->
    <form action="<?= base_url(menu()['controller']); ?>/add" method="post">
        <div class="mb-3">
            <label style="font-size: 12px;">Role</label>
            <select class="form-select form-select-sm mb-3" name="role">
                <?php foreach (options('role') as $i): ?>
                    <option <?= ($i['value'] == 'Member' ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label style="font-size: 12px;">Menu</label>
            <input placeholder="Menu" type="text" name="menu" class="form-control form-control-sm" required>
        </div>

        <div class="d-grid">
            <button class="btn btn-sm btn-secondary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
        </div>
    </form>


    <!-- utilities -->
    <!-- warning -->
    <div style="font-size:small;"><span class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span> DATA TIDAK DITEMUKAN!.</div>


    <script>
        let lat_bawah = parseFloat(-7.4412100);
        let lat_atas = parseFloat(-7.4410950);
        let long_atas = parseFloat(111.0364900);
        let long_bawah = parseFloat(111.035000);


        const successCb = (position) => {

            // console.log(111.0360068 < long_atas);
            // console.log(111.0360068 > long_bawah);
            let maphtml = '';
            let latitude = position.coords.latitude;
            let longitude = position.coords.longitude;


            // console.log(latitude + ' > ' + lat_atas + ' = ' + (latitude > lat_bawah));
            // console.log(latitude + ' < ' + lat_atas + ' = ' + (latitude > lat_atas));
            // console.log(longitude + ' > ' + long_atas + ' = ' + (longitude > long_atas));
            // console.log(longitude + ' < ' + long_bawah + ' = ' + (longitude > long_bawah));
            maphtml += '<p>Latitude: ' + latitude + ' Longitude: ' + longitude + '</p>';
            maphtml += '<iframe width="100%" height="600" src="https://maps.google.com/maps?q=' + latitude + ',' + longitude + '&amp;z=15&amp;output=embed"></iframe>';
            $('.map_lokasi_saya').html(maphtml);
            // if (latitude < lat_atas && latitude > lat_bawah && longitude < long_atas && longitude > lat_bawah) {
            //     $('.lokasimu').html('<h3 class="lokasimu" style="color:green"><i class="fa-solid fa-circle-check"></i> KAMU BERADA DALAM AREA!</h3>');
            //     setTimeout(() => {
            //         const d = new Date();
            //         let time = d.getTime();
            //         let data = {
            //             latitude,
            //             longitude,
            //             id: '<?= session('id'); ?>',
            //             time
            //         }

            //         post('absen/encode', {
            //             data
            //         }).then(res => {
            //             if (res.status == '200') {
            //                 window.location.href = "<?= base_url('presentation/'); ?>" + res.data;
            //             }
            //         })
            //     }, 2000);

            // } else {

            //     $('.lokasimu').html('<h3 style="color:red"><i class="fa-solid fa-triangle-exclamation"></i> LOKASIMU DI LUAR AREA!</h3>');

            //     setTimeout(() => {
            //         const d = new Date();
            //         let time = d.getTime();
            //         let data = {
            //             latitude,
            //             longitude,
            //             id: '<?= session('id'); ?>',
            //             time
            //         }

            //         post('absen/encode', {
            //             data
            //         }).then(res => {
            //             if (res.status == '200') {
            //                 window.location.href = "<?= base_url('presentation/'); ?>" + res.data;
            //             }
            //         })
            //     }, 2000);
            // }

            // if (longitude > long_atas && longitude < lat_bawah) {
            //     $('.body_login').text("(long) LOKASIMU DI LUAR AREA!.");
            //     return false;
            // }


        }

        const errorCb = (error) => {
            console.error(error);
        }

        // $(document).on('click', '.lokasi_saya', function(e) {
        //     e.preventDefault();


        navigator.geolocation.getCurrentPosition(successCb, errorCb, {
            enableHighAccuracy: true,
            maximumAge: 0
        });
    </script>
    <script>
        $(document).on('keyup', '.cari', function(e) {
            e.preventDefault();
            let value = $(this).val().toLowerCase();
            $('.tabel_search tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });

        });


        let data = <?= json_encode($data); ?>;
        let options = <?= json_encode(options('Role')); ?>;

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
            const popup1 = new Modal("button");
            let html = `<div class="container">
                        <form action="<?= base_url(menu()['controller']); ?>/update" method="post">
                        <input type="hidden" name="id" value="${val.id}">
                        <div class="mb-3">
                            <label style="font-size: 12px;">Role</label>
                            <select class="form-select form-select-sm mb-3" name="role">`;

            options.forEach(o => {
                html += `<option ${(o.value==val.role?'selected':'')} value="${o.value}">${o.value}</option>`
            });


            html += `</select>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Menu</label>
                            <input placeholder="Menu" type="text" name="menu" value="${val.menu}" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Tabel</label>
                            <input placeholder="Tabel" type="text" name="tabel" value="${val.tabel}" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Controller</label>
                            <input placeholder="Controller" type="text" name="controller" value="${val.controller}" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Icon</label>
                            <input placeholder="Icon" type="text" name="icon" value="${val.icon}" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label style="font-size: 12px;">Grup</label>
                            <input placeholder="Grup" type="text" name="grup" value="${val.grup}" class="form-control form-control-sm">
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-sm btn-secondary"><i class="fa-solid fa-square-pen"></i> Update</button>
                        </div>
                    </form></div>`;

            popup1.html(html);
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
                    popup.message(res.status, res.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                } else {
                    popup.message(res.status, res.message);
                }
            })
        })


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

        // let myModal = document.getElementById("fullscreen");
        // let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        // modal.show();

        // Mendefinisikan kelas
        class Modal {
            // Konstruktor untuk menginisialisasi properti
            constructor(header = "noButton", bg = "bg-dark") {
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

            confirm(cls, message = "Yakin hapus data ini?") {
                let html = `<div class="d-flex justify-content-center mt-5">
                            <div class="rounded text-center text-warning border bg-warning bg-opacity-10 border-warning px-4 py-2">
                                <div class="d-flex justify-content-between gap-3">
                                    <div class="fs-6 pt-1"><i class="fa-solid fa-circle-check"></i> ${message}</div>
                                    <div>
                                        <button `;
                let dataAttr = getDataAttr(cls);
                dataAttr.forEach(e => {
                    html += `data-${e.key}="${e.value}"`;
                });

                html += ` class="btn btn-sm btn-success btn_delete"><i class="fa-solid fa-circle-check"></i></button>
                                        <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-ban"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div></div>
                        </div>`;
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
        const popup_confirm = new Modal("confirm");

        // Menampilkan pesan sukses dan memanggil metode execute
        // popup.confirm("myElement");

        function getDataAttr(selector) {
            // Mengambil semua elemen dengan selector kelas yang diberikan
            const elements = document.querySelectorAll("." + selector);
            const result = [];
            // Iterasi melalui setiap elemen yang ditemukan
            elements.forEach((element) => {
                const dataAttributes = {};

                // Mendapatkan semua atribut elemen
                for (let attr of element.attributes) {
                    // Memeriksa apakah atribut dimulai dengan "data-"
                    if (attr.name.startsWith("data-")) {
                        const key = attr.name.slice(5); // Menghapus "data-" dari nama atribut
                        const value = attr.value; // Mengambil nilai atribut
                        // dataAttributes[key] = value;
                        result.push({
                            key,
                            value
                        });
                    }
                }

                // Menyimpan objek dataAttributes ke dalam array hasil
                // result.push(dataAttributes);
            });

            return result;
        }

        function angka(a, prefix) {
            let angka = a.toString();
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }


        const str_replace = (search, replace, subject) => {
            return subject.split(search).join(replace);
        }

        $(document).on('keyup', '.angka', function(e) {
            e.preventDefault();
            let val = $(this).val();

            $(this).val(angka(val));
        });

        // untuk format rupiah dalam td
        $(document).on('keyup', '.angka_text', function(e) {
            e.preventDefault();
            let value = $(this).text();

            // Hapus format lama (non-angka)
            value = value.replace(/[^0-9]/g, '');

            // Format angka tanpa "Rp"
            let formatted = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(value || 0);

            // Masukkan kembali angka ke dalam <td>
            $(this).text(formatted);

            // Memastikan kursor tetap berada di posisi terakhir
            const selection = window.getSelection();
            const range = document.createRange();
            range.selectNodeContents(this);
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);
        });

        const time_php_to_js = (date) => {
            let d = new Date(date * 1000);
            let month = (d.getMonth() + 1).toString().padStart(2, '0'); // Bulan dimulai dari 0, sehingga harus ditambah 1
            let day = d.getDate().toString().padStart(2, '0');
            let year = d.getFullYear();

            let res = `${day}/${month}/${year}`;
            return res;
        }

        <?php if (session()->getFlashdata('gagal')) : ?>
            let msg = "<?= session()->getFlashdata('gagal'); ?>";
            popup.message("400", msg);
        <?php endif; ?>
        <?php if (session()->getFlashdata('sukses')) : ?>
            let msg = "<?= session()->getFlashdata('sukses'); ?>";
            popup.message("200", msg);
        <?php endif; ?>
    </script>