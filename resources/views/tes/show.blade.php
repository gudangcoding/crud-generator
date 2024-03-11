@extends('layouts.app')

        @section('content')
            <div class="card">
                <div class="card-header">
                    <h1>Detail tes</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>{{ ucfirst('tes') }}</td>
                                    <td>{{ tes->tes }}</td>
                                </tr>
                                <tr>
                                    <td>{{ ucfirst('bnbn') }}</td>
                                    <td>{{ tes->bnbn }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endsection