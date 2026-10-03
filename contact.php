<?php
require __DIR__ . '/includes/init.php';

$sent = false;
$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        $errors[] = 'Security check failed, please try again.';
    }
    $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $subject = trim(isset($_POST['subject']) ? $_POST['subject'] : '');
    $message = trim(isset($_POST['message']) ? $_POST['message'] : '');
    $hp = isset($_POST['website']) ? trim($_POST['website']) : '';

    if ($hp !== '') { $sent = true; } // silently drop bots
    elseif ($name === '' || $email === '' || $message === '') { $errors[] = 'Please fill in all required fields.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Please enter a valid e-mail address.'; }

    if (!$errors && !$sent) {
        db_exec('INSERT INTO messages (name, email, subject, message, ip, created_at) VALUES (?, ?, ?, ?, ?, ?)',
            array($name, $email, $subject, $message, $_SERVER['REMOTE_ADDR'], date('Y-m-d H:i:s')));
        $sent = true;
    }
}

seo('title', 'Contact Us');
seo('description', 'Get in touch with us for feedback, tool suggestions or support questions.');
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
  <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li><li class="breadcrumb-item active">Contact</li></ol></nav>
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="article">
        <h1 class="fw-bold mb-2">Contact Us</h1>
        <p class="text-muted mb-4">Have a question, found a bug, or want a new tool built? Send us a message.</p>
        <?php if ($sent): ?>
          <div class="alert alert-success">Thanks! Your message has been sent. We'll get back to you soon.</div>
        <?php else: ?>
          <?php foreach ($errors as $er): ?><div class="alert alert-danger py-2"><?php echo e($er); ?></div><?php endforeach; ?>
          <form method="post" novalidate>
            <?php echo csrf_field(); ?>
            <input type="text" name="website" value="" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Your Name *</label><input class="form-control" name="name" required></div>
              <div class="col-md-6"><label class="form-label">Your Email *</label><input class="form-control" type="email" name="email" required></div>
              <div class="col-12"><label class="form-label">Subject</label><input class="form-control" name="subject"></div>
              <div class="col-12"><label class="form-label">Message *</label><textarea class="form-control" name="message" rows="5" required></textarea></div>
              <div class="col-12"><button class="btn btn-primary btn-lg">Send Message <i class="bi bi-send"></i></button></div>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
