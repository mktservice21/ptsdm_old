<?PHP
session_start();
$aksi="";
$psts=$_POST['usts'];
$pidinput=$_POST['unourut'];
$pkryid=$_POST['uidkry'];
$ptgl=$_POST['utgl'];
$pudoktid=$_POST['udoktid'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];


$tgl_pertama = date('d F Y', strtotime($ptgl));
$itgl = date('Y-m-d', strtotime($ptgl));
        
include "../../../config/koneksimysqli.php";
include "../../../config/fungsi_sql.php";

$sql = "select a.karyawanid, c.nama as namakaryawan, a.tanggal, a.tglinput, 
    a.dokterid, d.namalengkap, d.gelar, d.spesialis, a.jenis, a.notes, a.saran
    FROM hrd.dkd_new_real1 as a JOIN dr.masterdokter as d on a.dokterid=d.id 
    LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId
    WHERE a.karyawanid='$pkryid' AND a.tanggal='$itgl' AND a.dokterid='$pudoktid' ";
$tampil=mysqli_query($cnmy, $sql);
$row= mysqli_fetch_array($tampil);
$pnmkaryawan= $row['namakaryawan'];
$pnmdokt= $row['namalengkap'];
$pnotes= $row['notes'];
$psaran= $row['saran'];

?>

<!-- bootstrap-datetimepicker -->
<link href="vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

<script src="js/hanyaangka.js"></script>
<!-- jQuery -->
<script src="vendors/jquery/dist/jquery.min.js"></script>
<!--input mask -->
<script src="js/inputmask.js"></script>


<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Notes</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=isidatakomentarwekplan&act=input&idmenu=483"; ?>' 
                      id='d-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>


                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_idinput' name='e_idinput' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                                <input type='text' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                                <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                                <input type='text' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                            <div class='col-md-4'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_periode1' name='e_periode1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_pertama; ?>' Readonly>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>

                                            </div>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='hidden' id='e_idkry' name='e_idkry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkryid; ?>' Readonly>
                                                <input type='text' id='e_namakry' name='e_namakry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmkaryawan; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dokter <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='hidden' id='e_doktid' name='e_doktid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pudoktid; ?>' Readonly>
                                                <input type='text' id='e_doktnm' name='e_doktnm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmdokt; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Notes <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <textarea class='form-control' id="e_ketdetail" name='e_ketdetail' maxlength='300' readonly><?PHP echo $pnotes; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saran <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <textarea class='form-control' id="e_saran" name='e_saran' maxlength='300' readonly><?PHP echo $psaran; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <button type='button' id='btnakv' class='btn btn-info add-aktv' onclick=''>Approve</button>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>


                        </div>
                    </div>


                </form>
            </div>
            <!--end row-->
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
    </div>
</div>

    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script type='text/javascript' src='datetime/js/jquery-ui.min.js'></script>

    <!-- jquery.inputmask -->
    <script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>

    <!-- bootstrap-daterangepicker -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <!-- Custom Theme Scripts -->


<?PHP
mysqli_close($cnmy);
?>