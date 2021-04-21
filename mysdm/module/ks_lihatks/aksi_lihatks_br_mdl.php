<?PHP
session_start();
$aksi="";

include "../../config/koneksimysqli.php";
$pidbr=$_POST['uid'];

$query = "select brId as brid, aktivitas1, aktivitas2, tgl FROM hrd.br0 where brId='$pidbr'";
$tampil=mysqli_query($cnmy, $query);
$row= mysqli_fetch_array($tampil);

$ptanggal=$row['tgl'];
$paktivitas1=$row['aktivitas1'];
$paktivitas2=$row['aktivitas2'];

$ptanggal=date("d F Y", strtotime($ptanggal));
    
?>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Detail KI</h4>
        </div>
        <br/>
        <div class="">
            
            <?PHP //echo $pisql; ?>
            
            <div class="row">

                <div class="col-md-6 col-sm-6 col-xs-12">

                    <div class="x_panel">
                        
                        <div class="x_content">
                            <div class="dashboard-widget-content">

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                    <div class='col-md-9'>
                                    <div class='input-group date' id=''>
                                        <input type="text" class="form-control" id='e_periode1' name='e_periode1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptanggal; ?>' Readonly>
                                        <span class='input-group-addon'>
                                            <span class='glyphicon glyphicon-calendar'></span>
                                        </span>

                                    </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Aktivitas 1 <span class='required'></span></label>
                                    <div class='col-md-9'>
                                        <textarea class='form-control' id="e_akv1" name='e_akv1' Readonly><?PHP echo $paktivitas1; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Aktivitas 2 <span class='required'></span></label>
                                    <div class='col-md-9'>
                                        <textarea class='form-control' id="e_akv2" name='e_akv2' Readonly><?PHP echo $paktivitas2; ?></textarea>
                                    </div>
                                </div>

                                
                                
                            </div>
                            
                        </div>
                        
                    </div>

                </div>
                
            </div>
        
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
        
    </div>
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<?PHP
mysqli_close($cnmy);
?>