@extends('layouts.main')
@section('title', 'Manage User')
@section('content')
<div class="card">
    <div class="card-header">
        <form name="userFilter" action="{{route('user.index')}}" method="get" class="row">
            <div class="col-4">
                <div class="input-group input-group-sm">
                    <input type="text" placeholder="search" class="form-control form-control-sm" name="search" value="{{$params['search']}}">
                    <span class="input-group-append">
                        <button type="submit" class="btn btn-info btn-flat"><i class="fas fa-search"></i></button>
                    </span>
                </div>
            </div>
            <div class="col-4">
                <div class="d-inline custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="btnStatus" name="inactive" value="1" @if ($params['inactive']) checked @endif onchange="document.forms['userFilter'].submit()">
                    <label class="custom-control-label" for="btnStatus" id="txt-status">Show Inactive</label>
                </div>
            </div>
            <div class="col-4 text-right"><a href="{{route('user.create')}}" class="btn btn-sm btn-primary">{{__('manage.add')}} User</a></div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered">
                <thead>
                    <tr class="bg-info">
                        <th class="text-center">{{__('column.username')}}</th>
                        <th class="text-center">{{__('column.name')}}</th>
                        <th class="text-center">{{__('column.email')}}</th>
                        <th class="text-center">{{__('column.permission')}}</th>
                        <th class="text-center">{{__('column.status')}}</th>
                        <th class="text-center">{{__('column.created_by')}}</th>
                        <th class="text-center">{{__('column.created_at')}}</th>
                        <th class="text-center">{{__('column.updated_by')}}</th>
                        <th class="text-center">{{__('column.updated_at')}}</th>
                        <th class="text-center">{{__('column.edit')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{$user->username}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->permission}}</td>
                            <td>
                                @if (is_null($user->deleted_at))
                                    <a href="{{route('user.delete',$user->id)}}" class="btn btn-sm btn-success">Active</a>
                                @else
                                    <a href="{{route('user.restore',$user->id)}}" class="btn btn-sm btn-danger">Inactive</a>
                                @endif
                                {{$user->status}}</td>
                            <td>{{$user->createdBy->username}}</td>
                            <td>{{$user->created_at->format('d/m/Y H:i:s')}}</td>
                            <td>{{$user->updatedBy->username}}</td>
                            <td>{{$user->updated_at->format('d/m/Y H:i:s')}}</td>
                            <td class="text-center"><a href="{{route('user.edit',$user->id)}}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
