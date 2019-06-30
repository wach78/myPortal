
<?php require_once(VIEWINCLUDE. 'header.php');?>




<?php include VIEWINCLUDE .'sidebar.php';  ?>

<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Login</h2>
			
			<form action="<?php echo URLROOT;?>users/login" method="post">
				
				<div class="form-group">
				<label for="email">Email: <sub>*</sub></label>
				<input type="email" name="email" class="form-control form-control-lg  <?php echo (!empty($data['email_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['email'];?>">
				<span class="invalid-feedback"><?php echo $data['email_err']?></span>
				</div>
				
				<div class="form-group">
				<label for="password">Password: <sub>*</sub></label>
				<input type="password" name="password" class="form-control form-control-lg  <?php echo (!empty($data['password_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['password'];?>">
				<span class="invalid-feedback"><?php echo $data['password_err']?></span>
				</div>
			
				<div class="row">
					<div class="col">
					<input type="submit" value="login" class="btn btn-success btn-block">
					</div>
				</div> 
			</form>
			<div class="text-center">
          
          <a class="d-block small mt-2" href="<?php echo URLROOT;?>users/forgot">Glömt lösenord?</a>
        </div>
		</div>
	</div>
</div>



<?php require_once(VIEWINCLUDE. 'footer.php'); ?>


<?php

if ($privuserID == 0)
{
    ?>
   <script>
   $(document).ready(function(){
       $("#sidenavToggler").trigger("click");
   });
   </script>
   <?php 

}

?>