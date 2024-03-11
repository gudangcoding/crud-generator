@extends('layouts.app')
        @section('content')
            <div class="card">
                <div class="card-header">
                    <h1>Edit member</h1>
                </div>
                <div class="card-body">
                    <form id="editForm">
                        @csrf
                        <div class="form-row"><div class="form-group col">
                                    <label for="tes">{{ ucfirst(tes) }}</label>
                                    <input type="text" name="tes" class="form-control" id="tes" value="{{ $namaTabel->tes }}">
                                </div><div class="form-group col">
                                    <label for="bnbn">{{ ucfirst(bnbn) }}</label>
                                    <input type="text" name="bnbn" class="form-control" id="bnbn" value="{{ $namaTabel->bnbn }}">
                                </div></div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    $('#editForm').on('submit', function(e) {
                        e.preventDefault();
                        var formData = $(this).serialize();
                        $.ajax({
                            url: '{{ route('$namafile.update', ['id' => $namafile->id]) }}',
                            type: 'PUT',
                            data: formData,
                            success: function(response) {
                                $('#exampleModal').modal('hide');
                                // Tambahkan logika lainnya, seperti menampilkan pesan sukses atau mereset form
                            },
                            error: function(xhr) {
                                // Tambahkan logika untuk menangani kesalahan validasi atau lainnya
                            }
                        });
                    });
                });
            </script>
        @endsection