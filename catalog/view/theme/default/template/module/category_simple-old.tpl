<div class="box">
  <div class="box-heading"></div>
  <div class="box-content">
    <div class="box-category">
      <ul>
        <?php foreach ($categories as $category) { ?>
        <li class="">
          <?php if ($category['category_id'] == $category_id) { ?>
          <a href="<?php echo $category['href']; ?>/#" class="active"><?php echo $category['name']; ?></a>
          <?php } else { ?>
          <a href="<?php echo $category['href']; ?>/#"><?php echo $category['name']; ?></a>
          <?php } ?>
          <?php if ($category['children']) { ?>
          <ul id="left-side-bar-category">
            <?php foreach ($category['children'] as $child) { ?>
                <li class="">
              <?php if ($child['category_id'] == $child_id) { ?>
              <a href="<?php echo $category['href']; ?>/#cat_<?php echo $child['category_id']; ?>" class="active"> <?php echo $child['name']; ?></a>
              <?php } else { ?>
              <a href="<?php echo $category['href']; ?>/#cat_<?php echo $child['category_id']; ?>"> <span id="cat-firsr-heiffen"></span> <?php echo $child['name']; ?></a>
              <?php } ?>
            </li>
                <?php foreach ($child['child_2'] as $child_2) { ?>
                <li class="scat_ub_child">
                    <?php if ($child_2['category_id'] == $child_id) { ?>
                    <a href="<?php echo $child_2['href']; ?>/#cat_<?php echo $child_2['category_id']; ?>" class="active"> <?php echo $child_2['name']; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $child_2['href']; ?>/#cat_<?php echo $child_2['category_id']; ?>"> <span id="cat-firsr-heiffen"></span> <?php echo $child_2['name']; ?></a>
                    <?php } ?>
                </li>
                <?php } ?>
            <?php } ?>
          </ul>
          <?php } ?>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</div>
<script type="text/javascript">
$('document').ready(function(){

$('#left-side-bar-category li').click(function(){
    $('#left-side-bar-category li').removeClass('side-cat-selected');
    $(this).addClass('side-cat-selected');

});

});
</script>