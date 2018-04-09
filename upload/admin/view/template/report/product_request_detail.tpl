<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-user"></i> <?php echo $text_list_customer; ?></h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-left"><?php echo $column_name; ?></td>
                <td class="text-right"><?php echo $column_ip; ?></td>
				        <td class="text-right"><?php echo $column_date; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($requests) { ?>
              <?php foreach ($requests as $req) { ?>
              <tr>
                <td class="text-left"><?php echo $req['name']; ?></td>
                <td class="text-right"><?php echo $req['ip']; ?></td>
				        <td class="text-right"><?php echo $req['date']; ?></td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $Pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $Results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
