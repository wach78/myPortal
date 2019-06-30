
<?php use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\Csrf;

require_once(VIEWINCLUDE. 'header.php');?>




<?php include VIEWINCLUDE .'sidebar.php';  ?>
<?php 
    $privuser =  $data['privuser'];
    if($privuser->hasPrivileage('ShowCategories'))
    {
        $url = URLROOT .'categories/showcategory';
    }
    else
    {
        $url = '#';
    }
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item">
    	<a href="<?php echo $url; ?>">Kategori</a>
    </li>
    <li class="breadcrumb-item active">Uppdatera kategori</li>
 </ol>
 

<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Uppdatera kategorie</h2>
			
			<form action="<?php echo URLROOT;?>categories/editcategory/<?php echo $data['ID']?>" method="post">
			<?php echo Csrf::csrfTokenTag();?>
			<input type="hidden" name="ID" value="<?php echo $data['ID']; ?>">
				<div class="form-group">
				<label for="updatecategory">Namn: <sub>*</sub></label>
				<input type="text" name="categoriesname" id="updatecategory" class="form-control form-control-lg  <?php echo (!empty($data['categories_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['categoriesname'];?>">
				<span class="invalid-feedback"><?php echo $data['categories_err']?></span>
				</div>
				<div class="row">
					<div class="col">
					<input type="submit" value="Update" class="btn btn-success btn-block">
					</div>
				</div> 
			</form>
			<div class="text-center">
          <a class="d-block small mt-2" href="<?php echo URLROOT;?>categories/showcategory">Visa alla</a>
        </div>
		</div>
	</div>
</div>



<?php require_once(VIEWINCLUDE. 'footer.php'); ?>