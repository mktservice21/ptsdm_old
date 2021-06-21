<?php
    $bulan_array=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
        "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember");

    $hari_array = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );
    
$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];

$pcabid_pl=$_SESSION['RLWEKPLNCAB'];

$pidinput="";

$act="update";

include "config/fungsi_ubahget_id.php";

$pidinput_ec="new";
$pidinput = $pidinput_ec;
$pnewdate="";

$platitude="";
$plongitude="";

$hari_ini = date('Y-m-d');
$ntgl=$hari_ini;

$nkryid="";
$ptanggal="";
$pdokterid="";
$pdokternm="";
$pdokter="";


$ntanggal = date('l d F Y', strtotime($ntgl));

$xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
$xtgl= date('d', strtotime($ntgl));
$xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
$xthn= date('Y', strtotime($ntgl));

$ptglhari="$xhari, $xtgl $xbulan $xthn";

?>



<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
    
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='hidden' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_tanggal' name='e_tanggal' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptanggal; ?>' Readonly>
                                        <input type='text' id='e_tgl' name='e_tgl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptglhari; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_kryid' name='e_kryid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nkryid; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <?PHP
                                if ($pidact=="tambahbaru") {
                                ?>
                                
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                        <div class='col-xs-4'>
                                            <select class='soflow' name='cb_cabid' id='cb_cabid' onchange="ShowDataDokter('1', '', '')">
                                                <?php
                                                if ($pidgroup=="1" OR $pidgroup=="24") {
                                                    $query = "select iCabangId as icabangid, nama as nama_cabang from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
                                                    $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
                                                    $query .=" order by nama, iCabangId";
                                                }else{
                                                    if ($pidjbt=="10" OR $pidjbt=="18") {
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.ispv0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }elseif ($pidjbt=="08") {
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.idm0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }elseif ($pidjbt=="20") {
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.ism0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }else{
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.imr0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }
                                                }
                                                $tampilket= mysqli_query($cnmy, $query);
                                                $ketemu=mysqli_num_rows($tampilket);
                                                if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";
                                                $cno=1; $ppilihcab=""; $pbelum=false;
                                                while ($du= mysqli_fetch_array($tampilket)) {
                                                    $nidcab=$du['icabangid'];
                                                    $nnmcab=$du['nama_cabang'];
                                                    $nidcab_=(INT)$nidcab;

                                                    if ($nidcab==$pcabid_pl){
                                                        echo "<option value='$nidcab' selected>$nnmcab ($nidcab_)</option>";
                                                        $ppilihcab=$nidcab;
                                                        $pbelum=true;
                                                    }else{
                                                        echo "<option value='$nidcab'>$nnmcab ($nidcab_)</option>";
                                                        if ($cno==1 AND $pbelum==false) $ppilihcab=$nidcab;
                                                    }

                                                    $cno++;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>User <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <input type='hidden' id='e_userdokt' name='e_userdokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdokter; ?>' Readonly>

                                            <select class='soflow form-control s2' name='e_doktid' id='e_doktid' onchange="">
                                                <?php
                                                echo "<option value='' selected>-- Pilih --</option>";
                                                //$ipcabid="0000000094";
                                                $query = "select `id` as iddokter, namalengkap, gelar, spesialis from dr.masterdokter WHERE 1=1 ";
                                                $query .=" AND icabangid='$ppilihcab' ";
                                                $query .=" order by namalengkap, `id`";
                                                //$query .=" limit 100";
                                                $tampilket= mysqli_query($cnmy, $query);
                                                while ($du= mysqli_fetch_array($tampilket)) {
                                                    $niddokt=$du['iddokter'];
                                                    $nnmdokt=$du['namalengkap'];
                                                    $ngelar=$du['gelar'];
                                                    $nspesial=$du['spesialis'];

                                                    if (!empty($pnmdokt)) $pnmdokt=rtrim($pnmdokt, ',');

                                                    echo "<option value='$niddokt'>$nnmdokt ($ngelar), $nspesial - $niddokt</option>";

                                                }
                                                ?>
                                            </select>

                                        </div>
                                    </div>
                                
                                <?PHP
                                }else{
                                ?>
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>User <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <input type='hidden' id='e_doktid' name='e_doktid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdokterid; ?>' Readonly>
                                            <input type='text' id='e_userdokt' name='e_userdokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdokter; ?>' Readonly>
                                        </div>
                                    </div>
                                
                                <?PHP
                                }
                                ?>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><u>Lokasi</u> <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <p id="d_lokasi"></p>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Latitude <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_latitude' name='e_latitude' class='form-control col-md-7 col-xs-12' value='<?PHP echo $platitude; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Longitude <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_longitude' name='e_longitude' class='form-control col-md-7 col-xs-12' value='<?PHP echo $plongitude; ?>' Readonly>
                                    </div>
                                </div>
                                
                            </div>
                            
                                
                                
                        </div>
                    </div>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                                echo "<div class='col-sm-5'>";
                                include "module/dkd/dkd_ttddkdvisit/ttd_dkdvisit.php";
                                echo "</div>";
                                
                            ?>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                </form>
                
            </div>
            
        </div>
        
    </div>
    
    
</div>


<script>
    $(document).ready(function() {
        getLocation();
    } );
    
    var x = document.getElementById("d_lokasi");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else { 
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position) {
        document.getElementById("e_latitude").value=position.coords.latitude;
        document.getElementById("e_longitude").value=position.coords.longitude;
    }
    
    function ShowDataDokter(sKey, incab, indokt){
        var eidcan =document.getElementById('cb_cabid').value;
        
        $.ajax({
            type:"post",
            url:"module/dkd/viewdatadkd.php?module=viewdatadoktercabang",
            data:"uidcab="+eidcan+"&ukdcab="+incab+"&ukddokt="+indokt+"&skode="+sKey,
            success:function(data){
                $("#e_doktid").html(data);
            }
        });
    }
    
</script>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .ui-datepicker-calendar {
        display: none;
    }
    
    .divnone {
        display: none;
    }
    #datatableuc th {
        font-size: 12px;
    }
    #datatableuc td { 
        font-size: 12px;
        padding: 3px;
        margin: 1px;
    }
</style>



<link href="module/dkd/select2.min.css" rel="stylesheet" type="text/css" />
<script src="module/dkd/select2.min.js"></script>
<script>
$(document).ready(function() {
        $('.s2').select2();
    });
</script>