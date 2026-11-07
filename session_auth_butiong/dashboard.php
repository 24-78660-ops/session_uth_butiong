<?php
require_once __DIR__ . '/db.php';


if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}


$userId = $_SESSION['user']['id'];
$username = $_SESSION['user']['username'];
$email = $_SESSION['user']['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #212529;
            background: #fff;
        }
        svg { width: 1em; height: 1em; vertical-align: -0.125em; }


        /* Navbar */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 12px 24px;
            background: #14532d;
            color: #fff;
        }
        .navbar-left { display: flex; align-items: center; gap: 24px; }
        .brand { font-size: 1.25rem; font-weight: 700; color: #fff; text-decoration: none; }
        .navbar a.link { color: rgba(255, 255, 255, 0.85); text-decoration: none; }
        .navbar-right { display: flex; align-items: center; gap: 14px; }
        .navbar-right form { margin: 0; }
        .logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            font: inherit;
            font-size: 0.9rem;
            color: #dc3545;
            background: transparent;
            border: 1px solid #dc3545;
            border-radius: 6px;
            cursor: pointer;
        }
        .logout:hover { color: #fff; background: #dc3545; }


        /* Page */
        .container { max-width: 1140px; margin: 0 auto; padding: 32px 20px 48px; }


        .hero {
            padding: 48px 40px;
            margin-bottom: 24px;
            background: #eaf6ee;
            border-radius: 12px;
        }
        .hero h1 { margin: 0 0 12px; font-size: 3rem; font-weight: 700; color: #1b7a3a; line-height: 1.15; }
        .hero p { margin: 0 0 24px; font-size: 1.25rem; color: #495057; }
        .btn-primary {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1.05rem;
            color: #fff;
            background: #28a745;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-primary:hover { background: #1e8a38; }


        /* Info card */
        .card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            overflow: hidden;
        }
        .card-body { display: grid; grid-template-columns: repeat(3, 1fr); }
        .col { padding: 28px; }
        .col + .col { border-left: 1px solid #dee2e6; }
        .col h2 { margin: 0 0 12px; font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .col p { margin: 6px 0; font-size: 1rem; }
        .status h2 { color: #1b7a3a; }
        .details h2 { color: #28a745; }
        .session h2 { color: #1b7a3a; }
        code {
            font-family: SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 0.875rem;
            color: #d63384;
            word-break: break-all;
        }
        .muted { color: #6c757d; font-size: 0.9rem; }
        .card-footer {
            padding: 14px 28px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.95rem;
        }


        /* Bouncing notification */
        .bounce-note {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            max-width: 260px;
            padding: 10px 14px;
            font-size: 0.9rem;
            line-height: 1.35;
            color: #fff;
            background: #28a745;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            will-change: transform;
        }
        .bounce-note button {
            padding: 0;
            font-size: 1.1rem;
            line-height: 1;
            color: #fff;
            background: none;
            border: 0;
            cursor: pointer;
        }


        @media (max-width: 800px) {
            .card-body { grid-template-columns: 1fr; }
            .col + .col { border-left: 0; border-top: 1px solid #dee2e6; }
            .hero { padding: 32px 22px; }
            .hero h1 { font-size: 2.2rem; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a class="brand" href="dashboard.php">SessionAuthSystem</a>
            <a class="link" href="dashboard.php">Dashboard</a>
        </div>
        <div class="navbar-right">
            <span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path d="M6.17 18.5a7 7 0 0 1 11.66 0"/></svg>
                <?= e($username) ?>
            </span>
            <form method="post" action="logout.php">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <button class="logout" type="submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </nav>


    <main class="container">
        <section class="hero">
            <h1>Welcome, <?= e($username) ?>!</h1>
            <p>This page is protected and only visible to logged-in users.</p>
            <a class="btn-primary" href="dashboard.php">Explore Dashboard</a>
        </section>


        <section class="card">
            <div class="card-body">
                <div class="col status">
                    <h2>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V5l8-3 8 3z"/><path d="m9 12 2 2 4-4"/></svg>
                        Account Status
                    </h2>
                    <p>Your session is active and protected against Session Fixation using <code>session_regenerate_id(true)</code>.</p>
                </div>


                <div class="col details">
                    <h2>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M7.5 18a4.5 4.5 0 0 1 9 0"/></svg>
                        User Details
                    </h2>
                    <p><strong>User ID:</strong> <?= e((string) $userId) ?></p>
                    <p><strong>Username:</strong> <?= e($username) ?></p>
                    <p><strong>Email:</strong> <?= e($email) ?></p>
                </div>


                <div class="col session">
                    <h2>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="7.5" cy="15.5" r="4.5"/><path d="m10.7 12.3 9.3-9.3"/><path d="m16 7 3 3"/><path d="m14 9 2 2"/></svg>
                        Active Session ID
                    </h2>
                    <p><strong>Current Session ID:</strong></p>
                    <p><code><?= e(session_id()) ?></code></p>
                    <p class="muted">Generated after login call to <code>session_regenerate_id(true)</code></p>
                </div>
            </div>
        </section>
    </main>


    <div class="bounce-note" id="bounce-note" role="status">
        <span>Log out and log in again to see session regeneration in action.</span>
        <button type="button" id="bounce-close" aria-label="Dismiss notification">&times;</button>
    </div>


    <script>
    (function () {
        var el = document.getElementById('bounce-note');
        document.getElementById('bounce-close').addEventListener('click', function () {
            el.remove();
        });


        // Respect "reduce motion": just pin it to the corner.
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            el.style.top = 'auto';
            el.style.left = 'auto';
            el.style.right = '20px';
            el.style.bottom = '20px';
            return;
        }


        var x = 40, y = 100, dx = 1.6, dy = 1.3;
        var colors = ['#28a745', '#1b7a3a', '#14532d', '#2e9e5b', '#0f766e'];
        var ci = 0;


        function hit() {
            ci = (ci + 1) % colors.length;
            el.style.background = colors[ci];
        }


        function step() {
            if (!document.body.contains(el)) { return; }
            var maxX = Math.max(0, window.innerWidth - el.offsetWidth);
            var maxY = Math.max(0, window.innerHeight - el.offsetHeight);


            x += dx;
            y += dy;


            if (x <= 0)         { x = 0;    dx =  Math.abs(dx); hit(); }
            else if (x >= maxX) { x = maxX; dx = -Math.abs(dx); hit(); }
            if (y <= 0)         { y = 0;    dy =  Math.abs(dy); hit(); }
            else if (y >= maxY) { y = maxY; dy = -Math.abs(dy); hit(); }


            el.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
            requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    })();
    </script>
</body>
</html>