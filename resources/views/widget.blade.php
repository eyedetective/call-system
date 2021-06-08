@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Installation') }}</div>

                <div class="card-body">
                    <textarea class="form-control">
                        <script type="text/javascript">(function () { var d = document, h = d.getElementsByTagName("head")[0], s = d.createElement("script"); s.type = "text/javascript"; s.async = !0; s.src = "{{asset('js/widget.js')}}"; h.appendChild(s) }())</script>
                     </textarea>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
