<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'App')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        header { background: #333; color: white; padding: 1rem 2rem; }
        header h1 { font-size: 1.5rem; }
        nav { display: flex; gap: 1rem; margin-top: 0.5rem; }
        nav a { color: white; text-decoration: none; }
        main { max-width: 1200px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        footer { background: #333; color: white; text-align: center; padding: 1rem; margin-top: 2rem; }
        form div { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
        select { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #333; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #555; }
        span { color: red; font-size: 0.9rem; }
        a { color: #333; }
        tr { cursor: pointer; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <header>
        <h1>FilRouge App</h1>
        <nav>
            @auth
                <a href="/">Home</a>
                <a href="/companies">My Companies</a>
                @php
                    $userCompanies = Auth::user()->companies;
                @endphp
                @if($userCompanies->count() > 0)
                    <select onchange="if(this.value) window.location.href=this.value" style="padding: 0.5rem; border: 1px solid white; border-radius: 4px; background: #555; color: white; cursor: pointer;">
                        <option value="">Switch Company</option>
                        @foreach($userCompanies as $comp)
                            <option value="/companies/{{ $comp->id }}">{{ $comp->name }}</option>
                        @endforeach
                    </select>
                @endif
                <a href="/companies/create">Create Company</a>
                <form method="POST" action="/logout" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @else
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            @endauth
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2024 FilRouge App. All rights reserved.</p>
    </footer>
</body>
</html>
