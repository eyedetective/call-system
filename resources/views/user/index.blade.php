@extends('layouts.main')
@section('title', 'Manage User')
@section('head_last')
@endsection
@section('content')
<div class="card">
    <div class="card-header row">
        <div class="col-md-6"><input type="text" placeholder="search" class="form-control form-control-sm"></div>
        <div class="col-md-6"><a href="{{route('user.create')}}" class="btn btn-sm btn-primary">{{__('manage.add')}}</a></div>
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
                        <th class="text-center">{{__('column.created_by')}}</th>
                        <th class="text-center">{{__('column.created_at')}}</th>
                        <th class="text-center">{{__('column.updated_by')}}</th>
                        <th class="text-center">{{__('column.updated_at')}}</th>
                        <th class="text-center">{{__('column.edit')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{$item->username}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->email}}</td>
                            <td>{{$item->permission}}</td>
                            <td>{{$item->createdBy->username}}</td>
                            <td>{{$item->created_at->format('d/m/Y H:i:s')}}</td>
                            <td>{{$item->updatedBy->username}}</td>
                            <td>{{$item->updated_at->format('d/m/Y H:i:s')}}</td>
                            <td class="text-center"><a href="{{route('user.edit',$item->id)}}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $data->links() }}
        </div>
    </div>
</div>
@endsection
@section('afterbody')
@endsection
