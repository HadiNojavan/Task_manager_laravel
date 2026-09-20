@php use Illuminate\Support\Facades\Auth; @endphp
@props(['title'=>"tasks"])

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{$title}}</title>
</head>
<style>
    body{
        background: #4a5568;

    }
</style>
<body>

    <div>
        @auth
            @php
                $user = Auth::user();
            @endphp

            <p>User ID: {{ $user->id }}</p>

            <p>Name: {{ $user->name }}</p>

            <p>Email: {{ $user->email }}</p>

            <p>Verified: {{ $user->hasVerifiedEmail() ? 'true' : 'false' }}</p>
        @endauth

        @guest
            <p>User: Guest</p>
        @endguest
    </div>

    <div class="main-page">

    </div>

</body>
</html>
