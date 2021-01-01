<?PHP
    include "config/cek_akses_modul.php";
    include "config/koneksimysqli_it.php";
    
	//server 2020 11 19
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_pertama2 = date('F Y', strtotime($hari_ini));
    
    $pidkaryawan=$_SESSION['IDCARD'];
    if (!empty($_SESSION['KSDTKSKRY'])) $pidkaryawan = $_SESSION['KSDTKSKRY'];
    
    $piddoktpilih="";
    if (!empty($_SESSION['KSDTKSDOK'])) $piddoktpilih = $_SESSION['KSDTKSDOK'];
    if (!empty($_SESSION['KSDTKSBLN01'])) $tgl_pertama = $_SESSION['KSDTKSBLN01'];
    if (!empty($_SESSION['KSDTKSBLN02'])) $tgl_pertama2 = $_SESSION['KSDTKSBLN02'];
    
    
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fjbtid=$_SESSION['JABATANID'];
    $fgroupid=$_SESSION['GROUP'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    

    //$fkaryawan="0000000158"; $fjbtid="05";//hapussaja
    
    $pfilterkaryawan="";
    $pfilterkaryawan2="";
    $pfilterkry="";
    
    if ($fjbtid=="38" OR $fjbtid=="05" OR $fjbtid=="20" OR $fjbtid=="08" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {
        
        $pnregion="";
        if ($fkaryawan=="0000000159") $pnregion="T";
        elseif ($fkaryawan=="0000000158") $pnregion="B";
        $pfilterkry=CariDataKaryawanByCabJbt($fkaryawan, $fjbtid, $pnregion);
        
        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
        
    }
    
    
    //echo "karyawan : $pfilterkaryawan<br/>karyawan2 : $pfilterkaryawan2<br/>";exit;

    
?>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Kartu Status";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/ks_isiks/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >
                    
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        //KlikDataTabel();
                        ShowDataDokter();
                    } );

                    function KlikDataTabel() {
                        var eaksi = "module/ks_isiks/aksi_isiks.php";
                        var ekryid=document.getElementById('cb_karyawan').value;
                        var eidpilihkry=document.getElementById('e_idkaryawanpilih').value;
                        var eiddokt=document.getElementById('cb_dokerid').value;
                        var ebln=document.getElementById('bulan1').value;
                        var ebln2=document.getElementById('bulan2').value;
                        
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/ks_isiks/viewdatatabel_ksisi.php?module=viewdata",
                            data:"ukryid="+ekryid+"&uidpilihkry="+eidpilihkry+"&uiddokt="+eiddokt+"&ubln="+ebln+"&ubln2="+ebln2+"&uaksi="+eaksi,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function ShowDataDokter(){
                        var eidkry =document.getElementById('cb_karyawan').value;

                        $.ajax({
                            type:"post",
                            url:"module/ks_isiks/viewdataksisi.php?module=viewdatadrpilih",
                            data:"uidkry="+eidkry,
                            success:function(data){
                                $("#cb_dokerid").html(data);
                                //KosongDataInput();
                            }
                        });
                    }
                    
                    function KosongDataInput(){
                        $("#c-data").html("");
                    }
                </script>

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

                            <div class='col-sm-2'>
                                Karyawan (MR) 
                                <div class="form-group">
                                    
                                  <input type='hidden' id='e_idkaryawanpilih' name='e_idkaryawanpilih' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pfilterkaryawan2; ?>' >
                                  <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="ShowDataDokter()" data-live-search="true">
                                      <?PHP 
                                            //echo "<option value='' selected>-- Pilihan --</option>";
                                      /*
                                            $query = "select karyawanId as karyawanid, nama as nama From hrd.karyawan
                                                WHERE 1=1 ";
                                            if (!empty($pfilterkaryawan)) {
                                                $query .= " AND (karyawanid IN $pfilterkaryawan OR karyawanid='$pidkaryawan') ";
                                            }else{
                                                $query .=" AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                        . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                        . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                        . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                        . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                            }
                                            $query .= " ORDER BY nama";
                                            */
                                            
                                            
                                            
                                            $query = "select distinct a.karyawanid as karyawanid, b.nama as nama "
                                                    . " from hrd.mr_dokt as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid ";
                                            if (!empty($pfilterkaryawan)) {
                                                $query .= " AND (a.karyawanid IN $pfilterkaryawan OR a.karyawanid='$pidkaryawan') ";
                                            }
                                            $query .= " ORDER BY b.nama";
                                            
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pkaryid=$z['karyawanid'];
                                                $pkarynm=$z['nama'];
                                                $pkryid=(INT)$pkaryid;
                                                if ($pkaryid==$pidkaryawan) 
                                                    echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                else
                                                    echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                
                                            }

                                      ?>
                                  </select>
                                    
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Dokter
                                <div class="form-group">
                                    
                                  <select class='form-control input-sm' id='cb_dokerid' name='cb_dokerid' onchange="KosongDataInput()" data-live-search="true">
                                      <?PHP 
                                        if (empty($pidkaryawan)) {
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                        }else{
                                            $query ="select distinct a.dokterid as dokterid, a.nama as nama, a.alamat1 as alamat1, a.alamat2 as alamat2 "
                                                    . " from hrd.dokter as a JOIN hrd.mr_dokt as b on a.dokterid=b.dokterid WHERE b.karyawanid='$pidkaryawan' ORDER BY a.nama";

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
                                        }
                                      ?>
                                  </select>
                                    
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                Bulan
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01'>
                                        <input type='text' id='bulan1' name='bulan1' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                s/d.
                                <div class="form-group">
                                    <div class='input-group date' id='cbln02'>
                                        <input type='text' id='bulan2' name='bulan2' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama2; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                               </div>
                           </div>
                       </form>

                        <div id='loading'></div>
                        <div id='c-data'>

                        </div>

                    </div>
                </div>
                
                
                

                <?PHP

            break;

            case "tambahbaru":
                include "tambahdtks.php";
            break;

            case "editdata":
                include "tambahdtks.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }

</style>

<script>
    // SCROLL
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("myBtn").style.display = "block";
        } else {
            document.getElementById("myBtn").style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
    // END SCROLL
</script>
