<?php
session_start();
if (!isset($_SESSION['uploaded_images'])) $_SESSION['uploaded_images'] = [];
if (!isset($_SESSION['upload_count']))    $_SESSION['upload_count']    = 0;
if (!isset($_SESSION['upload_log']))      $_SESSION['upload_log']      = [];

require_once 'includes/upload_handler.php';

$errors = []; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $r = handleUpload($_FILES['image']);
    if ($r['success']) $success = $r['message'];
    else               $errors  = $r['errors'];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>PixelVault — Upload</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=Outfit:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="orbs">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>
</div>

<div class="wrap">

<!-- NAV -->
<nav class="navbar">
  <div class="nav-inner">
    <a href="index.php" class="brand">
      <div class="brand-mark"></div>
      <span class="brand-name">Pixel<em>Vault</em></span>
    </a>
    <div class="nav-divider"></div>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php" class="active">Upload</a></li>
      <li><a href="gallery.php">Gallery</a></li>
      <li><a href="session_info.php">Session</a></li>
    </ul>
    <div class="nav-right">
      <div class="count-chip">
        <span>Uploads</span>
        <strong><?= $_SESSION['upload_count'] ?></strong>
      </div>
      <div class="nav-divider"></div>
      <button class="theme-btn" id="themeBtn" title="Toggle theme">☽</button>
      <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<!-- HERO -->
<header class="hero">
  <div class="container">
    <div class="hero-eyebrow">Secure · Verified · Instant</div>
    <h1 class="hero-title">
      <strong>Upload</strong> with<br>
      <em>confidence &amp; clarity</em>
    </h1>
    <p class="hero-sub">Drop your finest images. JPG, PNG, GIF, or WebP — up to 5 MB — validated server‑side, every time.</p>
  </div>
</header>

<!-- BODY -->
<main class="container page-pad">

  <?php if ($success): ?>
  <div class="alert alert-ok">
    <span class="alert-icon">✓</span>
    <div style="flex:1">
      <strong>Upload successful</strong>
      <p><?= htmlspecialchars($success) ?></p>
    </div>
    <div class="alert-actions">
      <a href="gallery.php" class="btn btn-ghost btn-sm"><span>View Gallery</span></a>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
  <div class="alert alert-err">
    <span class="alert-icon">✕</span>
    <div>
      <strong>Upload failed</strong>
      <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
  </div>
  <?php endif; ?>

  <div class="upload-wrap">
    <div class="upload-card">
      <form id="uploadForm" action="index.php" method="POST" enctype="multipart/form-data" novalidate>

        <!-- Drop zone -->
        <div class="drop-zone" id="dropZone">
          <div class="dz-inner" id="dzDefault">
            <div class="dz-icon">⬆</div>
            <p class="dz-title">Drop your image here</p>
            <p class="dz-sub">or select a file to upload</p>
            <label class="browse-btn">
              <span>◆ Browse Files</span>
              <input type="file" id="fileInput" name="image" accept="image/jpeg,image/png,image/gif,image/webp" hidden>
            </label>
          </div>

          <div class="preview-wrap" id="previewWrap">
            <img id="previewImg" src="" alt="Preview">
            <div class="preview-meta-pill" id="previewMeta"></div>
            <button type="button" class="clear-btn" id="clearBtn" title="Remove">✕</button>
          </div>
        </div>

        <!-- Rules -->
        <div class="rules-row">
          <div class="rule-item">
            <div class="rule-dot gold"></div>
            <div>
              <div class="rule-label">Allowed types</div>
              <div class="rule-value">JPG · PNG · GIF · WebP</div>
            </div>
          </div>
          <div class="rule-item">
            <div class="rule-dot rose"></div>
            <div>
              <div class="rule-label">Max file size</div>
              <div class="rule-value">5 MB per upload</div>
            </div>
          </div>
          <div class="rule-item">
            <div class="rule-dot sap"></div>
            <div>
              <div class="rule-label">Verification</div>
              <div class="rule-value">MIME checked server-side</div>
            </div>
          </div>
        </div>

        <!-- Progress -->
        <div class="progress-box" id="progressBox">
          <div class="progress-bar-track">
            <div class="progress-bar-fill"></div>
          </div>
          <div class="progress-label">Processing your image…</div>
        </div>

        <!-- Submit -->
        <button type="submit" class="submit-btn" id="submitBtn" disabled>
          <span class="lbl" id="submitLbl">Upload Image</span>
        </button>

      </form>
    </div>
  </div>

  <!-- Recent -->
  <?php if (!empty($_SESSION['uploaded_images'])): ?>
  <section class="recent-section">
    <div class="section-head">
      <div>
        <div class="section-label">This Session</div>
        <h2 class="section-title">Recently Uploaded</h2>
      </div>
      <a href="gallery.php" class="see-all-link">View all ›</a>
    </div>
    <div class="recent-grid">
      <?php foreach (array_slice(array_reverse($_SESSION['uploaded_images']),0,6) as $img): ?>
      <div class="recent-card" onclick="location.href='gallery.php'">
        <img src="uploads/<?= htmlspecialchars($img['filename']) ?>" alt="<?= htmlspecialchars($img['original_name']) ?>" loading="lazy">
        <div class="recent-card-info">
          <p><?= htmlspecialchars(mb_strimwidth($img['original_name'],0,22,'…')) ?></p>
          <small><?= $img['size_human'] ?></small>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

</main>

<footer class="site-footer">
  <div class="footer-row">
    <span class="footer-brand">Pixel<em>Vault</em></span>
    <nav class="footer-links">
      <a href="index.php">Upload</a>
      <a href="gallery.php">Gallery</a>
      <a href="session_info.php">Session</a>
    </nav>
    <span class="footer-copy">© <?= date('Y') ?> &nbsp;·&nbsp; PHP Image Gallery</span>
  </div>
</footer>

</div><!-- .wrap -->
<script src="assets/js/app.js"></script>
</body>
</html>
