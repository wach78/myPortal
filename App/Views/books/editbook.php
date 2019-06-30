<?php
use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;

$authorsdata = $data['authorsdata'] ?? [];
$categoriedata = $data['categoriedata'] ?? [];
$seriedata = $data['seriedata'] ?? [];
$publiserdata = $data['publiserdata'] ?? [];
$bookhaveauthors = $data['bookhaveautors'] ?? [];



?>

<?php require_once(VIEWINCLUDE. 'header.php');?>

<?php require_once VIEWINCLUDE .'sidebar.php';  ?>

<?php 
    $privuser =  $data['privuser'];
    if($privuser->hasPrivileage('Showbooks'))
    {
        $url = URLROOT .'books/editbook';
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
    <li class="breadcrumb-item active">Uppdatera bok</li>
 </ol>
 
 <div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Uppdatera bok</h2>
			<?php Util::flash('editbook'); ?>
			<form action="<?php echo URLROOT;?>books/editbook/<?php echo $data['ID']?>" method="post">
			<?php echo Csrf::csrfTokenTag();?>
			
			<div class="form-group">
		    <label for="bookname">Namn: <sub>*</sub></label>
		    <input type="text" name="bookname" id="bookname" class="form-control form-control-lg  <?php echo (!empty($data['bookname_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['bookname'];?>">
		    <span class="invalid-feedback"><?php echo $data['bookname_err']?></span>
			</div>
			
			<div class="form-group">
			<label for="selectpickerserie">Välj serie</label>
			<select name="selectpickerserie" id="selectpickerserie" class="selectpicker form-control form-control">
  				
  				<?php 
  				$len = count($seriedata);
  				for ($i = 0; $i < $len; $i++)
  				{
  				    if ($seriedata[$i]->ID == $data['serieID'])
  				    {
  				        echo '<option value=' . $seriedata[$i]->ID . ' selected >';
  				        echo Sanitize::cleanOutput($seriedata[$i]->Seriename);
  				        echo '</option>';
  				    }
  				    else 
  				    {
  				      echo '<option value=' . $seriedata[$i]->ID . '>';
  				      echo Sanitize::cleanOutput($seriedata[$i]->Seriename);
  				      echo '</option>';
  				    }
  				}
  				
  				?>
  			</select>
  			</div>
  			
  			<div class="form-group">
			<label for="selectpickerauthors">Välj författare</label>
				<select name="selectpickerauthors[]" id="selectpickerauthors" class="selectpicker form-control form-control" multiple>
  				
  				<?php 
  				$len = count($authorsdata);

  				for ($i = 0; $i < $len; $i++)
  				{
  				    $print = false;
  				    $autorslen = count($bookhaveauthors);
  			
  				    for ($j = 0; $j < $autorslen; $j++)
  				    {

  				      if ($authorsdata[$i]->ID ==  $bookhaveauthors[$j]->ID)
  				      {
  				          echo '<option value=' . $authorsdata[$i]->ID . ' selected > ';
  				          echo Sanitize::cleanOutput($authorsdata[$i]->Authorname);
  				          echo '</option>';
  				          $print = false;
  				          break;
  				      }
  				      else 
  				      {
  				          $print = true;
  				      }
  				    }
  				    
  				    if ($print)
  				    {
  				      echo '<option value=' . $authorsdata[$i]->ID . '>';
  				      echo Sanitize::cleanOutput($authorsdata[$i]->Authorname);
  				      echo '</option>';
  				    }
  				}
  				
  				?>
  			</select>
  			</div>
  			
  			
  			
  		
  			
  			  	<div class="form-group">
				<label for="selectpickercategorie">Välj Kategory</label>
				<select name="selectpickercategorie" id="selectpickercategorie" class="selectpicker form-control form-control">
  				
  				<?php 
  				$len = count($categoriedata);
  				
  				for ($i = 0; $i < $len; $i++)
  				{
  				    
  				    if ($categoriedata[$i]->ID == $data['categoriID'])
  				    {
  				        echo '<option value=' . $categoriedata[$i]->ID . 'selected >';
  				        echo Sanitize::cleanOutput($categoriedata[$i]->Categoriesname);
  				        echo '</option>';
  				    }
  				    else 
  				    {
  				      echo '<option value=' . $categoriedata[$i]->ID . '>';
  				      echo Sanitize::cleanOutput($categoriedata[$i]->Categoriesname);
  				      echo '</option>';
  				    }
  				}
  				
  				?>
  				</select>
  				</div>
			
			
  				
  				<div class="form-group">
				<label for="pages">Antal sidor:</label>
				<input type="text" name="pages" id="pages" class="form-control form-control-lg  <?php echo (!empty($data['pages_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['pages'];?>">
				<span class="invalid-feedback"><?php echo $data['pages_err']?></span>
				</div>
				
				<div class="form-group">
				<label for="ISBN">ISBN:</label>
				<input type="text" name="ISBN" id="ISBN" class="form-control form-control-lg  <?php echo (!empty($data['ISBN_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['ISBN'];?>">
				<span class="invalid-feedback"><?php echo $data['ISBN_err']?></span>
				</div>
				
				<div class="form-group">
				<label for="selectpickerpubliser">Välj Förlag</label>
				<select name="selectpickerpubliser" id="selectpickerpubliser" class="selectpicker form-control form-control">
  				
  				<?php 
  				$len = count($publiserdata);
  				for ($i = 0; $i < $len; $i++)
  				{
  				    if ($publiserdata[$i]->ID == $data['publiserID']  )
  				    {
  				        echo '<option value=' . $publiserdata[$i]->ID . ' selected > ';
  				        echo Sanitize::cleanOutput($publiserdata[$i]->Publishername);
  				        echo '</option>';
  				    }
  				    else 
  				    {
  				      echo '<option value=' . $publiserdata[$i]->ID . '>';
  				      echo Sanitize::cleanOutput($publiserdata[$i]->Publishername);
  				      echo '</option>';
  				    }
  				}
  				
  				?>
  				</select>
  				</div>
  				
  				<div class="form-group">
				<label for="description">Beskrivning: </label>
				<textarea rows="4" cols="50" name="description" id="description" class="form-control form-control-lg  <?php echo (!empty($data['description_err'])) ? 'is-invalid' : '' ;?>"><?php echo $data['description'];?> </textarea>
				<span class="invalid-feedback"><?php echo $data['description_err']?></span>
				</div>
				
				<div class="form-group form-check">
                <label class="form-check-label">
               	<input type='hidden' value='0' name='haveread'>
                <input class="form-check-input chk" name='haveread' type="checkbox" <?php if ($data['haveread'] == 1) { echo 'checked="checked"'; } ?>  value="<?php echo $data['haveread'];?>"> Har läst 
                </label>
                </div>
			
				<div class="row">
					<div class="col">
					<input type="submit" value="Update" class="btn btn-success btn-block">
					</div>
				</div> 
			
			
			</form>
			<div class="text-center">
          <a class="d-block small mt-2" href="<?php echo URLROOT;?>books/showbooks">Visa alla</a>
        </div>
		</div>
	</div>
</div>








<?php require_once(VIEWINCLUDE. 'footer.php'); ?>