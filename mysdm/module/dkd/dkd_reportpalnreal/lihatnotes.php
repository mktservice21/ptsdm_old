<?PHP
session_start();
$aksi="";
$psts=$_POST['usts'];
$pidinput=$_POST['unourut'];
$pkryid=$_POST['uidkry'];
$ptgl=$_POST['utgl'];
$pudoktid=$_POST['udoktid'];

if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];

$apvby_=$pidcard;

$tgl_pertama = date('d F Y', strtotime($ptgl));
$itgl = date('Y-m-d', strtotime($ptgl));
        
include "../../../config/koneksimysqli.php";
include "../../../config/fungsi_sql.php";


$hari_ini = date('Y-m-d');
$query = "select CURRENT_DATE() as tglnow";
$tampilt=mysqli_query($cnmy, $query);
$pketemut= mysqli_num_rows($tampilt);
if ((INT)$pketemut>0) {
    $trow=mysqli_fetch_array($tampilt);
    $t_tglnow=$trow['tglnow'];
    if ($t_tglnow=="0000-00-00") $t_tglnow="";
    
    if (!empty($t_tglnow)) {
        $hari_ini = $t_tglnow;
    }
}

$pweekDay = date('w', strtotime($hari_ini));
$hari_ini=date_create($hari_ini);


//$xx_tgl = date('2021-10-31');
//$pweekDay = date('w', strtotime($xx_tgl));
//$hari_ini=date_create($xx_tgl);


if ((INT)$pweekDay==1 OR (INT)$pweekDay==2 OR (INT)$pweekDay==3) {
    date_sub($hari_ini,date_interval_create_from_date_string("3 days"));//5
}elseif ((INT)$pweekDay==4 OR (INT)$pweekDay==5 OR (INT)$pweekDay==6) {
    date_sub($hari_ini,date_interval_create_from_date_string("3 days"));
}elseif ((INT)$pweekDay==0) {
    date_sub($hari_ini,date_interval_create_from_date_string("3 days"));//4
}else{
    date_sub($hari_ini,date_interval_create_from_date_string("0 days"));
}

$ptanggal_minapv = date_format($hari_ini,"Y-m-d");



$sql = "select a.karyawanid, c.nama as namakaryawan, a.tanggal, a.tglinput, 
    a.dokterid, d.namalengkap, d.gelar, d.spesialis, a.jenis, a.notes, a.saran, a.user_tandatangan, a.user_foto,
    atasan1, tgl_atasan1, atasan2, tgl_atasan2, atasan3, tgl_atasan3, atasan4, tgl_atasan4,
    d.nama as nama_spv, e.nama as nama_dm, f.nama as nama_sm, g.nama as nama_gsm 
    FROM hrd.dkd_new_real1 as a JOIN dr.masterdokter as d on a.dokterid=d.id 
    LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
    LEFT JOIN hrd.karyawan as d on a.atasan1=d.karyawanid 
    LEFT JOIN hrd.karyawan as e on a.atasan2=e.karyawanid 
    LEFT JOIN hrd.karyawan as f on a.atasan3=f.karyawanid 
    LEFT JOIN hrd.karyawan as g on a.atasan4=g.karyawanid 
    WHERE a.karyawanid='$pkryid' AND a.tanggal='$itgl' AND a.dokterid='$pudoktid' ";
$tampil=mysqli_query($cnmy, $sql);
$row= mysqli_fetch_array($tampil);
$ntglinput=$row['tglinput'];

$pnmkaryawan= $row['namakaryawan'];
$pnmdokt= $row['namalengkap'];
$pnotes= $row['notes'];
$psaran= $row['saran'];

$pnmatasan1= $row['nama_spv'];
$patasan1= $row['atasan1'];
$ptglatasan1= $row['tgl_atasan1'];
$pnmatasan2= $row['nama_dm'];
$patasan2= $row['atasan2'];
$ptglatasan2= $row['tgl_atasan2'];
$pnmatasan3= $row['nama_sm'];
$patasan3= $row['atasan3'];
$ptglatasan3= $row['tgl_atasan3'];
$pnmatasan4= $row['nama_gsm'];
$patasan4= $row['atasan4'];
$ptglatasan4= $row['tgl_atasan4'];

