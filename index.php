<?php
// =============================================
//  index.php — Homepage
//  Smart Student Portal | Adarsh Ray | BCA 2026
// =============================================
session_start();

// Visit Counter using Cookies
$visits = isset($_COOKIE['visit_count']) ? (int)$_COOKIE['visit_count'] + 1 : 1;
setcookie('visit_count', $visits, time() + (30 * 24 * 60 * 60), '/'); // 30 days

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Student Portal — Adarsh Ray BCA 2026</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Typing effect */
        .typing-text::after {
            content: '|';
            animation: blink 0.8s step-end infinite;
        }
        @keyframes blink { 50% { opacity: 0; } }

        /* Feature cards grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        /* Gradient icon colors */
        .icon-purple { background: linear-gradient(135deg, #6c63ff33, #6c63ff66); color: #a89ec9; }
        .icon-pink   { background: linear-gradient(135deg, #ff658433, #ff658466); color: #ff8fab; }
        .icon-green  { background: linear-gradient(135deg, #43e97b33, #43e97b66); color: #43e97b; }
        .icon-orange { background: linear-gradient(135deg, #fa8b0c33, #fa8b0c66); color: #fa8b0c; }
        .icon-cyan   { background: linear-gradient(135deg, #0dd3bb33, #0dd3bb66); color: #0dd3bb; }
        .icon-red    { background: linear-gradient(135deg, #ff416c33, #ff416c66); color: #ff8fab; }

        /* Hero visual */
        .hero-visual {
            position: relative;
            width: 320px;
            height: 220px;
            margin: 50px auto 0;
        }

        .hero-card-float {
            position: absolute;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 16px 20px;
            font-size: 0.85rem;
            font-weight: 600;
            animation: floatCard 4s ease-in-out infinite;
            white-space: nowrap;
        }

        .hero-card-float:nth-child(1) { top: 0; left: 0; animation-delay: 0s; }
        .hero-card-float:nth-child(2) { top: 50px; right: 0; animation-delay: -1.3s; }
        .hero-card-float:nth-child(3) { bottom: 0; left: 30px; animation-delay: -2.6s; }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-12px); }
        }

        /* How it works section */
        .steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 32px; position: relative; }
        .step { text-align: center; padding: 24px 16px; }
        .step-num {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            font-weight: 800;
            margin: 0 auto 16px;
            font-family: 'Space Mono', monospace;
            box-shadow: 0 4px 20px rgba(108,99,255,0.4);
        }
        .step h4 { font-size: 0.95rem; font-weight: 700; margin-bottom: 8px; }
        .step p  { font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; }

        /* Testimonial marquee area */
        .notice-ticker {
            overflow: hidden;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            padding: 12px 24px;
            margin: 0 auto 40px;
            max-width: 700px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notice-ticker .tag {
            background: var(--primary);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            flex-shrink: 0;
        }

        .ticker-text {
            overflow: hidden;
            white-space: nowrap;
        }

        .ticker-inner {
            display: inline-block;
            animation: ticker 20s linear infinite;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        @keyframes ticker {
            0%   { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-wrapper">

    <!-- ══════════ HERO ══════════ -->
    <section class="hero">
        <div class="hero-badge">🎓 BCA Project 2026 — Adarsh Ray</div>

        <h1>
            Manage Your<br>
            <span class="gradient-text" id="typing-target">Academic Life</span>
        </h1>

        <p>
            A powerful student portal for registration, assignment uploads,
            notes downloads, feedback, and more — all in one place.
        </p>

        <!-- Notice ticker -->
        <div class="notice-ticker">
            <span class="tag">📢 Notice</span>
            <div class="ticker-text">
                <span class="ticker-inner">
                    Welcome to Smart Student Portal! &nbsp;•&nbsp;
                    Submit assignments before the deadline &nbsp;•&nbsp;
                    New notes uploaded for Sem 3 &nbsp;•&nbsp;
                    Feedback portal is now open &nbsp;•&nbsp;
                    Register today and get started!
                </span>
            </div>
        </div>

        <div class="hero-buttons">
            <?php if ($isLoggedIn): ?>
                <a href="dashboard.php" class="btn btn-primary">🚀 Go to Dashboard</a>
                <a href="upload.php"    class="btn btn-secondary">📤 Upload Assignment</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary">✨ Register Now</a>
                <a href="login.php"    class="btn btn-secondary">🔑 Login</a>
            <?php endif; ?>
        </div>

        <!-- Visit counter -->
        <div style="margin-top: 28px;">
            <div class="visits-badge">
                👁️ Your visit count: <strong><?= $visits ?></strong>
                &nbsp;|&nbsp; 🌐 Total portal visits: <strong>1,247</strong>
            </div>
        </div>

        <!-- Floating hero cards -->
        <div class="hero-visual">
            <div class="hero-card-float">✅ Assignment Submitted</div>
            <div class="hero-card-float">📥 Notes Downloaded</div>
            <div class="hero-card-float">🔐 Secure Login Active</div>
        </div>
    </section>

    <!-- ══════════ STATS BAR ══════════ -->
    <div class="stats-bar">
        <div class="stat-item">
            <div class="num counter" data-target="500">0</div>
            <div class="desc">Students Registered</div>
        </div>
        <div class="stat-item">
            <div class="num counter" data-target="1200">0</div>
            <div class="desc">Assignments Uploaded</div>
        </div>
        <div class="stat-item">
            <div class="num counter" data-target="350">0</div>
            <div class="desc">Notes Downloaded</div>
        </div>
        <div class="stat-item">
            <div class="num counter" data-target="98">0</div>
            <div class="desc">% Satisfaction</div>
        </div>
    </div>

    <!-- ══════════ FEATURES ══════════ -->
    <section class="section">
        <div class="section-header">
            <span class="section-label">Features</span>
            <h2>Everything You Need</h2>
            <p>Powerful tools built for students to manage their academic journey efficiently.</p>
        </div>

        <div class="features-grid">
            <div class="card reveal">
                <div class="card-icon icon-purple">🔐</div>
                <h3>Secure Login</h3>
                <p>Session-based authentication keeps your account safe. Password protection with PHP sessions and prepared statements.</p>
            </div>
            <div class="card reveal">
                <div class="card-icon icon-orange">📤</div>
                <h3>Assignment Upload</h3>
                <p>Upload your assignments easily. Only PDF and DOC files allowed with size restrictions for security.</p>
            </div>
            <div class="card reveal">
                <div class="card-icon icon-cyan">📥</div>
                <h3>Notes Download</h3>
                <p>Download study notes and materials shared by faculty directly from the server.</p>
            </div>
            <div class="card reveal">
                <div class="card-icon icon-pink">💬</div>
                <h3>Feedback System</h3>
                <p>Submit your feedback and suggestions. All responses are stored securely in the database.</p>
            </div>
            <div class="card reveal">
                <div class="card-icon icon-green">📊</div>
                <h3>Student CRUD</h3>
                <p>Full database operations — Add, View, Update, and Delete student records with ease.</p>
            </div>
            <div class="card reveal">
                <div class="card-icon icon-red">🍪</div>
                <h3>Visit Counter</h3>
                <p>Cookie-powered visit tracking remembers how many times you've visited the portal.</p>
            </div>
        </div>
    </section>

    <!-- ══════════ HOW IT WORKS ══════════ -->
    <section class="section" style="padding-top: 0;">
        <div class="section-header">
            <span class="section-label">How It Works</span>
            <h2>Get Started in Minutes</h2>
        </div>

        <div class="steps">
            <div class="step reveal">
                <div class="step-num">01</div>
                <h4>Create Account</h4>
                <p>Register with your name, course, and create a secure password.</p>
            </div>
            <div class="step reveal">
                <div class="step-num">02</div>
                <h4>Login Securely</h4>
                <p>Access the portal with your credentials. Sessions keep you logged in.</p>
            </div>
            <div class="step reveal">
                <div class="step-num">03</div>
                <h4>Manage Files</h4>
                <p>Upload assignments and download notes from the dashboard.</p>
            </div>
            <div class="step reveal">
                <div class="step-num">04</div>
                <h4>Give Feedback</h4>
                <p>Share your experience and help improve the system.</p>
            </div>
        </div>
    </section>

    <!-- ══════════ CTA ══════════ -->
    <section class="section" style="padding-top: 0; text-align: center;">
        <div class="card" style="max-width: 600px; margin: 0 auto; padding: 50px 40px;">
            <div style="font-size: 3rem; margin-bottom: 16px;">🚀</div>
            <h2 style="font-size: 2rem; margin-bottom: 12px; letter-spacing: -1px;">
                Ready to get started?
            </h2>
            <p style="color: var(--text-muted); margin-bottom: 28px;">
                Join hundreds of students already using Smart Student Portal.
            </p>
            <div style="display:flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <a href="register.php" class="btn btn-primary">Create Free Account</a>
                <a href="students.php" class="btn btn-secondary">View Students</a>
            </div>
        </div>
    </section>

</div><!-- end page-wrapper -->

<?php include 'includes/footer.php'; ?>

<script>
// Typing effect
const words = ['Academic Life', 'Assignments', 'Study Notes', 'The Future'];
let wIndex = 0, cIndex = 0, deleting = false;
const el = document.getElementById('typing-target');

function type() {
    const word = words[wIndex];
    el.textContent = deleting ? word.slice(0, cIndex--) : word.slice(0, ++cIndex);

    if (!deleting && cIndex === word.length) {
        setTimeout(() => deleting = true, 1800);
    } else if (deleting && cIndex === 0) {
        deleting = false;
        wIndex = (wIndex + 1) % words.length;
    }
    setTimeout(type, deleting ? 60 : 100);
}
type();
</script>
</body>
</html>
