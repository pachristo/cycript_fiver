@extends('admin.master',['menu'=>'coin'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <!-- breadcrumb -->
    <div class="custom-breadcrumb">
        <div class="row">
            <div class="col-9">
                <ul>
                    <li>{{__('Coin')}}</li>
                    <li class="active-item">{{ $title }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->

    <!-- User Management -->
    <div class="user-management">
        <div class="row">
            <div class="col-12">
                <div class="header-bar p-4">
                    <div class="table-title">
                        <h3>{{ $title }}</h3>
                    </div>
                </div>
                <div class="table-area">
                    <div>
                        <table id="table" class=" table table-borderless custom-table display text-center" width="100%">
                            <thead>
                            <tr>
                                <th scope="col">{{__('Coin Name')}}</th>
                                <th scope="col">{{__('Coin Type')}}</th>
                                <th scope="col">{{__('Status')}}</th>
                                <th scope="col">{{__('Updated At')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($coins))
                            @foreach($coins as $coin)
                                <tr>
                                    <td> {{$coin->name}} </td>
                                    <td> {{$coin->type}} </td>
                                    <td>
                                        <div>
                                            <label class="switch">
                                                <input type="checkbox" onclick="return processForm('{{$coin->id}}')"
                                                       id="notification" name="security" @if($coin->status == STATUS_ACTIVE) checked @endif>
                                                <span class="slider" for="status"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td> {{$coin->updated_at}}</td>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /User Management -->

@endsection

@section('script')
    <script>
        function processForm(active_id) {
            console.log(active_id)
            $.ajax({
                type: "POST",
                url: "{{ route('adminCoinStatus') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'active_id': active_id
                },
                success: function (data) {
                    console.log(data);
                }
            });
        }

    </script>
@endsection
