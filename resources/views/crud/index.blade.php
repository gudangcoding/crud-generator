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
                            <input type="checkbox" name="buat_dummy" class="form-check-input" checked>
                            <label for="folder_controller">Dummy Data?</label>
                        </div>
                        <div class="col-1 mt-5">
                            <input type="checkbox" name="batasi" class="form-check-input" checked>
                            <label for="batasi">Autentikasi?</label>
                        </div>
                        <div class="col-1 mt-5">
                            <input type="checkbox" name="api" class="form-check-input" checked>
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
            var columnsContainer = $('form');
            var newRow = $('<div class="columnRow">' +
                '<div class="row mt-3 mb-3">' +
                '<div class="col-2">' +
                '<label for="kolom">Nama Kolom</label>' +
                '<input type="text" class="form-control" name="kolom[]">' +
                '</div>' +
                '<div class="col-2">' +
                '<label for="type_data">Type</label>' +
                '<select class="form-select dataType" name="type_data[]">' +
                '<option value="char">CHAR</option>' +
                '<option value="varchar">VARCHAR</option>' +
                '<option value="text">TEXT</option>' +
                '<option value="int">INT</option>' +
                '<option value="bigint">BIGINT</option>' +
                '<option value="float">FLOAT</option>' +
                '<option value="double">DOUBLE</option>' +
                '<option value="decimal">DECIMAL</option>' +
                '<option value="date">DATE</option>' +
                '<option value="time">TIME</option>' +
                '<option value="datetime">DATETIME</option>' +
                '<option value="timestamp">TIMESTAMP</option>' +
                '<option value="enum">ENUM</option>' +
                '</select>' +
                '<div class="enum">' +
                '</div>' +
                '</div>' +
                '<div class="col-1">' +
                '<label>Length</label>' +
                '<input type="text" class="form-control" name="lengthData[]">' +
                '</div>' +
                '<div class="col-1">' +
                '<label>Decimal</label>' +
                '<input type="text" class="form-control" name="decimal[]">' +
                '</div>' +
                '<div class="col-1">' +
                '<label>Default</label>' +
                '<select class="form-select inputType" name="default[]">' +
                '<option value="nullable">Null</option>' +
                '<option value="">Empty</option>' +
                '<option value="Unique">Unique</option>' +
                '</select>' +
                '</div>' +

                '<div class="col-1">' +
                '<label for="relasi">Relasi Model</label>' +
                '<select class="form-select relasi" name="relasi[]">' +
                '<option value="">Pilih</option>' +

                @foreach ($models as $model)
                    '<option value="App\\Models\\{{ $model }}">{{ $model }}</option>' +
                @endforeach

                '</select>' +
                '</div>' +
                '<div class="col-1">' +
                '<label for="acuan">Kolom Acuan</label>' +
                '<select class="form-select acuan" name="acuan[]">' +
                '<option value="">Pilih</option>' +
                @foreach ($columns as $column)
                    '<option value="{{ $column }}">{{ $column }}</option>' +
                @endforeach
                '</select>' +
                '</div>' +
                '<div class="col-1">' +
                '<label for="inputType">Jenis Input</label>' +
                '<select class="form-select inputType" name="inputType[]">' +
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
                '<div class="col-12 additionalInput" style="display:none;">' +
                '<br>' +
                '<select class="form-select" name="additionalInput[]">' +
                '<option value="">Select</option>' +
                '<option value="Manual">Manual</option>' +
                '<option value="DB">Database</option>' +
                '</select>' +
                '</div>' +
                '<div class="manualInput" style="display:none;">' +
                '<div class="col-12 manualInput">' +
                '<label for="manualInput">Manual Input</label>' +
                '<input type="text" class="form-control" name="manualInput[]">' +
                '</div>' +
                '</div>' +
                '<div class="dbInput" style="display:none;">' +
                '<div class="col-12">' +
                '<label for="dbInput">Database Input</label>' +
                '<select class="form-select" name="dbInput[]" >' +
                @foreach ($columns as $column)
                    '<option value="{{ $column }}">{{ $column }}</option>' +
                @endforeach
                '</select>' +
                '</div>' +
                '</div>' +
                '</div>' +

                '<div class="col-1 mt-5">' +
                '<input type="checkbox" name="wajib[]" class="form-check-input" checked>' +
                '<label for="folder_controller"> Wajib?</label>' +
                '</div>' +
                '<div class="col-1 mt-3">' +
                '<button type="button" class="btn btn-danger deleteRow"><i class="fa fa-trash"></i></button>' +
                '</div>' +
                '</div>' +
                '</div>');

            columnsContainer.append(newRow);
        });

        // Menampilkan input tambahan berdasarkan pilihan jenis input
        $(document).on('change', '.dataType', function() {
            var selectedOption = $(this).val();
            var cekbaris = $(this).closest('.columnRow').find('.enum');
            cekbaris.hide();
            if (selectedOption === 'enum') {
                cekbaris.append(
                    '<input type="text" class="form-control" name="enum[]" placeholder="Pisah dengan koma">'
                );

                cekbaris.show();
            }
        });

        $(document).on('change', '.relasi', function() {
            var selectedOption = $(this).val();
            var cekbaris = $(this).closest('.columnRow').find('.acuan');
            cekbaris.empty(); // Kosongkan elemen select sebelum menambahkan opsi baru
            cekbaris.hide();
            // Kirim permintaan Ajax untuk mendapatkan informasi kolom
            $.ajax({
                url: '/crud/getkolom', // Sesuaikan dengan route yang Anda tentukan di web.php
                type: 'POST',
                data: {
                    modelName: selectedOption,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(columns) {
                    for (var i = 0; i < columns.length; i++) {
                        cekbaris.append('<option value="' + columns[i] + '">' + columns[i] +
                            '</option>');
                    }

                    // Tampilkan elemen select
                    cekbaris.show();
                }
            });
        });


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


        $(document).on('click', '#create', function() {
            var formData = $('form').serializeArray();
            // console.log(JSON.stringify(formData));
            $.ajax({
                url: '{{ route('crud.generate') }}', // Ganti dengan URL endpoint Anda
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log(response.message);
                    // Tambahkan logika atau tindakan lain setelah berhasil menyimpan data
                },
                error: function(error) {
                    console.error('Error:', error);
                    // Handle error
                }
            });
        });
    </script>
@endsection
