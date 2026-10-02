<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="staff-login-form">
        @csrf
        <div class="login-field">
            <label for="email">Alamat email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@instansi.go.id">
            @if ($errors->get('email'))
                <span class="login-error">{{ $errors->first('email') }}</span>
            @endif
        </div>
        <div class="login-field">
            <div class="password-label-row">
                <label for="password">Kata sandi</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
            @if ($errors->get('password'))
                <span class="login-error">{{ $errors->first('password') }}</span>
            @endif
        </div>
        <label class="remember-option" for="remember_me">
            <input id="remember_me" type="checkbox" name="remember">
            <span>Ingat saya di perangkat ini</span>
        </label>
        <button type="submit" class="button button-primary login-submit">
            Masuk ke ruang petugas
            <svg viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M3.5 9h11m0 0-4-4m4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </form>
</x-guest-layout>