<?php
// =============================================
//  upload.php — Assignment Upload
// =============================================
session_start();
require_once 'config.php';
requireLogin();
$user = getCurrentUser();

$error = $success = '';
$allowed_types = ['application/pdf', 'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$allowed_ext   = ['pdf', 'doc', 'docx'];
$max_size      = 5 * 1024 * 1024; // 5MB

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['assignment'])) {
    $file    = $_FILES['assignment'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $ftype   = mime_content_type($file['tmp_name']);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload error occurred. Please try again.';
    } elseif (!in_array($ext, $allowed_ext) || !in_array($ftype, $allowed_types)) {
        $error = 'Only PDF, DOC, and DOCX files are allowed.';
    } elseif ($file['size'] > $max_size) {
        $error = 'File too large. Maximum size is 5MB.';
    } else {
        $unique_name = uniqid('asgn_') . '_' . time() . '.' . $ext;
        $dest        = __DIR__ . '/uploads/' . $unique_name;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $stmt = $conn->prepare(
                "INSERT INTO uploads (user_id, filename, original_name, file_size) VALUES (?,?,?,?)"
            );
            $stmt->bind_param("issi", $user['id'], $unique_name, $file['name'], $file['size']);
            $stmt->execute();
            $success = 'Assignment "' . htmlspecialchars($file['name']) . '" uploaded successfully!';
        } else {
            $error = 'Failed to save the file. Check uploads/ folder permissions.';
        }
    }
}

// List user's previous uploads
$uploads = $conn->query(
    "SELECT original_name, file_size, uploaded_at FROM uploads WHERE user_id = " . (int)$user['id'] .
    " ORDER BY uploaded_at DESC LIMIT 10"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Assignment — Smart Student Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .upload-wrap { max-width: 700px; margin: 0 auto; padding: 30px 24px; }
        .file-preview {
            background: rgba(108,99,255,0.08);
            border: 1px solid rgba(108,99,255,0.25);
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            display: none;
        }
        .file-preview .fname { font-size: 0.9rem; font-weight: 600; }
        .file-preview .fsize { font-size: 0.75rem; color: var(--text-muted); }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-wrapper">
    <div class="page-title">
        <h1>📤 Upload <span class="gradient-text" style="background:linear-gradient(135deg,#fa8b0c,#ff6584);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Assignment</span></h1>
        <p>Allowed formats: PDF, DOC, DOCX &nbsp;·&nbsp; Max size: 5MB</p>
    </div>

    <div class="upload-wrap">
        <?php if ($error): ?>
            <div class="alert alert-error fade-up">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success fade-up">🎉 <?= $success ?></div>
        <?php endif; ?>

        <!-- Upload Card -->
        <div class="card fade-up" style="margin-bottom: 28px;">
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <!-- Drag & Drop Zone -->
                <div class="upload-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                    <span class="upload-icon">📂</span>
                    <p><strong>Drag & drop your file here</strong> or click to browse</p>
                    <p class="allowed">Accepted: .pdf, .doc, .docx &nbsp;|&nbsp; Max 5MB</p>
                </div>

                <input type="file" name="assignment" id="fileInput"
                       accept=".pdf,.doc,.docx" style="display:none;">

                <!-- File Preview -->
                <div class="file-preview" id="filePreview">
                    <span style="font-size:1.6rem;">📄</span>
                    <div>
                        <div class="fname" id="previewName">—</div>
                        <div class="fsize" id="previewSize">—</div>
                    </div>
                    <span style="margin-left:auto;cursor:pointer;opacity:0.5;" id="clearFile">✕</span>
                </div>

                <button type="submit" class="btn btn-primary btn-full" id="submitBtn" disabled>
                    📤 Upload Assignment
                </button>
            </form>
        </div>

        <!-- Previous Uploads -->
        <div class="card fade-up" style="animation-delay:0.15s;">
            <h3 style="margin-bottom: 16px;">📋 My Previous Uploads</h3>
            <?php if ($uploads && $uploads->num_rows > 0): ?>
                <ul class="file-list">
                    <?php while ($up = $uploads->fetch_assoc()): ?>
                        <li class="file-item">
                            <div class="file-icon bg-orange">📄</div>
                            <div class="file-info">
                                <div class="file-name"><?= htmlspecialchars($up['original_name']) ?></div>
                                <div class="file-meta">
                                    <?= round($up['file_size']/1024, 1) ?> KB &nbsp;·&nbsp;
                                    Uploaded <?= date('d M Y, g:i A', strtotime($up['uploaded_at'])) ?>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="alert alert-info">You haven't uploaded any assignments yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const preview   = document.getElementById('filePreview');
const fname     = document.getElementById('previewName');
const fsize     = document.getElementById('previewSize');
const submitBtn = document.getElementById('submitBtn');
const clearBtn  = document.getElementById('clearFile');

function formatSize(bytes) {
    return bytes < 1024 ? bytes + ' B'
         : bytes < 1024*1024 ? (bytes/1024).toFixed(1) + ' KB'
         : (bytes/1024/1024).toFixed(2) + ' MB';
}

function showPreview(file) {
    fname.textContent = file.name;
    fsize.textContent = formatSize(file.size);
    preview.style.display = 'flex';
    submitBtn.disabled = false;
    dropZone.style.borderColor = 'var(--primary)';
    dropZone.style.background  = 'rgba(108,99,255,0.06)';
}

fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) showPreview(fileInput.files[0]);
});

// Drag & Drop
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        showPreview(file);
    }
});

clearBtn.addEventListener('click', e => {
    e.stopPropagation();
    fileInput.value = '';
    preview.style.display = 'none';
    submitBtn.disabled = true;
    dropZone.style.borderColor = '';
    dropZone.style.background  = '';
});
</script>
</body>
</html>
