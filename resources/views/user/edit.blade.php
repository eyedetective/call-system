@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-body">
                <form action="{{route('user.update',$user->id)}}" method="POST" role="form">
                    {{ csrf_field() }}
                    @method('PUT')
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
                                <option value="{{$department}}" @if($department == $user->department) selected @endif>{{$department}}</option>
                            @endforeach
                        </select>
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
