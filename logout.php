<?php
// =============================================
//  logout.php — Destroy Session & Log Out
// =============================================
session_start();

$username = $_SESSION['username'] ?? 'Student';

// Destroy all session data
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out — Smart Student Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="refresh" content="3;url=index.php">
    <style>
        .logout-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .logout-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 60px 50px;
            text-align: center;
            max-width: 440px;
            animation: fadeInUp 0.5s ease;
        }
        .wave-emoji {
            font-size: 4rem;
            display: block;
            margin-bottom: 20px;
            animation: wave 1.5s ease infinite;
            transform-origin: 70% 70%;
        }
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25%       { transform: rotate(20deg); }
            75%       { transform: rotate(-10deg); }
        }
        .countdown {
            display: inline-block;
            background: var(--primary);
            color: #fff;
            width: 36px; height: 36px;
            border-radius: 50%;
            line-height: 36px;
            font-weight: 800;
            font-family: 'Space Mono', monospace;
            margin: 0 4px;
        }
    </style>
</head>
<body>
<div class="logout-wrap">
    <div class="logout-card">
        <span class="wave-emoji">👋</span>
        <h2 style="font-size: 2rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 10px;">
            Goodbye, <?= htmlspecialchars($username) ?>!
        </h2>
        <p style="color: var(--text-muted); margin-bottom: 28px; line-height: 1.6;">
            You've been logged out successfully.<br>
            Your session has been cleared.
        </p>
        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 24px;">
            Redirecting in <span class="countdown" id="cd">3</span> seconds…
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="login.php"    class="btn btn-primary btn-sm">🔑 Login Again</a>
            <a href="index.php"    class="btn btn-secondary btn-sm">🏠 Home</a>
        </div>
    </div>
</div>

<div class="particles" id="particles"></div>
<script>
let count = 3;
const cd = document.getElementById('cd');
const timer = setInterval(() => {
    cd.textContent = --count;
    if (count <= 0) {
        clearInterval(timer);
        window.location.href = 'index.php';
    }
}, 1000);

// Particles
const container = document.getElementById('particles');
for (let i = 0; i < 20; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    p.style.cssText = `left:${Math.random()*100}%;animation-duration:${Math.random()*15+10}s;animation-delay:${Math.random()*10}s;`;
    container.appendChild(p);
}
</script>
</body>
</html>
