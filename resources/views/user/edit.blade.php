@extends('layouts.main')
@section('head_last')
<link rel="stylesheet" href="{{asset('admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
@endsection
@section('afterbody')
<script src="{{asset('admin/plugins/moment/moment-with-locales.min.js')}}"></script>
<script src="{{asset('admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<script>
    $(function () {
        document.querySelectorAll('.datetimepicker-input').forEach(function(element){
            $(element.getAttribute('data-target')).datetimepicker({
                locale: 'th',
                format: 'HH:mm'
            });
            element.addEventListener('keypress',function (e) {
                e.preventDefault();
            })
        });
    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Info.</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="wkh-tab" data-toggle="tab" href="#wkh" role="tab" aria-controls="wkh" aria-selected="false">Working Hour</a>
                    </li>
                </ul>
                <form action="{{route('user.update',$user->id)}}" method="POST" role="form">
                    {{ csrf_field() }}
                    @method('PUT')
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active p-2" id="info" role="tabpanel" aria-labelledby="info-tab">
                            <div class="form-group">
                                <label>{{ __("column.username") }} <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    name="username"
                                    placeholder="{{__('column.username')}}"
                                    value="{{$user->username}}"
                                    readonly="readonly"
                                    disabled
                                />
                            </div>
                            <div class="form-group">
                                <label>{{ __("column.password") }} <span class="text-danger">*</span></label>
                                <input
                                type="password"
                                class="form-control form-control-sm"
                                name="password"
                                placeholder="{{__('column.password')}}"
                                autocomplete="off"
                                />
                            </div>
                            <div class="form-group">
                                <label>{{ __("column.name") }} <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    name="name"
                                    placeholder="{{__('column.name')}}"
                                    value="{{$user->name}}"
                                    autocomplete="off"
                                />
                            </div>
                            <div class="form-group">
                                <label>{{ __("column.email") }}</label>
                                <input
                                    type="email"
                                    class="form-control form-control-sm"
                                    name="email"
                                    placeholder="{{__('column.email')}}"
                                    value="{{$user->email}}"
                                    autocomplete="off"
                                />
                            </div>
                            <div class="form-group">
                                <label>{{ __("column.permission") }}<span class="text-danger">*</span></label>
                                <select name="permission"class="form-control form-control-sm">
                                    @foreach ($permissions as $permission)
                                        <option value="{{$permission}}" @if($permission == $user->permission) selected @endif>{{$permission}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __("column.department") }}</label>
                                <select name="department"class="form-control form-control-sm">
                                    <option value="">-- Select --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{$department->id}}" @if($department->id == $user->department) selected @endif>{{$department->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="tab-pane fade p-2" id="wkh" role="tabpanel" aria-labelledby="wkh-tab">
                            @foreach ($days as $d)
                                <div class="form-group row">
                                    <label for="" class="col-md-2 text-capitalize">{{$d}}</label>
                                    <div class="input-group date col-md-5" id="{{$d}}_start" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-target="#{{$d}}_start" name="{{$d}}_start" value="{{$user[$d.'_start']}}" maxlength="5" data-toggle="datetimepicker"/>
                                        <div class="input-group-append" data-target="#{{$d}}_start" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="far fa-clock"></i></div>
                                        </div>
                                    </div>
                                    <div class="input-group date col-md-5" id="{{$d}}_end" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-target="#{{$d}}_end" name="{{$d}}_end" value="{{$user[$d.'_end']}}" maxlength="5" data-toggle="datetimepicker"/>
                                        <div class="input-group-append" data-target="#{{$d}}_end" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="far fa-clock"></i></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit">{{__('manage.save')}}</button>
                        <a class="btn btn-sm btn-danger" href="{{route('user.index')}}">{{__('manage.back')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
