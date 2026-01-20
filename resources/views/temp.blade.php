<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Projects</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <h1>Employee Project View</h1>
    <p>Name: {{ auth()->user()->name }}</p>
    <p>Email: {{ auth()->user()->email }}</p>
    <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px;">
        @csrf
        <div class="sidebar-footer">
            <button type="submit" class="sign-out-link" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer; font-size: 1rem; color: #d33;">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </button>
        </div>
    </form>

</body>
</html>