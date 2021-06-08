<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Laravel</title>
        <script type="text/javascript">(function () { var d = document, h = d.getElementsByTagName("head")[0], s = d.createElement("script"); s.type = "text/javascript"; s.async = !0; s.src = "{{asset('/js/widget.js')}}"; h.appendChild(s) }())</script>
    </head>
    <body>

    </body>
</html>


