<?php 

require_once(APPROOT . '/views/inc/header.php'); 
?>

<?php include VIEWINCLUDE .'sidebar.php';  ?>


<?php 
    $privuser =  $data['privuser'];
    if($privuser->hasPrivileage('Dashboard'))
    {
        $url = URLROOT .'Dashboards/index';
    }
    else
    {
        $url = '#';
    }
    
    
    $numbooks = $data['numbooks'] ?? -1;
?>

<ol class="breadcrumb">
	<li class="breadcrumb-item">
    	<a href="<?php echo $url; ?>">Dashboard</a>
    </li>
    <li class="breadcrumb-item active"></li>
 </ol>
 
 
 
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-3">
          <div class="card text-white bg-primary o-hidden h-10">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-comments"></i>
              </div>
              <div class="mr-5"><?php echo $numbooks .' Böcker';?></div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="#">

            </a>
          </div>
        </div> <!-- col -->
 	  </div> <!-- row -->
 
 
 
 


<?php require_once(APPROOT . '/views/inc/footer.php'); ?>




<!-- 

              <span class="float-left">View Details</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>


 -->