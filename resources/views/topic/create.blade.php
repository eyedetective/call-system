@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-body">
                <form action="{{route('user.store')}}" method="POST" role="form">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>{{ __("column.name") }} <span class="text-danger">*</span></label>
                        <input
                        type="text"
                        class="form-control form-control-sm"
                        name="name"
                        placeholder="{{__('column.name')}}"
                        autocomplete="off"
                        />
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit">{{__('manage.add')}}</button>
                        <a class="btn btn-sm btn-danger" href="{{route('user.index')}}">{{__('manage.back')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
