@extends('layouts.main')
@section('title', 'Manage topic')
@section('content')
<div class="card">
    <div class="card-header">
        <form name="topicFilter" action="{{route('topic.index')}}" method="get" class="row">
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
                    <input type="checkbox" class="custom-control-input" id="btnStatus" name="inactive" value="1" @if ($params['inactive']) checked @endif onchange="document.forms['topicFilter'].submit()">
                    <label class="custom-control-label" for="btnStatus" id="txt-status">Show Inactive</label>
                </div>
            </div>
            <div class="col-4 text-right"><a href="{{route('topic.create')}}" class="btn btn-sm btn-primary">{{__('manage.add')}} Topic</a></div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered">
                <thead>
                    <tr class="bg-info">
                        <th class="text-center">{{__('column.name')}}</th>
                        <th class="text-center">{{__('column.status')}}</th>
                        <th class="text-center">{{__('column.created_by')}}</th>
                        <th class="text-center">{{__('column.created_at')}}</th>
                        <th class="text-center">{{__('column.updated_by')}}</th>
                        <th class="text-center">{{__('column.updated_at')}}</th>
                        <th class="text-center">{{__('column.edit')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topics as $item)
                        <tr>
                            <td>{{$item->name}}</td>
                            <td>{{$item->active}}</td>
                            <td>{{$item->createdBy->username}}</td>
                            <td>{{$item->created_at->format('d/m/Y H:i:s')}}</td>
                            <td>{{$item->updatedBy->username}}</td>
                            <td>{{$item->updated_at->format('d/m/Y H:i:s')}}</td>
                            <td class="text-center"><a href="{{route('topic.edit',$item->id)}}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $topics->links() }}
        </div>
    </div>
</div>
@endsection
