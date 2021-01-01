<?php
    include "config/cek_akses_modul.php";
    
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
    //if (!empty($_SESSION['DISCDPLCBOTL'])) $bidcabang=$_SESSION['DISCDPLCBOTL'];
    
    
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Program Trade Promo CHC (DPL)";
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
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/disc_dplchc/viewdatatabel_dplchc.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"module="+module,
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
                              id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  
                              enctype='multipart/form-data'>
                            
                            
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
                include "tambah_dplchc.php";
            break;
            case "editdata":
                include "edit_dplchc.php";
            break;
        
        }
        ?>
        
    </div>
    
    
    
</div>

