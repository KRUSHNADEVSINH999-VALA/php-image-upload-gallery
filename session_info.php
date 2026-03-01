<?php
session_start();
if (!isset($_SESSION['uploaded_images'])) $_SESSION['uploaded_images'] = [];
if (!isset($_SESSION['upload_count']))    $_SESSION['upload_count']    = 0;
if (!isset($_SESSION['upload_log']))      $_SESSION['upload_log']      = [];

if (isset($_GET['clear_session'])) { session_destroy(); header('Location: session_info.php'); exit; }

$total = array_sum(array_column($_SESSION['uploaded_images'],'size'));
function hSzS(int $b): string {
    if ($b>=1048576) return round($b/1048576,2).' MB';
    if ($b>=1024)    return round($b/1024,1).' KB';
    return $b.' B';
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>PixelVault — Session</title>
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

<nav class="navbar">
  <div class="nav-inner">
    <a href="index.php" class="brand">
      <div class="brand-mark"></div>
      <span class="brand-name">Pixel<em>Vault</em></span>
    </a>
    <div class="nav-divider"></div>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Upload</a></li>
      <li><a href="gallery.php">Gallery</a></li>
      <li><a href="session_info.php" class="active">Session</a></li>
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

<header class="hero hero-sm">
  <div class="container">
    <div class="hero-eyebrow">Live Tracking</div>
    <h1 class="hero-title"><em>Session Overview</em></h1>
    <p class="hero-sub">Real-time upload statistics, activity log, and file registry for your current session.</p>
  </div>
</header>

<main class="container page-pad">

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-block" style="animation-delay:.05s">
      <div class="stat-block-label">Session ID</div>
      <div class="stat-block-id"><?= substr(session_id(),0,18) ?>…</div>
      <div class="stat-block-sub">PHP session token</div>
    </div>
    <div class="stat-block" style="animation-delay:.1s">
      <div class="stat-block-label">Total Uploads</div>
      <div class="stat-block-val"><?= $_SESSION['upload_count'] ?></div>
      <div class="stat-block-sub">this session</div>
    </div>
    <div class="stat-block" style="animation-delay:.15s">
      <div class="stat-block-label">Tracked Images</div>
      <div class="stat-block-val"><?= count($_SESSION['uploaded_images']) ?></div>
      <div class="stat-block-sub">in registry</div>
    </div>
    <div class="stat-block" style="animation-delay:.2s">
      <div class="stat-block-label">Data Uploaded</div>
      <div class="stat-block-val" style="font-size:1.5rem"><?= $total > 0 ? hSzS($total) : '—' ?></div>
      <div class="stat-block-sub">total size</div>
    </div>
  </div>

  <!-- Activity Log -->
  <div class="info-block">
    <div class="info-block-head">
      <h2 class="info-block-title">Activity Log</h2>
      <span class="badge badge-gold"><?= count($_SESSION['upload_log']) ?> events</span>
    </div>
    <?php if (empty($_SESSION['upload_log'])): ?>
      <p style="color:var(--ink-3);font-size:.85rem;padding:.5rem 0;font-weight:300;">No activity recorded. <a href="index.php">Upload an image</a> to begin.</p>
    <?php else: ?>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr><th>#</th><th>Time</th><th>File</th><th>Size</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php foreach (array_reverse($_SESSION['upload_log']) as $i => $log): ?>
          <tr>
            <td style="color:var(--ink-3);font-size:.75rem"><?= count($_SESSION['upload_log'])-$i ?></td>
            <td class="mono"><?= htmlspecialchars($log['time']) ?></td>
            <td><?= htmlspecialchars($log['file']) ?></td>
            <td><?= htmlspecialchars($log['size']) ?></td>
            <td><span class="badge badge-<?= $log['status']==='success'?'ok':'err' ?>">
              <?= $log['status']==='success' ? '✓ OK' : '✕ Failed' ?>
            </span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Registry -->
  <div class="info-block">
    <div class="info-block-head">
      <h2 class="info-block-title">Image Registry</h2>
      <span class="badge badge-sap"><?= count($_SESSION['uploaded_images']) ?> files</span>
    </div>
    <?php if (empty($_SESSION['uploaded_images'])): ?>
      <p style="color:var(--ink-3);font-size:.85rem;padding:.5rem 0;font-weight:300;">No images in registry yet.</p>
    <?php else: ?>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr><th>Original Name</th><th>Stored As</th><th>MIME Type</th><th>Dimensions</th><th>Size</th><th>Uploaded At</th></tr>
        </thead>
        <tbody>
          <?php foreach (array_reverse($_SESSION['uploaded_images']) as $img): ?>
          <tr>
            <td><?= htmlspecialchars($img['original_name']) ?></td>
            <td class="mono"><?= htmlspecialchars($img['filename']) ?></td>
            <td><span class="badge badge-gold"><?= htmlspecialchars($img['mime_type']??'—') ?></span></td>
            <td><?= $img['width'] ?>×<?= $img['height'] ?></td>
            <td><?= $img['size_human'] ?></td>
            <td><?= $img['uploaded_at'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Danger -->
  <div class="danger-zone">
    <div class="danger-title">Danger Zone</div>
    <p class="danger-desc">Destroys all session tracking data. Uploaded files on disk are <em>not</em> deleted.</p>
    <a href="session_info.php?clear_session=1"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Clear entire session? This cannot be undone.')">
      <span>Clear Session</span>
    </a>
  </div>

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

</div>
<script src="assets/js/app.js"></script>
</body>
</html>
