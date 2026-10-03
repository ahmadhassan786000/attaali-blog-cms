<?php /* expects $comments, $count */ ?>
<section class="comments" id="comments" aria-labelledby="h-comments">
  <div class="cm-head"><h2 id="h-comments"><i class="bi bi-chat-square-text"></i> Discussion (<?= (int)$count ?> Comments)</h2><span class="mono-label">Moderated Forum</span></div>
  <?php foreach ($comments as $c): ?>
  <article class="cm">
    <div class="cm-top"><span class="cm-av" style="background:<?= e($c['color']) ?>"><?= e($c['initials']) ?></span>
      <div><strong><?= e($c['name']) ?></strong><span class="mono-meta d-block"><?= e($c['role']) ?> &bull; <?= e($c['time']) ?></span></div>
      <button class="reply-btn mono-meta blue" type="button" data-name="<?= e($c['name']) ?>"><i class="bi bi-reply"></i> Reply</button></div>
    <p><?= e($c['text']) ?></p>
    <?php foreach ($c['replies'] as $r): ?>
    <div class="cm-reply"><div class="cm-top"><span class="cm-av sm"><?= e($r['initials']) ?></span>
      <div><strong><?= e($r['name']) ?></strong><?php if (!empty($r['author'])): ?> <span class="author-flag">Author</span><?php endif; ?><span class="mono-meta d-block"><?= e($r['time']) ?></span></div></div>
      <p><?= e($r['text']) ?></p></div>
    <?php endforeach; ?>
  </article>
  <?php endforeach; ?>

  <form class="cm-form" id="commentForm" method="post" action="#" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e($csrf_token ?? '') ?>">
    <input type="text" name="website" class="visually-hidden" tabindex="-1" autocomplete="off" aria-hidden="true"><!-- honeypot -->
    <h3 id="replyTitle">Leave a Comment</h3>
    <p class="cm-note">Your email address will not be published. Required fields are marked with an asterisk (*). All submissions undergo editorial moderation.</p>
    <div class="row g-3">
      <div class="col-md-6"><label for="cName">Full Name *</label><input id="cName" name="name" class="form-control" placeholder="e.g. Linus Torvalds" required minlength="2" maxlength="80"></div>
      <div class="col-md-6"><label for="cEmail">Work Email *</label><input id="cEmail" name="email" type="email" class="form-control" placeholder="name@company.com" required maxlength="120"></div>
      <div class="col-12"><label for="cText">Technical Insights or Questions *</label><textarea id="cText" name="comment" rows="4" class="form-control" placeholder="Join the peer discussion..." required minlength="5" maxlength="3000"></textarea></div>
      <div class="col-12 d-flex align-items-center gap-3 flex-wrap"><button class="btn-navy" type="submit">Post Comment</button><span class="mono-meta" id="cMsg" role="status"></span></div>
    </div>
  </form>
</section>
