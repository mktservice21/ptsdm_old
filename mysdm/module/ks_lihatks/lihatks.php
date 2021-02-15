<?PHP
    
    include "config/cek_akses_modul.php";
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $pbulanpilih = date('F Y', strtotime($hari_ini));
    
    
    
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
    //$fjbtid=="38" OR $fjbtid=="33" OR 
    if ($fjbtid=="38" OR $fjbtid=="33" OR $fjbtid=="05" OR $fjbtid=="20" OR $fjbtid=="08" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {
        
        $pnregion="";
        if ($fkaryawan=="0000000159") $pnregion="T";
        elseif ($fkaryawan=="0000000158") $pnregion="B";
        $pfilterkry=CariDataKaryawanByCabJbt($fkaryawan, $fjbtid, $pnregion);
        
        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
        
    }elseif ($fjbtid=="38x" OR $fjbtid=="33x") {
        $pnregion="";
        $pfilterkry=CariDataKaryawanByRsmAuthCNIT($fkaryawan, $fjbtid, $pnregion);
        
        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
    }
    
    
    
    $pfiltercabpilih="";
        
    if ($fjbtid=="15" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="08" OR $fjbtid=="38" OR $fkaryawan=="0000000158" OR $fkaryawan=="0000000159") {
        if ($fkaryawan=="0000000158") {
            $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM MKT.icabang WHERE region='B'";
        }elseif ($fkaryawan=="0000000159") {
            $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM MKT.icabang WHERE region='T'";
        }else{
            if ($fjbtid=="15") {
                $query_cab = "select distinct icabangid, areaid, divisiid FROM MKT.imr0 WHERE karyawanid='$fkaryawan'";
            }elseif ($fjbtid=="10" OR $fjbtid=="18") {
                $query_cab = "select distinct icabangid, areaid, divisiid FROM MKT.ispv0 WHERE karyawanid='$fkaryawan'";
            }elseif ($fjbtid=="08") {
                $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM MKT.idm0 WHERE karyawanid='$fkaryawan'";
            }elseif ($fjbtid=="38") {
                $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM hrd.rsm_auth WHERE karyawanid='$fkaryawan'";
            }
        }
        if (!empty($query_cab)) {
            $tampil= mysqli_query($cnmy, $query_cab);
            while ($rs= mysqli_fetch_array($tampil)) {
                $vbicabangid=$rs['icabangid'];
                $vbareaid=$rs['areaid'];
                $vbdivisi=$rs['divisiid'];

                if (!empty($vbicabangid)) $pidcabangpil=$vbicabangid;

                if (strpos($pfiltercabpilih, $vbicabangid)==false) $pfiltercabpilih .="'".$vbicabangid."',";

            }
        }

    }elseif ($fjbtid=="38") {
        
    }
    
    
    if (!empty($pfiltercabpilih)) {
        $pfiltercabpilih="(".substr($pfiltercabpilih, 0, -1).")";
    }
    $pcabangselected="";
    
    //echo "karyawan : $pfilterkaryawan<br/>karyawan2 : $pfilterkaryawan2, cab : $pfiltercabpilih<br/>";exit;
    
    
?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Lihat Kartu Status</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='data_form01' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <span hidden><button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button></span>
                            <?PHP
                            if ($_SESSION['MOBILE']!="Y") {
                                echo "<span hidden><button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button></span>";
                            }
                            ?>
                            <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Cabang</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_cabang" name="cb_cabang" onchange="ShowDataKaryawan()">
                                                <?PHP                                                  
                                                
                                                    $nojm=1;
                                                    $query_cb = "select icabangid as icabangid, nama as nama, "
                                                            . " CASE WHEN IFNULL(aktif,'')='' then 'Y' else aktif end as aktif "
                                                            . " from MKT.icabang WHERE 1=1 ";
                                                    if ($pmygroupid=="24" or $pmygroupid=="1") {
                                                    }else{
                                                        if (!empty($pfiltercabpilih)) {
                                                            $query_cb .=" AND iCabangId IN $pfiltercabpilih ";
                                                        }
                                                    }
                                                    $query_cb .=" AND LEFT(nama,5) NOT IN ('OTC -', 'ETH -', 'PEA -')";
                                                    $query_cb .=" order by CASE WHEN IFNULL(aktif,'')='' then 'Y' else aktif end desc, nama";
                                                    $tampil = mysqli_query($cnmy, $query_cb);
                                                    
                                                    $ketemu= mysqli_num_rows($tampil);
                                                    echo "<option value='' selected>-- Pilih --</option>";
                                                    $pketaktif=false;
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pcabid=$z['icabangid'];
                                                        $pcabnm=$z['nama'];
                                                        $pstsaktif=$z['aktif'];
                                                        $pcbid=(INT)$pcabid;
                                                        
                                                        if ($pstsaktif=="N" AND $nojm<=1) { $pketaktif=true; $nojm++; }
                                                        
                                                        if ($pketaktif==true) {
                                                            echo "<option value=''>&nbsp;</option>";
                                                            echo "<option value=''>-- non aktif --</option>";
                                                            $pketaktif=false;
                                                        }
                                                        if ($fjbtid=="15" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="08") {
                                                            echo "<option value='$pcabid' selected>$pcabnm ($pcbid)</option>";
                                                            $pcabangselected=$pcabid;
                                                        }else
                                                            echo "<option value='$pcabid'>$pcabnm ($pcbid)</option>";
                                                    }
                                                 
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Karyawan</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                                <?PHP
                                                    $query = "select karyawanId, nama From hrd.karyawan
                                                        WHERE 1=1 ";
                                                    if (!empty($pfilterkaryawan)) {
                                                        $query .= " AND karyawanid IN $pfilterkaryawan ";
                                                    }else{
                                                        /*
                                                        $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                        $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                         * 
                                                         */
                                                        $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                        $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                                    }

                                                    $query .= " ORDER BY nama";
                                                    
                                                    
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    
                                                    $ketemu= mysqli_num_rows($tampil);
                                                    if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";
                                                    
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pkaryid=$z['karyawanId'];
                                                        $pkarynm=$z['nama'];
                                                        $pkryid=(INT)$pkaryid;
                                                        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Sampai dengan bulan</b>
                                        <div class="form-group">
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_bulan' name='e_bulan' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pbulanpilih; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Dokter</b><br/>
                                        <div class="form-group">
                                            
                                            <div class='input-group '>
                                                <span class='input-group-btn'>
                                                    <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataDokter('e_iddokt', 'e_nmdokt')">Pilih!</button>
                                                </span>
                                                <input type='text' class='form-control' id='e_nmdokt' name='e_nmdokt' value='' Readonly>
                                                <input type='hidden' class='form-control' id='e_iddokt' name='e_iddokt' value='' Readonly>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-6'>
                                        <b>Hilangkan KI</b>
                                        <div class="form-group">
                                            <input type="checkbox" id='chk_hki' name='chk_hki' value='HKI'>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Pilih Dokter</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_drks" name="cb_drks">
                                                <?PHP
                                                    echo "<option value='' selected>-- All --</option>";
                                                    echo "<option value='HKS'>Hanya Dokter KS</option>";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='col-sm-10'>
                                    <small>&nbsp;</small>
                                   <div class="form-group">
                                       <button type='button' class='btn btn-dark btn-xs' onclick='ListDataDokter()'>List Data Dokter</button>
                                       <?PHP
                                       if ($fjbtid=="38" OR ($fgroupid=="1" OR $fgroupid=="24") ) {
                                       ?>
                                            <button type='button' class='btn btn-warning btn-xs' onclick='ListDataAptKosong()'>List Data Dokter Apotik Kosong</button>
                                       <?PHP
                                       }
                                       ?>
                                   </div>
                               </div>

                            </div>
                        </div>           
                    </div>

                </div>
            </div>
        </form>

        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                <div id='loading'></div>
                <div id='c-data'>

                </div>
            </div>
        </div>
                        
    </div>
    <!--end row-->
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<script>
    
    function ShowDataKaryawan(){
        var eidcan =document.getElementById('cb_cabang').value;

        $.ajax({
            type:"post",
            url:"module/ks_lihatks/viewdata.php?module=viewdatakaryawan",
            data:"uidcab="+eidcan,
            success:function(data){
                $("#cb_karyawan").html(data);
            }
        });
    }
    
    function ListDataDokter() {
        var eidcab=document.getElementById('cb_cabang').value;
        var eidkry=document.getElementById('cb_karyawan').value;
        var estsdr =document.getElementById('cb_drks').value;
        
        if (eidkry=="") {
            alert("Karyawan harus diisi...");
            return false;
        }

        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");

        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/ks_lihatks/viewdatatabel_lihatdrmr.php?module="+module+"&idmenu="+idmenu+"&act="+act+"&uidkry="+eidkry+"&uidcab="+eidcab,
            data:"module="+module+"&uidkry="+eidkry+"&uidcab="+eidcab+"&ustsdr="+estsdr,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
    
    function ListDataAptKosong() {
        var eidcab=document.getElementById('cb_cabang').value;
        var eidkry=document.getElementById('cb_karyawan').value;
        var estsdr =document.getElementById('cb_drks').value;
        
        if (eidkry=="") {
            alert("Karyawan harus diisi...");
            return false;
        }

        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");

        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/ks_lihatks/viewdatatabel_lihatdrmrapt.php?module="+module+"&idmenu="+idmenu+"&act="+act+"&uidkry="+eidkry+"&uidcab="+eidcab,
            data:"module="+module+"&uidkry="+eidkry+"&uidcab="+eidcab+"&ustsdr="+estsdr,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
                    
    function disp_confirm(pText)  {
        var eidkry =document.getElementById('cb_karyawan').value;
        var eiddok =document.getElementById('e_iddokt').value;
        
        if (eidkry=="") {
            alert("karyawan harus diisi...!!!");
            return false;
        }
        
        if (eiddok=="") {
            alert("dokter harus diisi...!!!");
            return false;
        }
        
        if (pText == "excel") {
            document.getElementById("data_form01").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("data_form01").submit();
            return 1;
        }else{
            document.getElementById("data_form01").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("data_form01").submit();
            return 1;
        }
    }
</script>


<script>
    function getDataDokter(data1, data2){
        var eidkry =document.getElementById('cb_karyawan').value;
        
        $.ajax({
            type:"post",
            url:"module/ks_lihatks/viewdata_lks.php?module=viewdatadokter",
            data:"udata1="+data1+"&udata2="+data2+"&uidkry="+eidkry,
            success:function(data){
                $("#myModal").html(data);
                document.getElementById(data1).value="";
                document.getElementById(data2).value="";
            }
        });
    }
    
    function getDataModalDokter(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
    }
    
    function HapudDataDokter(){
        document.getElementById('e_iddokt').value="";
        document.getElementById('e_nmdokt').value="";
    }
</script>