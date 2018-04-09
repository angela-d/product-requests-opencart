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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-left"><?php echo $column_name; ?></td>
                <td class="text-right"><?php echo $column_total; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($requests) { ?>
              <?php foreach ($requests as $req) { ?>
              <tr>
                <td class="text-left"><?php echo $req['name']; ?></td>
                <td class="text-right"><?php echo $req['total']; ?></td>
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
    <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-group"></i> <?php echo $text_customers; ?></h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-left"><?php echo $column_email; ?></td>
                <td class="text-right"><?php echo $column_total; ?></td>
                <td class="text-right"><?php echo $column_action; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if (isset($customer)) { ?>
              <?php foreach ($customer as $cust) { ?>
              <tr>
                <td class="text-left"><?php echo $cust['email']; ?></td>
                <td class="text-right"><?php echo $cust['total']; ?></td>
                <td class="text-right"><a href="<?php echo $cust['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_details; ?>" class="btn btn-primary"><i class="fa fa-list"></i></a></td>
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
          <div class="col-sm-6 text-left"><?php echo $custPagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $custResults; ?></div>
        </div>
      </div>
    </div>
  </div>
  <?php if ($total_unconfirmed >0){ ?>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
    	<h3 class="panel-title"><i class="fa fa-ban"></i> <?php echo $text_unconfirmed; ?></h3>
      </div>
      <div class="panel-body">
      	<div class="table-responsive">
      	  <table class="table table-bordered">
      		<thead>
      		  <tr>
      			<td class="text-left"><?php echo $text_unconfirmed_info; ?></td>
      		  </tr>
      		</thead>
      	  </table>
      	</div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>
<?php echo $footer; ?>
