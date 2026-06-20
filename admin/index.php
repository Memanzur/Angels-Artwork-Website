<?php
session_start();

$PASSWORD = 'angelsart2026';
$SITE_JSON = __DIR__ . '/../data/site.json';
$ART_JSON  = __DIR__ . '/../data/artworks.json';

// Handle login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $PASSWORD) {
        $_SESSION['admin'] = true;
    } else {
        $login_error = 'Wrong password. Try again.';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /admin/');
    exit;
}

// Check auth
if (empty($_SESSION['admin'])) {
    show_login($login_error ?? null);
    exit;
}

// Handle saves
$save_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['login'])) {
    if (isset($_POST['save_site'])) {
        $site = json_decode(file_get_contents($SITE_JSON), true);
        $site['hero_whisper']  = $_POST['hero_whisper'];
        $site['hero_title']    = $_POST['hero_title'];
        $site['hero_subtitle'] = $_POST['hero_subtitle'];
        $site['shop_url']      = $_POST['shop_url'];
        $site['featured']['title']        = $_POST['feat_title'];
        $site['featured']['description']  = $_POST['feat_desc'];
        $site['featured']['purchase_url'] = $_POST['feat_url'];
        $site['about']['name']  = $_POST['about_name'];
        $site['about']['bio_1'] = $_POST['about_bio1'];
        $site['about']['bio_2'] = $_POST['about_bio2'];
        $site['contact']['address']      = $_POST['contact_address'];
        $site['contact']['hours']        = $_POST['contact_hours'];
        $site['contact']['facebook_url'] = $_POST['contact_facebook'];
        $site['contact']['email']        = $_POST['contact_email'];
        file_put_contents($SITE_JSON, json_encode($site, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $save_msg = 'Site info saved!';
    }
    if (isset($_POST['save_art'])) {
        $art = json_decode(file_get_contents($ART_JSON), true);
        foreach ($art['pieces'] as $i => &$piece) {
            if (isset($_POST['art_title_' . $i])) {
                $piece['title']       = $_POST['art_title_' . $i];
                $piece['description'] = $_POST['art_desc_' . $i];
                $piece['category']    = $_POST['art_cat_' . $i];
            }
        }
        unset($piece);
        file_put_contents($ART_JSON, json_encode($art, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $save_msg = 'Artwork saved!';
    }
}

// Load data
$site = json_decode(file_get_contents($SITE_JSON), true);
$art  = json_decode(file_get_contents($ART_JSON), true);

show_admin($site, $art, $save_msg);

// ── Login Page ──────────────────────────────────────────────
function show_login($error) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Angel's Artwork</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #FDF8F4; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-box { background: #fff; padding: 48px 40px; border-radius: 16px; box-shadow: 0 8px 40px rgba(107,79,135,0.1); text-align: center; max-width: 380px; width: 100%; }
    .login-box h1 { font-family: Georgia, serif; color: #6B4F87; font-size: 1.6rem; font-weight: 400; margin-bottom: 8px; }
    .login-box p { color: #7B6F8A; font-size: 0.9rem; margin-bottom: 28px; }
    .login-box input[type="password"] { width: 100%; padding: 14px 18px; border: 1.5px solid #E8DFF0; border-radius: 10px; font-size: 1rem; outline: none; margin-bottom: 16px; }
    .login-box input[type="password"]:focus { border-color: #6B4F87; }
    .login-box button { width: 100%; padding: 14px; background: #6B4F87; color: #fff; border: none; border-radius: 10px; font-size: 0.9rem; cursor: pointer; letter-spacing: 0.05em; }
    .login-box button:hover { background: #D4AF6E; }
    .error { color: #c0392b; font-size: 0.85rem; margin-bottom: 12px; }
  </style>
</head>
<body>
  <div class="login-box">
    <h1>Angel's Artwork</h1>
    <p>Admin Panel</p>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
      <input type="password" name="password" placeholder="Enter password" required autofocus>
      <button type="submit" name="login" value="1">Sign In</button>
    </form>
  </div>
</body>
</html>
<?php
}

// ── Admin Page ──────────────────────────────────────────────
function show_admin($site, $art, $save_msg) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Angel's Artwork</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #FDF8F4; color: #4A3F55; line-height: 1.6; }
    .topbar { background: #6B4F87; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10; }
    .topbar h1 { font-family: Georgia, serif; font-weight: 400; font-size: 1.2rem; }
    .topbar a { color: #E8DFF0; font-size: 0.85rem; text-decoration: none; }
    .topbar a:hover { color: #D4AF6E; }
    .wrap { max-width: 800px; margin: 0 auto; padding: 32px 24px 80px; }
    .saved { background: #d4edda; color: #155724; padding: 14px 20px; border-radius: 10px; margin-bottom: 24px; font-size: 0.95rem; }
    .tabs { display: flex; gap: 8px; margin-bottom: 28px; flex-wrap: wrap; }
    .tab { padding: 10px 24px; border-radius: 50px; border: 1.5px solid #E8DFF0; background: #fff; color: #6B4F87; cursor: pointer; font-size: 0.85rem; font-weight: 500; }
    .tab.active { background: #6B4F87; color: #fff; border-color: #6B4F87; }
    .panel { display: none; }
    .panel.active { display: block; }
    .section { background: #fff; border-radius: 14px; padding: 28px; margin-bottom: 20px; box-shadow: 0 2px 16px rgba(107,79,135,0.06); }
    .section h2 { font-family: Georgia, serif; color: #6B4F87; font-weight: 400; font-size: 1.3rem; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #F5F0F8; }
    .field { margin-bottom: 18px; }
    .field label { display: block; font-size: 0.82rem; font-weight: 600; color: #7B6F8A; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
    .field input, .field textarea, .field select { width: 100%; padding: 12px 16px; border: 1.5px solid #E8DFF0; border-radius: 10px; font-size: 0.95rem; font-family: inherit; color: #4A3F55; outline: none; background: #FDFBFF; }
    .field input:focus, .field textarea:focus, .field select:focus { border-color: #6B4F87; box-shadow: 0 0 0 3px rgba(107,79,135,0.08); }
    .field textarea { resize: vertical; min-height: 80px; }
    .save-btn { background: #6B4F87; color: #fff; border: none; padding: 14px 40px; border-radius: 50px; font-size: 0.9rem; cursor: pointer; letter-spacing: 0.05em; margin-top: 8px; }
    .save-btn:hover { background: #D4AF6E; }
    .art-card { background: #FDFBFF; border: 1px solid #E8DFF0; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
    .art-card h3 { font-family: Georgia, serif; color: #6B4F87; font-weight: 400; font-size: 1.1rem; margin-bottom: 14px; }
    .art-num { display: inline-block; background: #6B4F87; color: #fff; width: 28px; height: 28px; border-radius: 50%; text-align: center; line-height: 28px; font-size: 0.8rem; margin-right: 8px; }
    .help { font-size: 0.82rem; color: #7B6F8A; margin-top: 4px; }
    @media (max-width: 600px) {
      .wrap { padding: 20px 16px 60px; }
      .section { padding: 20px 18px; }
    }
  </style>
</head>
<body>
  <div class="topbar">
    <h1>Angel's Artwork Admin</h1>
    <div>
      <a href="/" target="_blank" style="margin-right: 16px;">View Site</a>
      <a href="?logout=1">Log Out</a>
    </div>
  </div>

  <div class="wrap">
    <?php if ($save_msg): ?><div class="saved"><?= htmlspecialchars($save_msg) ?></div><?php endif; ?>

    <div class="tabs">
      <div class="tab active" onclick="switchTab('site')">Site Info</div>
      <div class="tab" onclick="switchTab('art')">Artwork</div>
    </div>

    <!-- ── Site Info Tab ── -->
    <div class="panel active" id="panel-site">
      <form method="POST">
        <div class="section">
          <h2>Homepage Hero</h2>
          <div class="field">
            <label>Tagline</label>
            <input type="text" name="hero_whisper" value="<?= htmlspecialchars($site['hero_whisper']) ?>">
          </div>
          <div class="field">
            <label>Title</label>
            <input type="text" name="hero_title" value="<?= htmlspecialchars($site['hero_title']) ?>">
          </div>
          <div class="field">
            <label>Subtitle</label>
            <input type="text" name="hero_subtitle" value="<?= htmlspecialchars($site['hero_subtitle']) ?>">
          </div>
        </div>

        <div class="section">
          <h2>Shop Link</h2>
          <div class="field">
            <label>Square Shop URL</label>
            <input type="url" name="shop_url" value="<?= htmlspecialchars($site['shop_url']) ?>">
          </div>
        </div>

        <div class="section">
          <h2>Featured Piece</h2>
          <div class="field">
            <label>Title</label>
            <input type="text" name="feat_title" value="<?= htmlspecialchars($site['featured']['title']) ?>">
          </div>
          <div class="field">
            <label>Description</label>
            <textarea name="feat_desc"><?= htmlspecialchars($site['featured']['description']) ?></textarea>
          </div>
          <div class="field">
            <label>Purchase Link</label>
            <input type="url" name="feat_url" value="<?= htmlspecialchars($site['featured']['purchase_url']) ?>">
          </div>
        </div>

        <div class="section">
          <h2>About the Artist</h2>
          <div class="field">
            <label>Name</label>
            <input type="text" name="about_name" value="<?= htmlspecialchars($site['about']['name']) ?>">
          </div>
          <div class="field">
            <label>Bio - Paragraph 1</label>
            <textarea name="about_bio1"><?= htmlspecialchars($site['about']['bio_1']) ?></textarea>
          </div>
          <div class="field">
            <label>Bio - Paragraph 2</label>
            <textarea name="about_bio2"><?= htmlspecialchars($site['about']['bio_2']) ?></textarea>
          </div>
        </div>

        <div class="section">
          <h2>Contact Info</h2>
          <div class="field">
            <label>Address</label>
            <input type="text" name="contact_address" value="<?= htmlspecialchars($site['contact']['address']) ?>">
          </div>
          <div class="field">
            <label>Hours</label>
            <input type="text" name="contact_hours" value="<?= htmlspecialchars($site['contact']['hours']) ?>">
          </div>
          <div class="field">
            <label>Facebook URL</label>
            <input type="url" name="contact_facebook" value="<?= htmlspecialchars($site['contact']['facebook_url']) ?>">
          </div>
          <div class="field">
            <label>Email</label>
            <input type="email" name="contact_email" value="<?= htmlspecialchars($site['contact']['email']) ?>">
          </div>
        </div>

        <button type="submit" name="save_site" value="1" class="save-btn">Save Site Info</button>
      </form>
    </div>

    <!-- ── Artwork Tab ── -->
    <div class="panel" id="panel-art">
      <form method="POST">
        <p class="help" style="margin-bottom: 20px;">Edit the title, description (include sizes here), and category for each artwork. Hit "Save All Artwork" at the bottom when you're done.</p>

        <?php foreach ($art['pieces'] as $i => $piece): ?>
        <div class="art-card">
          <h3><span class="art-num"><?= $i + 1 ?></span><?= htmlspecialchars($piece['title']) ?></h3>
          <div class="field">
            <label>Title</label>
            <input type="text" name="art_title_<?= $i ?>" value="<?= htmlspecialchars($piece['title']) ?>">
          </div>
          <div class="field">
            <label>Description (include sizes here)</label>
            <textarea name="art_desc_<?= $i ?>"><?= htmlspecialchars($piece['description']) ?></textarea>
            <p class="help">Example: "Girl reading scriptures, guarded by angels. Sizes: 5x7, 8x12, 12x18"</p>
          </div>
          <div class="field">
            <label>Category</label>
            <select name="art_cat_<?= $i ?>">
              <option value="angels" <?= $piece['category'] === 'angels' ? 'selected' : '' ?>>Ministry of Angels</option>
              <option value="spirit" <?= $piece['category'] === 'spirit' ? 'selected' : '' ?>>Spirit World</option>
              <option value="bookmarks" <?= $piece['category'] === 'bookmarks' ? 'selected' : '' ?>>Bookmarks</option>
            </select>
          </div>
        </div>
        <?php endforeach; ?>

        <button type="submit" name="save_art" value="1" class="save-btn">Save All Artwork</button>
      </form>
    </div>
  </div>

  <script>
    function switchTab(name) {
      document.querySelectorAll('.tab').forEach(function(t) { t.classList.remove('active'); });
      document.querySelectorAll('.panel').forEach(function(p) { p.classList.remove('active'); });
      document.getElementById('panel-' + name).classList.add('active');
      event.target.classList.add('active');
    }
  </script>
</body>
</html>
<?php
}
?>