if ($ptglatasan1=="0000-00-00" OR $ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
if ($ptglatasan2=="0000-00-00" OR $ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
if ($ptglatasan3=="0000-00-00" OR $ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
if ($ptglatasan4=="0000-00-00" OR $ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";

$nfolderbulan = date('Ym', strtotime($ntglinput));
$pfttd=$row['user_tandatangan'];
$pffoto=$row['user_foto'];

$pittd="Y";
$pimages_pl=$pfttd;
$ffolderfile="images/user_ttd/";
$ffolderfile2="images/user_ttd/$nfolderbulan/";
$fketttdfoto="Tanda tangan user";
if (!empty($pffoto)) {
    $pimages_pl=$pffoto;
    $pittd="N";
    $ffolderfile="images/user_foto/";
	$ffolderfile2="images/user_foto/$nfolderbulan/";
    $fketttdfoto="Foto user";
}


$query_apv="";
$ketemuats=0;
if ($pidjbt=="10" OR $pidjbt=="18") {
    $query_apv = "select distinct b.karyawanid from sls.ispv0 as a JOIN sls.imr0 as b on a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.divisiid=b.divisiid "
            . " WHERE a.karyawanid='$pidcard' AND b.karyawanid IN ('$pkryid')";
}elseif ($pidjbt=="08") {
    $query_apv = "select distinct b.karyawanid from sls.idm0 as a JOIN sls.ispv0 as b on a.icabangid=b.icabangid "
            . " WHERE a.karyawanid='$pidcard' AND b.karyawanid IN ('$pkryid', '$patasan1')";
}elseif ($pidjbt=="20") {
    $query_apv = "select distinct b.karyawanid from sls.ism0 as a JOIN sls.idm0 as b on a.icabangid=b.icabangid "
            . " WHERE a.karyawanid='$pidcard' AND b.karyawanid IN ('$pkryid', '$patasan2')";
}

if (!empty($query_apv)) {
    $tampilats=mysqli_query($cnmy, $query_apv);
    $ketemuats= mysqli_num_rows($tampilats);
}

$psudahapprovespv=false;
$psudahapprovedm=false;
$psudahapprovesm=false;
$psudahapprovegsm=false;

$pwwnangspv=false;
$pwwnangdm=false;
$pwwnangsm=false;
$pwwnanggsm=false;

if ($pidjbt=="10" OR $pidjbt=="18") {
    if ((INT)$ketemuats>0) {
        $pwwnangspv=true;
        $pwwnangdm=false;
        $pwwnangsm=false;
        $pwwnanggsm=false;
    }
    
    if (!empty($ptglatasan1)) $psudahapprovespv=true;
    else{
        if ($pwwnangspv==true AND empty($patasan1)) {
            
            $query = "UPDATE hrd.dkd_new_real1 SET atasan1='$pidcard' WHERE nourut='$pidinput' AND "
                    . " tanggal='$itgl' and karyawanid='$pkryid' and dokterid='$pudoktid' AND IFNULL(tgl_atasan1,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') and IFNULL(atasan1,'')='' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $patasan1=$pidcard;
        }else{
            
            if ($apvby_==$patasan1 AND empty($ptglatasan1)) {
                $pwwnangspv=true;
            }
            
        }
    }
    
}elseif ($pidjbt=="08") {
    if ((INT)$ketemuats>0) {
        $pwwnangspv=false;
        $pwwnangdm=true;
        $pwwnangsm=false;
        $pwwnanggsm=false;
    }
    
    if (!empty($ptglatasan2)) $psudahapprovedm=true;
    else{
        if ($pwwnangdm==true AND empty($patasan2)) {
            
            $query = "UPDATE hrd.dkd_new_real1 SET atasan2='$pidcard' WHERE nourut='$pidinput' AND "
                    . " tanggal='$itgl' and karyawanid='$pkryid' and dokterid='$pudoktid' AND IFNULL(tgl_atasan2,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') and IFNULL(atasan2,'')='' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $patasan2=$pidcard;
            
        }else{
            
            if ($apvby_==$patasan2 AND empty($ptglatasan2)) {
                $pwwnangdm=true;
            }
            
        }
    }
}elseif ($pidjbt=="20") {
    if ((INT)$ketemuats>0) {
        $pwwnangspv=false;
        $pwwnangdm=false;
        $pwwnangsm=true;
        $pwwnanggsm=false;
    }
    
    if (!empty($ptglatasan3)) $psudahapprovesm=true;
    else{
        if ($pwwnangsm==true AND empty($patasan3)) {
            
            $query = "UPDATE hrd.dkd_new_real1 SET atasan3='$pidcard' WHERE nourut='$pidinput' AND "
                    . " tanggal='$itgl' and karyawanid='$pkryid' and dokterid='$pudoktid' AND IFNULL(tgl_atasan3,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') and IFNULL(atasan3,'')='' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $patasan3=$pidcard;
            
        }else{
            
            if ($apvby_==$patasan3 AND empty($ptglatasan3)) {
                $pwwnangsm=true;
            }
            
        }
    }
    
}


if (!empty($ptglatasan1)) $ptglatasan1 = date('d F Y H:i:s', strtotime($ptglatasan1));
if (!empty($ptglatasan2)) $ptglatasan2 = date('d F Y H:i:s', strtotime($ptglatasan2));
if (!empty($ptglatasan3)) $ptglatasan3 = date('d F Y H:i:s', strtotime($ptglatasan3));
if (!empty($ptglatasan4)) $ptglatasan4 = date('d F Y H:i:s', strtotime($ptglatasan4));

$pbolehapprove=true;
if ($pidjbt=="10" OR $pidjbt=="18") {
    
}elseif ($pidjbt=="08") {
    if ($patasan1<>$pkryid AND !empty($patasan1) AND empty($ptglatasan1)) $pbolehapprove=false;
}elseif ($pidjbt=="20") {
    if ($patasan2<>$pkryid AND !empty($patasan2) AND empty($ptglatasan2)) $pbolehapprove=false;
}elseif ($pidjbt=="05") {
    
}

$psudahmelewatihari=false;
if ($ptanggal_minapv>$ptgl) {
    $pbolehapprove=false;
    $psudahmelewatihari=true;
}
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

                                        <?PHP
                                        //echo "$pweekDay : $ptgl - $ptanggal_minapv : $pidinput - $ketemuats : $query_apv<br/>";
                                        ?>
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4 col-sm-4 col-xs-12'>
                                                <input type='text' id='e_idinput' name='e_idinput' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                                <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                                <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                                <input type='hidden' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                            <div class='col-md-4 col-sm-4 col-xs-12'>
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
                                            <div class='col-md-5 col-sm-5 col-xs-12'>
                                                <input type='hidden' id='e_idkry' name='e_idkry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkryid; ?>' Readonly>
                                                <input type='text' id='e_namakry' name='e_namakry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmkaryawan; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>User <span class='required'></span></label>
                                            <div class='col-md-5 col-sm-5 col-xs-12'>
                                                <input type='hidden' id='e_doktid' name='e_doktid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pudoktid; ?>' Readonly>
                                                <input type='text' id='e_doktnm' name='e_doktnm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmdokt; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Notes <span class='required'></span></label>
                                            <div class='col-md-5 col-sm-5 col-xs-12'>
                                                <textarea class='form-control' id="e_ketdetail" name='e_ketdetail' maxlength='300' readonly><?PHP echo $pnotes; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saran <span class='required'></span></label>
                                            <div class='col-md-5 col-sm-5 col-xs-12'>
                                                <textarea class='form-control' id="e_saran" name='e_saran' maxlength='300' readonly><?PHP echo $psaran; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <?PHP
                                        if (!empty($pimages_pl)) {
                                        ?>
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><?PHP echo $fketttdfoto; ?> <span class='required'></span></label>
                                                <div class='col-md-5 col-sm-5 col-xs-12'>
                                                    <?PHP
                                                        if (file_exists("../../../".$ffolderfile2."/".$pimages_pl)) {
                                                            $pnamafilefolder=$ffolderfile2."".$pimages_pl;
                                                        }else{
                                                            $pnamafilefolder=$ffolderfile."".$pimages_pl;
                                                        }
                                                        //echo "<img class='img_ttdfoto' src='$ffolderfile/$pimages_pl' width='140px' height='150px' data-toggle='modal' data-target='#myModalImages' onClick=\"ShowDataFotoTTD('$pittd', '$pimages_pl')\" />";
														echo "<img class='img_ttdfoto' src='$pnamafilefolder' width='140px' height='150px' data-toggle='modal' data-target='#myModalImages' onClick=\"ShowDataFotoTTD('$pittd', '$pimages_pl')\" />";
                                                    ?>
                                                </div>
                                            </div>
                                        <?PHP
                                        }
                                        ?>
                                        
                                        <?PHP
                                        
                                        echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>";
                                            echo "<div class='col-md-7 col-sm-7 col-xs-12'>";
                                            
                                                echo "<span id='div_sts'>";
                                                if (!empty($ptglatasan1) AND !empty($patasan1)) {
                                                    echo "<b>Sudah Approve SPV/AM : </b>$pnmatasan1<br/>Tgl : $ptglatasan1<br/><br/>";
                                                }
                                                if (!empty($ptglatasan2) AND !empty($patasan2)) {
                                                    echo "<b>Sudah Approve DM : </b>$pnmatasan2<br/>Tgl : $ptglatasan2<br/><br/>";
                                                }
                                                if (!empty($ptglatasan3) AND !empty($patasan3)) {
                                                    echo "<b>Sudah Approve SM : </b>$pnmatasan3<br/>Tgl : $ptglatasan3<br/><br/>";
                                                }
                                                if (!empty($ptglatasan4) AND !empty($patasan4)) {
                                                    echo "<b>Sudah Approve GSM : </b>$pnmatasan4<br/>Tgl : $ptglatasan4<br/><br/>";
                                                }
                                                echo "</span>";
                                                
                                            echo "</div>";
                                        echo "</div>";
                                        
                                        if ($pidgroup=="1" OR $pidgroup=="24") {
                                                echo "<div class='form-group'>";
                                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>";
                                                    echo "<div class='col-md-7 col-sm-7 col-xs-12'>";
                                                        echo "<button type='button' id='btnakv' class='btn btn-info add-aktv' onclick=\"\">Approve</button>";
                                                    echo "</div>";
                                                echo "</div>";
                                        }else{
                                            
                                            if ($pwwnangspv==true OR $pwwnangdm==true OR $pwwnangsm==true OR $pwwnanggsm==true) {
                                                $pwwnang_="";
                                                if ($pidjbt=="10" OR $pidjbt=="18") {
                                                    $pwwnang_="SPV";
                                                }elseif ($pidjbt=="08") {
                                                    $pwwnang_="DM";
                                                }elseif ($pidjbt=="20") {
                                                    $pwwnang_="SM";
                                                }elseif ($pidjbt=="05") {
                                                    $pwwnang_="GSM";
                                                }
                                                echo "<div class='form-group'>";
                                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>";
                                                    echo "<div class='col-md-7 col-sm-7 col-xs-12'>";
                                                        if (empty($apvby_)) {
                                                            echo "Anda Tidak Bisa Approve, silakan login ulang...";
                                                        }else{
                                                            //belum approve atasan sebelumnya
                                                            if ($pbolehapprove==true) {
                                                                echo "<button type='button' id='btnakv' class='btn btn-info add-aktv' onclick=\"Disp_ApproveData('$pwwnang_', '$apvby_', '$pidinput', '$pkryid', '$ptgl', '$pudoktid')\">Approve</button>";
                                                            }else{
                                                                if ($psudahmelewatihari==true) {
                                                                    echo "<span style='color:red;'><b>Tidak bisa approve, karena sudah melewati batas hari approve.<br/>Maksimal 3 hari</b></span>";
                                                                }
                                                            }

                                                        }
                                                    echo "</div>";
                                                echo "</div>";
                                            }
                                            
                                        }
                                        ?>

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
            <button type='button' class='btn btn-default' data-dismiss='modal' id="btnclose">Close</button>
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
    
    <script>
        function Disp_ApproveData(iapvby, ikryapv, inourut, ikryid, itgl, idoktid) {
            //alert(iapvby+", "+ikryapv+", "+inourut);
            
            var pText_="Apakah akan approve data...?";
            var r=confirm(pText_)
            if (r==false) {
                return false;
            }
            
            $.ajax({
                type:"post",
                url:"module/dkd/dkd_reportpalnreal/simpanapv.php?module=simpanapv",
                data:"uapvby="+iapvby+"&ukryapv="+ikryapv+"&unourut="+inourut+"&ukryid="+ikryid+"&utgl="+itgl+"&udoktid="+idoktid,
                success:function(data){
                    var tconfrm_d = myTrim(data);
                    var iberhasil=tconfrm_d.substring(0, 8);
                    
                    if (iberhasil=="berhasil") {
                        
                        $.ajax({
                            type:"post",
                            url:"module/dkd/dkd_reportpalnreal/simpanapv.php?module=caridataapv",
                            data:"uapvby="+iapvby+"&ukryapv="+ikryapv+"&unourut="+inourut+"&ukryid="+ikryid+"&utgl="+itgl+"&udoktid="+idoktid,
                            success:function(data){
                                var tconfrm_g = myTrim(data);
                                var iberhasil_g=tconfrm_g.substring(0, 5);
                                
                                if (iberhasil_g=="GAGAL" || iberhasil_g=="") {
                                    alert("Tidak ada data yang diapprove");
                                }else{
                                    //alert(data);
                                    document.getElementById("btnclose").click();
                                }
                                
                            }
                        });
                        
                    }else{
                        alert("Gagal Approve...");
                    }
                    
                    //window.location.reload(true);
                    /*
                    alert(data);
                    
                    $.ajax({
                        type:"post",
                        url:"module/dkd/dkd_reportpalnreal/simpanapv.php?module=caridataapv",
                        data:"uapvby="+iapvby+"&ukryapv="+ikryapv+"&unourut="+inourut+"&ukryid="+ikryid+"&utgl="+itgl+"&udoktid="+idoktid,
                        success:function(data){
                            $("#div_sts").html(data);
                        }
                    });
                    */
                    
                }
            });
            
        }
        
        function myTrim(x) {
            return x.replace(/^\s+|\s+$/gm,'');
        }
    
    </script>

<?PHP
mysqli_close($cnmy);
?>