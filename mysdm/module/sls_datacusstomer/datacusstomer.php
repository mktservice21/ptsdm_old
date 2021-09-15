<?PHP
    include "config/cek_akses_modul.php";
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_pilih = date('d F Y', strtotime($hari_ini));
    
    $pperiode_ = date('Ym', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    
    include "config/koneksimysqli_ms.php";
        $pmyidcard=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        
        $filter_karyawan="('$pmyidcard')";
                
        $pidcabangpil="";
        $pidareapil="";
        $piddivisipil="";
        $pfilterregionpilih="";
        
        $ptextcabang="";
        $ptextcabarea="";
        
        $pnviddivisipil="";
        $pfiltercabpilih="";
        $pfilterareapilih="";
        $pfilterdivisipilih="";
        $pfiltercabarea="";
        $pjmldivisicover=0;
        
        
        if ($pmyjabatanid=="15" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08") {
            if ($pmyjabatanid=="15") {
                $query_cab = "select distinct icabangid, areaid, divisiid FROM sls.imr0 WHERE karyawanid='$pmyidcard'";
            }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
                $query_cab = "select distinct icabangid, areaid, divisiid FROM sls.ispv0 WHERE karyawanid='$pmyidcard'";
            }elseif ($pmyjabatanid=="08") {
                $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM sls.idm0 WHERE karyawanid='$pmyidcard'";
            }
            $tampil= mysqli_query($cnms, $query_cab);
            while ($rs= mysqli_fetch_array($tampil)) {
                $vbicabangid=$rs['icabangid'];
                $vbareaid=$rs['areaid'];
                $vbdivisi=$rs['divisiid'];
                
                if (!empty($vbicabangid)) $pidcabangpil=$vbicabangid;
                
                if (strpos($pfiltercabpilih, $vbicabangid)==false) $pfiltercabpilih .="'".$vbicabangid."',";
                if (!empty($vbareaid)) {
                    if (strpos($pfilterareapilih, $vbareaid)==false) $pfilterareapilih .="'".$vbareaid."',";
                }
                
                if (!empty($vbdivisi)) {
                    if (strpos($pfilterdivisipilih, $vbdivisi)==false) {
                        $pfilterdivisipilih .="'".$vbdivisi."',";

                        $pjmldivisicover++;
                        $pnviddivisipil=$vbdivisi;

                    }
                }
                
                if (strpos($pfiltercabarea, $vbicabangid.$vbareaid)==false) $pfiltercabarea .="'".$vbicabangid.$vbareaid."',";
                
            }
            
        }else{
            
            $query_cab = "select distinct icabangid from hrd.rsm_auth WHERE karyawanid='$pmyidcard'";
            $tampil= mysqli_query($cnmy, $query_cab);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($rs= mysqli_fetch_array($tampil)) {
                    $vbicabangid=$rs['icabangid'];
                    
                    if (!empty($vbicabangid)) $pidcabangpil=$vbicabangid;
                    if (strpos($pfiltercabpilih, $vbicabangid)==false) $pfiltercabpilih .="'".$vbicabangid."',";
                    
                }
            }
            
        }
        
        
        
        $pkaryawanareakosong=false;
        $pcaridarikry=true;
        if ($pmyjabatanid=="15" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="38" OR $pmyjabatanid=="39") {
            $pcaridarikry=false;
            if (empty($pfiltercabpilih)) {
                $pcaridarikry=true;
                $pkaryawanareakosong=true;
            }
        }
        
        if ($pcaridarikry==true) {
            $queryk = "select icabangid, areaId, divisiid from hrd.karyawan where karyawanid='$pmyidcard'";
            $tampilk= mysqli_query($cnms, $queryk);
            $nk= mysqli_fetch_array($tampilk);
            if (!empty($nk['icabangid'])) {
                $pidcabangpil=$nk['icabangid'];
            }
            if (!empty($nk['areaId'])) {
                $pidareapil=$nk['areaId'];
            }
            $sdivid_divisi="";
            if (!empty($nk['divisiid'])) {
                $sdivid_divisi=$nk['divisiid'];
            }
            
            if ($pkaryawanareakosong==true) {
                $pfiltercabpilih="'$pidcabangpil',";
                $pfilterareapilih="'$pidareapil',";
                $pfilterdivisipilih="'$sdivid_divisi',";
                $pfiltercabarea="'".$pidcabangpil.$pidareapil."',";
            }
                
        }
        
        $ptextcabang=$pfiltercabpilih;
        if (!empty($pfiltercabpilih)) $pfiltercabpilih="(".substr($pfiltercabpilih, 0, -1).")";
        if (!empty($pfilterareapilih)) $pfilterareapilih="(".substr($pfilterareapilih, 0, -1).")";
        if (!empty($pfilterdivisipilih)) $pfilterdivisipilih="(".substr($pfilterdivisipilih, 0, -1).")";
        $ptextcabarea=$pfiltercabarea;
        if (!empty($pfiltercabarea)) $pfiltercabarea="(".substr($pfiltercabarea, 0, -1).")";
        
        if ($pjmldivisicover==1 AND $pmyjabatanid=="15") $piddivisipil=$pnviddivisipil;
        
        
        if (!empty($pfiltercabpilih)) {
            $query = "select distinct region from sls.icabang where icabangid IN $pfiltercabpilih";
            $tampil= mysqli_query($cnms, $query);
            while ($nr= mysqli_fetch_array($tampil)) {
                if (!empty($nr['region'])) $pvbregion=$nr['region'];
                if (strpos($pfilterregionpilih, $pvbregion)==false) $pfilterregionpilih .="'".$pvbregion."',";
            }
            if (!empty($pfilterregionpilih)) $pfilterregionpilih="(".substr($pfilterregionpilih, 0, -1).")";
        }
        
        $pcabangidpl="";
