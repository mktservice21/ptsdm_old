<?PHP
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    $puserid=$_SESSION['USERID'];
    
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_combo.php";
    $act="input";
    $aksi="";
    
    $pnodivisi=$_POST['unodivisi'];
    $pidinput=$_POST['uidinput'];
    $pidkode=$_POST['uidkode'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpcrbtl01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpcrbtl02_".$puserid."_$now ";

    $query = "select bridinput, amount, jml_adj FROM dbmaster.t_suratdana_br1 WHERE idinput='$pidinput'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    if ($pidkode=="A") {
        $query = "select a.bridinput, '' as karyawanid, '' as nama_karyawan, b.aktivitas1 as keterangan, b.aktivitas2, a.amount as jumlah, a.jml_adj "
                . " FROM $tmp01 as a JOIN hrd.br0 as b "
                . " on a.bridinput=b.brid "
                . " WHERE IFNULL(b.batal,'')='Y'";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }elseif ($pidkode=="F" OR $pidkode=="G") {
        $query = "select a.bridinput, b.karyawanid, b.nama_karyawan, b.keterangan, a.amount as jumlah, CAST(0 as DECIMAL(20,2)) as jml_adj  "
                . " FROM $tmp01 as a JOIN dbmaster.t_brrutin0 as b "
                . " on a.bridinput=b.idrutin "
                . " WHERE IFNULL(b.stsnonaktif,'')='Y'";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp02 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid SET a.nama_karyawan=b.nama "
                . " WHERE a.karyawanid NOT IN ('0000002083', '0000002200')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }else{
        $query="select bridinput, '' as karyawanid, '' as nama_karyawan, '' as keterangan, '0' as jumlah from $tmp01 WHERE bridinput='XXXXXXXX'";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    $pjumlah="";
    $pjmlkuranglebih="";
    $pketerangan="";
    $pnmkaryawan="";
    $pbridinput="";
    
    $sql = "SELECT bridinput, karyawanid, nama_karyawan, keterangan, jumlah, jml_adj FROM $tmp02";
    $tampil= mysqli_query($cnmy, $sql);
    
    while ($row= mysqli_fetch_array($tampil)) {
        //$pptgl=$row['tgl'];
        //$tgl1 = date('d/m/Y', strtotime($pptgl));
        $pjumlah=(DOUBLE)$pjumlah+(DOUBLE)$row['jumlah'];
        $pjmlkuranglebih=(DOUBLE)$pjmlkuranglebih+(DOUBLE)$row['jml_adj'];
        $pbridinput_ =$row['bridinput'];
        $pnmkaryawan =$row['nama_karyawan'];
        
        $pketerangan .=$row['keterangan'].", ";

        if ($pidkode=="F" OR $pidkode=="G") {
            $pbridinput_=$pbridinput_." (".$pnmkaryawan.")";
        }
        $pbridinput .=$pbridinput_.", ";
    }
    
    
?>


    <!-- bootstrap-datetimepicker -->
    <link href="vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

    <script src="js/hanyaangka.js"></script>
    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!--input mask -->
    <script src="js/inputmask.js"></script>


    
<script> window.onload = function() { document.getElementById("e_jumlah").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Info</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=glreportspd&act=input&idmenu=224"; ?>' id='demo-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Divisi <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_idnodivisi' name='e_idnodivisi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnodivisi; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_idbr' name='e_idbr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbridinput; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jumlah' name='e_jumlah' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kurang Lebih <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jumlah2' name='e_jumlah2' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlkuranglebih; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                            <div class='col-md-7'>
                                                <input type='text' id='e_ket' name='e_ket' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>' readonly>
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


        <script type='text/javascript' src='datetime/js/jquery-ui.min.js'></script>
       
        <!-- jquery.inputmask -->
        <script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
        
        <!-- bootstrap-daterangepicker -->
        <script src="vendors/moment/min/moment.min.js"></script>
        <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
        <script src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
        <!-- Custom Theme Scripts -->
        
        

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_close($cnmy);
?>