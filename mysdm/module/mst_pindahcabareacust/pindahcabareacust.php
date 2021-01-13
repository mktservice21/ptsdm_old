<?php
    include "config/cek_akses_modul.php";
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    
    $aksi="eksekusi3.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Pindah Data Cabang Area dan Customer";
                if ($pact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pact=="editdata")
                    echo "Edit $judul";
                elseif ($pact=="duplikaticust")
                    echo "Copy Data iCust Dari Cabang dan Area Lain";
                elseif ($pact=="pindahecust")
                    echo "Pindah eCust Dari Cabang dan Area Lain";
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
                        //var ejabatan=document.getElementById('e_jabatan').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mst_pindahcabareacust/viewdatatabelecab.php?module="+module+"&idmenu="+idmenu+"&act="+act,
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
                        
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=import&idmenu=$pidmenu"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  
                              enctype='multipart/form-data'>
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
                                    <input class='btn btn-default btn-sm' type=button value='Tambah Cabang Baru'
                                           accept=""onclick="window.location.href='<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=tambahbaru"; ?>';">
                                    
                                    <input class='btn btn-dark btn-sm' type=button value='Copy iCust Dari Cabang Lain'
                                           accept=""onclick="window.location.href='<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=duplikaticust"; ?>';">
                                    
                                    <input class='btn btn-warning btn-sm' type=button value='Pindah eCust Dari Cabang Lain'
                                           accept=""onclick="window.location.href='<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=pindahecust"; ?>';">
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
                        
                        
                        <div id='loading2'></div>
                        <div id='c-data2'>
                           
                        </div>
                        
                        <div id='loading3'></div>
                        <div id='c-data3'>
                           
                        </div>
                        
                        <div id='loading4'></div>
                        <div id='c-data4'>
                           
                        </div>
                        
                        
                    </div>
                </div>
        
                <?PHP
                
            break;

            case "tambahbaru":
                include "tambah_cab.php";
            break;
            case "editdata":
                include "tambah_cab.php";
            break;
            case "duplikaticust":
                include "duplikaticust.php";
            break;
            case "pindahecust":
                include "prosespindahecust.php";
            break;
        
        }
        ?>
        
    </div>
    
    
    
</div>

