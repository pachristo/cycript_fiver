@extends('admin.master',['menu'=>'faq', 'sub_menu'=>'faq'])
@section('title',isset($cp) ? 'Update Custom Page' : 'Add Custom Page')
@section('style')
@endsection
@section('content')
    <div class="custom-breadcrumb">
        <div class="row">
            <div class="col-12">
                <ul>
                    <li>{{__('Custom Pages')}}</li>
                    <li class="active-item">{{$title}}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="user-management add-custom-page">
        <div class="row">
            <div class="col-12">
                <div class="header-bar">
                    <div class="table-title">
                        <h3>{{$title}}</h3>
                    </div>
                </div>
                <div class="profile-info-form">
                    <form action="{{route('adminCustomPageSave')}}" method="post">
                        @if(!empty($cp->id))
                            <input type="hidden" name="edit_id" value="{{$cp->id}}">
                        @endif
                        @csrf
                        <div class="row">
                            <div class="col-xl-12 mb-xl-0 mb-4">
                                <div class="form-group">
                                    <label>{{__('Menu name')}}</label>
                                    <input type="text" name="menu" placeholder="{{__('Menu name')}}" @if(isset($cp))value="{{$cp->key}}" @else value="{{old('key')}}" @endif>
                                </div>
                                <div class="form-group">
                                    <label>{{__('Page Title')}}</label>
                                    <input type="text" name="title" placeholder="{{__('Title')}}" @if(isset($cp))value="{{$cp->title}}" @else value="{{old('title')}}" @endif>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('Description')}}</label>
                                    <textarea rows="10" name="description" id="editor" class="textarea" class="form-control">@if(isset($cp)){!! $cp->description !!} @else {{old('description')}} @endif</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn add-faq-btn">
                                        @if(isset($cp)) {{__('Update')}} @else {{__('Submit')}} @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- .user-area end -->
@endsection
@section('script')
    <script src="{{asset('assets/admin/js/ckeditor.js')}}"></script>
    <script>
        ClassicEditor
            .create( document.querySelector( '#editor',function () {
                config.width = 500;
            } ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
    <script>
        CKEDITOR.replace( '#editor', {
            uiColor: '#14B8C4',
            toolbar: [
                [ 'Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink' ],
                [ 'FontSize', 'TextColor', 'BGColor' ]
            ],
            width:['250px']

        });

    </script>


@endsection
