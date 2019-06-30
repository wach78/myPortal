
<?php 
use Simpleframework\Helpers\Util;
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
    <li class="breadcrumb-item active">Lägg till kategori</li>
 </ol>

<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Lägg till kategori</h2>
			<?php Util::flash('addcategory'); ?>
			<form action="<?php echo URLROOT;?>Categories/addcategory" method="post">
			<?php echo Csrf::csrfTokenTag();?>
				<div class="form-group">
				<label for="addcategory">Namn: <sub>*</sub></label>
				<input type="text" name="categoriesname" id="addcategory" class="form-control form-control-lg  <?php echo (!empty($data['categoriesname_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['categoriesname'];?>">
				<span class="invalid-feedback"><?php echo $data['categoriesname_err']?></span>
				</div>
				<div class="row">
					<div class="col">
					<input type="submit" value="Add" class="btn btn-success btn-block">
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