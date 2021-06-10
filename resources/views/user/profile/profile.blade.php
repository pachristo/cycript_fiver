@extends('user.master',['menu'=>'profile'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">

            <ul class="nav cp-user-profile-nav" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{($qr == 'profile-tab') ? 'active' : ''}}" data-id="profile-tab"
                       id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab"
                       aria-controls="pills-profile" aria-selected="true">
                                <span class="cp-user-img">
                                    <img src="{{asset('assets/user/images/profile-icons/profile.svg')}}"
                                         class="img-fluid img-normal" alt="">
                                    <img src="{{asset('assets/user/images/profile-icons/active/profile.svg')}}"
                                         class="img-fluid img-active" alt="">
                                </span>
                        {{__('Profil')}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{($qr == 'eProfile-tab') ? 'active' : ''}}" data-id="eProfile-tab"
                       id="pills-edit-profile-tab" data-toggle="pill" href="#pills-edit-profile" role="tab"
                       aria-controls="pills-edit-profile" aria-selected="true">
                                <span class="cp-user-img">
                                    <img src="{{asset('assets/user/images/profile-icons/edit-profile.svg')}}"
                                         class="img-fluid img-normal" alt="">
                                    <img src="{{asset('assets/user/images/profile-icons/active/edit-profile.svg')}}"
                                         class="img-fluid img-active" alt=""
                                    ></span>
                        {{__('Profil bearbeiten')}}
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{($qr == 'idvarification-tab') ? 'active' : ''}}" data-id="idvarification-tab"
                       id="pills-id-verify-tab" data-toggle="pill" href="#pills-id-verify" role="tab"
                       aria-controls="pills-id-verify" aria-selected="true">
                                <span class="cp-user-img">
                                    <img src="{{asset('assets/user/images/profile-icons/id-verify.svg')}}"
                                         class="img-fluid img-normal" alt="">
                                    <img src="{{asset('assets/user/images/profile-icons/active/id-verify.svg')}}"
                                         class="img-fluid img-active" alt="">
                                </span>
                        {{__('KYC')}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  {{($qr == 'rpassword-tab') ? 'active' : ''}}" data-id="rpassword-tab"
                       id="pills-reset-pass-tab" data-toggle="pill" href="#pills-reset-pass" role="tab"
                       aria-controls="pills-id-verify" aria-selected="true">
                                <span class="cp-user-img">
                                    <img src="{{asset('assets/user/images/profile-icons/reset-pass.svg')}}"
                                         class="img-fluid img-normal" alt="">
                                    <img src="{{asset('assets/user/images/profile-icons/active/reset-pass.svg')}}"
                                         class="img-fluid img-active" alt="">
                                </span>
                        {{__('Passwort zurücksetzen')}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{($qr == 'activity-tab') ? 'active' : ''}}" data-id="activity-tab"
                       id="pills-activity-log-tab" data-toggle="pill" href="#pills-activity-log" role="tab"
                       aria-controls="pills-id-verify" aria-selected="true">
                                <span class="cp-user-img">
                                    <img src="{{asset('assets/user/images/profile-icons/activity-log.svg')}}"
                                         class="img-fluid img-normal" alt="">
                                    <img src="{{asset('assets/user/images/profile-icons/active/activity-log.svg')}}"
                                         class="img-fluid img-active" alt=""
                                    >
                                </span>
                        {{__('Aktivitäten')}}
                    </a>
                </li>
            </ul>
            <div class="tab-content cp-user-profile-tab-content" id="pills-tabContent">
                <div class="tab-pane fade show {{($qr == 'profile-tab') ? 'show active in' : ''}}" id="pills-profile"
                     role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="row">
                        <div class="col-xl-4 mb-xl-0 mb-4">
                            <div class="card cp-user-custom-card">
                                <div class="card-body">
                                    <div class="user-profile-area">
                                        <div class="user-profile-img">
                                            <img src="{{show_image($user->id,'user')}}" class="img-fluid" alt="">
                                        </div>
                                        <div class="user-cp-user-profile-info">
                                            <h4>{{$user->first_name.' '.$user->last_name}}</h4>
                                            <p>{{$user->email}}</p>
                                            <p class="cp-user-btc">
                                                @if(!empty($clubInfos['club_id']))
                                                    <span>
                                                    <img src="{{ $clubInfos['plan_image'] }}"  class="img-fluid" alt="">
                                                </span>
                                            </p>
                                            <p>
                                                {{ $clubInfos['plan_name'] }}
                                            </p>
                                            @endif
                                            <div class="cp-user-available-balance-profile">
                                                <p>{{__('Blocked Coin')}}
                                                    <span>{{number_format(get_blocked_coin(Auth::id()),2)}}</span> {{allsetting('coin_name')}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8">
                            <div class="card cp-user-custom-card">
                                <div class="card-body">
                                    <div class="cp-user-profile-header">
                                        <h5>{{__('Profil Informationen')}}</h5>
                                    </div>
                                    <div class="cp-user-profile-info">
                                        <ul>
                                            <li>
                                                <span>{{__('Name')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{$user->first_name.' '.$user->last_name}}</span>
                                            </li>
                                            <li>
                                                <span>{{__('Land')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{$user->country}}</span>
                                            </li>
                                            <li>
                                                <span>{{__('Email')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{$user->email}}</span>
                                            </li>
                                            <li>
                                                <span>{{__('Email Verifizierung')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{statusAction($user->is_verified)}}</span>
                                            </li>
                                            <li>
                                                <span>{{__('Tel.-Nr.')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{$user->phone}}</span>
                                            </li>
                                            {{--                                            <li>--}}
                                            {{--                                                <span>{{__('Phone Verification')}}</span>--}}
                                            {{--                                                <span class="cp-user-dot">:</span>--}}
                                            {{--                                                <span class="pending">{{statusAction($user->phone_verified)}}</span>--}}
                                            {{--                                            </li>--}}

                                            <li>
                                                <span>{{__('Status')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{userRole($user->role)}}</span>
                                            </li>
                                            <li>
                                                <span>{{__('Aktivitätsstatus')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{statusAction($user->status)}}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{($qr == 'eProfile-tab') ? 'show active in' : ''}}" id="pills-edit-profile"
                     role="tabpanel" aria-labelledby="pills-edit-profile-tab">
                    <div class="row">
                        <div class="col-xl-4 mb-xl-0 mb-4">
                            <div class="card cp-user-custom-card">
                                <div class="card-body">
                                    <div class="user-profile-area">
                                        <div class="user-profile-img">
                                            <img src="{{show_image($user->id,'user')}}" class="img-fluid" alt="">
                                        </div>
                                        <form enctype="multipart/form-data" method="post"
                                              action="{{route('uploadProfileImage')}}">
                                            @csrf
                                            <div class="user-cp-user-profile-info">
                                                <input type="file" name="file_one" id="upload-user-img">
                                                <label for="upload-user-img" class="upload-user-img">
                                                    {{__('Neues Bild hochladen')}}
                                                </label>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8">
                            <div class="card cp-user-custom-card">
                                <div class="card-body">
                                    <div class="cp-user-profile-header">
                                        <h5>{{__('Profil Infos bearbeiten')}}</h5>
                                    </div>
                                    <div class="cp-user-profile-info">
                                        <form action="{{route('userProfileUpdate')}}" method="post">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{__('Vorname')}}</label>
                                                <input type="text" name="first_name" value="{{$user->first_name}}"
                                                       class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Nachname')}}</label>
                                                <input type="text" name="last_name" value="{{$user->last_name}}"
                                                       class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Tel.-Nr.')}}</label>
                                                <input type="text" name="phone" value="{{$user->phone}}"
                                                       class="form-control">
                                                <span style="color: #ff0000;">{{__('Bitte Tel.-Nr mit Landesvorwahl, aber nicht (+ Zeichen) hinzufügen. für Deutschland 49***')}}</span>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Land')}}</label>
                                                <select name="country" id="" class="form-control">
                                                    @foreach(country() as $key => $value)
                                                        <option value="{{$key}}"
                                                                @if(isset($user->country) && ($user->country == $key)) selected @endif>
                                                            {{$value}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Geschlecht')}}</label>
                                                <select class="form-control" name="gender" id="">
                                                    <option @if($user->gender == 1) selected
                                                            @endif value="1">{{__('Male')}}</option>
                                                    <option @if($user->gender == 2) selected
                                                            @endif value="2">{{__('Female')}}</option>
                                                    <option @if($user->gender == 3) selected
                                                            @endif value="3">{{__('Others')}}</option>
                                                </select>
                                            </div>
                                            <div class="form-group m-0">
                                                <button class="btn profile-edit-btn"
                                                        type="submit">{{__('Speichern')}}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{($qr == 'pvarification-tab') ? 'show active in' : ''}}"
                     id="pills-phone-verify" role="tabpanel" aria-labelledby="pills-phone-verify-tab">
                    <div class="card cp-user-custom-card">
                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <div class="cp-user-profile-header">
                                        <h5>{{__('Phone Verification')}}</h5>
                                    </div>
                                    <div class="cp-user-profile-info">
                                        <form method="post" action="{{route('phoneVerify')}}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="number">{{__('Tel.-Nr')}}</label>
                                                <div class="code-list">
                                                    @if(!empty($user->phone))
                                                        <input type="text" readonly value="{{Auth::user()->phone}}"
                                                               class="form-control" id="">
                                                        @if((Auth::user()->phone_verified == 0 )  && (!empty(\Illuminate\Support\Facades\Cookie::get('code'))))
                                                            <a href="{{route('sendSMS')}}"
                                                               class="primary-btn">{{__('Resend SMS')}}</a>
                                                            <p>{{__('Did not receive code?')}}</p>
                                                        @elseif(Auth::user()->phone_verified == 1 )
                                                            <span class="verified">{{__('Verified')}}</span>
                                                        @else
                                                            <a href="{{route('sendSMS')}}"
                                                               class="primary-btn">{{__('Send SMS')}}</a>
                                                        @endif
                                                    @else
                                                        <p>{{__('Please add mobile no. first from edit profile')}}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if((Auth::user()->phone_verified == 0) && (!empty(\Illuminate\Support\Facades\Cookie::get('code'))))
                                                <div class="form-group">
                                                    <label for="number">{{__('Verify Code')}}</label>
                                                    <div class="code-list">
                                                        <input name="code" type="text" min="" max=""
                                                               class="form-control" id="">
                                                    </div>
                                                </div>
                                                <button type="submit"
                                                        class="btn profile-edit-btn phn-verify-btn">{{__('Verify')}}</button>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{($qr == 'idvarification-tab') ? 'show active in' : ''}}"
                     id="pills-id-verify" role="tabpanel" aria-labelledby="pills-id-verify-tab">
                    <div class="card cp-user-custom-card idverifycard">
                        <div class="card-body">
                            <div class="cp-user-profile-header">
                                <h5>{{__('Identitätsnachweis wählen')}}</h5>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-10">
                                    <div class="cp-user-profile-info-id-type">
                                        <div class="id-card-type">
                                            <div class="id-card" data-toggle="modal"
                                                 data-target=".cp-user-idverifymodal">
                                                <img src="{{asset('assets/user/images/cards/nid.svg')}}"
                                                     class="img-fluid" alt="">
                                            </div>
                                            <div class="card-bottom">
                                                @if((!empty($nid_back ) && !empty($nid_front)) && (($nid_back->status == STATUS_SUCCESS) && ($nid_front->status == STATUS_SUCCESS)))
                                                    <span class="text-success">{{__('Genehmigt')}}</span>
                                                @elseif((!empty($nid_back ) && !empty($nid_front)) && (($nid_back->status == STATUS_REJECTED) && ($nid_front->status == STATUS_REJECTED)))
                                                    <span class="text-danger">{{__('Abgelehnt')}}</span>
                                                @elseif((!empty($nid_back ) && !empty($nid_front)) && (($nid_back->status == STATUS_PENDING) && ($nid_front->status == STATUS_PENDING)))
                                                    <span class="text-warning">{{__('Steht aus')}}</span>
                                                @else
                                                    <span class="text-warning">{{__('Nicht hochgeladen')}}</span>
                                                @endif
                                                <h5>{{__('Personalausweis')}}</h5>
                                            </div>
                                        </div>
                                        <div class="id-card-type">
                                            <div class="id-card" data-toggle="modal"
                                                 data-target=".cp-user-passwordverifymodal">
                                                <img src="{{asset('assets/user/images/cards/passport.svg')}}"
                                                     class="img-fluid" alt="">
                                            </div>
                                            <div class="card-bottom">
                                                @if((!empty($pass_back ) && !empty($pass_front)) && (($pass_back->status == STATUS_SUCCESS) && ($pass_front->status == STATUS_SUCCESS)))
                                                    <span class="text-success">{{__('Genehmigt')}}</span>
                                                @elseif((!empty($pass_back ) && !empty($pass_front)) && (($pass_back->status == STATUS_REJECTED) && ($pass_front->status == STATUS_REJECTED)))
                                                    <span class="text-danger">{{__('Abgelehnt')}}</span>

                                                @elseif((!empty($pass_back ) && !empty($pass_front)) && (($pass_back->status == STATUS_PENDING) && ($pass_front->status == STATUS_PENDING)))
                                                    <span class="text-warning">{{__('Steht aus')}}</span>
                                                @else
                                                    <span class="text-warning">{{__('Nicht hochgeladen')}}</span>
                                                @endif
                                                <h5>{{__('Reisepass')}}</h5>
                                            </div>
                                        </div>
                                        <div class="id-card-type">
                                            <div class="id-card" data-toggle="modal"
                                                 data-target=".cp-user-driververifymodal">
                                                <img src="{{asset('assets/user/images/cards/driving-license.svg')}}"
                                                     class="img-fluid" alt="">
                                            </div>
                                            <div class="card-bottom">
                                                @if((!empty($drive_back ) && !empty($drive_front)) && (($drive_back->status == STATUS_SUCCESS) && ($drive_front->status == STATUS_SUCCESS)))
                                                    <span class="text-success">{{__('Genehmigt')}}</span>
                                                @elseif((!empty($drive_back ) && !empty($drive_front)) && (($drive_back->status == STATUS_REJECTED) && ($drive_front->status == STATUS_REJECTED)))
                                                    <span class="text-danger">{{__('Abgelehnt')}}</span>
                                                @elseif((!empty($drive_back ) && !empty($drive_front)) && (($drive_back->status == STATUS_PENDING) && ($drive_front->status == STATUS_PENDING)))
                                                    <span class="text-warning">{{__('Steht auss')}}</span>
                                                @else
                                                    <span class="text-warning">{{__('Nicht hochgeladen')}}</span>
                                                @endif
                                                <h5>{{__('Führerschein')}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{($qr == 'rpassword-tab') ? 'show active in' : ''}}" id="pills-reset-pass"
                     role="tabpanel" aria-labelledby="pills-reset-pass-tab">
                    <div class="card cp-user-custom-card">
                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <div class="cp-user-profile-header">
                                        <h5>{{__('Passwort zurücksetzen')}}</h5>
                                    </div>

                                </div>
                                <div class="col-lg-9">
                                    <div class="cp-user-profile-info">
                                        <form method="POST" action="{{route('changePasswordSave')}}">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{__('gültiges Passwort')}}</label>
                                                <input name="password" type="password"
                                                       placeholder="{{__('Current Password')}}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('neuesPasswort')}}</label>
                                                <input name="new_password" type="password"
                                                       placeholder="{{__('New Password')}}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('neues Passwort bestätigen')}}</label>
                                                <input name="confirm_new_password" type="password"
                                                       placeholder="{{__('Re Enter New Password')}}"
                                                       class="form-control">
                                            </div>
                                            <div class="form-group m-0">
                                                <button class="btn profile-edit-btn"
                                                        type="submit">{{__('Speichern')}}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{($qr == 'activity-tab') ? 'show active in' : ''}}" id="pills-activity-log"
                     role="tabpanel" aria-labelledby="pills-activity-log-tab">
                    <div class="card cp-user-custom-card">
                        <div class="card-body">
                            <div class="cp-user-profile-header">
                                <h5>{{__('Alle Aktivitäten')}}</h5>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="cp-user-wallet-table table-responsive">
                                        <table id="activity-tbl" class="table table-borderless cp-user-custom-table"
                                               width="100%">
                                            <thead>
                                            <tr>
                                                <th class="all">{{__('Art')}}</th>
                                                <th class="desktop">{{__('Woher')}}</th>
                                                <th class="desktop">{{__('IP Adresse')}}</th>
                                                {{--                                                <th class="all">Location</th>--}}
                                                <th class="all">{{__('aktualisiert am')}}</th>
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

    <!-- Modal -->
    <div class="modal fade cp-user-idverifymodal" id="exampleModalCenter" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><img src="{{asset('assets/user/images/close.svg')}}" class="img-fluid"
                                                      alt=""></span>
                    </button>
                    <form id="nidUpload" class="Upload" action="{{route('nidUpload')}}" enctype="multipart/form-data"
                          method="post">
                        @csrf
                        <div class="container">
                            <div class="row">

                                <div class="col-12">
                                    <div class="card-list">
                                        <div class="alert alert-danger d-none error_msg" id="" role="alert">
                                        </div>
                                        <div class="alert alert-success d-none succ_msg" id="" role="alert">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-lg-0 mb-4">
                                    <div class="idcard">
                                        <h3 class="title">{{__('Vorderseite')}}</h3>
                                        <div id="file-upload" class="section-p">
                                            @if(isset($nid_back) && isset($nid_front))
                                                @if((empty($nid_back ) && empty($nid_front)) || (($nid_back->status == STATUS_REJECTED) && ($nid_front->status == STATUS_REJECTED)))
                                                    <input type="file" accept="image/x-png,image/jpeg" name="file_two"
                                                           id="file" ref="file" class="dropify"
                                                           @if(!empty($nid_front) && (!empty($nid_front->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$nid_front->photo)}}" @endif />
                                                @else
                                                    <div class="card-inner">
                                                        <img src="{{asset(IMG_USER_VIEW_PATH.$nid_front->photo)}}"
                                                             class="img-fluid" alt="">
                                                    </div>
                                                @endif
                                            @else
                                                <input type="file" accept="image/x-png,image/jpeg" name="file_two"
                                                       id="file" ref="file" class="dropify"
                                                       @if(!empty($nid_front) && (!empty($nid_front->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$nid_front->photo)}}" @endif />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-lg-0 mb-4">
                                    <div class="idcard">
                                        <h3 class="title">{{__('Rückseite')}}</h3>
                                        @if(isset($nid_back) && isset($nid_front))
                                            @if((empty($nid_back ) && empty($nid_front)) || (($nid_back->status == STATUS_REJECTED) && ($nid_front->status == STATUS_REJECTED)))
                                                <input type="file" accept="image/x-png,image/jpeg" name="file_three"
                                                       id="file" ref="file" class="dropify"
                                                       @if(!empty($nid_back) && (!empty($nid_back->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$nid_back->photo)}}" @endif />
                                            @else
                                                <div class="card-inner">
                                                    <img src="{{asset(IMG_USER_VIEW_PATH.$nid_back->photo)}}"
                                                         class="img-fluid" alt="">
                                                </div>
                                            @endif
                                        @else
                                            <input type="file" accept="image/x-png,image/jpeg" name="file_three"
                                                   id="file" ref="file" class="dropify"
                                                   @if(!empty($nid_back) && (!empty($nid_back->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$nid_back->photo)}}" @endif />
                                        @endif

                                    </div>
                                </div>
                                @if(isset($nid_back) && isset($nid_front))
                                    @if((empty($nid_back ) && empty($nid_front)) || (($nid_back->status == STATUS_REJECTED) && ($nid_front->status == STATUS_REJECTED)))
                                        <div class="col-12">
                                            <button type="submit" class="btn carduploadbtn">{{__('Hochladen')}}</button>
                                        </div>
                                    @endif
                                @else
                                    <div class="col-12">
                                        <button type="submit" class="btn carduploadbtn">{{__('Hochladen')}}</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade cp-user-passwordverifymodal" id="exampleModalCenter" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><img src="{{asset('assets/user/images/close.svg')}}" class="img-fluid"
                                                      alt=""></span>
                    </button>
                    <form id="nidUpload" class="Upload" action="{{route('passUpload')}}" enctype="multipart/form-data"
                          method="post">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-list">
                                        <div class="alert alert-danger d-none error_msg" id="" role="alert">
                                        </div>
                                        <div class="alert alert-success d-none succ_msg" id="" role="alert">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-lg-0 mb-4">
                                    <div class="idcard">
                                        <h3 class="title">{{__('Vorderseite')}}</h3>
                                        <div id="file-upload" class="section-p">
                                            @if((isset($pass_back ) && isset($pass_front)))
                                                @if((empty($pass_back ) && empty($pass_front)) || (($pass_back->status == STATUS_REJECTED) && ($pass_front->status == STATUS_REJECTED)))
                                                    <input type="file" accept="image/x-png,image/jpeg" name="file_two"
                                                           id="file" ref="file" class="dropify"
                                                           @if(!empty($pass_front) && (!empty($pass_front->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$pass_front->photo)}}" @endif />
                                                @else
                                                    <div class="card-inner">
                                                        <img src="{{asset(IMG_USER_VIEW_PATH.$pass_front->photo)}}"
                                                             class="img-fluid" alt="">
                                                    </div>
                                                @endif
                                            @else
                                                <input type="file" accept="image/x-png,image/jpeg" name="file_two"
                                                       id="file" ref="file" class="dropify"
                                                       @if(!empty($pass_front) && (!empty($pass_front->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$pass_front->photo)}}" @endif />
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-lg-0 mb-4">
                                    <div class="idcard">
                                        <h3 class="title">{{__('Rückseite')}}</h3>
                                        @if((isset($pass_back ) && isset($pass_front)))
                                            @if((empty($pass_back ) && empty($pass_front)) || (($pass_back->status == STATUS_REJECTED) && ($pass_front->status == STATUS_REJECTED)))
                                                <input type="file" accept="image/x-png,image/jpeg" name="file_three"
                                                       id="file" ref="file" class="dropify"
                                                       @if(!empty($pass_back) && (!empty($pass_back->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$pass_back->photo)}}" @endif />
                                            @else
                                                <div class="card-inner">
                                                    <img src="{{asset(IMG_USER_VIEW_PATH.$pass_back->photo)}}"
                                                         class="img-fluid" alt="">
                                                </div>
                                            @endif
                                        @else
                                            <input type="file" accept="image/x-png,image/jpeg" name="file_three"
                                                   id="file" ref="file" class="dropify"
                                                   @if(!empty($pass_back) && (!empty($pass_back->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$pass_back->photo)}}" @endif />

                                        @endif
                                    </div>
                                </div>
                                @if((isset($pass_back ) && isset($pass_front)))
                                    @if((empty($pass_back ) && empty($pass_front)) || (($pass_back->status == STATUS_REJECTED) && ($pass_front->status == STATUS_REJECTED)))
                                        <div class="col-12">
                                            <button type="submit" class="btn carduploadbtn">{{__('Hochladen')}}</button>
                                        </div>
                                    @endif
                                @else
                                    <div class="col-12">
                                        <button type="submit" class="btn carduploadbtn">{{__('Hochladen')}}</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade cp-user-driververifymodal" id="exampleModalCenter" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><img src="{{asset('assets/user/images/close.svg')}}" class="img-fluid"
                                                      alt=""></span>
                    </button>
                    <form id="nidUpload" class="Upload" action="{{route('driveUpload')}}" enctype="multipart/form-data"
                          method="post">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-list">
                                        <div class="alert alert-danger d-none error_msg" id="" role="alert">
                                        </div>
                                        <div class="alert alert-success d-none succ_msg" id="" role="alert">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-lg-0 mb-4">
                                    <div class="idcard">
                                        <h3 class="title">{{__('Vorderseite')}}</h3>
                                        <div id="file-upload" class="section-p">
                                            @if((isset($drive_back ) && isset($drive_front)))
                                                @if((empty($drive_back ) && empty($drive_front)) || (($drive_back->status == STATUS_REJECTED) && ($drive_front->status == STATUS_REJECTED)))
                                                    <input type="file" accept="image/x-png,image/jpeg" name="file_two"
                                                           id="file" ref="file" class="dropify"
                                                           @if(!empty($drive_front) && (!empty($drive_front->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$drive_front->photo)}}" @endif />
                                                @else
                                                    <div class="card-inner">
                                                        <img src="{{asset(IMG_USER_VIEW_PATH.$drive_front->photo)}}"
                                                             class="img-fluid" alt="">
                                                    </div>
                                                @endif
                                            @else
                                                <input type="file" accept="image/x-png,image/jpeg" name="file_two"
                                                       id="file" ref="file" class="dropify"
                                                       @if(!empty($drive_front) && (!empty($drive_front->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$drive_front->photo)}}" @endif />

                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-lg-0 mb-4">
                                    <div class="idcard">
                                        <h3 class="title">{{__('Rückseite')}}</h3>
                                        @if((isset($drive_back ) && isset($drive_front)))
                                            @if((empty($drive_back ) && empty($drive_front)) || (($drive_back->status == STATUS_REJECTED) && ($drive_front->status == STATUS_REJECTED)))
                                                <input type="file" accept="image/x-png,image/jpeg" name="file_three"
                                                       id="file" ref="file" class="dropify"
                                                       @if(!empty($drive_back) && (!empty($drive_back->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$drive_back->photo)}}" @endif />
                                            @else
                                                <div class="card-inner">
                                                    <img src="{{asset(IMG_USER_VIEW_PATH.$drive_back->photo)}}"
                                                         class="img-fluid" alt="">
                                                </div>
                                            @endif
                                        @else
                                            <input type="file" accept="image/x-png,image/jpeg" name="file_three"
                                                   id="file" ref="file" class="dropify"
                                                   @if(!empty($drive_back) && (!empty($drive_back->photo)))  data-default-file="{{asset(IMG_USER_VIEW_PATH.$drive_back->photo)}}" @endif />

                                        @endif
                                    </div>
                                </div>
                                @if((isset($drive_back ) && isset($drive_front)))
                                    @if((empty($drive_back ) && empty($drive_front)) || (($drive_back->status == STATUS_REJECTED) && ($drive_front->status == STATUS_REJECTED)))
                                        <div class="col-12">
                                            <button type="submit" class="btn carduploadbtn">{{__('Hochladen')}}</button>
                                        </div>
                                    @endif
                                @else
                                    <div class="col-12">
                                        <button type="submit" class="btn carduploadbtn">{{__('Hochladen')}}</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script>
        $('.nav-link').on('click', function () {
            var query = $(this).data('id');
            window.history.pushState('page2', 'Title', '{{route('userProfile')}}?qr=' + query);
        });

        jQuery("#upload-user-img").change(function () {
            this.form.submit();
        });

        $(function () {
            $(document.body).on('submit', '.Upload', function (e) {
                e.preventDefault();
                $('.error_msg').addClass('d-none');
                $('.succ_msg').addClass('d-none');
                var form = $(this);
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: form.attr('action'),
                    data: new FormData($(this)[0]),
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data.success == true) {
                            $('.succ_msg').removeClass('d-none');
                            $('.succ_msg').html(data.message);

                            $(".succ_msg").fadeTo(2000, 500).slideUp(500, function () {
                                $(".succ_msg").slideUp(500);
                            });
                        } else {
                            $('.error_msg').removeClass('d-none');
                            $('.error_msg').html(data.message);

                            $(".error_msg").fadeTo(2000, 500).slideUp(500, function () {
                                $(".error_msg").slideUp(500);
                            });
                        }
                    }
                });
                return false;
            });
        });

        $(".reveal").on('click', function () {
            var $pwd = $(".show-pass");
            if ($pwd.attr('type') === 'password') {
                $pwd.attr('type', 'text');
            } else {
                $pwd.attr('type', 'password');
            }
        });

        $(".reveal-1").on('click', function () {
            var $pwd = $(".show-pass-1");
            if ($pwd.attr('type') === 'password') {
                $pwd.attr('type', 'text');
            } else {
                $pwd.attr('type', 'password');
            }
        });
        $(".reveal-2").on('click', function () {
            var $pwd = $(".show-pass-2");
            if ($pwd.attr('type') === 'password') {
                $pwd.attr('type', 'text');
            } else {
                $pwd.attr('type', 'password');
            }
        });
    </script>

    <script>
        $(".toggle-password").click(function () {
            $(this).toggleClass("fa-eye-slash fa-eye");
        });

        function showHidePassword(id) {

            var x = document.getElementById(id);
            if (x.type === "password") {
                x.type = "text";

            } else {
                x.type = "password";
            }
        }

        function readURL(input, img) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#' + img).attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document.body).on('click', '#password_btn', function () {
            console.log($('#password').input.type);
        });
    </script>

    <script>
        $(document.body).on('click', '.iti__country', function () {
            var cd = $(this).find('.iti__dial-code').html();
            $('#code_v').val(cd)
        });
    </script>

    <script>
        $('#activity-tbl').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            retrieve: true,
            bLengthChange: true,
            responsive: true,
            ajax: '{{route('userProfile')}}',
            order: [3, 'desc'],
            autoWidth: false,
            language: {
                paginate: {
                    next: 'Next &#8250;',
                    previous: '&#8249; Previous'
                }
            },
            columns: [
                {"data": "action", "orderable": false},
                {"data": "source", "orderable": false},
                {"data": "ip_address", "orderable": false},
                {"data": "updated_at", "orderable": true},
            ],
        });

    </script>
@endsection