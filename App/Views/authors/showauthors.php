
<?php use Simpleframework\Middleware\Csrf;

require_once(VIEWINCLUDE. 'header.php');?>




<?php include VIEWINCLUDE .'sidebar.php';  ?>
<?php 
    $privuser =  $data['privuser'];
    if($privuser->hasPrivileage('ShowAuthor'))
    {
        $url = URLROOT .'Authors/showauthors';
    }
    else
    {
        $url = '#';
    }
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item">
    	<a href="<?php echo $url; ?>">Författare</a>
    </li>
    <li class="breadcrumb-item active">Visa alla Författare</li>
 </ol>
<div class="row">
	<div class="col-md-6 mx-auto">
	<div id="csrf"><?php echo Csrf::csrfTokenTag();?></div>
        <div class="table-responsive">
        
			<table class="table table-bordered table-hover table-striped" id="allauthorsTable">
			
				<thead>
				<tr>
					<th
					<?php  if ($privuser->hasPrivileage('AddAuthor')) {?>
						class="tacenter w-15"><a href="<?php echo URLROOT;?>Authors/addauthor" class="btn"><i class="fa fa-user-plus fa-1x text-success"></i></a> 
				    <?php }?>
						
					</th>
				
					<th class="w-85" >Författare </th>
					
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