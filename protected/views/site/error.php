<div class="container">
  <?php
  $isServerError = $code == 500;
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => $isServerError ? 'Maintenance' : 'Error ' . $code,
  ]);
  ?>
  <?php if ($isServerError): ?>
    <div class="error">
      <p>The site is momentarily under maintenance. Please, come back later</p>
      <div class="mt-10">
        <a href="/">
          Go to the home page
        </a>
      </div>
    </div>
  <?php else: ?>
    <div class="error">
      <?php echo CHtml::encode($message); ?>
      <div class="mt-10">
        <a href="/">
          Go to the home page
        </a>
      </div>
    </div>
  <?php endif; ?>
</div>