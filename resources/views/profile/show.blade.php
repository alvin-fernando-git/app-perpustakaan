@extends('layouts.app')

@section('title', 'Profil Petugas')

@section('content')
    <style>
        .info th { width: 200px; background: #f3f4f6; }
        .profile-form { max-width: 460px; }
        .profile-form label { display: block; margin-top: 12px; font-weight: bold; }
        .profile-form input { width: 100%; padding: 6px; margin-top: 4px; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 4px; }
    </style>

    <h1>Profil Petugas</h1>

    <table class="info">
        <tr>
            <th>Nama</th>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>Role</th>
            <td>{{ ucfirst($user->role) }}</td>
        </tr>
    </table>

    <h2>Ganti Password</h2>

    <form class="profile-form" action="{{ route('profile.password') }}" method="POST">
        @csrf
        @method('PUT')

        <label for="password_lama">Password Lama</label>
        <input type="password" id="password_lama" name="password_lama" autocomplete="current-password">
        @error('password_lama')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password_baru">Password Baru</label>
        <input type="password" id="password_baru" name="password_baru" autocomplete="new-password">
        @error('password_baru')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password_baru_confirmation">Konfirmasi Password Baru</label>
        <input type="password" id="password_baru_confirmation" name="password_baru_confirmation" autocomplete="new-password">

        <p><button type="submit" class="btn">Simpan Password</button></p>
    </form>
@endsection