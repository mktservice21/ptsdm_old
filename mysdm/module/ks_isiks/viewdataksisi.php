<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdataaptdr") {
    $pidkar=$_POST['uidkry'];
    $piddokt=$_POST['uiddr'];
    $piddokt2=$_POST['uidapt2'];
    
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $query = "select idapotik as idapotik, aptid as aptid, nama as nama, apttype as apttype from hrd.mr_apt where srid='$pidkar' and IFNULL(aktif,'')<>'N' order by nama";
    $result = mysqli_query($cnit, $query);
    $record = mysqli_num_rows($result);
    
    if ((DOUBLE)$record<=0) echo "<option value='' selected>--Pilih--</option>";
    
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result);
        
        $papotikid  = $row['idapotik'];
        $aptid  = $row['aptid'];
        $nama = $row['nama'];
        if ($nama<>"") {
            if ($aptid==$piddokt2)
                echo "<option value=\"$papotikid\" selected>$nama - $papotikid</option>";
            else
                echo "<option value=\"$papotikid\">$nama - $papotikid</option>";
        }
    }
    
    mysqli_close($cnit);
}elseif ($pmodule=="viewdatacndr") {
    $pidkar=$_POST['uidkry'];
    $piddokt=$_POST['uiddr'];
    $pbulan=$_POST['ubln'];
    $pbulan = date('Y-m-d', strtotime($pbulan));
    
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $cn_cn="";
    $query_sa = "select cn as cn from hrd.cn where karyawanid='$pidkar' and dokterid='$piddokt' and tgl<='$pbulan' order by tgl desc"; //echo"$query_sa";
    $result_sa = mysqli_query($cnit, $query_sa);
    $num_results_sa = mysqli_num_rows($result_sa);
    if ($num_results_sa) {
        $row_sa = mysqli_fetch_array($result_sa);
        $cn_cn = $row_sa['cn']; 
        if ((DOUBLE)$cn_cn==0) $cn_cn="";
    }
        
    
    $cn_dk="";
    $query_dk = "select cn as cn from hrd.mr_dokt where karyawanid='$pidkar' and dokterid='$piddokt'"; 
    $result_dk = mysqli_query($cnit, $query_dk);
    $num_results_dk = mysqli_num_rows($result_dk);
    if ($num_results_dk) {
        $row_dk = mysqli_fetch_array($result_dk);
        $cn_dk = $row_dk['cn'];
        if ((DOUBLE)$cn_dk==0) $cn_dk="";
    }	
	
	
    if ($cn_cn == '') {
        $cn = $cn_dk;
    } else {
        $cn = $cn_cn;
    }
    if (empty($cn)) $cn=0;
    
    mysqli_close($cnit);
    
    echo $cn;
    
}elseif ($pmodule=="viewdatadrpilih") {
    $pidkar=$_POST['uidkry'];
    
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $piddoktpilih="";
    if (!empty($_SESSION['KSDTKSDOK'])) $piddoktpilih = $_SESSION['KSDTKSDOK'];
    
    $query ="select distinct a.dokterid as dokterid, a.nama as nama, a.alamat1 as alamat1, a.alamat2 as alamat2 "
            . " from hrd.dokter as a JOIN hrd.mr_dokt as b on a.dokterid=b.dokterid WHERE b.karyawanid='$pidkar' ORDER BY a.nama";
    
    $result = mysqli_query($cnit, $query);
    $record = mysqli_num_rows($result);
    
    if ((DOUBLE)$record<=0) echo "<option value='' selected>--Pilih--</option>";
    
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result);
        
        $doktid  = $row['dokterid'];
        $nama = $row['nama'];
        if ($nama<>"") {
            if ($doktid==$piddoktpilih)
                echo "<option value=\"$doktid\" selected>$nama - $doktid</option>";
            else
                echo "<option value=\"$doktid\">$nama - $doktid</option>";
        }
    }
    
    mysqli_close($cnit);
    
}elseif ($pmodule=="cekdatasudahada") {
    $pidkar=$_POST['ukry'];
    $piddokt=$_POST['udoktid'];
    $papotikid=$_POST['uapotikid'];
    $pidapt=$_POST['uaptid'];
    $pbulan=$_POST['ubln'];
    $pbulan = date('Y-m', strtotime($pbulan));
    
    $pidgrpuser="";
    if (isset($_SESSION['GROUP'])) $pidgrpuser=$_SESSION['GROUP'];
    
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;

    //jika ks samasekali belum ada, maka tidak bisa input.
    $query  = "select distinct dokterid FROM hrd.ks1 WHERE srid='$pidkar' AND dokterid='$piddokt'";
    $tampil = mysqli_query($cnit, $query);
    $ketemu = mysqli_num_rows($tampil);
    if ((INT)$ketemu<=0) {
        
        //boleh input jika ada data exspsion
        $query = "select distinct dokterid FROM hrd.ks1_buka WHERE srid='$pidkar' AND dokterid='$piddokt' AND ifnull(aktif,'')<>'N'";
        $tampilb = mysqli_query($cnit, $query);
        $ketemub = mysqli_num_rows($tampilb);
        if ((INT)$ketemub>0) {
            mysqli_close($cnit);
            $bolehinput="boleh";
            echo $bolehinput;
            exit;
        }else{
            
            mysqli_close($cnit);
            $bolehinput="KS samasekali belum ada, silakan info ke MS untuk input...";
            echo $bolehinput;
            exit;
            
        }
    }
    
    
    $query  = "select distinct dokterid FROM hrd.ks1 WHERE bulan='$pbulan' AND srid='$pidkar' AND dokterid='$piddokt' AND idapotik='$papotikid'";
    $result = mysqli_query($cnit, $query);
    $record = mysqli_num_rows($result);
    
    $bolehinput="boleh";
    if ((DOUBLE)$record>0) $bolehinput="Sudah ada data... Tidak bisa diubah / hapus";
    if ($pidgrpuser=="1" OR $pidgrpuser=="24") $bolehinput="boleh";
    
    
    mysqli_close($cnit);
    
    echo $bolehinput;
    
}elseif ($pmodule=="viewdatapilihbulan") {
    
    $pidkar=$_POST['uidkry'];
    $piddokt=$_POST['uiddr'];
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $cnit=$cnmy;
    
    $pfeldbln="cbln01x";
    
    $hari_ini = date("Y-m-d");
    $pbulanpilih = date('F Y', strtotime($hari_ini));

    $piltgl="";
    $query = "select * from hrd.ks1_buka where srid='$pidkar' AND dokterid='$piddokt' AND IFNULL(aktif,'')='Y'";
    $tampil = mysqli_query($cnit, $query);
    $ketemu = mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $row = mysqli_fetch_array($tampil);
        $piltgl=$row['bulan'];
        if ($piltgl=="0000-00-00") $piltgl="";
    }
    
    
    if (!empty($piltgl)) $ptgl_mulai_sl=$piltgl;
    else $ptgl_mulai_sl  = '2020-10-01';
    $ptgl_selesai_sl=date("Y-m-01");
    
    $pblnselish=CariSelisihPeriodeDua($ptgl_mulai_sl, $ptgl_selesai_sl);
    if (empty($pblnselish)) $pblnselish=0;
    $pblnselish="-".$pblnselish."M";
    
    
    //$pblnselish="-1M";
    
    
    ?>
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
        <input type="hidden" class="form-control" id='e_bulan2' name='e_bulan2' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pbulanpilih; ?>' Readonly>
        <div class='col-md-4'>
            <div class='input-group date' id='<?PHP echo $pfeldbln; ?>'>
                <input type="text" class="form-control" id='e_bulan' name='e_bulan' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pbulanpilih; ?>' Readonly>
                <span class='input-group-addon'>
                    <span class='glyphicon glyphicon-calendar'></span>
                </span>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {

            $('#e_bulan').datepicker({
                showButtonPanel: true,
                changeMonth: true,
                changeYear: true,
                dateFormat: 'MM yy',
                //minDate: '-3M',
                minDate: '<?PHP echo $pblnselish; ?>',
                onSelect: function(dateStr) {

                },
                onClose: function() {
                    var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                    ShowDataCN();
                },

                beforeShow: function() {
                    if ((selDate = $(this).val()).length > 0) 
                    {
                        iYear = selDate.substring(selDate.length - 4, selDate.length);
                        iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                        $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                        $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                    }
                }

            });
        });
    </script>

    <style>
        .ui-datepicker-calendar {
            display: none;
        }
    </style>
    
    <?PHP
    mysqli_close($cnit);
}

?>