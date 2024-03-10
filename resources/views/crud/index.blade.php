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
                            <input type="checkbox" name="buat_dummy" class="form-check-input">
                            <label for="folder_controller">Dummy Data?</label>
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
                <div class="col-6">
                    <button type="button" name="addRow" id="addRow" class="btn btn-success">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" name="create" id="create" class="btn btn-primary">
                Create
            </button>
        </div>
    </div>

    <script>
        // Logika untuk menambahkan baris baru
        $(document).on('click', '#addRow', function() {
            var columnsContainer = $('#columnsContainer');
            var newRow = $('<div class="columnRow">' +
                '<div class="row mt-3 mb-3">' +
                '<div class="col-2">' +
                '<label for="nama_kolom">Nama Kolom</label>' +
                '<input type="text" class="form-control" name="nama_kolom" id="nama_kolom" placeholder="nama kolom">' +
                '</div>' +
                '<div class="col-2">' +
                '<label for="type_data">Type Data</label>' +
                '<select class="form-select" name="type_data" id="type_data">' +
                '<option selected>Select one</option>' +
                '<option value="CHAR">CHAR</option>' +
                '<option value="VARCHAR">VARCHAR</option>' +
                '<option value="TEXT">TEXT</option>' +
                '<option value="INT">INT</option>' +
                '<option value="BIGINT">BIGINT</option>' +
                '<option value="FLOAT">FLOAT</option>' +
                '<option value="DOUBLE">DOUBLE</option>' +
                '<option value="DECIMAL">DECIMAL</option>' +
                '<option value="DATE">DATE</option>' +
                '<option value="TIME">TIME</option>' +
                '<option value="DATETIME">DATETIME</option>' +
                '<option value="TIMESTAMP">TIMESTAMP</option>' +
                '<option value="ENUM">ENUM</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-1">' +
                '<label for="Panjang Data">Panjang Data</label>' +
                '<input type="text" class="form-control" name="panjang" id="panjang">' +
                '</div>' +
                '<div class="col-1">' +
                '<label for="inputType">Jenis Input</label>' +
                '<select class="form-select inputType" name="inputType" id="inputType">' +
                '<option selected>Select one</option>' +
                '<option value="text">Text</option>' +
                '<option value="number">Number</option>' +
                '<option value="date">Date</option>' +
                '<option value="time">Time</option>' +
                '<option value="email">Email</option>' +
                '<option value="password">Password</option>' +
                '<option value="checkbox">Checkbox</option>' +
                '<option value="radio">Radio Button</option>' +
                '<option value="file">File</option>' +
                '<option value="textarea">Textarea</option>' +
                '<option value="select">Select</option>' +
                '<option value="multiselect">Multiselect</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-2 additionalInput" style="display:none;">' +
                '<label for="additionalInput">Additional Input</label>' +
                '<select class="form-select" name="additionalInput" id="additionalInput">' +
                '<option value="">Manual</option>' +
                '<option value="DB">Database</option>' +
                '</select>' +
                '</div>' +
                '<div class="additionalInput manualInput" style="display:none;">' +
                '<div class="col-2">' +
                '<label for="manualInput">Manual Input</label>' +
                '<input type="text" class="form-control" name="manualInput" id="manualInput" placeholder="Separated by comma">' +
                '</div>' +
                '</div>' +
                '<div class="additionalInput dbInput" style="display:none;">' +
                '<div class="col-2">' +
                '<label for="dbInput">Database Input</label>' +
                '<select class="form-select" name="dbInput" id="dbInput">' +
                @foreach ($columns as $column)
                    '<option value="{{ $column }}">{{ $column }}</option>' +
                @endforeach
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-2">' +
                '<label for="relasi">Relasi Model</label>' +
                '<select class="form-select" name="relasi" id="relasi">' +
                '<option selected>Select one</option>' +
                '@foreach ($models as $model)' +
                '<option value="{{ $model }}">{{ $model }}</option>' +
                '@endforeach' +
                '</select>' +
                '</div>' +
                '<div class="col-2">' +
                '<label for="acuan">Kolom Acuan</label>' +
                '<select class="form-select" name="acuan" id="acuan">' +
                '<option selected>Select one</option>' +
                @foreach ($columns as $column)
                    '<option value="{{ $column }}">{{ $column }}</option>' +
                @endforeach
                '</select>' +
                '</div>' +
                '<div class="col-1 mt-5">' +
                '<input type="checkbox" name="wajib" class="form-check-input">' +
                '<label for="folder_controller">Wajib?</label>' +
                '</div>' +
                '<div class="col-1 mt-3">' +
                '<button type="button" class="btn btn-danger deleteRow"><i class="fa fa-trash"></i></button>' +
                '</div>' +
                '</div>' +
                '</div>');

            columnsContainer.append(newRow);
        });

        // Menampilkan input tambahan berdasarkan pilihan jenis input
        $(document).on('change', '.inputType', function() {
            var selectedOption = $(this).val();
            var additionalInputContainer = $(this).closest('.columnRow').find('.additionalInput');
            additionalInputContainer.hide();
            if (selectedOption === 'select' || selectedOption === 'multiselect') {
                additionalInputContainer.show();
            }
        });

        // Menampilkan input tambahan berdasarkan pilihan tambahan input (Manual atau Database)
        $(document).on('change', '[name="additionalInput"]', function() {
            var selectedOption = $(this).val();
            var manualInput = $(this).closest('.columnRow').find('.manualInput');
            var dbInput = $(this).closest('.columnRow').find('.dbInput');
            manualInput.hide();
            dbInput.hide();
            if (selectedOption === 'Manual') {
                manualInput.show();
            } else if (selectedOption === 'DB') {
                dbInput.show();
            }
        });

        // Logika untuk menghapus baris dengan jquery
        $(document).on('click', '.deleteRow', function(event) {
            $(this).closest('.columnRow').remove();
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
        });

        $('#create').click(function(e) {
            e.preventDefault();
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
                },
                success: function(response) {
                    console.log(response);
                }
            });
        });
    </script>
@endsection
