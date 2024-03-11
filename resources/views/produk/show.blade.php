@extends('layouts.app')

        @section('content')
            <div class="card">
                <div class="card-header">
                    <h1>Detail produk</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>{{ ucfirst('tes') }}</td>
                                    <td>{{ produk->tes }}</td>
                                </tr>
                                <tr>
                                    <td>{{ ucfirst('bnbn') }}</td>
                                    <td>{{ produk->bnbn }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endsection