<?PHP
session_start();
$aksi="";

include "../../config/koneksimysqli.php";
$pidbr=$_POST['uid'];

$query = "select a.brId as brid, a.aktivitas1, a.aktivitas2, "
        . " a.tgl, a.tgltrans, a.realisasi1, a.karyawanid, b.nama as nama_karyawan "
        . " FROM hrd.br0 as a LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId where a.brId='$pidbr'";
$tampil=mysqli_query($cnmy, $query);
$row= mysqli_fetch_array($tampil);

$ptanggal=$row['tgl'];
$ptgltrans=$row['tgltrans'];
$pnmbuat=$row['nama_karyawan'];
$pnmrealisasi=$row['realisasi1'];
$paktivitas1=$row['aktivitas1'];
$paktivitas2=$row['aktivitas2'];

$ptanggal=date("d F Y", strtotime($ptanggal));
if ($ptgltrans=="0000-00-00") $ptgltrans="";

if (empty($ptgltrans)) $ptgltrans=$ptanggal;
else $ptgltrans=date("d F Y", strtotime($ptgltrans));

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
            
            <?PHP //echo $query; ?>
            
            <div class="row">

                <div class="col-md-8 col-sm-8 col-xs-12">

                    <div class="x_panel">
                        
                        <div class="x_content">
                            <div class="dashboard-widget-content">

                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Tgl. Input <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_periode1' name='e_periode1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptanggal; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>

                                        </div>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Tgl. Transfer <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_periode2' name='e_periode2' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptgltrans; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>

                                        </div>
                                    </div>
                                </div>

                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Yang Membuat <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <input type='text' id='e_nmbuat' name='e_nmbuat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmbuat; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Realisasi <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <input type='text' id='e_nmrealisasi' name='e_nmrealisasi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmrealisasi; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Aktivitas 1 <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <textarea class='form-control' id="e_akv1" name='e_akv1' Readonly><?PHP echo $paktivitas1; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Aktivitas 2 <span class='required'></span></label>
                                    <div class='col-md-8'>
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