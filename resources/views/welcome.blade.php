<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Gestão de Produtos</title>
    <style>
        :root {
            --gov-blue: #0042b1;
            --gov-blue-dark: #003894;
            --bg: #f8fafc;
            --text: #1e293b;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Figtree, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        .card {
            width: min(92vw, 680px);
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            padding: 40px 32px;
            text-align: center;
        }
        h1 {
            margin: 0 0 24px;
            font-size: clamp(1.5rem, 2vw + 1rem, 2rem);
            font-weight: 700;
            color: var(--gov-blue);
        }
        .cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--gov-blue);
            border-radius: 10px;
            padding: 12px 24px;
            background: var(--gov-blue);
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color .2s ease, transform .2s ease;
        }
        .cta:hover {
            background: var(--gov-blue-dark);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>📦 Sistema de Gestão de Produtos</h1>
        @auth
            <a class="cta" href="{{ route('dashboard') }}">Entrar no Sistema</a>
        @else
            <a class="cta" href="{{ route('login') }}">Entrar no Sistema</a>
        @endauth
    </main>
</body>
</html>
