@extends('auth.master',['menu'=>'dashboard'])
@section('title', isset($title) ? $title : '')

@section('content')
    <div class="user-content-wrapper">
        <div>
            <div class="user-form">
                <div class="right">
                    <div class="form-top">
                        <a class="auth-logo" href="javascript:;">
                            <img src="{{show_image(1,'login_logo')}}" class="img-fluid" alt="">
                        </a>
{{--                        <h2>{{__('Anmelden')}}</h2>--}}
                        <p>{{__('Konto erstellen')}}</p>
                    </div>
                    <div class="col-md-12">
                        @if(session()->has('dismiss'))

                            <div class="alert alert-danger">
                                <strong>{{session('dismiss')}}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if(session()->has('success'))
                            <div class="alert alert-success">
                                <strong>{{session('success')}}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    {{ Form::open(['route' => 'signUpProcess', 'files' => true]) }}
                        <div class="form-group">
                            <label>{{__('Vorname')}}<span class="text-danger">*</span></label>
                            <input type="text" name="first_name" value="{{old('first_name')}}" class="form-control" placeholder="{{__('Your first name here')}}">
                            @if ($errors->has('first_name'))
                                <p class="invalid-feedback">{{ $errors->first('first_name') }} </p>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>{{__('Nachname')}}<span class="text-danger">*</span></label>
                            <input type="text" name="last_name" value="{{old('last_name')}}" class="form-control" placeholder="{{__('Your last name here')}}">
                            @if ($errors->has('last_name'))
                                <p class="invalid-feedback">{{ $errors->first('last_name') }} </p>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>{{__('Email Adresse')}}<span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{old('email')}}" class="form-control" placeholder="{{__('Your email here')}}">
                            @if ($errors->has('email'))
                                <p class="invalid-feedback">{{ $errors->first('email') }} </p>
                            @endif
                        </div>
                    <div class="form-group">
                            <label>{{__('Tel.Nr.')}}<span class="text-danger">*</span></label>
                            <input type="phone" name="phone" value="{{old('phone')}}" class="form-control" placeholder="{{__('Your phonenumber here')}}">
                            @if ($errors->has('phone'))
                                <p class="invalid-feedback">{{ $errors->first('phone') }} </p>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>{{__('Passwort')}}<span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-password look-pass" placeholder="{{__('Your password here')}}">
                            @if ($errors->has('password'))
                                <p class="invalid-feedback">{{ $errors->first('password') }} </p>
                            @endif
                            <span class="eye rev"><i class="fa fa-eye-slash toggle-password"></i></span>
                        </div>
                        <div class="form-group">
                            <label>{{__('Passwort best??tigen')}}<span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control form-control-password look-pass-1" placeholder="{{__('Your confirm password here')}}">
                            @if ($errors->has('password_confirmation'))
                                <p class="invalid-feedback">{{ $errors->first('password_confirmation') }} </p>
                            @endif
                            <span class="eye rev-1"><i class="fa fa-eye-slash toggle-password"></i></span>
                        </div>
                        @if( app('request')->input('ref_code'))
                                {{Form::hidden('ref_code', app('request')->input('ref_code') )}}
                        @endif
                    <h6 style="text-align: center;"><strong><span style="color: #ffffff;">&Uuml;berpr&uuml;fen Sie Ihren Spam-Ordner auf die Best&auml;tigungs-E-Mail</span></strong></h6><br>
                        <button type="submit" class="btn btn-primary nimmu-user-sibmit-button">{{__('registrieren')}}</button>
                    {{ Form::close() }}
                    <div class="form-bottom text-center">
                        <p>{{__('Haben Sie schon ein Konto? ')}} <a href="{{route('login')}}">{{__('Zur??ck zum Login')}}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

<script>
    $(".toggle-password").click(function () {
            $(this).toggleClass("fa-eye-slash fa-eye");
        });
    $(".rev").on('click',function() {
		   var $pwd = $(".look-pass");
		   if ($pwd.attr('type') === 'password') {
			   $pwd.attr('type', 'text');
		   } else {
			   $pwd.attr('type', 'password');
		   }
	   });

	   $(".rev-1").on('click',function() {
		   var $pwd = $(".look-pass-1");
		   if ($pwd.attr('type') === 'password') {
			   $pwd.attr('type', 'text');
		   } else {
			   $pwd.attr('type', 'password');
		   }
	   });
</script>
@endsection
