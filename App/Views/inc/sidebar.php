<?php 

use Simpleframework\Helpers\Util;
use Simpleframework\RABC\PrivilegedUser;

Util::startSession();

$privuser = new PrivilegedUser();
$privuserID = $_SESSION['UserID'] ?? 0;




$privuser->getPriUserByID($privuserID);


// ckeck om inloggad
require_once (MODELS.DS.'User.php');
$user = new User();
$username = $user->getUserFullName($privuserID) ?? $user->getUsernamrByID($privuserID) ?? "Blankt";

if (empty($username))
{
    $username = 'Användarenamn';
}

echo '';
?>

<input type="hidden" id="jsurlroot" value="<?php echo URLROOT; ?>">
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="<?php echo URLROOT; ?>Dashboards/index">myPortal</a> 
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
   
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
      
      
      	<?php if ($privuser->hasPrivileage('Dashboard')) :?>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
          <a class="nav-link" href="<?php echo URLROOT; ?>Dashboards/index">
            <i class="fa fa-fw fa-tachometer-alt"></i>
            <span class="nav-link-text">Dashboard</span>
          </a>
        </li>
        <?php endif;?>
       
		<?php if ($privuser->hasPrivileage('adminsettings')) :?>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseExamplePages" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-cog"></i>
            <span class="nav-link-text">Admin</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseExamplePages">
            <li>
              <a href="login.html">Lägg till användare</a>
            </li>
            <li>
              <a href="register.html">Rättigheter</a>
            </li>
            <li>
              <a href="forgot-password.html">lorem ipsum</a>
            </li>
            <li>
              <a href="blank.html">lorem ipsum</a>
            </li>
          </ul>
        </li>
        <?php endif;?>
        
        <?php if ($privuser->hasPrivileage('Books')) :?>
        

        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Books">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapsebooks" data-parent="#booksAccordion">
          
            <i class="fa fa-fw fa-book"></i>
            <span class="nav-link-text">Böcker</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapsebooks">
           <?php if ($privuser->hasPrivileage('Showbooks')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>Books/showbooks">Visa alla böcker</a>
            </li>
             <?php endif;?>
             <?php if ($privuser->hasPrivileage('AddBooks')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>Books/addbook">Lägg till bok</a>
            </li>
             <?php endif;?>
             <?php if ($privuser->hasPrivileage('ShowAuthor')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>Authors/showauthors">Visa alla författare</a>
            </li>
             <?php endif;?>
             
             <?php if ($privuser->hasPrivileage('AddAuthor')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>Authors/addauthor">Lägg till författare</a>
            </li>
             <?php endif;?>
             
             <?php if ($privuser->hasPrivileage('AddPublisher')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>Publishers/addpublisher">Lägg till förlag</a>
            </li>
             <?php endif;?>
             <?php if ($privuser->hasPrivileage('ShowPublisher')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>Publishers/showpublisher">Visa alla förlag</a>
            </li>
             <?php endif;?>
             
             <?php if ($privuser->hasPrivileage('AddSerie')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>series/addserie">Lägg till serie</a>
            </li>
             <?php endif;?>
             <?php if ($privuser->hasPrivileage('ShowSerie')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>series/showserie">Visa alla serier</a>
            </li>
             <?php endif;?>
             
             <?php if ($privuser->hasPrivileage('AddCategories')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>Categories/addCategory">Lägg till kategori</a>
            </li>
             <?php endif;?>
             <?php if ($privuser->hasPrivileage('ShowCategories')) :?>
            <li>
              <a href="<?php echo URLROOT; ?>Categories/showsCategory">Visa alla kategorier</a>
            </li>
             <?php endif;?>
             
             
          </ul>
        </li>
        
         <?php endif;?>
         
         
      
         
         
         
        
      </ul>
      <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
          <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
          </a>
        </li>
      </ul>
      
      <?php if(isset($_SESSION['userlogin']) && $_SESSION['userlogin'] ) :?>
      <ul class="navbar-nav ">
      <li class="nav-item btn-group">
         <a class="dropdown-toggle btn-link cssdropdown"  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $username; ?></a>
         
         	<div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <a class="dropdown-item" href="#">Inställningar</a>
                    <a class="dropdown-item" href="<?php echo URLROOT;?>users/changepass">Ändra lösenord</a>
                    <a class="dropdown-item" href="<?php echo URLROOT;?>users/logout">Logga ut</a>
           </div>
         
      </li>
      
       
       </ul>
      <?php endif;?>
      
  
  
    
    </div>
    
  </nav>
  



 

