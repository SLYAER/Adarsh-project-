<?php
// =============================================
//  register.php — Student Registration
// =============================================

require_once 'config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $course    = trim($_POST['course']    ?? '');
    $password  = $_POST['password']  ?? '';
    $confirm   = $_POST['confirm']   ?? '';

    if (!$full_name || !$username || !$password) {
        $error = 'Please fill in all required fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Username already taken. Please choose another.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare(
                "INSERT INTO users (full_name, username, email, course, password) VALUES (?,?,?,?,?)"
            );

            if (!$ins) {
                die("Prepare failed: " . $conn->error);
            }

                $ins->bind_param("sssss", $full_name, $username, $email, $course, $hashed);

            if ($ins->execute()) {
            $success = 'Registration Successful! <a href="login.php">Click here to login</a>';
            } else {
                $error = 'Registration failed: ' . $ins->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Smart Student Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Password strength indicator */
        .strength-bar {
            height: 4px;
            border-radius: 4px;
            background: rgba(255,255,255,0.1);
            margin-top: 8px;
            overflow: hidden;
        }
        .strength-fill {
            height: 100%;
            border-radius: 4px;
            width: 0%;
            transition: all 0.4s ease;
        }
        .strength-label {
            font-size: 0.72rem;
            margin-top: 4px;
            color: var(--text-muted);
        }
        /* Progress dots */
        .progress-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 28px;
        }
        .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--glass-border);
            transition: all 0.3s ease;
        }
        .dot.active { background: var(--primary); transform: scale(1.3); }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-wrapper">
    <div class="form-container" style="max-width:520px;">
        <div class="form-box fade-up">
            <div class="form-header">
                <div class="icon">✨</div>
                <h2>Create Account</h2>
                <p>Join Smart Student Portal today</p>
            </div>

            <!-- Progress dots -->
            <div class="progress-dots">
                <div class="dot active" id="d1"></div>
                <div class="dot" id="d2"></div>
                <div class="dot" id="d3"></div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">🎉 <?= $success ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off" id="regForm">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" placeholder="Adarsh Ray"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" placeholder="adarsh_ray"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required
                           pattern="[a-zA-Z0-9_]+" title="Letters, numbers, and underscores only">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@example.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Course</label>
                    <select name="course">
                        <option value="" disabled selected>Select your course</option>
                        <option value="BCA" <?= ($_POST['course'] ?? '') === 'BCA' ? 'selected' : '' ?>>BCA</option>
                        <option value="BBA" <?= ($_POST['course'] ?? '') === 'BBA' ? 'selected' : '' ?>>BBA</option>
                        <option value="BSc" <?= ($_POST['course'] ?? '') === 'BSc' ? 'selected' : '' ?>>BSc</option>
                        <option value="MCA" <?= ($_POST['course'] ?? '') === 'MCA' ? 'selected' : '' ?>>MCA</option>
                        <option value="MBA" <?= ($_POST['course'] ?? '') === 'MBA' ? 'selected' : '' ?>>MBA</option>
                        <option value="BCom"<?= ($_POST['course'] ?? '') === 'BCom'? 'selected' : '' ?>>BCom</option>
                        <option value="Other"<?= ($_POST['course'] ?? '') === 'Other'?'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" id="pw" placeholder="At least 6 characters" required>
                    <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                    <div class="strength-label" id="strengthLabel">Enter a password</div>
                </div>

                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm" id="cpw" placeholder="Repeat password" required>
                    <div class="strength-label" id="matchLabel" style="color:var(--text-muted);">—</div>
                </div>

                <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px;">
                    Create Account →
                </button>
            </form>

            <div class="divider"></div>
            <div class="form-footer">
                Already have an account? <a href="login.php">Log in here</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
const pw    = document.getElementById('pw');
const cpw   = document.getElementById('cpw');
const fill  = document.getElementById('strengthFill');
const label = document.getElementById('strengthLabel');
const match = document.getElementById('matchLabel');
const dots  = [document.getElementById('d1'), document.getElementById('d2'), document.getElementById('d3')];

function checkStrength(val) {
    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^a-zA-Z0-9]/.test(val)) score++;
    return score;
}

pw.addEventListener('input', () => {
    const s = checkStrength(pw.value);
    const pct  = Math.min(s * 25, 100);
    const colors = ['#ff416c','#fa8b0c','#ffd700','#43e97b','#43e97b'];
    const labels = ['Weak','Fair','Good','Strong','Very Strong'];
    fill.style.width  = pct + '%';
    fill.style.background = colors[Math.min(s-1, 4)] || '#ff416c';
    label.textContent = pw.value ? labels[Math.min(s-1, 4)] || 'Very Weak' : 'Enter a password';
    label.style.color = colors[Math.min(s-1, 4)] || 'var(--text-muted)';

    // Update progress dots
    dots.forEach((d, i) => d.classList.toggle('active', i <= Math.floor(s/2)));
});

cpw.addEventListener('input', () => {
    if (!cpw.value) { match.textContent = '—'; match.style.color = 'var(--text-muted)'; return; }
    if (cpw.value === pw.value) {
        match.textContent = '✅ Passwords match';
        match.style.color = '#43e97b';
    } else {
        match.textContent = '❌ Passwords do not match';
        match.style.color = '#ff8fab';
    }
});
</script>
</body>
</html>
