@extends('admin.master',['menu'=>'setting', 'sub_menu'=>'feature'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <!-- breadcrumb -->
    <div class="custom-breadcrumb">
        <div class="row">
            <div class="col-12">
                <ul>
                    <li>{{__('Settings')}}</li>
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
                <ul class="nav user-management-nav mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="@if(isset($tab) && $tab=='co-pocket') active @endif nav-link " id="pills-user-tab"
                           data-toggle="pill" data-controls="co-pocket" href="#co-pocket" role="tab"
                           aria-controls="pills-user" aria-selected="true">
                            <span>{{__(' Multi-signature Pocket')}}</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane show @if(isset($tab) && $tab=='co-pocket')  active @endif" id="co-pocket"
                         role="tabpanel" aria-labelledby="pills-user-tab">
                        <div class="header-bar">
                            <div class="table-title">
                                <h3>{{__(' Multi-signature Pocket')}}</h3>
                            </div>
                        </div>
                        <div class="profile-info-form">
                            <form action="{{route('saveAdminFeatureSettings')}}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 col-12  mt-20">
                                        <div class="form-group">
                                            <label for="{{CO_WALLET_FEATURE_ACTIVE_SLUG}}">{{' Multi-signature Pocket Feature Status'}}</label>
                                            <br>
                                            <label class="switch">
                                                <input type="checkbox"
                                                       id="{{CO_WALLET_FEATURE_ACTIVE_SLUG}}" name="{{CO_WALLET_FEATURE_ACTIVE_SLUG}}"
                                                       @if(isset($settings[CO_WALLET_FEATURE_ACTIVE_SLUG]) &&
                                                        $settings[CO_WALLET_FEATURE_ACTIVE_SLUG] == STATUS_ACTIVE) checked
                                                       @endif value="{{STATUS_ACTIVE}}">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-12  mt-20">
                                        <div class="form-group">
                                            <label for="#">{{__('Max Co User For One Pocket')}}</label>
                                            <input class="form-control" type="text" name="{{MAX_CO_WALLET_USER_SLUG}}" required
                                                   placeholder="{{__('5')}}" value="{{$settings[MAX_CO_WALLET_USER_SLUG] ?? ''}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-12  mt-20">
                                        <div class="form-group">
                                            <label for="#">{{__('The (%) Users Approval Needed For A Withdraw')}}</label>
                                            <input class="form-control" type="text" required name="{{CO_WALLET_WITHDRAWAL_USER_APPROVAL_PERCENTAGE_SLUG}}"
                                                   placeholder="{{__('60')}}" value="{{$settings[CO_WALLET_WITHDRAWAL_USER_APPROVAL_PERCENTAGE_SLUG] ?? ''}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @if(isset($itech))
                                        <input type="hidden" name="itech" value="{{$itech}}">
                                    @endif
                                    <div class="col-lg-2 col-12 mt-20">
                                        <button class="button-primary theme-btn">{{__('Save')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /User Management -->

@endsection

@section('script')
    <script>
        $('.nav-link').on('click', function () {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            var str = '#' + $(this).data('controls');
            $('.tab-pane').removeClass('show active');
            $(str).addClass('show active');
        });
    </script>
@endsection
