<?php
require __DIR__ . '/_base.php';

$editSlug = isset($_GET['slug']) ? $_GET['slug'] : '';
$existing = $editSlug ? db_row('SELECT * FROM custom_tools WHERE slug = ?', array($editSlug)) : null;
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        $errors[] = 'Security check failed, please try again.';
    } else {
        $name = trim($_POST['name']);
        $slugInput = trim($_POST['slug']);
        $icon = trim($_POST['icon']) ?: 'bi-tools';
        $category = trim($_POST['category']);
        $description = trim($_POST['description']);
        $about = trim($_POST['about']);
        $howToUse = trim($_POST['how_to_use']);
        $code = $_POST['code'];
        $libsChecked = isset($_POST['libs']) && is_array($_POST['libs']) ? $_POST['libs'] : array();
        $libsCustom = trim($_POST['libs_custom']);
        if ($libsCustom !== '') {
            foreach (explode(',', $libsCustom) as $l) {
                $l = trim($l);
                if ($l !== '') { $libsChecked[] = $l; }
            }
        }
        $libs = implode(',', array_unique($libsChecked));

        $faqQ = isset($_POST['faq_q']) ? $_POST['faq_q'] : array();
        $faqA = isset($_POST['faq_a']) ? $_POST['faq_a'] : array();
        $faqArr = array();
        foreach ($faqQ as $i => $q) {
            $q = trim($q);
            $a = isset($faqA[$i]) ? trim($faqA[$i]) : '';
            if ($q !== '' && $a !== '') { $faqArr[] = array($q, $a); }
        }
        $faqJson = json_encode($faqArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($name === '') { $errors[] = 'Tool name is required.'; }
        if (trim($code) === '') { $errors[] = 'Widget code cannot be empty.'; }
        if (!in_array($category, array_keys(tool_categories()), true)) { $errors[] = 'Please choose a valid category.'; }

        $slug = slugify($slugInput !== '' ? $slugInput : $name);
        if (!$existing || $existing['slug'] !== $slug) {
            $clash = db_row('SELECT slug FROM custom_tools WHERE slug = ?', array($slug))
                  ?: (is_file(SITE_ROOT . '/tool-ui/' . $slug . '.php') && !isset(tool_registry()[$slug]) ? array('slug' => $slug) : null);
            $i = 2;
            $base = $slug;
            while ($clash) {
                $slug = $base . '-' . $i;
                $clash = db_row('SELECT slug FROM custom_tools WHERE slug = ?', array($slug));
                $i++;
            }
        }
        if (isset(tool_registry()[$slug])) {
            $errors[] = 'This slug is already used by a built-in tool. Please choose a different name/slug.';
        }

        if (!$errors) {
            if (!is_dir(SITE_ROOT . '/tool-ui')) { @mkdir(SITE_ROOT . '/tool-ui', 0755, true); }
            $written = @file_put_contents(SITE_ROOT . '/tool-ui/' . $slug . '.php', $code);
            if ($written === false) {
                $errors[] = 'Could not write the tool file. Check that the tool-ui folder is writable.';
            } else {
                $now = date('Y-m-d H:i:s');
                if ($existing && $existing['slug'] !== $slug) {
                    @unlink(SITE_ROOT . '/tool-ui/' . $existing['slug'] . '.php');
                    db_exec('DELETE FROM custom_tools WHERE slug = ?', array($existing['slug']));
                    db_exec('DELETE FROM tools_status WHERE slug = ?', array($existing['slug']));
                }
                db_exec('INSERT INTO custom_tools (slug, name, icon, category, description, about, how_to_use, faq, libs, code, created_at, updated_at)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
                         ON DUPLICATE KEY UPDATE name=VALUES(name), icon=VALUES(icon), category=VALUES(category),
                             description=VALUES(description), about=VALUES(about), how_to_use=VALUES(how_to_use),
                             faq=VALUES(faq), libs=VALUES(libs), code=VALUES(code), updated_at=VALUES(updated_at)',
                    array($slug, $name, $icon, $category, $description, $about, $howToUse, $faqJson, $libs, $code, $now, $now));
                flash('success', 'Tool "' . $name . '" saved and is now live at /tools/' . $slug);
                redirect(admin_url('tools.php'));
            }
        }
    }
}

$v = function ($key, $default = '') use ($existing) {
    if (isset($_POST[$key])) { return $_POST[$key]; }
    return $existing && isset($existing[$key]) ? $existing[$key] : $default;
};
$commonLibs = array(
    'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js' => 'jsPDF (create PDFs)',
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js' => 'PDF.js (read/render PDFs)',
    'https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js' => 'PDF-lib (edit/merge PDFs)',
    'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js' => 'JSZip (zip downloads)',
    'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js' => 'QRCode.js',
    'https://cdn.jsdelivr.net/npm/tesseract.js@5.1.1/dist/tesseract.min.js' => 'Tesseract.js (OCR)',
);
$selectedLibs = $existing && $existing['libs'] ? array_map('trim', explode(',', $existing['libs'])) : array();
$faqRows = array();
if ($existing && $existing['faq']) {
    $d = json_decode($existing['faq'], true);
    if (is_array($d)) { $faqRows = $d; }
}
if (!$faqRows) { $faqRows = array(array('', '')); }

