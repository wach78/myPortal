
<?php 
use Simpleframework\Middleware\Csrf;

require_once(VIEWINCLUDE. 'header.php');?>




<?php include VIEWINCLUDE .'sidebar.php';  ?>

<?php 
    $privuser =  $data['privuser'];
    if($privuser->hasPrivileage('Showbooks'))
    {
        $url = URLROOT .'books/showbooks';
    }
    else
    {
        $url = '#';
    }
?>

<ol class="breadcrumb">
	<li class="breadcrumb-item">
    	<a href="<?php echo $url; ?>">Böcker</a>
    </li>
    <li class="breadcrumb-item active">Visa alla böcker</li>
 </ol>
<div class="row">
	<div class="col-md-10 mx-auto">
        <div class="table-responsive">
        <div id="csrf"><?php echo Csrf::csrfTokenTag();?></div>
			<table class="table table-bordered table-hover table-striped" id="allbooksTable">
		
				<thead>
				<tr>
					<th 
					<?php  if ($privuser->hasPrivileage('AddBooks')) {?>
						class="tacenter"><a href="<?php echo URLROOT;?>Books/addbook" class="btn"><i class="fa fa-user-plus fa-1x text-success"></i></a> 
						<?php }?>
					</th>
				
					<th>Name</th>
					<th>Serie</th>
					<th>Författare</th>
					<th>Kategorie</th>
					<th>Antal Sidor</th>
					<th>ISBN</th>
					<th>Förlag</th>
					<th>Beskrivning</th>
					<th>Har läst</th>
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