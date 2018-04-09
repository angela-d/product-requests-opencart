<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php
		foreach ($breadcrumbs as $breadcrumb) {
			if (!empty($breadcrumb['href'])){
				echo'<li><a href="'.$breadcrumb['href'].'">'.$breadcrumb['text'].'</a></li>';
			}else{
				echo'<li>'.$breadcrumb['text'].'</li>';
			}
		}
	?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <div class="row">
        <?php if ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-8'; ?>
        <?php } ?>
        <div class="<?php echo $class; ?>">
		      <h3><?php echo $thanks_confirm.'</h3>'. $thanks_body; ?>
        </div>
        <?php if ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-4'; ?>
        <?php } ?>
        <div class="<?php echo $class; ?>">
          <h1><?php echo $heading_title; ?></h1>
          <?php if (!empty($thumb) || !empty($images)) { ?>
          <ul class="thumbnails">
            <?php if ($thumb) { ?>
            <li><a class="thumbnail" href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>"><img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a></li>
            <?php } ?>
            <?php if ($images) { ?>
            <?php
      				$i = 0;
      				foreach ($images as $image) {
      					if ($i >0){
						      echo '<li class="image-additional"><a class="thumbnail" href="'.$image['popup'].'" title="'.$heading_title.'"> <img src="'.$image['thumb'].'" title="'.$heading_title.'" alt="'.$heading_title.'" /></a></li>';
      					++$i;
      					}
              }
            } ?>
          </ul>
          <?php } ?>
      </div>
	</div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>

<script type="text/javascript"><!--
$(document).ready(function() {
	$('.thumbnails').magnificPopup({
		type:'image',
		delegate: 'a',
		gallery: {
			enabled:true
		}
	});
});
//--></script>
<?php echo $footer; ?>
