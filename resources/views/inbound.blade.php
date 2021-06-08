@extends('layouts.main')
@section('title', 'Voice Agent Screen')
@section('head_last')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
@endsection
@section('content')
<div class="row">
    <div class="col-md-4 order-md-2 mb-4">
        <div class="card">
            <h5 class="card-header">
                Status :
                <div class="d-inline custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox" class="custom-control-input" id="btnStatus" onchange="setupStatus(this)">
                    <label class="custom-control-label" for="btnStatus" id="txt-status">Offline</label>
                </div>
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
                <button class="btn btn-lg btn-success answer-button" disabled>Answer call</button>
                <button class="btn btn-lg btn-danger hangup-button" disabled onclick="hangUp()">Hang up</button>
            </div>
        </div>
    </div>

    <div class="col-md-8 order-md-1">
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

@section('afterbody')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="/js/browser-calls.js"></script>
<script>
    var user_list = {!! $users !!};
    window.me = {!! auth()->user() !!};
    var data_table = null;
    $(document).ready(function () {
        data_table = $('#tickets-table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '/api/ticket',
                method:'post',
                headers: { Authorization: 'Bearer ' + document.querySelector('meta[name=api-token]').getAttribute('content') }
            },
            "drawCallback": function (settings) {
                $('[data-toggle=popover]').popover();
            },
            "columns": [
                {
                    "data": "call_status", "name": "call_status",
                    render: function (data, type, row, meta) {
                        if (data == 'Answered') {
                            return '<button class="btn btn-sm btn-success">' + data + '</button>'
                        } else if (data == 'Scheduled') {
                            var d = new Date(row.schedule_datetime);
                            return '<button class="btn btn-sm btn-secondary" data-toggle="popover" data-trigger="focus" title="Schedule datetime" data-content="' + d.toLocaleString('th-TH') + '">' + data + '</button>'
                        } else {
                            return '<button class="btn btn-sm btn-danger">' + data + '</button>'
                        }
                    }
                },
                { "data": "customer_name", "name": "customer_name" },
                { "data": "customer_phone", "name": "customer_phone" },
                { "data": "topic", "name": "topic", className: 'text-center', },
                {
                    "data": "created_at", "name": "created_at",
                    render: function (data, type, row, meta) {
                        var date = new Date(data);
                        return date.toLocaleDateString('th-TH', {
                            year: 'numeric',
                            month: 'numeric',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric',
                            second: 'numeric'
                        });
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
