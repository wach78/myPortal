
<?php use Simpleframework\Middleware\Csrf;

require_once(VIEWINCLUDE. 'header.php');?>




<?php include VIEWINCLUDE .'sidebar.php';  ?>

<?php 
    $privuser =  $data['privuser'];
    if($privuser->hasPrivileage('ShowSerie'))
    {
        $url = URLROOT .'series/showserie';
    }
    else
    {
        $url = '#';
    }
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item">
    	<a href="<?php echo $url; ?>">Serie</a>
    </li>
    <li class="breadcrumb-item active">Visa alla serier</li>
 </ol>
 
<div class="row">
	<div class="col-md-6 mx-auto">
        <div class="table-responsive">
        <div id="csrf"><?php echo Csrf::csrfTokenTag();?></div>
			<table class="table table-bordered table-hover table-striped" id="allserieTable">
		
				<thead>
				<tr>
					<th 
						<?php  if ($privuser->hasPrivileage('AddSerie')) {?>
						class="tacenter w-25"><a href="<?php echo URLROOT;?>series/addserie" class="btn"><i class="fa fa-user-plus fa-1x text-success"></i></a> 
						<?php }?>
					</th>
				
					<th class="w-75">Serie</th>
					
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