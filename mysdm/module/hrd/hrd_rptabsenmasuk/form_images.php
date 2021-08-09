<?PHP
session_start();
$aksi="";
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];
$fgroupid=$_SESSION['GROUP'];
$fstsadmin=$_SESSION['STSADMIN'];
$flvlposisi=$_SESSION['LVLPOSISI'];
$fdivisi=$_SESSION['DIVISI'];

$ffolderfile="";
if (isset($_POST['ukey'])) $ffolderfile=$_POST['ukey'];

?>


<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Foto</h4>
        </div>
        <br/>
        <div class="">
            
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">

                    <div class="x_panel">
                        
                        <div class="x_content">
                            <div class="dashboard-widget-content">
                                <form id="form">
                                    
                                    <div class='form-group'>
                                        <center>
                                        <?PHP
                                        echo "<img src='$ffolderfile' width='310px' height='390px' />";
                                        ?>
                                        </center>
                                    </div>
                                    
                                </form>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>
</div>
