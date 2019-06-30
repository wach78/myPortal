
<?php use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\Csrf;

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
    <li class="breadcrumb-item active">Uppdatera Författare</li>
 </ol>
 
<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Uppdatera författare</h2>
			<?php Util::flash('updateauthor'); ?>
			<form action="<?php echo URLROOT;?>Authors/editauthor/<?php echo $data['ID']?>" method="post">
			<?php echo Csrf::csrfTokenTag();?>
			<input type="hidden" name="AID" value="<?php echo $data['ID']; ?>">
				<div class="form-group">
				<label for="updateauthor">Namn: <sub>*</sub></label>
				<input type="text" name="authorname" id="updateauthor" class="form-control form-control-lg  <?php echo (!empty($data['authorname_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['authorname'];?>">
				<span class="invalid-feedback"><?php echo $data['authorname_err']?></span>
				</div>
				<div class="row">
					<div class="col">
					<input type="submit" value="Update" class="btn btn-success btn-block">
					</div>
				</div> 
			</form>
			<div class="text-center">
          <a class="d-block small mt-2" href="<?php echo URLROOT;?>Authors/showauthors">Visa alla</a>
        </div>
		</div>
	</div>
</div>



<?php require_once(VIEWINCLUDE. 'footer.php'); ?>