<?php
    include "config/cek_akses_modul.php";
    include "config/koneksimysqli_ms.php";
    $fkaryawan=$_SESSION['IDCARD'];
    $fjbtid=$_SESSION['JABATANID'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    

    //$fkaryawan="0000000158"; $fjbtid="05";//hapussaja
    
    $pfilterkaryawan="";
    $pfilterkaryawan2="";
    $pfilterkry="";
    //$fjbtid=="38" OR 
    if ($fjbtid=="20" OR $fjbtid=="08" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {
        
        $pnregion="";
        if ($fkaryawan=="0000000159") $pnregion="T";
        elseif ($fkaryawan=="0000000158") $pnregion="B";
        $pfilterkry=CariDataKaryawanByCabJbt($fkaryawan, $fjbtid, $pnregion);
        
        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
        
    }elseif ($fjbtid=="38" OR $fjbtid=="33") {
        $pnregion="";
        $pfilterkry=CariDataKaryawanByRsmAuthCNIT($fkaryawan, $fjbtid, $pnregion);
        
        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
    }
    
    
    $aksi="eksekusi3.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
    
    
    $piddistpl=$_SESSION['MAPCUSTBAGIDCAB'];
    $pidecabpl=$_SESSION['MAPCUSTBAGIIDARE'];
    $pfilterpl=$_SESSION['MAPCUSTBAGIFILTE'];
    
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Pembagian Sales Manual";
                if ($pact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pact=="editdata")
                    echo "Edit $judul";
                else
                    echo "$judul";
                ?>
            </h3>
            
        </div>
        
    </div>
    <div class="clearfix"></div>
    
    <div class="row">
        <?php
        switch($pact){
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
                        var ecabang=document.getElementById('cb_dist').value;
                        var earea=document.getElementById('cb_ecabang').value;
                        var enamafilter=document.getElementById('e_namafilter').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/map_bagisalesmanual/viewdatatabelecustd.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"ucabang="+ecabang+"&uarea="+earea+"&unamafilter="+enamafilter,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    function ShowDataEcabangDist() {
                        var ecabang=document.getElementById('cb_dist').value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/map_bagisalesmanual/viewdatabagi.php?module=caridataecust",
                            data:"ucabang="+ecabang,
                            success:function(data){
                                $("#cb_ecabang").html(data);
                            }
                        });
                    }
                </script>
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <div hidden class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' 
                              id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                            <div class='col-sm-2'>
                                Distirbutor
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_dist" name="cb_dist" onchange="ShowDataEcabangDist()">
                                        <?PHP
                                            echo "<option value=''>--Piihan--</option>";
                                            
                                            $query_aktif ="select distid, nama from MKT.distrib0 ";
                                            $query_aktif .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_aktif);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $piddis=$row['distid'];
                                                $pnmdist=$row['nama'];
                                                $pditint=(INT)$piddis;
                                                if ($piddistpl==$piddis)
                                                    echo "<option value='$piddis' selected>$pnmdist ($pditint)</option>";
                                                else
                                                    echo "<option value='$piddis'>$pnmdist ($pditint)</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_ecabang" name="cb_ecabang" onchange="">
                                        <?PHP
                                        echo "<option value=''>--All--</option>";
                                        if (!empty($piddistpl)) {
                                            $query="SELECT distid, ecabangid, nama from MKT.ecabang where distid='$piddistpl' ";
                                            $query .=" order by nama";
                                            $tampil= mysqli_query($cnms, $query);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $pidecab=$row['ecabangid'];
                                                $pnmecab=$row['nama'];
                                                if ($pidecab==$pidecabpl)
                                                    echo "<option value='$pidecab' selected>$pnmecab ($pidecab)</option>";
                                                else
                                                    echo "<option value='$pidecab'>$pnmecab ($pidecab)</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-4'>
                                No. Faktur
                                <div class="form-group">
                                    <input type='text' id='e_namafilter' name='e_namafilter' class='form-control col-md-7 col-xs-12' value='<?PHP echo "$pfilterpl"; ?>'>
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

            case "tambahbaru":
                include "tambah_custsdm.php";
            break;
            case "editdata":
                include "tambah_custsdm.php";
            break;
        
        }
        ?>
        
    </div>
    
    
    
</div>

