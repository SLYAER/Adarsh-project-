<?php
// =============================================
//  students.php — Student CRUD Operations
// =============================================
session_start();
require_once 'config.php';
requireLogin();

$error = $success = '';
$editStudent = null;

// ── CREATE ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $name   = trim($_POST['name']   ?? '');
    $course = trim($_POST['course'] ?? '');
    $email  = trim($_POST['email']  ?? '');

    if ($_POST['action'] === 'add') {
        if (!$name) {
            $error = 'Student name is required.';
        } else {
            $stmt = $conn->prepare("INSERT INTO students (name, course, email) VALUES (?,?,?)");
            $stmt->bind_param("sss", $name, $course, $email);
            $success = $stmt->execute() ? "Student \"$name\" added!" : 'Failed to add student.';
            if (!$stmt->execute() && !$success) $error = 'Database error.';
        }
    }

    if ($_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE students SET name=?, course=?, email=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $course, $email, $id);
        $success = $stmt->execute() ? "Student updated successfully!" : 'Update failed.';
    }
}

// ── DELETE ──
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$id");
    $success = 'Student record deleted.';
}

// ── READ for EDIT ──
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM students WHERE id=$id");
    $editStudent = $res ? $res->fetch_assoc() : null;
}

// ── READ ALL ──
$search  = trim($_GET['q'] ?? '');
$baseSQL = "SELECT * FROM students";
if ($search) {
    $s = $conn->real_escape_string($search);
    $baseSQL .= " WHERE name LIKE '%$s%' OR course LIKE '%$s%' OR email LIKE '%$s%'";
}
$baseSQL .= " ORDER BY id DESC";
$students = $conn->query($baseSQL);
$total    = $students ? $students->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students — Smart Student Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .students-wrap { max-width: 1000px; margin: 0 auto; padding: 30px 24px 60px; }
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .search-input-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
        }
        .search-input-wrap input {
            width: 100%;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            padding: 10px 18px 10px 44px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            color: var(--text-light);
            outline: none;
            transition: var(--transition);
        }
        .search-input-wrap input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.15);
        }
        .search-input-wrap input::placeholder { color: rgba(255,255,255,0.3); }
        .search-input-wrap::before {
            content: '🔍';
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            font-size: 0.9rem;
        }
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(6px);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .modal-box {
            background: #1a1448;
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 36px;
            width: 100%;
            max-width: 440px;
            position: relative;
            animation: fadeInUp 0.3s ease;
        }
        .modal-close {
            position: absolute;
            top: 16px; right: 20px;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.2s;
            background: none;
            border: none;
            color: #fff;
        }
        .modal-close:hover { opacity: 1; }
        .delete-confirm {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .delete-box {
            background: #1a1448;
            border: 1px solid rgba(255,65,108,0.3);
            border-radius: 20px;
            padding: 32px;
            max-width: 380px;
            text-align: center;
            animation: fadeInUp 0.3s ease;
        }
        .action-icons {
            display: flex;
            gap: 8px;
        }
        .icon-btn {
            width: 34px; height: 34px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            font-size: 0.9rem;
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid var(--glass-border);
            background: var(--glass);
            cursor: pointer;
            color: var(--text-light);
        }
        .icon-btn:hover { transform: scale(1.1); }
        .icon-btn.edit-btn:hover { background: rgba(108,99,255,0.2); border-color: var(--primary); }
        .icon-btn.del-btn:hover { background: rgba(255,65,108,0.2); border-color: #ff416c; }
        .course-badge {
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 50px;
            background: rgba(108,99,255,0.15);
            color: #a89ec9;
        }
        .empty-state {
            text-align: center;
            padding: 60px 24px;
            color: var(--text-muted);
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 12px; }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-wrapper">
    <div class="page-title">
        <h1>👥 <span class="gradient-text" style="background:linear-gradient(135deg,#43e97b,#0dd3bb);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Students</span></h1>
        <p>Manage student records — Add, View, Edit, Delete</p>
    </div>

    <div class="students-wrap">
        <?php if ($error): ?>
            <div class="alert alert-error fade-up">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success fade-up">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Top Bar -->
        <div class="top-bar fade-up">
            <form method="GET" style="flex:1; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                <div class="search-input-wrap">
                    <input type="text" name="q" placeholder="Search by name, course, or email…"
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
                <?php if ($search): ?>
                    <a href="students.php" class="btn btn-secondary btn-sm">✕ Clear</a>
                <?php endif; ?>
            </form>
            <button class="btn btn-primary btn-sm" onclick="openModal()">+ Add Student</button>
        </div>

        <!-- Student Table -->
        <div class="card fade-up" style="padding: 0; overflow: visible;">
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--glass-border); display:flex; align-items:center; gap:10px;">
                <span style="font-weight:700;">📋 Student Records</span>
                <span style="margin-left:auto; font-size:0.8rem; color:var(--text-muted);">
                    <?= $total ?> record<?= $total !== 1 ? 's' : '' ?> found
                </span>
            </div>

            <?php if ($total > 0): ?>
                <div class="table-wrapper" style="border:none; border-radius:0 0 16px 16px;">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Email</th>
                                <th>Added On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; while ($s = $students->fetch_assoc()): ?>
                                <tr>
                                    <td style="color:var(--text-muted); font-size:0.8rem;"><?= $i++ ?></td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap: 10px;">
                                            <div style="
                                                width:34px;height:34px;border-radius:50%;
                                                background:linear-gradient(135deg,var(--primary),var(--secondary));
                                                display:grid;place-items:center;font-weight:700;font-size:0.85rem;
                                                flex-shrink:0;">
                                                <?= mb_strtoupper(mb_substr($s['name'],0,1)) ?>
                                            </div>
                                            <?= htmlspecialchars($s['name']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($s['course']): ?>
                                            <span class="course-badge"><?= htmlspecialchars($s['course']) ?></span>
                                        <?php else: ?>
                                            <span style="color:var(--text-muted);font-size:0.8rem;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size:0.85rem; color:var(--text-muted);">
                                        <?= htmlspecialchars($s['email'] ?: '—') ?>
                                    </td>
                                    <td style="font-size:0.8rem; color:var(--text-muted);">
                                        <?= date('d M Y', strtotime($s['created_at'])) ?>
                                    </td>
                                    <td>
                                        <div class="action-icons">
                                            <a href="students.php?edit=<?= $s['id'] ?>"
                                               class="icon-btn edit-btn" title="Edit">✏️</a>
                                            <button class="icon-btn del-btn" title="Delete"
                                                    onclick="confirmDelete(<?= $s['id'] ?>, '<?= htmlspecialchars($s['name'], ENT_QUOTES) ?>')">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">👥</div>
                    <p><?= $search ? 'No students match your search.' : 'No student records yet.' ?></p>
                    <?php if (!$search): ?>
                        <button class="btn btn-primary btn-sm" style="margin-top: 16px;" onclick="openModal()">
                            Add First Student
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── ADD Modal ── -->
<div class="modal-overlay" id="addModal" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal()">✕</button>
        <h3 style="margin-bottom:20px; font-size:1.2rem;">➕ Add New Student</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" required placeholder="Student name">
            </div>
            <div class="form-group">
                <label>Course</label>
                <select name="course">
                    <option value="">Select course</option>
                    <?php foreach (['BCA','BBA','BSc','MCA','MBA','BCom','Other'] as $c): ?>
                        <option value="<?= $c ?>"><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="student@example.com">
            </div>
            <div style="display:flex;gap:12px;margin-top:8px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Add Student</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- ── EDIT Modal ── -->
<?php if ($editStudent): ?>
<div class="modal-overlay" id="editModal" style="display:flex;">
    <div class="modal-box">
        <a href="students.php" class="modal-close">✕</a>
        <h3 style="margin-bottom:20px; font-size:1.2rem;">✏️ Edit Student</h3>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $editStudent['id'] ?>">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($editStudent['name']) ?>">
            </div>
            <div class="form-group">
                <label>Course</label>
                <select name="course">
                    <option value="">Select course</option>
                    <?php foreach (['BCA','BBA','BSc','MCA','MBA','BCom','Other'] as $c): ?>
                        <option value="<?= $c ?>" <?= $editStudent['course'] === $c ? 'selected' : '' ?>>
                            <?= $c ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($editStudent['email'] ?? '') ?>">
            </div>
            <div style="display:flex;gap:12px;margin-top:8px;">
                <button type="submit" class="btn btn-success" style="flex:1;">Update Student</button>
                <a href="students.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- ── DELETE Confirm ── -->
<div class="delete-confirm" id="deleteConfirm" style="display:none;">
    <div class="delete-box">
        <div style="font-size:2.5rem;margin-bottom:12px;">🗑️</div>
        <h3 style="margin-bottom:8px;">Delete Student?</h3>
        <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:24px;">
            Are you sure you want to delete <strong id="deleteName"></strong>?
            This action cannot be undone.
        </p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <a id="deleteLink" href="#" class="btn btn-danger btn-sm">Yes, Delete</a>
            <button class="btn btn-secondary btn-sm" onclick="document.getElementById('deleteConfirm').style.display='none'">Cancel</button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
function openModal() { document.getElementById('addModal').style.display = 'flex'; }
function closeModal() { document.getElementById('addModal').style.display = 'none'; }

function confirmDelete(id, name) {
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteLink').href = 'students.php?delete=' + id;
    document.getElementById('deleteConfirm').style.display = 'flex';
}

// Close on backdrop click
document.getElementById('addModal')?.addEventListener('click', (e) => {
    if (e.target === e.currentTarget) closeModal();
});
document.getElementById('deleteConfirm')?.addEventListener('click', (e) => {
    if (e.target === e.currentTarget) e.currentTarget.style.display = 'none';
});
</script>
</body>
</html>
