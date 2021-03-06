@extends('user.master',['menu'=>'coin','sub_menu'=>'buy_coin_history'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12 mb-xl-0 mb-4">
            <div class="card cp-user-custom-card">
                <div class="card-body">
                    <div class="cp-user-card-header-area">
                        <h4>{{__('Einzahlungen bisher')}}</h4>
                    </div>
                    <div class="cp-user-buy-coin-content-area">
                        <div class="cp-user-wallet-table table-responsive">
                            <table id="table" class="table">
                                <thead>
                                <tr>
                                    <th>{{__('Adresse')}}</th>
                                    <th>{{__('Betrag')}}</th>
                                    <th>{{__('Typ')}}</th>
                                    <th>{{__('Bezahlart')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Erstellt')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            retrieve: true,
            bLengthChange: true,
            responsive: true,
            ajax: '{{route('buyCoinHistory')}}',
            order: [5, 'desc'],
            autoWidth: false,
            language: {
                paginate: {
                    next: 'nächste &#8250;',
                    previous: '&#8249; zurück'
                }
            },
            columns: [
                {"data": "address","orderable": false},
                {"data": "coin","orderable": false},
                {"data": "coin_type","orderable": false},
                {"data": "type","orderable": false},
                {"data": "status","orderable": false},
                {"data": "created_at","orderable": false},
            ],
        });
    </script>
@endsection
