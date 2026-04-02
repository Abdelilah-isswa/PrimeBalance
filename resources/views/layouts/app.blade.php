<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Expenso')</title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; 
            background: #f5f7fa; 
        }
        
        /* Header - Xero Style */
        header { 
            background: #1a2c3e; 
            color: white; 
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .logo h1 { 
            font-size: 1.5rem;
            color: #13B5EC;
            margin: 0;
        }
        
        /* Navbar Styles */
        .navbar { 
            display: flex; 
            gap: 1.5rem; 
            align-items: center;
            flex-wrap: wrap;
        }
        
        .navbar a, 
        .navbar .nav-link { 
            color: white; 
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .navbar a:hover, 
        .navbar .nav-link:hover { 
            color: #13B5EC;
        }
        
        /* Company Selector */
        .company-selector {
            padding: 0.5rem 1rem;
            border: 1px solid #4a627a;
            border-radius: 6px;
            background: #2c3e50;
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .company-selector:hover {
            background: #3a5670;
            border-color: #13B5EC;
        }
        
        /* Logout Button - Fixed */
        .logout-form {
            display: inline;
            margin: 0;
            padding: 0;
        }
        
        .logout-btn {
            background: transparent;
            color: white;
            padding: 0;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: color 0.2s;
            font-family: inherit;
        }
        
        .logout-btn:hover {
            background: transparent;
            color: #13B5EC;
        }
        
        /* Auth Links */
        .auth-links {
            display: flex;
            gap: 1.5rem;
        }
        
        .auth-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .auth-links a:hover {
            color: #13B5EC;
        }
        
        /* Main Container */
        main { 
            max-width: 1200px; 
            margin: 2rem auto; 
            padding: 2rem; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            min-height: 500px;
        }
        
        footer { 
            background: #1a2c3e; 
            color: #8a99aa; 
            text-align: center; 
            padding: 1.5rem; 
            margin-top: 2rem;
            font-size: 14px;
        }
        
        /* Form Styles */
        .form-group { 
            margin-bottom: 1.25rem; 
        }
        
        label { 
            display: block; 
            margin-bottom: 0.5rem; 
            font-weight: 500;
            color: #1a2c3e;
        }
        
        input, 
        .form-input { 
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            font-size: 16px;
            transition: border-color 0.2s;
        }
        
        input:focus,
        .form-input:focus {
            outline: none;
            border-color: #13B5EC;
        }
        
        select { 
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #ddd; 
            border-radius: 6px;
            font-size: 14px;
            background: white;
        }
        
        select:focus {
            outline: none;
            border-color: #13B5EC;
        }
        
        button, 
        .btn { 
            background: #13B5EC; 
            color: white; 
            padding: 0.75rem 1.5rem; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }
        
        button:hover, 
        .btn:hover { 
            background: #0e9fd1;
        }
        
        /* Error Messages */
        .error-message, 
        .text-red { 
            color: #e74c3c; 
            font-size: 0.85rem; 
            margin-top: 0.25rem;
            display: block;
        }
        
        a { 
            color: #13B5EC;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        /* Table Hover */
        table tr { 
            cursor: pointer; 
        }
        
        table tr:hover { 
            background: #f8f9fa; 
        }
        
        /* Utility Classes */
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            color: #c33;
        }
        
        .alert-success {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            color: #2e7d32;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            
            .navbar {
                justify-content: center;
            }
            
            main {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Expenso</h1>
            </div>
            
            <div class="navbar">
                @auth
                    <a href="/">Home</a>
                    <a href="/companies">My Companies</a>
                    
                    @php
                        $userCompanies = Auth::user()->companies;
                    @endphp
                    
                    @if($userCompanies->count() > 0)
                        <select class="company-selector" onchange="if(this.value) window.location.href=this.value">
                            <option value="">Select Company</option>
                            @foreach($userCompanies as $comp)
                                <option value="/companies/{{ $comp->id }}">{{ $comp->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    
                    <a href="/companies/create">Create Company</a>
                    
                    <form method="POST" action="/logout" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                @else
                    <div class="auth-links">
                        <a href="/login">Login</a>
                        <a href="/register">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2024 Expenso. All rights reserved.</p>
    </footer>
</body>
</html>