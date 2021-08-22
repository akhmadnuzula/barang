@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between bg-success">
                {{ __('Dashboard') }}
                @if (Auth::user()->role == 'superadmin')
                    <button class="btn btn-primary btn-sm" onclick="add()">Add Data</button>
                @endif
            </div>

            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Foto Barang</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Diskon</th>
                            @if (Auth::user()->role == 'superadmin')
                                <th scope="col">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="row">
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination" id="paginate">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>


    {{-- modal form barang --}}
    <div class="modal fade" id="formBarang" tabindex="-1" aria-labelledby="fBarang" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="fBarang">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" id="nama" aria-describedby="nama">
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <select name="kategori" id="kategori" class="form-control">
                            <option value="">Pilih Kategori</option>
                            <option value="Retail">Retail</option>
                            <option value="Wholesale">Wholesale</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input type="number" class="form-control" id="harga" aria-describedby="harga">
                    </div>
                    {{-- <div class="form-group">
                            <label for="diskon">Diskon</label>
                            <input type="number" class="form-control" id="diskon" readonly>
                        </div> --}}
                    <div class="form-group">
                        <label for="image">Foto Barang</label>
                        <input type="file" class="form-control" id="image">
                        <span class="text-muted ">Gambar boleh kosong</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="modalMessage"></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <div id="modalButton"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteBarang" tabindex="-1" aria-labelledby="dbarang" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="dbarang">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Anda yakin ingin menghapus barang ini ?
                    </p>
                </div>
                <div class="modal-footer">
                    <div id="messageDel"></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <div id="delButton"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function add() {
            var nama = $('#nama');
            var kategori = $('#kategori');
            var harga = $('#harga');
            var modalButton = $('#modalButton');
            $('#formBarang').modal('show');
            modalButton.html(`<button type="button" class="btn btn-primary" onclick="create()"><span id="buttonLoading">Submit</span></button>`);
            nama.val('');
            kategori.val('');
            harga.val('');
        }

        function create() {
            var nama = $('#nama');
            var kategori = $('#kategori');
            var harga = $('#harga');
            var modalButton = $('#modalButton');
            var modalMessage = $('#modalMessage');
            var buttonLoading = $(`#buttonLoading`);
            // var file = $('#image')[0];
            var dataform = new FormData();
            dataform.append("_token", '{{ csrf_token() }}');
            dataform.append("nama", nama.val());
            dataform.append("kategori", kategori.val());
            dataform.append("harga", harga.val());
            var image = document.getElementById('image');
            var file = image.files[0];
            if (file) {
                dataform.append("image", file);
            }
            modalMessage.html(``);
            modalButton.disabled = true;
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: "{{ route('barang_create') }}",
                data: dataform,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                beforeSend: function() {
                    buttonLoading.html(`<div class="spinner-border text-success spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>`);
                },
                success: function(res) {
                    if (res['status'] == 1) {
                        getdata(1);
                    }
                    buttonLoading.html(`Submit`);
                    modalMessage.html(res['message']);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    modalMessage.html(`Kesalahan server`);
                }
            });
        }

        function edit(id, datanama, datakategori, dataharga) {
            var nama = $('#nama');
            var kategori = $('#kategori');
            var harga = $('#harga');
            var modalButton = $('#modalButton');
            $('#formBarang').modal('show');
            modalButton.html(`<button type="button" class="btn btn-primary" onclick="update('${id}')"><span id="buttonLoading">Update</span></button>`);
            nama.val(datanama);
            kategori.val(datakategori);
            harga.val(dataharga);
        }

        function update(id) {
            var nama = $('#nama');
            var kategori = $('#kategori');
            var harga = $('#harga');
            var modalButton = $('#modalButton');
            var modalMessage = $('#modalMessage');
            var buttonLoading = $(`#buttonLoading`);
            // var file = $('#image')[0];
            var dataform = new FormData();
            dataform.append("_token", '{{ csrf_token() }}');
            dataform.append("nama", nama.val());
            dataform.append("kategori", kategori.val());
            dataform.append("harga", harga.val());
            var image = document.getElementById('image');
            var file = image.files[0];
            if (file) {
                dataform.append("image", file);
            }
            modalMessage.html(``);
            modalButton.disabled = true;
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: "{{ route('barang_create') }}/" + id,
                headers: {
                    "X-CSRF-Token": '{{ csrf_token() }}'
                },
                data: dataform,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                beforeSend: function() {
                    buttonLoading.html(`<div class="spinner-border text-success spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>`);
                },
                success: function(res) {
                    if (res['status'] == 1) {
                        getdata(1);
                    }
                    buttonLoading.html(`Update`);
                    modalMessage.html(res['message']);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    modalMessage.html(`Kesalahan server`);
                }
            });
        }

        function del(id) {
            $('#deleteBarang').modal('show')
            var delLoading = $('#delLoading');
            var delButton = $('#delButton');
            delButton.html(`<button type="button" class="btn btn-primary" onclick="yesdel(${id})"><span id="delLoading">Yes Delete</span></button>`);
            delLoading.val('');
        }

        function yesdel(id) {
            var messageDel = $('#messageDel');
            var delLoading = $('#delLoading');
            $.ajax({
                type: "GET",
                enctype: 'application/x-www-form-urlencoded',
                url: "{{ route('root') }}/api/barang/delete/" + id,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                beforeSend: function() {
                    delLoading.html(`<div class="spinner-border text-success spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>`);
                },
                success: function(res) {
                    list = res['list_barang'];
                    if (res['status'] == 1) {
                        getdata(1);
                    }
                    delLoading.html(`Yes Delete`);
                    messageDel.html(res['message']);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    delLoading.html(`Kesalahan Server`);
                }
            });
        }

        window.addEventListener('DOMContentLoaded', (event) => {
            getdata(1)
        });

        function getdata(page) {
            var row = document.getElementById('row');
            no = (page * 10) - 9;
            $.ajax({
                type: "GET",
                enctype: 'application/x-www-form-urlencoded',
                url: "{{ route('root') }}/api/barang/" + no,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                beforeSend: function() {
                    row.innerHTML = `<tr><td colspan="7" class="text-center"><div class="spinner-border text-success spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div></td></tr>`;
                },
                success: function(res) {
                    list = res['list_barang'];
                    row.innerHTML = '';
                    for (i = 0; i < list.length; i++) {
                        img = list[i]['image'] != null || list[i]['image'] != '' ? `{{ asset('storage/images') }}/${list[i]['image']}` : '';
                        btn = '';
                        @if (Auth::user()->role == 'superadmin')
                            btn = `<td>
                                <button class="btn btn-success btn-sm" onclick="edit('${list[i]['id']}','${list[i]['nama']}','${list[i]['kategori']}','${list[i]['harga']}')">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="del('${list[i]['id']}')">Delete</button>
                            </td>`;
                        @endif
                        row.innerHTML += `<tr>
                                <th scope="row">${no++}</th>
                                <td>
                                    <img src="${img}" style="height: 50px; width:50px;">
                                </td>
                                <td>${list[i]['nama']}</td>
                                <td>${list[i]['kategori']}</td>
                                <td>${list[i]['harga']}</td>
                                <td>${list[i]['diskon']}</td>
                                ${btn}
                            </tr>`;
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    row.innerHTML = `<tr><td colspan="7" class="text-center">Kesalahan Server</td></tr>`;
                }
            });
        }

        function makePaginate(total_records, pageno) {
            var paging = $('#paginate');
            var limit = 10;
            var jumlah_page = Math.ceil(total_records / limit);

            // out of range check
            if (pageno > jumlah_page) {
                pageno = jumlah_page;
            } else if (pageno < 1) {
                pageno = 1;
            }

            paging.innerHTML = '';
            if (total_records > 0) {
                // links
                if (pageno == 1) { // Jika page adalah page ke 1, maka disable link PREV
                    paging.innerHTML += `<li class="page-item disabled"><button class="page-link">First</button></li>
                        <li class="page-item disabled"><button class="page-link">&laquo;</button></li>
                        `;
                } else { // Jika page bukan page ke 1
                    var link_prev = (pageno > 1) ? pageno - 1 : 1;
                    paging.innerHTML += `<li class="page-item"><button onclick="getdata(1)" class="page-link">First</button></li>
                        <li class="page-item"><button onclick="data(${link_prev})" class="page-link">&laquo;</button></li>
                        `;
                }

                // links Paging Number
                var jumlah_number = total_records < 3 ? total_records : 3; // Tentukan jumlah link number sebelum dan sesudah page yang aktif
                var start_number = (pageno > jumlah_number) ? pageno - jumlah_number : 1; // Untuk awal link number
                var end_number = (pageno < (jumlah_page - jumlah_number)) ? pageno + jumlah_number : jumlah_page; // Untuk akhir link number

                for (i = start_number; i <= end_number; i++) {
                    var link_active = (pageno == i) ? ' active' : '';
                    paging.innerHTML += `<li class="page-item ${link_active}"><button class="page-link" onclick="getdata(${i})">${i}</button></li>`;
                }

                if (pageno == jumlah_page) { // Jika page terakhir
                    paging.innerHTML += `<li class="page-item disabled"><button class="page-link">&raquo;</button></li>
                        <li class="page-item disabled"><button class="page-link">Last</button></li>
                        `;
                } else { // Jika Bukan page terakhir
                    link_next = (pageno < jumlah_page) ? pageno + 1 : jumlah_page;
                    paging.innerHTML += `<li class="page-item"><button onclick="getdata(${link_next})" class="page-link">&raquo;</button></li>
                        <li class="page-item"><button onclick="getdata(${jumlah_page})" class="page-link">Last</button></li>
                        `;
                }
            }
        }
    </script>
@endsection
