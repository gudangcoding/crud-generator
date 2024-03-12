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
                //kolom
                '<div class="col-2">' +
                '<label for="kolom">Nama Kolom</label>' +
                '<input type="text" class="form-control" name="kolom[]">' +
                '</div>' +
                //type data pada kolom
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
                '<br>' +
                '</div>' +
                '</div>' +
                //menerima panjang data
                '<div class="col-1">' +
                '<label>Length</label>' +
                '<input type="text" class="form-control" value="255" name="lengthData[]">' +
                '</div>' +
                //jika pilihan decimal datanya
                '<div class="col-1">' +
                '<label>Decimal</label>' +
                '<input type="text" class="form-control" name="decimal[]" value="0">' +
                '</div>' +
                //pilihan default data
                '<div class="col-1">' +
                '<label>Default</label>' +
                '<select class="form-select inputType" name="default[]">' +
                '<option value="nullable">Null</option>' +
                '<option value="">Empty</option>' +
                '<option value="Unique">Unique</option>' +
                '</select>' +
                '</div>' +
                //menampilkan seluruh model dari folder App\Models
                '<div class="col-1">' +
                '<label for="relasi">Relasi Model</label>' +
                '<select class="form-select relasi" name="relasi[]">' +
                '<option value="">Pilih</option>' +
                @foreach ($models as $model)
                    '<option value="App\\Models\\{{ $model }}">{{ $model }}</option>' +
                @endforeach
                '</select>' +
                //pilihan jenis relasi
                '<div class="col-12 relasiModel" style="display:none;">' +
                '<br>' +
                '<select class="form-select" name="relasiModel[]">' +
                '<option value="">Select</option>' +
                '<option value="HasOne">HasOne</option>' +
                '<option value="BelongsTo">BelongsTo</option>' +
                '<option value="BelongsToMany">BelongsToMany</option>' +
                '<option value="HasMany">HasMany</option>' +
                '<option value="hasManyThrough">hasManyThrough</option>' +
                '<option value="morphTo">morphTo</option>' +
                '<option value="morphMany">morphMany</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                //jika model relasi dipilih tampilkan kolom pada migration
                '<div class="col-1">' +
                '<label for="acuan">Kolom Acuan</label>' +
                '<select class="form-select acuan" name="acuan[]">' +
                '<option value="">Pilih</option>' +
                @foreach ($columns as $column)
                    '<option value="{{ $column }}">{{ $column }}</option>' +
                @endforeach
                '</select>' +
                '</div>' +
                //untuk input
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
                //pemilihan additional input
                '<div class="col-12 additionalInput" style="display:none;">' +
                '<br>' +
                '<select class="form-select additionalInputType" name="additionalInput[]">' +
                '<option value="" selected>Select</option>' +
                '<option value="Manual">Manual</option>' +
                '<option value="DB">Database</option>' +
                '</select>' +
                '</div>' +
                //jika manual
                '<div class="col-12 manualInput"  style="display:none;">' +
                '<label for="manual">Data Manual</label>' +
                '<input type="text" class="form-control" name="manual[]" placeholder="makanan,minuman,cemilan">' +
                '</div>' +
                //jika database
                '<div class="col-12 dbInput" style="display:none;">' +
                '<label for="dbInput">Key DB</label>' +
                '<select class="form-select keydb" name="keydb[]" >' +
                @foreach ($columns as $column)
                    '<option value="{{ $column }}">{{ $column }}</option>' +
                @endforeach
                '</select>' +
                '<label for="dbInput">value DB</label>' +
                '<select class="form-select valuedb" name="valuedb[]" >' +
                @foreach ($columns as $column)
                    '<option value="{{ $column }}">{{ $column }}</option>' +
                @endforeach
                '</select>' +
                '</div>' +
                //jika type file
                '<div class="col-12 mt-3 fileInput" style="display:none;">' +
                '<label for="fileType">File Type</label>' +
                '<select class="form-select" name="fileType[]">' +
                '<option value="" selected>Select</option>' +
                '<option value="foto">Foto</option>' +
                '<option value="dokumen">Dokumen</option>' +
                '</select>' +
                '<br>' +
                '<label for="fileFilter">File Filter</label>' +
                '<input type="text" class="form-control" name="fileFilter[]" placeholder="jpg,jpeg,png">' +
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
            var relasiModel = $(this).closest('.columnRow').find('.relasiModel');
            var parentRow = $(this).closest('.columnRow');
            var dbInput = parentRow.find('.dbInput');
            var keydb = dbInput.find('.keydb');
            var valuedb = dbInput.find('.valuedb');
            dbInput.hide();

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
                        keydb.append('<option value="' + columns[i] + '">' + columns[i] + '</option>');
                        valuedb.append('<option value="' + columns[i] + '">' + columns[i] +
                        '</option>');
                    }
                    // Tampilkan elemen select
                    cekbaris.show();
                    dbInput.show();
                }
            });
            selectedOption = selectedOption ? relasiModel.show() : relasiModel.hide();
        });


        $(document).on('change', '.inputType', function() {
            var opsi = $(this).val();
            var inputTambahan = $(this).closest('.columnRow').find('.additionalInput');
            inputTambahan.hide();

            var manualInput = $(this).closest('.columnRow').find('.manualInput');
            var dbInput = $(this).closest('.columnRow').find('.dbInput');
            var fileInput = $(this).closest('.columnRow').find('.fileInput');
            manualInput.hide();
            dbInput.hide();
            fileInput.hide();

            if (opsi === 'select' ||
                opsi === 'multiselect' ||
                opsi === 'checkbox' ||
                opsi === 'radio' ||
                opsi === 'file') {
                inputTambahan.show();

                if (opsi === 'file') {
                    inputTambahan.hide();
                    fileInput.show();
                }
            }
        });



        $(document).on('change', '.additionalInputType', function() {
            var selectedOption = $(this).val();
            var parentRow = $(this).closest('.columnRow');
            var manualInput = parentRow.find('.manualInput');
            var dbInput = parentRow.find('.dbInput');
            var fileInput = parentRow.find('.fileInput');

            manualInput.hide();
            dbInput.hide();
            fileInput.hide();

            if (selectedOption === 'Manual') {
                manualInput.show();
            } else if (selectedOption === 'DB') {
                dbInput.show();
            } else if (selectedOption === 'File') {
                fileInput.show();
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
