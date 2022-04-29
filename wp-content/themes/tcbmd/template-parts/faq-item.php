<?php
	/**
	* Template Part: FAQ Item 
	*/
?>

<?php
  if (have_rows('faq_items')):
?>
<h3>Frequently Asked Questions</h3>
  <dl class="faq-container wrap">
    <?php
      while (have_rows('faq_items')) : the_row();
      $title = get_sub_field('title');
      $content = get_sub_field('content');
      $row_index = get_row_index();
    ?>
      <!-- <div class="faq-item"> -->
        <dt id="faq-<?php echo $row_index ?>" class="collapseomatic noarrow">
          <h2>
            <?php echo $title ?>
          </h2>
          <div></div>
        </dt>
        <dd id="target-faq-<?php echo $row_index ?>" class="collapseomatic_content">
          <?php echo $content ?>
        </dd>
      <!-- </div> -->
    <?php endwhile; ?>
  </dl>
<?php endif; ?>
