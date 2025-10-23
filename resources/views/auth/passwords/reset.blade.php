<!-- TODO リファクタリング -->
{{-- resources/views/auth/passwords/reset.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>パスワードリセット</title>
</head>
<body>
    <h1>パスワードリセット</h1>

    @if (session('status'))
        <div>{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
        </div>

        <div>
            <label for="password">新しいパスワード</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div>
            <label for="password_confirmation">パスワード確認</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button type="submit">パスワードをリセット</button>
    </form>
</body>
</html>
