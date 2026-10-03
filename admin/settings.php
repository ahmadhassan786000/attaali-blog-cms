<?php
require __DIR__ . '/_base.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $tab = isset($_POST['tab']) ? $_POST['tab'] : '';
    if ($tab === 'general') {
        foreach (array('site_name', 'tagline', 'meta_description', 'contact_email', 'site_url', 'posts_per_page') as $k) {
            set_setting($k, trim($_POST[$k]));
        }
        flash('success', 'General settings saved.');
    } elseif ($tab === 'ads') {
        set_setting('adsense_client', trim($_POST['adsense_client']));
        set_setting('ads_txt', $_POST['ads_txt']);
        foreach (array('ad_header', 'ad_sidebar', 'ad_in_article', 'ad_tool_top', 'ad_tool_bottom', 'ad_footer') as $k) {
            set_setting($k, $_POST[$k]);
        }
        flash('success', 'Ad settings saved.');
    } elseif ($tab === 'social') {
        foreach (array('facebook', 'twitter', 'youtube', 'instagram', 'whatsapp') as $k) {
            set_setting($k, trim($_POST[$k]));
        }
        flash('success', 'Social links saved.');
    } elseif ($tab === 'code') {
        set_setting('head_code', $_POST['head_code']);
        set_setting('footer_code', $_POST['footer_code']);
        flash('success', 'Custom code saved.');
    } elseif ($tab === 'password') {
        $cur = admin_user();
        $row = db_row('SELECT * FROM users WHERE id = ?', array($cur['id']));
        if (!password_verify($_POST['current_password'], $row['password'])) {
            flash('danger', 'Current password is incorrect.');
        } elseif (strlen($_POST['new_password']) < 8) {
            flash('danger', 'New password must be at least 8 characters.');
        } else {
            db_exec('UPDATE users SET password = ? WHERE id = ?', array(password_hash($_POST['new_password'], PASSWORD_DEFAULT), $cur['id']));
            flash('success', 'Password updated.');
        }
    }
    redirect('settings.php#' . $tab);
}

