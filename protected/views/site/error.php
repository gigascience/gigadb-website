<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Error' . $code,
    'breadcrumbItems' => [
      ['label' => 'Home', 'href' => '/'],
      ['isActive' => true, 'label' => 'Error']
    ]
  ]);
    ?>

  <div class="error">
    <?php echo CHtml::encode($message); ?>
    <div class="mt-10">
      <a href="/">
        Go to the home page
      </a>
    </div>
  </div>
</div>