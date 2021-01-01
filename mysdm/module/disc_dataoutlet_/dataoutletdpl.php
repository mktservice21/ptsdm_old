<?php

    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    $aksi="eksekusi3.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
    
    
    $bidcabang="";
    if (!empty($_SESSION['DISCDPLCBOTL'])) $bidcabang=$_SESSION['DISCDPLCBOTL'];
    
    
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Data Outlet DPL";
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
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var ecabid=document.getElementById('cb_cabang').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/disc_dataoutlet/viewdatatabel.php?module="+module+"&idmenu="+idmenu+"&act="+act+"&ucabid="+ecabid,
                            data:"module="+module+"&ucabid="+ecabid,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
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
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=import&idmenu=$pidmenu"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  
                              enctype='multipart/form-data'>
                            
                            
                            <div class='col-sm-2'>
                                Cabang 
                                <div class="form-group">
                                    
                                    <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="" data-live-search="true">
                                        <?PHP
                                        echo "<option value='' selected>--Pilih--</option>";
                                        if ($fjbtid=="38") {
                                            $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                                    . " JOIN hrd.rsm_auth as b on a.icabangid=b.icabangid WHERE b.karyawanid='$fkaryawan' ";
                                            $query .=" order by a.nama";
                                        }else{
                                            $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE 1=1 ";
                                            $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -') ";
                                            $query .=" AND IFNULL(aktif,'')<>'N' ";
                                            $query .=" order by nama";
                                        }
                                        $tampiledu= mysqli_query($cnmy, $query);
                                        while ($du= mysqli_fetch_array($tampiledu)) {
                                            $bidcab=$du['icabangid'];
                                            $bnmcab=$du['nama'];

                                            if ($bidcab==$bidcabang) 
                                                echo "<option value='$bidcab' selected>$bnmcab</option>";
                                            else
                                                echo "<option value='$bidcab'>$bnmcab</option>";

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

            case "tambahbaru":
                include "tambah.php";
            break;
            case "editdata":
                include "tambah.php";
            break;
        
        }
        ?>
        
    </div>
    
    
    
</div>

