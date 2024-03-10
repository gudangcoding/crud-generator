@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h1>CRUD Generator</h1>
        </div>
        <div class="card-body">
            <div id="columnsContainer">
                <form action="" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-2">
                            <label for="nama_tabel">Nama Tabel</label>
                            <input type="text" class="form-control" name="nama_tabel" placeholder="Masukkan nama Tabel">
                        </div>
                        <div class="col-2">
                            <label for="nama_model">Nama Model</label>
                            <input type="text" class="form-control" name="nama_model">
                        </div>
                        <div class="col-2">
                            <label for="nama_controller">Nama Controller</label>
                            <input type="text" class="form-control" name="nama_controller">
                        </div>
                        <div class="col-3">
                            <label for="folder_controller">Folder Controller</label>
                            <input type="text" class="form-control" name="folder_controller"
                                placeholder="Isi jika controller di buat dalam folder">
                        </div>

                        <div class="col-1 mt-5">
                            <input type="checkbox" name="batasi" class="form-check-input">
                            <label for="batasi">Autentikasi?</label>
                        </div>
                        <div class="col-1 mt-5">
                            <input type="checkbox" name="api" class="form-check-input">
                            <label for="api">Api?</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row mt-3 justify-content-between">
                <div>
                    <button type="button" name="addRow" id="addRow" class="btn btn-success">
                        add
                    </button>
                </div>
                <div>
                    <button type="button" name="create" id="create" class="btn btn-primary">
                        Create
                    </button>
                </div>
            </div>





        </div>

        <script>
            // Logika untuk menambahkan baris baru
            document.getElementById('addRow').addEventListener('click', function() {
                var columnsContainer = document.getElementById('columnsContainer');
                var newRow = document.createElement('div');
                newRow.classList.add('columnRow');
                newRow.innerHTML = `

                        <div class="row mt-3 mb-3">
                        <div class="col-2">
                            <label for="nama_kolom">Nama Kolom</label>
                            <input type="text" class="form-control" name="nama_kolom" id="nama_kolom"
                                placeholder="nama kolom">
                        </div>
                        <div class="col-2">
                            <label for="type_data">Type Data</label>
                            <select class="form-select" name="type_data" id="type_data">
                                <option selected>Select one</option>
                                <option value="">New Delhi</option>
                                <option value="">Istanbul</option>
                                <option value="">Jakarta</option>
                            </select>

                        </div>

                        <div class="col-2">
                            <label for="inputType">Jenis Input</label>
                            <select class="form-select" name="inputType" id="inputType">
                                <option selected>Select one</option>
                                <option value="">New Delhi</option>
                                <option value="">Istanbul</option>
                                <option value="">Jakarta</option>
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="relasi">Relasi Model</label>
                            <select class="form-select" name="relasi" id="relasi">
                                <option selected>Select one</option>
                                @foreach ($models as $model)
                                    <option value="{{ $model }}">{{ $model }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="acuan">Kolom Acuan</label>
                            <select class="form-select" name="acuan" id="acuan">
                                <option selected>Select one</option>
                                @foreach ($columns as $column)
                                    <option value="{{ $column }}">{{ $column }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-1 mt-5">
                            <input type="checkbox" name="buat_dummy" class="form-check-input">
                            <label for="folder_controller">Dummy Data?</label>
                        </div>
                        <div class="col-1 mt-5">
                            <input type="checkbox" name="wajib" class="form-check-input">
                            <label for="folder_controller">Wajib?</label>
                        </div>
                `;
                columnsContainer.appendChild(newRow);
            });

            // Logika untuk menghapus baris dengan jquery
            document.getElementById('columnsContainer').addEventListener('click', function(event) {
                if (event.target.classList.contains('deleteRow')) {
                    event.target.parentElement.remove();
                }
            });
            //membuat event jika nama tabel di enter menggunakan jquery
            $('input[name="nama_tabel"]').on('change', function() {
                //menjadikan value huruf kapital di awal kata
                var nama = $(this).val().replace(/\b\w/g, l => l.toUpperCase());
                nama_controller = nama + "Controller"
                $('input[name="nama_model"]').val(nama);
                $('input[name="nama_controller"]').val(nama_controller);
                var folder = $('input[name="folder_controller"]').val();
                $('input[name="folder_controller"]').val(nama + "/" + nama_controller);
                //ajax jquery post ke CrudController create
                $.ajax({
                    url: "{{ route('crud.generate') }}",
                    method: "POST",
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        nama_tabel: $('input[name="nama_tabel"]').val(),
                        nama_model: $('input[name="nama_model"]').val(),
                        nama_controller: $('input[name="nama_controller"]').val(),
                        folder_controller: $('input[name="folder_controller"]').val(),
                        batasi: $('input[name="batasi"]').val(),
                        api: $('input[name="api"]').val(),
                        success: function(response) {
                            console.log(response);
                        }
                    }
                });
            });
        </script>
    @endsection
