<!DOCTYPE html>
</html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/js/app.js')
        @vite('resources/css/app.css')
        @routes
        <x-inertia::head />
    </head>
    <body class="bg-slate-950">
        <x-inertia::app />
    </body>
</html>
