<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Tiket Kampus') }}</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #111827;
            background: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(ellipse at 15% 15%, rgba(99, 102, 241, 0.1), transparent 42%),
                #f8fafc;
        }

        .landing {
            display: flex;
            min-height: 100vh;
            align-items: center;
            padding: 64px 24px;
        }

        .landing__content {
            width: min(100%, 880px);
            margin: 0 auto;
        }

        .landing__eyebrow {
            margin: 0 0 20px;
            color: #4f46e5;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .landing h1 {
            max-width: 760px;
            margin: 0;
            font-size: clamp(38px, 6vw, 64px);
            font-weight: 750;
            letter-spacing: -0.045em;
            line-height: 1.08;
        }

        .landing__description {
            max-width: 650px;
            margin: 24px 0 0;
            color: #4b5563;
            font-size: 18px;
            line-height: 1.7;
        }

        .landing__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 32px;
        }

        .landing__button {
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            color: #1f2937;
            font-size: 15px;
            font-weight: 650;
            text-decoration: none;
            transition: background-color 150ms ease, transform 150ms ease;
        }

        .landing__button:hover {
            background: #fff;
            transform: translateY(-1px);
        }

        .landing__button--primary {
            border-color: #4f46e5;
            background: #4f46e5;
            color: #fff;
        }

        .landing__button--primary:hover {
            background: #4338ca;
        }

        .landing__button:focus-visible {
            outline: 3px solid rgba(79, 70, 229, 0.35);
            outline-offset: 3px;
        }

        @media (max-width: 480px) {
            .landing {
                padding: 48px 22px;
            }

            .landing__description {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <main class="landing">
        <div class="landing__content">
        <p class="landing__eyebrow">Festival Teknologi Kampus</p>
        <h1>
            Pesan tiket acara kampus dengan mudah.
        </h1>
        <p class="landing__description">
            Amankan tiket Anda. Bukti pemesanan akan dikirim melalui email setelah transaksi berhasil diproses.
        </p>
        <div class="landing__actions">
            @auth
                <a href="{{ route('booking.create') }}" class="landing__button landing__button--primary">
                    Pesan tiket
                </a>
                <a href="{{ route('booking.index') }}" class="landing__button">
                    Tiket saya
                </a>
            @else
                <a href="{{ route('register') }}" class="landing__button landing__button--primary">
                    Daftar peserta
                </a>
                <a href="{{ route('login') }}" class="landing__button">
                    Masuk
                </a>
            @endauth
        </div>
        </div>
    </main>
</body>
</html>
