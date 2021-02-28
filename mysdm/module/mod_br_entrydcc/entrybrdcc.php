<?PHP
	//server 2020 10 20
	include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['FINDDPERENTY1'])) $tgl_pertama = $_SESSION['FINDDPERENTY1'];
    if (!empty($_SESSION['FINDDPERENTY2'])) $tgl_akhir = $_SESSION['FINDDPERENTY2'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fidgrouppil=$_SESSION['GROUP'];
    
    $ppilkryuser=$fkaryawan;
    if (!empty($_SESSION['FINUSPL'])) $ppilkryuser = $_SESSION['FINUSPL'];
    
    $pnmactid="";
    if (isset($_GET['act'])) $pnmactid=$_GET['act'];


    
    if ($pnmactid=="tambahbaru") {
    }elseif ($pnmactid=="editdata") {
    }else{
        
        $query = "CALL hrd.proses_br0_karyawan_new()";
        
        //$query = "CALL hrd.proses_isi_temp_karyawancab()";
        
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }    

        $query = "select * from hrd.tempkaryawandccdss_inp";
        $tampilkry= mysqli_query($cnmy, $query);
        $ketemukry= mysqli_num_rows($tampilkry);
        if ($ketemukry==0) {
            $query = "INSERT INTO hrd.tempkaryawandccdss_inp "
                    . " select karyawanId, nama, jabatanId, iCabangId, aktif, tglmasuk, tglkeluar, iCabangId icabangkaryawan, atasanId, atasanId2 from hrd.karyawan "
                    . " WHERE karyawanId NOT IN (select distinct IFNULL(karyawanId,'') FROM hrd.tempkaryawandccdss_inp)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy);
            if (!empty($erropesan)) { 
                exit;
            }
        }
    
    }
    
    /*
    $query = "DELETE FROM hrd.tempkaryawandccdss_inp";
    mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query ="select krycab.karyawanId, b.nama, b.jabatanId, krycab.iCabangId, b.aktif, b.tglmasuk, b.tglkeluar, krycab.iCabangId icabangkaryawan, '' as atasanId, '' as atasanId2 from (
        select DISTINCT iCabangId, karyawanId from MKT.imr0
        UNION 
        select DISTINCT iCabangId, karyawanId from MKT.ispv0
        UNION 
        select DISTINCT iCabangId, karyawanId from MKT.idm0
        UNION 
        select DISTINCT iCabangId, karyawanId from MKT.ism0
        ) as krycab
        JOIN hrd.karyawan b on krycab.karyawanid=b.karyawanid";
    $query = "INSERT INTO hrd.tempkaryawandccdss_inp $query"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan)) { 
        exit;
    }
    
    
    $query = "INSERT INTO hrd.tempkaryawandccdss_inp "
            . " select karyawanId, nama, jabatanId, iCabangId, aktif, tglmasuk, tglkeluar, iCabangId icabangkaryawan, atasanId, atasanId2 from hrd.karyawan "
            . " WHERE karyawanId NOT IN (select distinct IFNULL(karyawanId,'') FROM hrd.tempkaryawandccdss_inp) AND divisiId <>'OTC' ";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy);
    //if (!empty($erropesan)) { 
   //     exit;
   // }
    
    $query = "UPDATE hrd.tempkaryawandccdss_inp a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid SET "
            . " a.icabangkaryawan=b.icabangid, a.atasanId=b.atasanId, a.atasanId2=b.atasanId2 WHERE IFNULL(b.icabangid,'')<>'' AND a.jabatanId IN ('08', '20')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan)) { 
        exit;
    }
    
    $query = "UPDATE hrd.tempkaryawandccdss_inp a JOIN MKT.ispv0 b on a.icabangid=b.icabangid SET "
            . " a.atasanId=b.karyawanid WHERE IFNULL(b.icabangid,'')<>'' AND a.jabatanId IN ('15') AND IFNULL(a.atasanId,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan)) {  exit; }
    
    $query = "UPDATE hrd.tempkaryawandccdss_inp a JOIN MKT.idm0 b on a.icabangid=b.icabangid SET "
            . " a.atasanId2=b.karyawanid WHERE IFNULL(b.icabangid,'')<>'' AND a.jabatanId IN ('15') AND IFNULL(a.atasanId2,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan)) {  exit; }
    
    $query = "UPDATE hrd.tempkaryawandccdss_inp a JOIN MKT.idm0 b on a.icabangid=b.icabangid SET "
            . " a.atasanId=b.karyawanid WHERE IFNULL(b.icabangid,'')<>'' AND a.jabatanId IN ('10', '18') AND IFNULL(a.atasanId,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan)) {  exit; }
    
    $query = "UPDATE hrd.tempkaryawandccdss_inp a JOIN MKT.ism0 b on a.icabangid=b.icabangid SET "
            . " a.atasanId2=b.karyawanid WHERE IFNULL(b.icabangid,'')<>'' AND a.jabatanId IN ('10', '18') AND IFNULL(a.atasanId2,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan)) {  exit; }
    
    
    */
    
