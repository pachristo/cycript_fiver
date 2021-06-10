@extends('admin.master',['menu'=>'setting','sub_menu'=>'landing'])
@section('title', 'Landing Setting')
@section('style')
@endsection
@section('content')
    <!-- coin-area start -->
    <div class="landing-page-area user-management">
        <div class="container-fluid">
            <div class="page-wraper section-padding">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="single-tab section-height">
                            <div class="section-body ">
                                <ul class="nav nav-pills nav-pill-three landing-tab user-management-nav" id="tab" role="tablist">
                                    <li>
                                        <a class="nav-link active" @if(isset($tab) && $tab=='hero')class="active" @endif data-toggle="tab"
                                        href="#hero">{{__('Header Setting')}}</a>
                                    </li>
                                    <li>
                                        <a class="nav-link" @if(isset($tab) && $tab=='about_as')class="active" @endif data-toggle="tab"
                                        href="#about_as">{{__('About us')}}</a>
                                    </li>
                                    <li>
                                        <a class="nav-link" @if(isset($tab) && $tab=='features')class="active" @endif data-toggle="tab"
                                       href="#features">{{__('Features')}}</a>
                                    </li>
                                    <li>
                                        <a class="nav-link" @if(isset($tab) && $tab=='integration')class="active" @endif data-toggle="tab"
                                       href="#integration">{{__('Integration')}}</a>
                                    </li>
                                    <li>
                                        <a class="nav-link" @if(isset($tab) && $tab=='roadmap')class="active" @endif data-toggle="tab"
                                           href="#roadmap">{{__('Roadmap')}}</a>
                                    </li>
                                    <li>
                                        <a class="nav-link" @if(isset($tab) && $tab=='contact')class="active" @endif data-toggle="tab"
                                           href="#contact">{{__('Contact')}}</a>
                                    </li>
                                    <li>
                                        <a class="nav-link" @if(isset($tab) && $tab=='contactUs')class="active" @endif data-toggle="tab"
                                           href="#contactUs">{{__('Received Emails')}}</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <!-- genarel-setting start-->
                                    <div class="tab-pane fade  @if(isset($tab) && $tab=='hero')show active @endif " id="hero" role="tabpanel" aria-labelledby="header-setting-tab">
                                        <div class="page-title">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="page-title-inner">
                                                        <div class="table-title mb-4">
                                                            <h3>{{__('Landing Page Settings')}}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-area plr-65 profile-info-form">
                                            <form enctype="multipart/form-data" method="POST" action="{{route('adminLandingSettingSave')}}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page Title')}}</label>
                                                                    <input class="form-control" type="text" name="landing_title" @if(isset($adm_setting['landing_title'])) value="{{$adm_setting['landing_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page Description')}}</label>
                                                                    <textarea class="form-control" rows="5" name="landing_description">@if(isset($adm_setting['landing_description'])){{$adm_setting['landing_description']}} @endif</textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page 1st button Link')}}</label>
                                                                    <input class="form-control" type="text" name="landing_1st_button_link" @if(isset($adm_setting['landing_1st_button_link'])) value="{{$adm_setting['landing_1st_button_link']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page 2nd button link')}}</label>
                                                                    <input class="form-control" type="text" name="landing_2nd_button_link" @if(isset($adm_setting['landing_2nd_button_link'])) value="{{$adm_setting['landing_2nd_button_link']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Footer Short Description')}}</label>
                                                                    <textarea class="form-control" rows="5" name="footer_description">@if(isset($adm_setting['footer_description'])){{$adm_setting['footer_description']}} @endif</textarea>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="form-group">

                                                                            <label for="#">{{__('Landing Page Image')}}</label>
                                                                            <div id="file-upload" class="section-width">
                                                                                <input type="hidden" name="landing_page_logo" value="">
                                                                                <input type="file" placeholder="0.00" name="landing_page_logo"
                                                                                       value="" id="file" ref="file" class="dropify" @if(isset($adm_setting['landing_page_logo']) && (!empty($adm_setting['landing_page_logo']))) data-default-file="{{asset(path_image().$adm_setting['landing_page_logo'])}}" @endif />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if(isset($itech))
                                                                    <input type="hidden" name="itech" value="{{$itech}}">
                                                                @endif
                                                                <button class="button-primary theme-btn">{{__('Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade  @if(isset($tab) && $tab=='roadmap')show active @endif " id="roadmap" role="tabpanel" aria-labelledby="header-setting-tab">
                                        <div class="page-title">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="page-title-inner">
                                                        <div class="table-title mb-4">
                                                            <h3>{{__('Landing Page roadmap')}}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-area plr-65 profile-info-form">
                                            <form enctype="multipart/form-data" method="POST" action="{{route('adminLandingSettingSave')}}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page Roadmap Title')}}</label>
                                                                    <input type="text" class="form-control" name="landing_roadmap_title" @if(isset($adm_setting['landing_roadmap_title'])) value="{{$adm_setting['landing_roadmap_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page Roadmap Subtitle')}}</label>
                                                                    <textarea type="text" rows="10" class="form-control" name="landing_roadmap_subtitle"> @if(isset($adm_setting['landing_roadmap_subtitle'])){{$adm_setting['landing_roadmap_subtitle']}} @endif </textarea>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-lg-4">
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('1st Roadmap Date')}}</label>
                                                                            <input type="date" class="form-control" name="roadmap_1st_date" @if(isset($adm_setting['roadmap_1st_date'])) value="{{$adm_setting['roadmap_1st_date']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('1st Roadmap Title')}}</label>
                                                                            <input type="text" class="form-control" name="roadmap_1st_title" @if(isset($adm_setting['roadmap_1st_title'])) value="{{$adm_setting['roadmap_1st_title']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('1st Roadmap Subtitle')}}</label>
                                                                            <textarea type="text" class="form-control" name="roadmap_1st_subtitle"> @if(isset($adm_setting['roadmap_1st_subtitle'])){{$adm_setting['roadmap_1st_subtitle']}}@endif </textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('2nd Roadmap Date')}}</label>
                                                                            <input type="date" class="form-control" name="roadmap_2nd_date" @if(isset($adm_setting['roadmap_2nd_date'])) value="{{$adm_setting['roadmap_2nd_date']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('2nd Roadmap Title')}}</label>
                                                                            <input type="text" class="form-control" name="roadmap_2nd_title" @if(isset($adm_setting['roadmap_2nd_title'])) value="{{$adm_setting['roadmap_2nd_title']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('2nd Roadmap Subtitle')}}</label>
                                                                            <textarea type="text" class="form-control" name="roadmap_2nd_subtitle"> @if(isset($adm_setting['roadmap_2nd_subtitle'])){{$adm_setting['roadmap_2nd_subtitle']}}@endif </textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('3rd Roadmap Date')}}</label>
                                                                            <input type="date" class="form-control" name="roadmap_3rd_date" @if(isset($adm_setting['roadmap_3rd_date'])) value="{{$adm_setting['roadmap_3rd_date']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('3rd Roadmap Title')}}</label>
                                                                            <input type="text" class="form-control" name="roadmap_3rd_title" @if(isset($adm_setting['roadmap_3rd_title'])) value="{{$adm_setting['roadmap_3rd_title']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('3rd Roadmap Subtitle')}}</label>
                                                                            <textarea type="text" class="form-control" name="roadmap_3rd_subtitle"> @if(isset($adm_setting['roadmap_3rd_subtitle'])){{$adm_setting['roadmap_3rd_subtitle']}}@endif </textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('4th Roadmap Date')}}</label>
                                                                            <input type="date" class="form-control" name="roadmap_4th_date" @if(isset($adm_setting['roadmap_4th_date'])) value="{{$adm_setting['roadmap_4th_date']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('4th Roadmap Title')}}</label>
                                                                            <input type="text" class="form-control" name="roadmap_4th_title" @if(isset($adm_setting['roadmap_4th_title'])) value="{{$adm_setting['roadmap_4th_title']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('4th Roadmap Subtitle')}}</label>
                                                                            <textarea type="text" class="form-control" name="roadmap_4th_title"> @if(isset($adm_setting['roadmap_4th_title'])){{$adm_setting['roadmap_4th_title']}}@endif </textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('5th Roadmap Date')}}</label>
                                                                            <input type="date" class="form-control" name="roadmap_5th_date" @if(isset($adm_setting['roadmap_5th_date'])) value="{{$adm_setting['roadmap_5th_date']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('5th Roadmap Title')}}</label>
                                                                            <input type="text" class="form-control" name="roadmap_5th_title" @if(isset($adm_setting['roadmap_5th_title'])) value="{{$adm_setting['roadmap_5th_title']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('5th Roadmap Subtitle')}}</label>
                                                                            <textarea type="text" class="form-control" name="roadmap_5th_subtitle"> @if(isset($adm_setting['roadmap_5th_subtitle'])){{$adm_setting['roadmap_5th_subtitle']}}@endif </textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('Current Roadmap Date')}}</label>
                                                                            <input type="date" class="form-control" name="roadmap_current_date" @if(isset($adm_setting['roadmap_current_date'])) value="{{$adm_setting['roadmap_current_date']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('Current Roadmap Title')}}</label>
                                                                            <input type="text" class="form-control" name="roadmap_current_title" @if(isset($adm_setting['roadmap_current_title'])) value="{{$adm_setting['roadmap_current_title']}}" @endif>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @if(isset($itech))
                                                                    <input type="hidden" name="itech" value="{{$itech}}">
                                                                @endif
                                                                <button class="button-primary theme-btn">{{__('Update')}}</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- genarel-setting start-->
                                    <div class="tab-pane fade  @if(isset($tab) && $tab=='about_as')show active @endif "
                                         id="about_as" role="tabpanel" aria-labelledby="header-setting-tab">
                                        <div class="page-title">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="page-title-inner">
                                                        <div class="table-title mb-4">
                                                            <h3>{{__('Landing Page About us Settings')}}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-area plr-65 profile-info-form">
                                            <form enctype="multipart/form-data" method="POST"
                                                  action="{{route('adminLandingSettingSave')}}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="#">{{__('About us 1st paragraph title')}}</label>
                                                                    <input type="text" class="form-control" name="about_1st_title" @if(isset($adm_setting['about_1st_title'])) value="{{$adm_setting['about_1st_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('About us 1st paragraph description')}}</label>
                                                                    <textarea type="text" class="form-control" name="about_1st_description"> @if(isset($adm_setting['about_1st_description'])) {{$adm_setting['about_1st_description']}} @endif </textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('About us Image for 1st paragraph')}}</label>
                                                                    <div id="file-upload" class="section-width">
                                                                        <input type="hidden" name="about_1st_logo" value="">
                                                                        <input type="file" placeholder="0.00" name="about_1st_logo" value="" id="file" ref="file"
                                                                               class="dropify" @if(isset($adm_setting['about_1st_logo'])) data-default-file="{{asset(path_image().$adm_setting['about_1st_logo'])}}"@endif />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('About us 2nd paragraph title')}}</label>
                                                                    <input type="text" class="form-control" name="about_2nd_title" @if(isset($adm_setting['about_2nd_title'])) value="{{$adm_setting['about_2nd_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('About us 2nd paragraph description')}}</label>
                                                                    <textarea class="form-control" rows="5" name="about_2nd_description">@if(isset($adm_setting['about_2nd_description'])){{$adm_setting['about_2nd_description']}} @endif</textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('About us Image for 2nd paragraph')}}</label>
                                                                    <div id="file-upload" class="section-width">
                                                                        <input type="hidden" name="about_2nd_logo" value="">
                                                                        <input type="file" placeholder="0.00" name="about_2nd_logo" value="" id="file" ref="file"
                                                                               class="dropify" @if(isset($adm_setting['about_2nd_logo'])) data-default-file="{{asset(path_image().$adm_setting['about_2nd_logo'])}}"@endif />
                                                                    </div>
                                                                </div>
                                                                @if(isset($itech))
                                                                    <input type="hidden" name="itech" value="{{$itech}}">
                                                                @endif
                                                                <button class="button-primary theme-btn">{{__('Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade  @if(isset($tab) && $tab=='contact')show active @endif "
                                         id="contact" role="tabpanel" aria-labelledby="header-setting-tab">
                                        <div class="page-title">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="page-title-inner">
                                                        <div class="table-title mb-4">
                                                            <h3>{{__('Landing Page Contact Settings')}}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-area plr-65 profile-info-form">
                                            <form enctype="multipart/form-data" method="POST"
                                                  action="{{route('adminLandingSettingSave')}}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Contact title')}}</label>
                                                                    <input type="text" class="form-control" name="contact_title" @if(isset($adm_setting['contact_title'])) value="{{$adm_setting['contact_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Contact sub-title')}}</label>
                                                                    <input type="text" class="form-control" name="contact_sub_title" @if(isset($adm_setting['contact_sub_title'])) value="{{$adm_setting['contact_sub_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Address field title')}}</label>
                                                                    <input type="text" class="form-control" name="address_field_title" @if(isset($adm_setting['address_field_title'])) value="{{$adm_setting['address_field_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Address field details')}}</label>
                                                                    <textarea class="form-control" rows="5" name="address_field_details">@if(isset($adm_setting['address_field_details'])){{$adm_setting['address_field_details']}} @endif</textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Phone field title')}}</label>
                                                                    <input type="text" class="form-control" name="phone_field_title" @if(isset($adm_setting['phone_field_title'])) value="{{$adm_setting['phone_field_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Phone field details')}}</label>
                                                                    <textarea class="form-control" rows="5" name="phone_field_details">@if(isset($adm_setting['phone_field_details'])){{$adm_setting['phone_field_details']}} @endif</textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Email field title')}}</label>
                                                                    <input type="text" class="form-control" name="email_field_title" @if(isset($adm_setting['email_field_title'])) value="{{$adm_setting['email_field_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Email field details')}}</label>
                                                                    <input class="form-control" name="email_field_details" @if(isset($adm_setting['email_field_details'])) value="{{$adm_setting['email_field_details']}}" @endif>
                                                                </div>
                                                                @if(isset($itech))
                                                                    <input type="hidden" name="itech" value="{{$itech}}">
                                                                @endif
                                                                <button class="button-primary theme-btn">{{__('Update')}}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade  @if(isset($tab) && $tab=='integration')show active @endif "
                                         id="integration" role="tabpanel" aria-labelledby="header-setting-tab">
                                        <div class="page-title">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="page-title-inner">
                                                        <div class="table-title mb-4">
                                                            <h3>{{__('Integration Settings')}}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-area plr-65 profile-info-form">
                                            <form enctype="multipart/form-data" method="POST"
                                                  action="{{route('adminLandingSettingSave')}}">
                                                @csrf
                                                <div class="row">
                                                    @if(isset($itech))
                                                        <input type="hidden" name="itech" value="{{$itech}}">
                                                    @endif
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page Integration Title')}}</label>
                                                                    <input type="text" class="form-control" name="landing_integration_title"
                                                                           @if(isset($adm_setting['landing_integration_title']))value="{{$adm_setting['landing_integration_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page Integration Button Link')}}</label>
                                                                    <input type="text" class="form-control"
                                                                           name="landing_integration_button_link"
                                                                           @if(isset($adm_setting['landing_integration_button_link']))value="{{$adm_setting['landing_integration_button_link']}}" @endif>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page Integration Description')}}</label>
                                                                    <textarea class="form-control" rows="5"
                                                                              name="landing_integration_description">@if(isset($adm_setting['landing_integration_description'])){{$adm_setting['landing_integration_description']}} @endif</textarea>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="form-group">

                                                                            <label for="#">{{__('Landing Page integration Image')}}</label>
                                                                            <div id="file-upload" class="section-width">
                                                                                <input type="hidden" name="landing_integration_page_logo" value="">
                                                                                <input type="file" placeholder="0.00" name="landing_integration_page_logo" value="" id="file" ref="file"
                                                                                       class="dropify" @if(isset($adm_setting['landing_integration_page_logo'])) data-default-file="{{asset(path_image().$adm_setting['landing_integration_page_logo'])}}"@endif />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button class="button-primary theme-btn">{{__('Update')}}</button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade  @if(isset($tab) && $tab=='features')show active @endif "
                                         id="features" role="tabpanel" aria-labelledby="header-setting-tab">
                                        <div class="page-title">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="page-title-inner">
                                                        <div class="table-title mb-4">
                                                            <h3>{{__('Landing Page Features Settings')}}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-area plr-65 profile-info-form">
                                            <form enctype="multipart/form-data" method="POST"
                                                  action="{{route('adminLandingSettingSave')}}">
                                                @csrf
                                                <div class="row">
                                                    @if(isset($itech))
                                                        <input type="hidden" name="itech" value="{{$itech}}">
                                                    @endif
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing Page Features Title')}}</label>
                                                                    <input type="text" class="form-control" name="landing_feature_title"
                                                                           @if(isset($adm_setting['landing_feature_title']))value="{{$adm_setting['landing_feature_title']}}" @endif>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="#">{{__('Landing feature subtitle')}}</label>
                                                                    <input type="text" class="form-control" name="landing_feature_subtitle"
                                                                           @if(isset($adm_setting['landing_feature_subtitle']))value="{{$adm_setting['landing_feature_subtitle']}}" @endif>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('1st feature title')}}</label>
                                                                            <input type="text" class="form-control" name="1st_feature_title"
                                                                                   @if(isset($adm_setting['1st_feature_title']))value="{{$adm_setting['1st_feature_title']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('1st feature subtitle')}}</label>
                                                                            <input type="text" class="form-control"
                                                                                   name="1st_feature_subtitle"
                                                                                   @if(isset($adm_setting['1st_feature_subtitle']))value="{{$adm_setting['1st_feature_subtitle']}}" @endif>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="#">{{__('1st feature image')}}</label>
                                                                            <div id="file-upload" class="section-width">
                                                                                <input type="hidden" name="1st_feature_icon" value="">
                                                                                <input type="file" placeholder="0.00" name="1st_feature_icon" value="" id="file" ref="file"
                                                                                       class="dropify" @if(isset($adm_setting['1st_feature_icon'])) data-default-file="{{asset(path_image().$adm_setting['1st_feature_icon'])}}"@endif />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('2nd feature title')}}</label>
                                                                            <input type="text" class="form-control" name="2nd_feature_title"
                                                                                   @if(isset($adm_setting['2nd_feature_title']))value="{{$adm_setting['2nd_feature_title']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('2nd feature subtitle')}}</label>
                                                                            <input type="text" class="form-control"
                                                                                   name="2nd_feature_subtitle"
                                                                                   @if(isset($adm_setting['2nd_feature_subtitle']))value="{{$adm_setting['2nd_feature_subtitle']}}" @endif>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="#">{{__('2nd feature image')}}</label>
                                                                            <div id="file-upload" class="section-width">
                                                                                <input type="hidden" name="2nd_feature_icon" value="">
                                                                                <input type="file" placeholder="0.00" name="2nd_feature_icon" value="" id="file" ref="file"
                                                                                       class="dropify" @if(isset($adm_setting['2nd_feature_icon'])) data-default-file="{{asset(path_image().$adm_setting['2nd_feature_icon'])}}"@endif />
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('3rd feature title')}}</label>
                                                                            <input type="text" class="form-control" name="3rd_feature_title"
                                                                                   @if(isset($adm_setting['3rd_feature_title']))value="{{$adm_setting['3rd_feature_title']}}" @endif>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="#">{{__('3rd feature subtitle')}}</label>
                                                                            <input type="text" class="form-control" name="3rd_feature_subtitle"
                                                                                   @if(isset($adm_setting['3rd_feature_subtitle']))value="{{$adm_setting['3rd_feature_subtitle']}}" @endif>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="#">{{__('3rd feature image')}}</label>
                                                                            <div id="file-upload" class="section-width">
                                                                                <input type="hidden" name="3rd_feature_icon" value="">
                                                                                <input type="file" placeholder="0.00" name="3rd_feature_icon" value="" id="file" ref="file"
                                                                                       class="dropify" @if(isset($adm_setting['3rd_feature_icon'])) data-default-file="{{asset(path_image().$adm_setting['3rd_feature_icon'])}}"@endif />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button class="button-primary theme-btn">{{__('Update')}}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade @if(isset($tab) && $tab=='contactUs')show active @endif "
                                         id="contactUs" role="tabpanel" aria-labelledby="header-setting-tab">
                                        <div class="user-management">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="header-bar p-4">
                                                        <div class="table-title">
                                                            <h3>{{__('User Mailed Through Contact Us Form')}}</h3>
                                                        </div>
                                                    </div>
                                                    <div class="table-area">
                                                        <div>
                                                            <table id="table" class="table-responsive table table-borderless custom-table display text-center" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th scope="col">{{__('Name')}}</th>
                                                                    <th scope="col">{{__('Email')}}</th>
                                                                    <th scope="col">{{__('Phone')}}</th>
                                                                    <th scope="col">{{__('Address')}}</th>
                                                                    <th scope="col">{{__('Details')}}</th>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Us Modal for description show -->
        <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="descriptionModalLabel">{{__('Full Email')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-2"><label>{{__('Name: ')}}</label></div>
                            <div class="col-lg-10">
                                <label>
                                    <input name="name" readonly>
                                </label>
                            </div>
                            <div class="col-lg-2"><label>{{__('Email: ')}}</label></div>
                            <div class="col-lg-10">
                                <label>
                                    <input name="email" readonly>
                                </label>
                            </div>
                            <div class="col-lg-2"><label>{{__('Phone: ')}}</label></div>
                            <div class="col-lg-10">
                                <label>
                                    <input name="phone" readonly>
                                </label>
                            </div>
                            <div class="col-lg-2"><label>{{__('Address: ')}}</label></div>
                            <div class="col-lg-10">
                                <label>
                                    <input name="address" readonly>
                                </label>
                            </div>
                            <div class="col-lg-2"><label>{{__('Description: ')}}</label></div>
                            <div class="col-lg-10">
                                <label>
                                    <div class="description" style="height: 100px;overflow: auto"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
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
            responsive: false,
            ajax: '{{route('contactEmailList')}}',
            order: [4, 'desc'],
            autoWidth: false,
            language: {
                paginate: {
                    next: 'Next &#8250;',
                    previous: '&#8249; Previous'
                }
            },
            columns: [
                {"data": "name","orderable": false},
                {"data": "email","orderable": false},
                {"data": "phone","orderable": false},
                {"data": "address","orderable": false},
                {"data": "details","orderable": false}
            ],
        });

        $('#descriptionModal').on('show.bs.modal', function (event) {
            var modal = $(this)
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            $.ajax({
                /* the route pointing to the post function */
                url: "{{route('getDescriptionByID')}}",
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {"_token": "{{ csrf_token() }}", id:id},
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function (data) {
                    console.log(data)
                    console.log(modal.find('.modal-body input[name="name"]'))
                    modal.find('.modal-body input[name="name"]').val(data.name)
                    modal.find('.modal-body input[name="email"]').val(data.email)
                    modal.find('.modal-body input[name="phone"]').val(data.phone)
                    modal.find('.modal-body input[name="address"]').val(data.address)
                    modal.find('.modal-body .description').text(data.description)
                }
            });

        })

        {{--$(document).on('click','.show_details',function (){--}}
        {{--    var id = $(this).data('id');--}}
        {{--    console.log(id)--}}
        {{--    $.ajax({--}}
        {{--        /* the route pointing to the post function */--}}
        {{--        url: "{{route('getDescriptionByID')}}",--}}
        {{--        type: 'POST',--}}
        {{--        /* send the csrf-token and the input to the controller */--}}
        {{--        data: {"_token": "{{ csrf_token() }}", id:id},--}}
        {{--        dataType: 'JSON',--}}
        {{--        /* remind that 'data' is the response of the AjaxController */--}}
        {{--        success: function (data) {--}}
        {{--            console.log(data)--}}
        {{--        }--}}
        {{--    });--}}
        {{--})--}}

    </script>
@endsection