?>


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

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>


<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Mapping Outlet Pareto dan Non Pareto";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                else
                    echo "$judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_brrutin/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script>
                    
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        //KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var ecabang=document.getElementById('cb_cabang').value;
                        var earea=document.getElementById('cb_area').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/sls_datacusstomer/viewdatatabel.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"ucabang="+ecabang+"&uarea="+earea,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    
                    function ProsesData(ket, noid){

                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                            if (r==true) {

                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("d-form2").action = "module/sls_datacusstomer/aksi_datacusstomer.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket+"&id="+noid;
                                document.getElementById("d-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }



                    }
                    
                    function ShowDataArea() {
                        var ecabang=document.getElementById('cb_cabang').value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_datacusstomer/viewdata.php?module=caridataarea",
                            data:"ucabang="+ecabang,
                            success:function(data){
                                $("#cb_area").html(data);
                            }
                        });
                    }
                    
                </script>

                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_cabang" name="cb_cabang" onchange="ShowDataArea()">
                                        <?PHP
                                            if ($pmyjabatanid=="38"){
                                            }else{
                                                echo "<option value=''>--Piihan--</option>";
                                            }
                                            $query_cb = "select icabangid, nama from mkt.icabang WHERE 1=1 ";
                                            $query_cb .=" AND left(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
                                            if (!empty($pfiltercabpilih)) {
                                                if ($pmyjabatanid=="15" OR $pmyjabatanid=="38" OR $pmyjabatanid=="39" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08" OR $pmyjabatanid=="20") $query_cb .=" AND iCabangId IN $pfiltercabpilih ";
                                            }
                                            $query_aktif =$query_cb." AND IFNULL(aktif,'')='Y' ";
                                            $query_aktif .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_aktif);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $pidcab=$row['icabangid'];
                                                $pnmcab=$row['nama'];
                                                echo "<option value='$pidcab'>$pnmcab</option>";
                                                $pcabangidpl=$pidcab;
                                            }
                                            
                                            $query_non =$query_cb." AND IFNULL(aktif,'')<>'Y' ";
                                            $query_non .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_non);
                                            $ketemunon= mysqli_num_rows($tampil);
                                            if ($ketemunon>0) {
                                                echo "<option value='NONAKTIF'>-- Non Aktif--</option>";
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $pidcab=$row['icabangid'];
                                                    $pnmcab=$row['nama'];
                                                    echo "<option value='$pidcab'>$pnmcab</option>";
                                                    $pcabangidpl=$pidcab;
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                Area
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_area" name="cb_area" onchange="">
                                        <?PHP
                                        echo "<option value=''>--All--</option>";
                                        if (!empty($pcabangidpl)) {
                                            $query_area="SELECT * from MKT.iarea where icabangid='$pcabangidpl' ";
                                            $query_ak =$query_area." AND IFNULL(aktif,'')='Y' ";
                                            $query_ak .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_ak);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $pidarea=$row['areaId'];
                                                $pnmarea=$row['Nama'];
                                                echo "<option value='$pidarea'>$pnmarea</option>";
                                            }
                                            
                                            
                                            $query_non =$query_area." AND IFNULL(aktif,'')<>'Y' ";
                                            $query_non .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_non);
                                            $ketemunon= mysqli_num_rows($tampil);
                                            if ($ketemunon>0) {
                                                echo "<option value='NONAKTIF'>-- Non Aktif--</option>";
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $pidarea=$row['areaId'];
                                                    $pnmarea=$row['Nama'];
                                                    echo "<option value='$pidarea'>$pnmarea</option>";
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            


                            <div class='col-sm-2'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <button type='button' class='btn btn-success btn-xs' onclick='KlikDataTabel()'>View Data</button>
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
        
        }
        ?>

    </div>
    <!--end row-->
</div>


<script>
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
</script>