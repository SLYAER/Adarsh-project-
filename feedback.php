<?php
// =============================================
//  feedback.php — Feedback Submission
// =============================================
session_start();
require_once 'config.php';
requireLogin();
$user = getCurrentUser();

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $rating  = (int)($_POST['rating'] ?? 5);

    if (!$name || !$message) {
        $error = 'Please fill in your name and message.';
    } elseif (strlen($message) < 10) {
        $error = 'Message must be at least 10 characters.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO feedback (name, email, subject, message, rating) VALUES (?,?,?,?,?)"
        );
        $stmt->bind_param("ssssi", $name, $email, $subject, $message, $rating);
        if ($stmt->execute()) {
            $success = 'Thank you for your feedback! It has been submitted successfully.';
            $_SESSION['last_feedback'] = $message;
        } else {
            $error = 'Failed to submit feedback. Please try again.';
        }
    }
}

// Fetch all feedback (last 10)
$feedbacks = $conn->query(
    "SELECT name, subject, message, rating, created_at FROM feedback ORDER BY created_at DESC LIMIT 10"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback — Smart Student Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .feedback-wrap { max-width: 900px; margin: 0 auto; padding: 30px 24px 60px; }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; }
        @media (max-width:768px) { .two-col { grid-template-columns: 1fr; } }

        /* Star rating */
        .star-rating { display: flex; gap: 8px; margin-top: 4px; }
        .star-rating input { display: none; }
        .star-rating label {
            font-size: 2rem;
            cursor: pointer;
            opacity: 0.35;
            transition: opacity 0.2s, transform 0.2s;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label { opacity: 1; transform: scale(1.15); }
        .star-rating { flex-direction: row-reverse; justify-content: flex-end; }
        .star-rating label:hover, .star-rating label:hover ~ label { opacity: 1; }

        /* Feedback cards */
        .fb-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 14px;
            transition: var(--transition);
        }
        .fb-card:hover {
            border-color: rgba(255,255,255,0.25);
            transform: translateX(4px);
        }
        .fb-card .fb-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }
        .fb-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .fb-name { font-weight: 700; font-size: 0.9rem; }
        .fb-subject { font-size: 0.78rem; color: var(--text-muted); }
        .fb-date { font-size: 0.72rem; color: var(--text-muted); margin-left: auto; }
        .fb-msg { font-size: 0.88rem; color: var(--text-muted); line-height: 1.6; }
        .fb-stars { font-size: 0.85rem; margin-top: 8px; }
        .char-count { font-size: 0.75rem; color: var(--text-muted); text-align: right; margin-top: 4px; }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-wrapper">
    <div class="page-title">
        <h1>💬 <span class="gradient-text" style="background:linear-gradient(135deg,#ff6584,#6c63ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Feedback</span></h1>
        <p>Share your experience and help us improve the portal</p>
    </div>

    <div class="feedback-wrap">
        <div class="two-col">

            <!-- ── Form Column ── -->
            <div class="fade-up">
                <div class="card">
                    <h3 style="margin-bottom: 20px; font-size: 1.1rem;">✍️ Submit Feedback</h3>

                    <?php if ($error): ?>
                        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success">🎉 <?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form method="POST" id="fbForm">
                        <div class="form-group">
                            <label>Your Name *</label>
                            <input type="text" name="name" required
                                   value="<?= htmlspecialchars($_POST['name'] ?? $user['full_name']) ?>"
                                   placeholder="Full name">
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   placeholder="optional@email.com">
                        </div>

                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject"
                                   value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"
                                   placeholder="What's this about?">
                        </div>

                        <div class="form-group">
                            <label>Your Rating</label>
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" id="star<?= $i ?>"
                                           value="<?= $i ?>" <?= ($i === 5) ? 'checked' : '' ?>>
                                    <label for="star<?= $i ?>">⭐</label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Message *</label>
                            <textarea name="message" id="msgArea" rows="5" required
                                      placeholder="Share your thoughts, suggestions, or issues…"
                                      maxlength="500"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                            <div class="char-count"><span id="charLeft">500</span> characters remaining</div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">
                            Submit Feedback 🚀
                        </button>
                    </form>
                </div>
            </div>

            <!-- ── Recent Feedback Column ── -->
            <div class="fade-up" style="animation-delay: 0.1s;">
                <h3 style="margin-bottom: 16px; font-size: 1.1rem; font-weight: 700;">
                    📋 Recent Feedback
                    <span style="font-size:0.75rem; color:var(--text-muted); font-weight:400; margin-left:8px;">
                        (Last 10)
                    </span>
                </h3>

                <?php if ($feedbacks && $feedbacks->num_rows > 0): ?>
                    <?php while ($fb = $feedbacks->fetch_assoc()): ?>
                        <div class="fb-card reveal">
                            <div class="fb-top">
                                <div class="fb-avatar">
                                    <?= mb_strtoupper(mb_substr($fb['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fb-name"><?= htmlspecialchars($fb['name']) ?></div>
                                    <div class="fb-subject">
                                        <?= htmlspecialchars($fb['subject'] ?: 'General Feedback') ?>
                                    </div>
                                </div>
                                <div class="fb-date">
                                    <?= date('d M', strtotime($fb['created_at'])) ?>
                                </div>
                            </div>
                            <div class="fb-msg">
                                <?= htmlspecialchars(mb_substr($fb['message'], 0, 140)) ?>
                                <?= strlen($fb['message']) > 140 ? '…' : '' ?>
                            </div>
                            <div class="fb-stars">
                                <?= str_repeat('⭐', min($fb['rating'], 5)) ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        No feedback yet. Be the first to share yours!
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- end two-col -->
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
const msgArea = document.getElementById('msgArea');
const charLeft = document.getElementById('charLeft');

msgArea.addEventListener('input', () => {
    const remaining = 500 - msgArea.value.length;
    charLeft.textContent = remaining;
    charLeft.style.color = remaining < 50 ? '#ff8fab' : remaining < 100 ? '#fa8b0c' : 'var(--text-muted)';
});
</script>
</body>
</html>
