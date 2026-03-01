<?php
session_start();
if (!isset($_SESSION['uploaded_images'])) $_SESSION['uploaded_images'] = [];
if (!isset($_SESSION['upload_count']))    $_SESSION['upload_count']    = 0;

if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $del = basename($_GET['delete']);
    $fp  = __DIR__ . '/uploads/' . $del;
    if (file_exists($fp) && is_file($fp)) unlink($fp);
    $_SESSION['uploaded_images'] = array_values(
        array_filter($_SESSION['uploaded_images'], fn($i) => $i['filename'] !== $del)
    );
    header('Location: gallery.php?deleted=1'); exit;
}

$dir   = __DIR__ . '/uploads/';
$disk  = [];
if (is_dir($dir)) {
    foreach (glob($dir.'*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE) as $f)
        $disk[] = basename($f);
}

$sess  = array_column($_SESSION['uploaded_images'],'filename');
$items = $_SESSION['uploaded_images'];

foreach ($disk as $df) {
    if (!in_array($df, $sess, true)) {
        $p = $dir.$df; $s = filesize($p); $i = @getimagesize($p);
        $items[] = ['filename'=>$df,'original_name'=>$df,
            'size_human'=>hSz($s),'uploaded_at'=>date('Y-m-d H:i:s',filemtime($p)),
            'width'=>$i?$i[0]:'?','height'=>$i?$i[1]:'?'];
    }
}
$items = array_filter($items, fn($i) => in_array($i['filename'],$disk,true));
$items = array_values(array_reverse($items));

function hSz(int $b): string {
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
  <title>PixelVault — Gallery</title>
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
      <li><a href="gallery.php" class="active">Gallery</a></li>
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

<header class="hero hero-sm">
  <div class="container">
    <div class="hero-eyebrow">Your Collection</div>
    <h1 class="hero-title">
      <em>The Gallery</em>
    </h1>
    <p class="hero-sub"><?= count($items) ?> image<?= count($items)!==1?'s':'' ?> archived in your vault.</p>
  </div>
</header>

<main class="container page-pad">

  <?php if (isset($_GET['deleted'])): ?>
  <div class="alert alert-ok">
    <span class="alert-icon">✓</span>
    <div><strong>Image removed from vault.</strong></div>
  </div>
  <?php endif; ?>

  <?php if (empty($items)): ?>
  <div class="empty">
    <div class="empty-mark">◇</div>
    <h2>The vault is empty</h2>
    <p>Upload your first image and it will appear here.</p>
    <a href="index.php" class="btn btn-gold"><span>⬆ Upload Image</span></a>
  </div>

  <?php else: ?>

  <div class="g-toolbar">
    <div class="g-toolbar-left">
      <span class="g-count"><strong><?= count($items) ?></strong> images</span>
      <div class="view-btns">
        <button class="view-btn on" id="gridBtn" title="Grid view">⊞</button>
        <button class="view-btn"   id="listBtn" title="List view">☰</button>
      </div>
    </div>
    <a href="index.php" class="btn btn-gold btn-sm"><span>+ Upload</span></a>
  </div>

  <div class="g-grid" id="gGrid">
    <?php foreach ($items as $ix => $img): ?>
    <div class="g-card"
         data-idx="<?= $ix ?>"
         data-src="uploads/<?= htmlspecialchars($img['filename']) ?>"
         data-name="<?= htmlspecialchars($img['original_name']) ?>"
         data-info="<?= htmlspecialchars($img['size_human'].' · '.$img['width'].'×'.$img['height']) ?>"
         style="animation-delay:<?= min($ix*.05,.6) ?>s">
      <div class="g-img">
        <img src="uploads/<?= htmlspecialchars($img['filename']) ?>"
             alt="<?= htmlspecialchars($img['original_name']) ?>"
             loading="lazy">
        <div class="g-overlay">
          <button class="ov-btn lb-open" data-idx="<?= $ix ?>">View</button>
          <a class="ov-btn"
             href="uploads/<?= htmlspecialchars($img['filename']) ?>"
             download="<?= htmlspecialchars($img['original_name']) ?>">Save</a>
          <a class="ov-btn del"
             href="gallery.php?delete=<?= urlencode($img['filename']) ?>"
             onclick="return confirm('Remove this image permanently?')">Delete</a>
        </div>
      </div>
      <div class="g-meta">
        <p class="g-name"><?= htmlspecialchars(mb_strimwidth($img['original_name'],0,34,'…')) ?></p>
        <div class="g-tags">
          <span class="tag tag-gold"><?= $img['size_human'] ?></span>
          <span class="tag tag-blue"><?= $img['width'] ?>×<?= $img['height'] ?></span>
        </div>
        <time class="g-date"><?= date('d M Y · H:i', strtotime($img['uploaded_at'])) ?></time>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php endif; ?>
</main>

<!-- Lightbox -->
<div class="lb" id="lb">
  <button class="lb-x" id="lbClose">✕</button>
  <button class="lb-arrow lb-l" id="lbPrev">‹</button>
  <div class="lb-img-box">
    <img id="lbImg" src="" alt="">
  </div>
  <button class="lb-arrow lb-r" id="lbNext">›</button>
  <div class="lb-cap" id="lbCap"></div>
</div>

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
