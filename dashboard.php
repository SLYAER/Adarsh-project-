<?php
// =============================================
//  dashboard.php — Student Dashboard
// =============================================
session_start();
require_once 'config.php';
requireLogin();
$user = getCurrentUser();

// Fetch counts
$total_students = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'] ?? 0;
$total_feedback = $conn->query("SELECT COUNT(*) as c FROM feedback")->fetch_assoc()['c'] ?? 0;
$total_uploads  = $conn->query("SELECT COUNT(*) as c FROM uploads WHERE user_id = " . (int)$user['id'])->fetch_assoc()['c'] ?? 0;

// Fetch recent feedback
$fb_result = $conn->query("SELECT name, subject, message, created_at FROM feedback ORDER BY created_at DESC LIMIT 3");

// Fetch notes list
$notes = [];
$notesDir = __DIR__ . '/notes/';
if (is_dir($notesDir)) {
    foreach (glob($notesDir . '*.*') as $file) {
        $notes[] = [
            'name' => basename($file),
            'size' => round(filesize($file) / 1024, 1) . ' KB',
            'date' => date('d M Y', filemtime($file)),
        ];
    }
}

// Recent uploads
$up_result = $conn->query(
    "SELECT original_name, file_size, uploaded_at FROM uploads WHERE user_id = " . (int)$user['id'] .
    " ORDER BY uploaded_at DESC LIMIT 4"
);

