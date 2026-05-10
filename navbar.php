<?php
// =============================================
//  includes/navbar.php — Reusable Navigation
// =============================================
if (session_status() === PHP_SESSION_NONE) session_start();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$isLoggedIn  = isset($_SESSION['user_id']);
$username    = $_SESSION['username'] ?? '';
?>
<nav class="navbar" id="navbar">
    <a href="index.php" class="logo">
        <div class="logo-icon">🎓</div>
        Smart<span>Portal</span>
    </a>

    <ul class="nav-links">
        <li><a href="index.php"    class="<?= $currentPage === 'index'    ? 'active' : '' ?>">Home</a></li>
        <?php if ($isLoggedIn): ?>
            <li><a href="dashboard.php" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="upload.php"    class="<?= $currentPage === 'upload'    ? 'active' : '' ?>">Upload</a></li>
            <li><a href="download.php"  class="<?= $currentPage === 'download'  ? 'active' : '' ?>">Notes</a></li>
            <li><a href="feedback.php"  class="<?= $currentPage === 'feedback'  ? 'active' : '' ?>">Feedback</a></li>
            <li><a href="students.php"  class="<?= $currentPage === 'students'  ? 'active' : '' ?>">Students</a></li>
            <li>
                <a href="logout.php" class="btn-nav" style="background:linear-gradient(135deg,#ff416c,#ff4b2b);">
                    👋 Logout
                </a>
            </li>
        <?php else: ?>
            <li><a href="register.php" class="<?= $currentPage === 'register' ? 'active' : '' ?>">Register</a></li>
            <li><a href="login.php" class="btn-nav">Login →</a></li>
        <?php endif; ?>
    </ul>
</nav>
