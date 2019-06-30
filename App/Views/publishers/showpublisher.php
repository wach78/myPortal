
<?php use Simpleframework\Middleware\Csrf;

require_once(VIEWINCLUDE. 'header.php');?>




<?php include VIEWINCLUDE .'sidebar.php';  ?>

<?php 
    $privuser =  $data['privuser'];
    if($privuser->hasPrivileage('ShowPublisher'))
    {
        $url = URLROOT .'publishers/showpublisher';
    }
    else
    {
        $url = '#';
    }
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item">
    	<a href="<?php echo $url; ?>">Förlag</a>
    </li>
    <li class="breadcrumb-item active">Visa alla Förlag</li>
 </ol>
 
<div class="row">
	<div class="col-md-6 mx-auto">
        <div class="table-responsive">
        <div id="csrf"><?php echo Csrf::csrfTokenTag();?></div>
			<table class="table table-bordered table-hover table-striped" id="allpublisherTable">
		
				<thead>
				<tr>
					<th 
						<?php  if ($privuser->hasPrivileage('AddPublisher')) {?>
						class="tacenter w-15"><a href="<?php echo URLROOT;?>publishers/addpublisher" class="btn"><i class="fa fa-user-plus fa-1x text-success"></i></a> 
						<?php }?>
					</th>
				
					<th class="w-85">Förlag</th>
					
				</tr>
				</thead>
					<tbody>
				</tbody>
				<tfoot>
				
			
				</tfoot>
				
			</table>
        </div>     
     </div>
</div>


<?php require_once(VIEWINCLUDE. 'footer.php'); ?>