?>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                if ($pnmactid=="tambahbaru") {
                    echo "Tambah Budget  Request DCC/DSS";
                }elseif ($pnmactid=="editdata") {
                    echo "Edit Budget  Request DCC/DSS";
                }else{
                    echo "Entry Budget  Request DCC/DSS";
                }
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_br_entrydcc/aksi_entrybrdcc.php";
        switch($_GET['act']){
            default:
                ?>
                <div class='modal fade' id='myModal' role='dialog'></div>
                
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        $("#c-data").html("");
                        $("#loading").html("");
                        var etipeproses=document.getElementById('cb_tipeisi').value;
                        if (etipeproses=="") {
                            PilihData1();
                        }else if (etipeproses=="A") {
                            PilihData2();
                        }else if (etipeproses=="B") {
                            PilihData3();
                        }
                    }

                    function PilihData1() {
                        var ket="";
                        var etipeproses=document.getElementById('cb_tipeisi').value;
                        var etgltipe=document.getElementById('cb_tgltipe').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi=document.getElementById('cb_divisi').value;
                        var eidkarpilih=document.getElementById('cb_karyawanid').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var act = urlku.searchParams.get("act");
                        var idmenu = urlku.searchParams.get("idmenu");

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entrydcc/viewdatatabel.php?module="+module+"&act="+act+"&idmenu="+idmenu+"&nmun="+idmenu,
                            data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uidc="+eidc+"&utipeproses="+etipeproses+"&uidkarpilih="+eidkarpilih,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function PilihData2() {
                        var etipeproses=document.getElementById('cb_tipeisi').value;
                        var etgltipe=document.getElementById('cb_tgltipe').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi=document.getElementById('cb_divisi').value;
                        var eidkarpilih=document.getElementById('cb_karyawanid').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var act = urlku.searchParams.get("act");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entrydcc/viewdatatabelreal.php?module="+module+"&act="+act+"&idmenu="+idmenu+"&nmun="+idmenu,
                            data:"utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uidc="+eidc+"&utipeproses="+etipeproses+"&uidkarpilih="+eidkarpilih,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function PilihData3() {
                        var etipeproses=document.getElementById('cb_tipeisi').value;
                        var etgltipe=document.getElementById('cb_tgltipe').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi=document.getElementById('cb_divisi').value;
                        var eidkarpilih=document.getElementById('cb_karyawanid').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var act = urlku.searchParams.get("act");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entrydcc/viewdatatabelsby.php?module="+module+"&act="+act+"&idmenu="+idmenu+"&nmun="+idmenu,
                            data:"utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uidc="+eidc+"&utipeproses="+etipeproses+"&uidkarpilih="+eidkarpilih,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    
                    function TambahDataInputPajak(eidbr){
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entrydcc/tambah_pajak.php?module=viewisipajak",
                            data:"uidbr="+eidbr,
                            success:function(data){
                                $("#myModal").html(data);
                            }
                        });
                    }
                    
                    
                    
                    function ProsesDataHapus(ket, noid, snodivi){

                        ok_ = 1;
                        if (ok_) {
                            if (snodivi=="") {
                                var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                            }else{
                                var r = confirm('Sudah Ada Nodivisi /no BR ('+snodivi+')...!!!\n\
Apakah akan melakukan proses '+ket+' ...?\n\
Status pada SPD akan berubah menjadi BATAL (merah)...');
                            }
                            if (r==true) {

                                var txt;
                                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                                    if (textket == null || textket == "") {
                                        txt = textket;
                                    } else {
                                        txt = textket;
                                    }
                                }

                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("demo-form2").action = "module/mod_br_entrydcc/aksi_entrybrdcc.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }



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
                        
                        <?PHP
                        $pildivgrp="";
                        if ($fidgrouppil!="1" AND $fidgrouppil!="24") $pildivgrp="hidden";
                        ?>
                        <div <?PHP echo $pildivgrp; ?> class='col-sm-1'>
                            Karyawan
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_karyawanid" name="cb_karyawanid">
                                    <?PHP
                                    $query = "select distinct b.karyawanId, b.nama from hrd.karyawan b where 1=1 ";//b.jabatanid NOT IN ('08', '10', '15', '18', '20') 
                                    $query .=" AND (b.karyawanId IN ('0000001043', '0000000148', '0000000566') OR b.karyawanid='$fkaryawan')";
                                    $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  "
                                            . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                            . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') "
                                            . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR') ";
                                    $query .=" ORDER BY b.nama ";
                                    $tampil = mysqli_query($cnmy, $query);
                                    echo "<option value=''>--Pilih--</option>";
                                    while ($ir=  mysqli_fetch_array($tampil)) {
                                        $iridkar=$ir['karyawanId'];
                                        $irnmkar=$ir['nama'];
                                        
                                        if ($iridkar==$ppilkryuser)
                                            echo "<option value='$iridkar' selected>$irnmkar</option>";
                                        else
                                            echo "<option value='$iridkar'>$irnmkar</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            Type Proses
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_tipeisi" name="cb_tipeisi">
                                    <option value="" selected>Input BR</option>
                                    <?PHP
                                    $sa=""; $sb=""; $sc="";
                                    if ($_SESSION['FINDDTIPE']=="A") $sa=" selected";
                                    if ($_SESSION['FINDDTIPE']=="B") $sb=" selected";
                                    if ($_SESSION['FINDDTIPE']=="C") $sc=" selected";
                                    ?>
                                    <option value="A" <?PHP echo $sa; ?>>Realisasi</option>
                                    <option value="B" <?PHP echo $sb; ?>>Input Tgl Rpt. SBY</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            Periode By
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                    <?PHP
                                    $sa=""; $sb=""; $sc=""; $sd=""; $se="";
                                    if ($_SESSION['FINDDTGLTIPE']=="1") $sa=" selected";
                                    if ($_SESSION['FINDDTGLTIPE']=="2") $sb=" selected";
                                    if ($_SESSION['FINDDTGLTIPE']=="3") $sc=" selected";
                                    if ($_SESSION['FINDDTGLTIPE']=="4") $sd=" selected";
                                    if ($_SESSION['FINDDTGLTIPE']=="5") $se=" selected";
                                    if (empty($_SESSION['FINDDTGLTIPE'])) $sd="selected"
                                    ?>
                                    <option value="1" <?PHP echo $sa; ?>>Last Input / Update</option>
                                    <option value="2" <?PHP echo $sb; ?>>Tanggal Transfer</option>
                                    <option value="3" <?PHP echo $sc; ?>>Tanggal Terima</option>
                                    <option value="4" <?PHP echo $sd; ?>>Tanggal Input</option>
                                    <option value="5" <?PHP echo $se; ?>>Tanggal Rpt. SBY</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            Periode
                            <div class="form-group">
                                <div class='input-group date' id='tgl01'>
                                    <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                           <small>s/d.</small>
                           <div class="form-group">
                               <div class='input-group date' id='tgl02'>
                                   <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                   <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                   </span>
                               </div>
                           </div>
                       </div>
                        

                        <div class='col-sm-2'>
                            Divisi
                            <div class="form-group">
                                <?PHP
                                    $ppilihdiv=$_SESSION['FINDDDIV'];
                                    
                                    $sql=mysqli_query($cnmy, "SELECT DivProdId, nama FROM dbmaster.divprod where br='Y' AND DivProdId NOT IN ('OTHER', 'OTC', 'CAN') order by nama");
                                    echo "<select class='form-control input-sm' id='cb_divisi' name='cb_divisi'>";
                                    echo "<option value=''>-- Pilihan --</option>";
                                    while ($Xt=mysqli_fetch_array($sql)){
                                        $pddividi=$Xt['DivProdId'];
                                        $pddivinm=$Xt['nama'];
                                        if ($pddividi==$ppilihdiv)
                                            echo "<option value='$pddividi' selected>$pddivinm</option>";
                                        else
                                            echo "<option value='$pddividi'>$pddivinm</option>";
                                    }
                                    echo "</select>";
                                ?>
                            </div>
                        </div>
                        
                        <div class='col-sm-1'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-success  btn-xs' id="s-submit" value="Refresh" onclick="RefreshDataTabel()">
                           </div>
                       </div>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                        
                        </div>
                        
                    </div>
                </div>
                
                <?PHP

            break;

            case "tambahbaru":
                include "tambah.php";
            break;

            case "editdata":
                include "tambah.php";
            break;

            case "editterima":
                include "terima.php";
            break;
        
            case "edittransfer":
                include "transfer.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