admin_header('Settings');
?>
<ul class="nav nav-tabs mb-3">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabGeneral">General</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAds">AdSense &amp; Ads</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabSocial">Social Links</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCode">Custom Code</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPassword">Password</button></li>
</ul>
<div class="tab-content">
  <div class="tab-pane fade show active" id="tabGeneral">
    <div class="card"><div class="card-body">
      <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="tab" value="general">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Site Name</label><input class="form-control" name="site_name" value="<?php echo e(setting('site_name')); ?>"></div>
          <div class="col-md-6"><label class="form-label">Tagline</label><input class="form-control" name="tagline" value="<?php echo e(setting('tagline')); ?>"></div>
          <div class="col-md-6"><label class="form-label">Contact Email</label><input class="form-control" type="email" name="contact_email" value="<?php echo e(setting('contact_email')); ?>"></div>
          <div class="col-md-6"><label class="form-label">Site URL (e.g. https://attaali.com)</label><input class="form-control" name="site_url" value="<?php echo e(setting('site_url')); ?>" placeholder="https://attaali.com"></div>
          <div class="col-md-4"><label class="form-label">Posts per page</label><input class="form-control" type="number" name="posts_per_page" value="<?php echo e(setting('posts_per_page', 9)); ?>" min="3" max="30"></div>
          <div class="col-12"><label class="form-label">Meta Description (used on homepage)</label><textarea class="form-control" name="meta_description" rows="2"><?php echo e(setting('meta_description')); ?></textarea></div>
        </div>
        <button class="btn btn-primary mt-3"><i class="bi bi-check2"></i> Save General Settings</button>
      </form>
    </div></div>
  </div>

  <div class="tab-pane fade" id="tabAds">
    <div class="card"><div class="card-body">
      <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="tab" value="ads">
        <label class="form-label">Google AdSense Publisher ID (e.g. ca-pub-1234567890123456)</label>
        <input class="form-control mb-3" name="adsense_client" value="<?php echo e(setting('adsense_client')); ?>" placeholder="ca-pub-XXXXXXXXXXXXXXXX">
        <label class="form-label">ads.txt content</label>
        <textarea class="form-control mb-3 mono" name="ads_txt" rows="2"><?php echo e(setting('ads_txt')); ?></textarea>
        <hr>
        <p class="text-muted small">Paste your AdSense ad unit HTML into any slot below. Leave blank to hide that ad slot.</p>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label small">Header ad</label><textarea class="form-control mono" name="ad_header" rows="3"><?php echo e(setting('ad_header')); ?></textarea></div>
          <div class="col-md-6"><label class="form-label small">Sidebar ad</label><textarea class="form-control mono" name="ad_sidebar" rows="3"><?php echo e(setting('ad_sidebar')); ?></textarea></div>
          <div class="col-md-6"><label class="form-label small">In-article ad</label><textarea class="form-control mono" name="ad_in_article" rows="3"><?php echo e(setting('ad_in_article')); ?></textarea></div>
          <div class="col-md-6"><label class="form-label small">Tool page - top</label><textarea class="form-control mono" name="ad_tool_top" rows="3"><?php echo e(setting('ad_tool_top')); ?></textarea></div>
          <div class="col-md-6"><label class="form-label small">Tool page - bottom</label><textarea class="form-control mono" name="ad_tool_bottom" rows="3"><?php echo e(setting('ad_tool_bottom')); ?></textarea></div>
          <div class="col-md-6"><label class="form-label small">Footer ad</label><textarea class="form-control mono" name="ad_footer" rows="3"><?php echo e(setting('ad_footer')); ?></textarea></div>
        </div>
        <button class="btn btn-primary mt-3"><i class="bi bi-check2"></i> Save Ad Settings</button>
      </form>
    </div></div>
  </div>

  <div class="tab-pane fade" id="tabSocial">
    <div class="card"><div class="card-body">
      <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="tab" value="social">
        <div class="row g-3">
          <?php foreach (array('facebook' => 'Facebook', 'twitter' => 'X / Twitter', 'youtube' => 'YouTube', 'instagram' => 'Instagram', 'whatsapp' => 'WhatsApp') as $k => $label): ?>
          <div class="col-md-6"><label class="form-label"><?php echo e($label); ?> URL</label><input class="form-control" name="<?php echo e($k); ?>" value="<?php echo e(setting($k)); ?>"></div>
          <?php endforeach; ?>
        </div>
        <button class="btn btn-primary mt-3"><i class="bi bi-check2"></i> Save Social Links</button>
      </form>
    </div></div>
  </div>

  <div class="tab-pane fade" id="tabCode">
    <div class="card"><div class="card-body">
      <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="tab" value="code">
        <label class="form-label">Custom code before &lt;/head&gt; (Google Analytics, Search Console verification, etc.)</label>
        <textarea class="form-control mono mb-3" name="head_code" rows="5"><?php echo e(setting('head_code')); ?></textarea>
        <label class="form-label">Custom code before &lt;/body&gt;</label>
        <textarea class="form-control mono mb-3" name="footer_code" rows="5"><?php echo e(setting('footer_code')); ?></textarea>
        <button class="btn btn-primary"><i class="bi bi-check2"></i> Save Custom Code</button>
      </form>
    </div></div>
  </div>

  <div class="tab-pane fade" id="tabPassword">
    <div class="card"><div class="card-body">
      <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="tab" value="password">
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label">Current Password</label><input class="form-control" type="password" name="current_password" required></div>
          <div class="col-md-4"><label class="form-label">New Password</label><input class="form-control" type="password" name="new_password" minlength="8" required></div>
        </div>
        <button class="btn btn-primary mt-3"><i class="bi bi-shield-lock"></i> Update Password</button>
      </form>
    </div></div>
  </div>
</div>
<script>
(function(){
  var h=location.hash;
  if(h){ var btn=document.querySelector('.nav-link[data-bs-target="'+h+'"]'); if(btn) new bootstrap.Tab(btn).show(); }
})();
</script>
<?php admin_footer(); ?>
