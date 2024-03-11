@extends('layouts.app')

        @section('content')
        <div class="card">
            <div class="card-header">
                <h1>Data {{ $namaTabel }}</h1>
            </div>
            <div class="card-body">
                <!-- Tambahkan tombol-tombol untuk tambah data, edit data, dan lihat data -->
                <div class="mb-3">
                    <a href="{{ route('member.create') }}" class="btn btn-success">Tambah Data</a>
                    <button type="button" class="btn btn-primary" id="bulkDelete">Hapus Data Terpilih</button>
                </div>
                <form id="filterForm">
                    @csrf
                    <div class="form-row"><div class="form-group col"><input type="text" name="{{ tes }}" class="form-control" placeholder="Filter {{ ucfirst(tes) }}"></div><div class="form-group col"><input type="text" name="{{ bnbn }}" class="form-control" placeholder="Filter {{ ucfirst(bnbn) }}"></div><div class="form-group col">
                            <button type="button" id="applyFilter" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </form>
                <table id="dataTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <!-- Tambahkan kolom untuk cek semua -->
                            <th><input type="checkbox" id="selectAll"></th><th>{{ ucfirst(t) }}</th><th>{{ ucfirst(n) }}</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimasukkan di sini melalui JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Event handler untuk tombol Apply Filter
                $('#applyFilter').on('click', function() {
                    applyFilter();
                });

                // Event handler untuk tombol Hapus Data Terpilih
                $('#bulkDelete').on('click', function() {
                    bulkDelete();
                });

                // Function untuk mengirimkan data filter ke server
                function applyFilter() {
                    $.ajax({
                        url: '{{ route('member.index') }}',
                        type: 'POST',
                        data: $('#filterForm').serialize(),
                        success: function(data) {
                            table.clear().draw();
                            table.rows.add(data).draw();
                        }
                    });
                }

                // Function untuk mengirimkan data ID yang dipilih untuk penghapusan bulk
                function bulkDelete() {
                    var selectedIds = [];

                    $('input:checked').each(function() {
                        if ($(this).attr('id') !== 'selectAll') {
                            selectedIds.push($(this).val());
                        }
                    });

                    if (selectedIds.length > 0) {
                        // Kirim ID yang dipilih ke server untuk penghapusan bulk
                        $.ajax({
                            url: '{{ route('member.bulkDelete') }}',
                            type: 'POST',
                            data: {
                                ids: selectedIds,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                // Tindakan setelah penghapusan berhasil
                            }
                        });
                    } else {
                        alert('Pilih setidaknya satu item untuk dihapus.');
                    }
                }

                // Inisialisasi DataTables
                var table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('member.index') }}',
                        type: 'POST', // Ganti tipe ke POST
                        data: function(d) {
                            d._token = '{{ csrf_token() }}'; // Sertakan CSRF token
                        },
                    },
                    columns: [
                        // Tambahkan kolom checkbox
                        {
                            data: 'checkbox',
                            orderable: false,
                            searchable: false
                        },data: '{{ $namaKolom }}',name: '{{ $namaKolom }}'data: '{{ $namaKolom }}',name: '{{ $namaKolom }}'},
                        // Kolom untuk aksi
                        {
                            data: 'aksi',
                            orderable: false,
                            searchable: false
                        }
                    ],
                });

                // Menambahkan kolom filter secara dinamis
                $('#dataTable thead th').each(function() {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                });

                // Menerapkan filter
                table.columns().every(function() {
                    var that = this;

                    $('input', this.header()).on('keyup change', function() {
                        if (that.search() !== this.value) {
                            that
                                .search(this.value)
                                .draw();
                        }
                    });
                });
            });
        </script>
        @endsection