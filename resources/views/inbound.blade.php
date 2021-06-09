@extends('layouts.main')
@section('title', 'Voice Agent Screen')
@section('head_last')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{asset('admin/plugins/daterangepicker/daterangepicker.css')}}">
@endsection
@section('afterbody')
<script src="{{asset('admin/plugins/moment/moment-with-locales.min.js')}}"></script>
<script src="{{asset('admin/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="{{mix('/js/browser-calls.js')}}"></script>
<script>
    var user_list = {!! $users !!};
    window.me = {!! auth()->user() !!};
    var data_table = null;
    $(function () {
        $('#date').daterangepicker({maxDate:new Date, locale: {format: 'DD/MM/YYYY'}})
            .on('apply.daterangepicker', function(ev, picker) {
                $('#date_start').val(picker.startDate.format('YYYY-MM-DD'));
                $('#date_end').val(picker.endDate.format('YYYY-MM-DD'));
            });
        data_table = $('#tickets-table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '/api/ticket',
                method:'post',
                headers: { Authorization: 'Bearer ' + document.querySelector('meta[name=api-token]').getAttribute('content') },
                data : function ( d ) {
                    d.call_status = document.querySelector('[name=call_status]').value;
                    d.status = document.querySelector('[name=status]').value;
                    d.assignment = document.querySelector('[name=assignment]').value;
                    d.repBy = document.querySelector('[name=repBy]').value;
                    d.topic_id = document.querySelector('[name=topic_id]').value;
                    d.date_start = document.querySelector('[name=date_start]').value || moment().format('YYYY-MM-DD');
                    d.date_end = document.querySelector('[name=date_end]').value || moment().format('YYYY-MM-DD');
                }
            },
            "drawCallback": function (settings) {
                $('[data-toggle=popover]').popover();
            },
            "columns": [
                {
                    "data": "call_status", "name": "call_status",
                    render: function (data, type, row, meta) {
                        if (data == 'Answered') {
                            return '<span class="badge badge-success">' + data + '</span>'
                        } else if (data == 'Scheduled') {
                            var d = new Date(row.schedule_datetime);
                            return '<span class="badge badge-info">' + data + '</span>'
                            + '<br><small>'+moment(row.schedule_datetime).format('DD/MM/YYYY HH:mm')+'</small>'
                        } else {
                            return '<span class="badge badge-danger">' + data + '</span>'
                        }
                    }
                },
                { "data": "customer_name", "name": "customer_name" },
                { "data": "customer_phone", "name": "customer_phone" },
                { "data": "topic_id", "name": "topic_id", className: 'text-center', render:function(data, type, row, meta){return row.topic.name;}},
                {
                    "data": "created_at", "name": "created_at",
                    render: function (data, type, row, meta) {
                        return moment(data).format('DD/MM/YYYY HH:mm');
                    }
                },
                {
                    "data": "rep_user_id", "name": "rep_user_id",
                    render: function (data, type, row, meta) {
                        return row.rep_by ? row.rep_by.username : '-';
                    }
                },
                {
                    "data": "assign_user_id", "name": "assign_user_id",
                    render: function (data, type, row, meta) {
                        @if (auth()->user()->permission == 'Agent')
                            return row.assign_to.username;
                        @else
                            var $dropdown = '<select class="form-control form-control-sm text-capitalize" onchange="updateTicketField(' + row.id + ',\'assign_user_id\',this)">\
                                <option '+ (data == '' ? 'selected' : '') + ' value="">-</option>'
                            user_list.forEach(u => {
                                $dropdown += '<option ' + (data == u.id ? 'selected' : '') + ' value="' + u.id + '">' + u.username + '</option>';
                            });
                            $dropdown += '</select>';
                            return $dropdown;
                        @endif
                    }
                },
                {
                    "data": "status", "name": "status",
                    render: function (data, type, row, meta) {
                        @if (auth()->user()->permission == 'Agent')
                            return data;
                        @else
                            return '<select class="form-control form-control-sm text-capitalize" onchange="updateTicketField(' + row.id + ',\'status\',this)">\
                                <option '+ (data == 'pending' ? 'selected' : '') + ' value="pending">pending</option>\
                                <option '+ (data == 'spam' ? 'selected' : '') + ' value="spam">spam</option>\
                                <option '+ (data == 'alert' ? 'selected' : '') + ' value="alert">alert</option>\
                                <option '+ (data == 'closed' ? 'selected' : '') + ' value="closed">closed</option>\
                            </select>';
                        @endif
                    }
                },
                {
                    "data": "action",
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return '<button data-widget="control-sidebar" data-slide="true" class="btn btn-sm btn-primary" onclick="setCallDetail('+row.id+')">View</button>';
                    }
                }
            ]
        });
    });

    window.refreshTable = function () { data_table.ajax.reload() }
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4 col-md-4">
        <div class="card">
            <h5 class="card-header">
                Status :
                <div class="d-inline custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox" class="custom-control-input" id="btnStatus" onchange="setupStatus(this)">
                    <label class="custom-control-label" for="btnStatus" id="txt-status">Offline</label>
                </div>

                <button class="btn btn-sm btn-secondary float-right" onclick="toggleMute(this);"><i class="fas fa-microphone"></i></button>
            </h5>
            <div class="card-body">
                <div class="form-group row">
                    <label for="call-status" class="col-3 col-form-label">Twilio Status</label>
                    <div class="col-9">
                        <input id="call-status" class="form-control" type="text" placeholder="Offline" readonly>
                    </div>
                </div>
                <div class="caller-info d-none">
                    <div class="form-group row">
                        <label for="call-topic" class="col-3 col-form-label">Topic</label>
                        <div class="col-9">
                            <input id="call-topic" class="form-control" type="text" placeholder="" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="caller-name" class="col-3 col-form-label">Caller Name</label>
                        <div class="col-9">
                            <input id="caller-name" class="form-control" type="text" placeholder="" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="caller-phone" class="col-3 col-form-label">Caller Phone</label>
                        <div class="col-9">
                            <input id="caller-phone" class="form-control" type="text" placeholder="" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group text-center">
                <button class="btn btn-sm btn-success answer-button" disabled>Answer call</button>
                <button class="btn btn-sm btn-danger hangup-button" disabled onclick="hangUp()">Hang up</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="card">
            <div class="card-header">
                <button class="btn btn-sm btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    <i class="fas fa-search mr-1"></i> Filter Data
                </button>
            </div>

            <div id="collapseOne" class="collapse">
            <div class="card-body row">
                <div class="form-group col-4">
                    <label>Date range</label>

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="far fa-calendar-alt"></i>
                        </span>
                      </div>
                      <input type="text" class="form-control float-right" id="date">
                      <input type="hidden" id="date_start" name="date_start" value="">
                      <input type="hidden" id="date_end" name="date_end" value="">
                    </div>
                    <!-- /.input group -->
                </div>
                <div class="form-group col-4">
                    <label for="">Call Status</label>
                    <select name="call_status" class="form-control form-control-sm text-capitalize">
                        <option value="">All</option>
                        <option value="No answer">No answer</option>
                        <option value="Answered">Answered</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Failed">Failed</option>
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="">Topic</label>
                    <select name="topic_id" id="" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach ($topics as $topic)
                            <option value="{{$topic->id}}">{{$topic->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="">Call Rep</label>
                    <select name="repBy" id="" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach ($users as $user)
                            <option value="{{$user->id}}">{{$user->username}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="">Assignment</label>
                    <select name="assignment" id="" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach ($users as $user)
                            <option value="{{$user->id}}">{{$user->username}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="">Status</label>
                    <select name="status" id="" class="form-control form-control-sm text-capitalize">
                        <option value="">All</option>
                        <option value="pending">pending</option>
                        <option value="spam">spam</option>
                        <option value="alert">alert</option>
                        <option value="closed">closed</option>
                    </select>
                </div>
                <button class="btn btn-block btn-sm btn-primary" onclick="refreshTable()"><i class="fas fa-search"></i> Search</button>
            </div>
            </div>
        </div>
        <table id="tickets-table" class="table table-sm table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Call Status</th>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Topic</th>
                    <th>Date</th>
                    <th>Call Rep</th>
                    <th>Assignment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection
@section('control-sidebar')
    <h5>Call Detail <button class=" btn btn-sm btn-danger float-right" data-widget="control-sidebar" data-slide="true" onclick="document.querySelector('.ui--control-overlay').style.display ='none'">&times;</button></h5>
    <hr class="mb-2">
    <label>Call Status</label>
    <input class="form-control" value="" id="cdetail-call-status" readonly="">
    <label>Topic</label>
    <input class="form-control" value="" id="cdetail-topic" readonly="">
    <label>Customer Phone</label>
    <input class="form-control" value="" id="cdetail-customer-phone" readonly="">
    <label>Customer Name</label>
    <input class="form-control" value="" id="cdetail-customer-name" readonly="">
    <label>Representative Name</label>
    <input class="form-control" value="" id="cdetail-call-rep" readonly="">
    <label>Date</label>
    <input class="form-control" value="" id="cdetail-date" readonly="">
    <label>Time</label>
    <input class="form-control" value="" id="cdetail-time" readonly="">
    <label>Duration</label>
    <input class="form-control" value="" id="cdetail-duration" readonly="">
    <label>Source</label>
    <input class="form-control" value="" id="cdetail-source" readonly="">
    <label>IP Address</label>
    <input class="form-control" value="" id="cdetail-ip-address" readonly="">
    <label>UTM Source</label>
    <input class="form-control" value="" id="cdetail-utm-source" readonly="">
    <label>UTM Medium</label>
    <input class="form-control" value="" id="cdetail-utm-medium" readonly="">
    <label>UTM Campaign</label>
    <input class="form-control" value="" id="cdetail-utm-campaign" readonly="">
    <label>UTM Content</label>
    <input class="form-control" value="" id="cdetail-utm-content" readonly="">
    <label>UTM Term</label>
    <input class="form-control" value="" id="cdetail-utm-term" readonly="">
    <form method="post" name="cdetail-form">
        <input type="hidden" name="id" id="cdetail-id" value="">
        <label for="comment">Comment</label>
        <textarea name="comment" id="cdetail-comment" class="form-control" rows="3"></textarea>
        <label for="assign_user_id">Assigned Call Rep:</label>
        <select name="assign_user_id" id="cdetail-assign" class="form-control">
            <option value="">-</option>
            @foreach ($users as $u)
                <option value="{{$u->id}}">{{$u->username}}</option>
            @endforeach
        </select>
        <label  for="status">Status:</label>
        <select name="status" id="cdetail-status" class="form-control text-capitalize">
            <option value="">-</option>
            <option value="pending">pending</option>
            <option value="alert">alert</option>
            <option value="spam">spam</option>
            <option value="closed">closed</option>
        </select>
    </form>
    <hr>
    <button class="btn btn-primary mb-4" onclick="updateTicket()">Update</button>
@endsection
