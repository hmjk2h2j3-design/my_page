<?php
/**
 * 공통 푸터.
 *   $pageJs — 추가로 불러올 js 파일명 배열 (예: ['portfolio.js'])
 */

$site   = site();
$pageJs = $pageJs ?? [];
$links  = array_filter($site['links'] ?? []);
?>
</main>

<footer class="site-footer">
  <div class="container">

    <div class="footer-cta">
      <div>
        <span class="label">Contact</span>
        <p style="margin-top:14px">
          <a class="footer-cta__mail" href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a>
        </p>
      </div>
      <div class="footer-cta__side">
        <span class="footer-status">
          <span class="footer-status__dot" aria-hidden="true"></span>
          <?= e($site['available']) ?>
        </span>
        <br>
        <?= e($site['location']) ?>
      </div>
    </div>

    <div class="footer-meta">
      <p>&copy; <?= date('Y') ?> <?= e($site['name_ko']) ?>. 이 사이트는 직접 설계하고 만들었습니다.</p>

      <?php if ($links): ?>
        <nav class="footer-links" aria-label="외부 링크">
          <?php foreach ($links as $label => $href): ?>
            <a href="<?= e($href) ?>" target="_blank" rel="noopener noreferrer"><?= e($label) ?></a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>
    </div>

  </div>
</footer>

<script src="<?= e(asset('assets/js/common.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/motion.js')) ?>" defer></script>
<?php foreach ($pageJs as $js): ?>
<script src="<?= e(asset('assets/js/' . $js)) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