$greeting = date('H') < 12 ? 'Good morning' : (date('H') < 17 ? 'Good afternoon' : 'Good evening');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Smart Student Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .action-links {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 8px;
        }
        .action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-light);
            font-size: 0.85rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .action-btn .aicon {
            font-size: 1.3rem;
            width: 38px; height: 38px;
            background: var(--glass);
            border-radius: 10px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }
        .action-btn:hover {
            background: var(--glass-hover);
            border-color: var(--primary);
            transform: translateY(-2px);
            color: #fff;
        }
        .action-btn small {
            display: block;
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 400;
            margin-top: 1px;
        }
        /* Timeline for recent activity */
        .timeline-item {
            display: flex;
            gap: 14px;
            padding-bottom: 16px;
            position: relative;
        }
        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 19px; top: 40px;
            width: 2px;
            height: calc(100% - 24px);
            background: var(--glass-border);
        }
        .timeline-dot {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            font-size: 1rem;
        }
        .timeline-content .title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .timeline-content .meta {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        /* Progress bars */
        .prog-bar {
            height: 6px;
            background: rgba(255,255,255,0.1);
            border-radius: 6px;
            overflow: hidden;
            margin-top: 8px;
        }
        .prog-fill {
            height: 100%;
            border-radius: 6px;
            width: 0;
            transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .profile-avatar {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: grid;
            place-items: center;
            font-size: 1.6rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-wrapper">

    <!-- Header -->
    <div class="dashboard-header">
        <div style="display:flex; align-items:center; gap: 18px; flex-wrap: wrap;">
            <div class="profile-avatar">
                <?= mb_strtoupper(mb_substr($user['full_name'], 0, 1)) ?>
            </div>
            <div>
                <div class="welcome-title">
                    <?= $greeting ?>, <span><?= htmlspecialchars($user['full_name']) ?></span> 👋
                </div>
                <div class="dashboard-subtitle">
                    @<?= htmlspecialchars($user['username']) ?>
                    <?php if ($user['course']): ?>
                        &nbsp;·&nbsp; 🎓 <?= htmlspecialchars($user['course']) ?>
                    <?php endif; ?>
                    &nbsp;·&nbsp; 📅 <?= date('l, d F Y') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="dashboard-grid">
        <div class="stat-card reveal">
            <div class="stat-icon bg-purple">📤</div>
            <div class="stat-info">
                <div class="value"><?= $total_uploads ?></div>
                <div class="label">My Uploads</div>
            </div>
        </div>
        <div class="stat-card reveal">
            <div class="stat-icon bg-cyan">📥</div>
            <div class="stat-info">
                <div class="value"><?= count($notes) ?></div>
                <div class="label">Notes Available</div>
            </div>
        </div>
        <div class="stat-card reveal">
            <div class="stat-icon bg-green">👥</div>
            <div class="stat-info">
                <div class="value"><?= $total_students ?></div>
                <div class="label">Total Students</div>
            </div>
        </div>
        <div class="stat-card reveal">
            <div class="stat-icon bg-pink">💬</div>
            <div class="stat-info">
                <div class="value"><?= $total_feedback ?></div>
                <div class="label">Feedbacks</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-content">

        <!-- Quick Actions -->
        <div class="dash-card reveal">
            <div class="dash-card-header">
                <div class="icon bg-purple">⚡</div>
                <h3>Quick Actions</h3>
            </div>
            <div class="action-links">
                <a href="upload.php" class="action-btn">
                    <div class="aicon">📤</div>
                    <div>Upload<small>PDF / DOC files</small></div>
                </a>
                <a href="download.php" class="action-btn">
                    <div class="aicon">📥</div>
                    <div>Download<small>Study notes</small></div>
                </a>
                <a href="feedback.php" class="action-btn">
                    <div class="aicon">💬</div>
                    <div>Feedback<small>Share your thoughts</small></div>
                </a>
                <a href="students.php" class="action-btn">
                    <div class="aicon">👥</div>
                    <div>Students<small>View CRUD</small></div>
                </a>
            </div>
        </div>

        <!-- Academic Progress -->
        <div class="dash-card reveal">
            <div class="dash-card-header">
                <div class="icon bg-green">📊</div>
                <h3>Academic Progress</h3>
            </div>

            <div style="margin-bottom: 18px;">
                <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
                    <span>Assignments Submitted</span>
                    <span style="color:var(--accent);"><?= $total_uploads ?>/10</span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" data-width="<?= min($total_uploads * 10, 100) ?>"
                         style="background: linear-gradient(90deg, #6c63ff, #43e97b);"></div>
                </div>
            </div>

            <div style="margin-bottom: 18px;">
                <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
                    <span>Notes Downloaded</span>
                    <span style="color:#0dd3bb;">7/<?= max(count($notes), 7) ?></span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" data-width="70"
                         style="background: linear-gradient(90deg, #0dd3bb, #6c63ff);"></div>
                </div>
            </div>

            <div>
                <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
                    <span>Profile Completion</span>
                    <span style="color:#fa8b0c;">
                        <?= ($user['course'] ? 80 : 60) ?>%
                    </span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" data-width="<?= $user['course'] ? 80 : 60 ?>"
                         style="background: linear-gradient(90deg, #fa8b0c, #ff6584);"></div>
                </div>
            </div>
        </div>

        <!-- Recent Feedback -->
        <div class="dash-card reveal">
            <div class="dash-card-header">
                <div class="icon bg-pink">💬</div>
                <h3>Recent Feedback</h3>
            </div>

            <?php if ($fb_result && $fb_result->num_rows > 0): ?>
                <div>
                    <?php while ($fb = $fb_result->fetch_assoc()): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot bg-pink">
                                <?= mb_strtoupper(mb_substr($fb['name'], 0, 1)) ?>
                            </div>
                            <div class="timeline-content">
                                <div class="title">
                                    <?= htmlspecialchars($fb['subject'] ?: 'General Feedback') ?>
                                    <span style="font-weight:400;color:var(--text-muted);">
                                        — <?= htmlspecialchars($fb['name']) ?>
                                    </span>
                                </div>
                                <div class="meta">
                                    <?= htmlspecialchars(mb_substr($fb['message'], 0, 60)) ?>…
                                </div>
                                <div class="meta" style="margin-top:2px;">
                                    🕐 <?= date('d M, g:i A', strtotime($fb['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No feedback yet. <a href="feedback.php" style="color:var(--primary);">Be the first!</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Uploads -->
        <div class="dash-card reveal">
            <div class="dash-card-header">
                <div class="icon bg-orange">📁</div>
                <h3>My Recent Uploads</h3>
            </div>

            <?php if ($up_result && $up_result->num_rows > 0): ?>
                <ul class="file-list">
                    <?php while ($up = $up_result->fetch_assoc()): ?>
                        <li class="file-item">
                            <div class="file-icon bg-orange">📄</div>
                            <div class="file-info">
                                <div class="file-name"><?= htmlspecialchars($up['original_name']) ?></div>
                                <div class="file-meta">
                                    <?= round($up['file_size']/1024, 1) ?> KB &nbsp;·&nbsp;
                                    <?= date('d M Y', strtotime($up['uploaded_at'])) ?>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="alert alert-info">
                    No uploads yet. <a href="upload.php" style="color:var(--primary);">Upload now</a>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- end dashboard-content -->
</div>

<?php include 'includes/footer.php'; ?>
<script>
// Animate progress bars on load
document.querySelectorAll('.prog-fill').forEach(bar => {
    setTimeout(() => {
        bar.style.width = bar.dataset.width + '%';
    }, 300);
});
</script>
</body>
</html>