admin_header($existing ? 'Edit Tool: ' . $existing['name'] : 'Add New Tool');
?>
<?php foreach ($errors as $er): ?><div class="alert alert-danger py-2"><?php echo e($er); ?></div><?php endforeach; ?>
<div class="alert alert-info"><i class="bi bi-info-circle"></i> Paste the same kind of HTML + JavaScript you'd put in a <code>tool-ui/*.php</code> file below (see existing tools for examples: forms, drop-zones, buttons, <code>TK.*</code> helper functions are available). No manual file creation needed - saving here writes the file and registers the tool automatically.</div>
<form method="post">
  <?php echo csrf_field(); ?>
  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label fw-bold">Tool Name *</label><input class="form-control" name="name" value="<?php echo e($v('name')); ?>" required></div>
          <div class="col-md-4"><label class="form-label fw-bold">Icon (Bootstrap Icons class)</label><input class="form-control" name="icon" value="<?php echo e($v('icon', 'bi-tools')); ?>" placeholder="bi-magic"></div>
          <div class="col-md-6"><label class="form-label">URL slug (leave blank to auto-generate)</label><input class="form-control" name="slug" value="<?php echo e($existing ? $existing['slug'] : ''); ?>" placeholder="my-new-tool"></div>
          <div class="col-md-6"><label class="form-label">Category *</label>
            <select class="form-select" name="category" required>
              <?php foreach (tool_categories() as $cn => $cm): ?><option value="<?php echo e($cn); ?>" <?php echo $v('category') === $cn ? 'selected' : ''; ?>><?php echo e($cn); ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-12"><label class="form-label">Short description (shown on cards, 1 line)</label><input class="form-control" name="description" value="<?php echo e($v('description')); ?>" maxlength="150"></div>
          <div class="col-12"><label class="form-label">About this tool (longer paragraph shown on the tool page)</label><textarea class="form-control" name="about" rows="3"><?php echo e($v('about')); ?></textarea></div>
          <div class="col-12"><label class="form-label">How to use (one step per line)</label><textarea class="form-control" name="how_to_use" rows="4" placeholder="Upload your file.&#10;Adjust the settings.&#10;Click download."><?php echo e($v('how_to_use')); ?></textarea></div>
        </div>
      </div></div>

      <div class="card mb-3"><div class="card-body">
        <label class="form-label fw-bold">FAQ (optional)</label>
        <div id="faqRows">
          <?php foreach ($faqRows as $i => $fr): ?>
          <div class="row g-2 mb-2 faq-row">
            <div class="col-5"><input class="form-control form-control-sm" name="faq_q[]" placeholder="Question" value="<?php echo e($fr[0]); ?>"></div>
            <div class="col-6"><input class="form-control form-control-sm" name="faq_a[]" placeholder="Answer" value="<?php echo e(isset($fr[1]) ? $fr[1] : ''); ?>"></div>
            <div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger removeFaq"><i class="bi bi-x"></i></button></div>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" id="addFaq" class="btn btn-sm btn-outline-secondary mt-1"><i class="bi bi-plus"></i> Add FAQ row</button>
      </div></div>

      <div class="card"><div class="card-body">
        <label class="form-label fw-bold">Widget Code * (HTML + JavaScript, pasted as-is)</label>
        <textarea class="form-control mono" name="code" id="codeBox" rows="20" style="font-size:.85rem" required placeholder="<div>...your tool's HTML...</div>&#10;<script>...your tool's JavaScript, using TK.* helpers...</script>"><?php echo e($existing ? $existing['code'] : ''); ?></textarea>
      </div></div>
    </div>

    <div class="col-lg-5">
      <div class="card mb-3"><div class="card-body">
        <button class="btn btn-primary w-100 btn-lg"><i class="bi bi-check2-circle"></i> <?php echo $existing ? 'Update Tool' : 'Save &amp; Publish Tool'; ?></button>
        <?php if ($existing): ?><a class="btn btn-outline-secondary w-100 mt-2" href="<?php echo e(url('tools/' . $existing['slug'])); ?>" target="_blank"><i class="bi bi-eye"></i> View Live</a><?php endif; ?>
      </div></div>
      <div class="card"><div class="card-body">
        <label class="form-label fw-bold">Optional JS libraries to load</label>
        <p class="small text-muted">Only tick these if your pasted code needs them (e.g. jsPDF for PDF creation).</p>
        <?php foreach ($commonLibs as $url => $label): ?>
          <div class="form-check"><input class="form-check-input" type="checkbox" name="libs[]" value="<?php echo e($url); ?>" id="lib<?php echo md5($url); ?>" <?php echo in_array($url, $selectedLibs, true) ? 'checked' : ''; ?>>
          <label class="form-check-label small" for="lib<?php echo md5($url); ?>"><?php echo e($label); ?></label></div>
        <?php endforeach; ?>
        <label class="form-label small mt-2">Other library URL(s), comma separated</label>
        <input class="form-control form-control-sm" name="libs_custom" placeholder="https://cdnjs.cloudflare.com/...">
      </div></div>
    </div>
  </div>
</form>
<script>
document.getElementById('addFaq').addEventListener('click', function(){
  var row = document.createElement('div');
  row.className = 'row g-2 mb-2 faq-row';
  row.innerHTML = '<div class="col-5"><input class="form-control form-control-sm" name="faq_q[]" placeholder="Question"></div><div class="col-6"><input class="form-control form-control-sm" name="faq_a[]" placeholder="Answer"></div><div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger removeFaq"><i class="bi bi-x"></i></button></div>';
  document.getElementById('faqRows').appendChild(row);
});
document.getElementById('faqRows').addEventListener('click', function(e){
  var b = e.target.closest('.removeFaq');
  if (b) { b.closest('.faq-row').remove(); }
});
</script>
<?php admin_footer(); ?>
