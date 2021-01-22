<?php
    //include "config/cek_akses_modul.php";
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
    
    
    $pidcabangpl=$_SESSION['MAPCUSTIDCAB'];
    $pidareapl=$_SESSION['MAPCUSTIDARE'];
    $pfilterpl=$_SESSION['MAPCUSTFILTE'];
    
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Customer SDM";
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
                        var ecabang=document.getElementById('cb_cabang').value;
                        var earea=document.getElementById('cb_area').value;
                        var enamafilter=document.getElementById('e_namafilter').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/map_customersdm/viewdatatabelecust.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"ucabang="+ecabang+"&uarea="+earea+"&unamafilter="+enamafilter,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    function ShowDataArea() {
                        var ecabang=document.getElementById('cb_cabang').value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/map_customersdm/viewdatacust.php?module=caridataarea",
                            data:"ucabang="+ecabang,
                            success:function(data){
                                $("#cb_area").html(data);
                            }
                        });
                    }
                </script>
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' 
                              id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
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
                                                if ($pidcabangpl==$pidcab)
                                                    echo "<option value='$pidcab' selected>$pnmcab</option>";
                                                else
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
                                                    if ($pidcabangpl==$pidcab)
                                                        echo "<option value='$pidcab' selected>$pnmcab</option>";
                                                    else
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
                                        if (!empty($pidcabangpl)) {
                                            $query_area="SELECT * from MKT.iarea where icabangid='$pidcabangpl' ";
                                            $query_ak =$query_area." AND IFNULL(aktif,'')='Y' ";
                                            $query_ak .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_ak);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $pidarea=$row['areaId'];
                                                $pnmarea=$row['Nama'];
                                                if ($pidarea==$pidareapl)
                                                    echo "<option value='$pidarea' selected>$pnmarea</option>";
                                                else
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
                                                    if ($pidarea==$pidareapl)
                                                        echo "<option value='$pidarea' selected>$pnmarea</option>";
                                                    else
                                                        echo "<option value='$pidarea'>$pnmarea</option>";
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-4'>
                                Nama
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

