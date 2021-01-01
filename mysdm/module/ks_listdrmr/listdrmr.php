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
    
    
    $bnmdrcari="";
    if (!empty($_SESSION['KSLSTDRMR'])) $bnmdrcari=$_SESSION['KSLSTDRMR'];
    
    
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="List Data Dokter MR";
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
                        var enmdokt=document.getElementById('e_nmdokcari').value;
                        
                        if (enmdokt=="") {
                            alert("Isi Nama Dokter, Untuk dicari...");
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
                            url:"module/ks_listdrmr/viewdatatabel_listdrmr.php?module="+module+"&idmenu="+idmenu+"&act="+act+"&unmdokt="+enmdokt,
                            data:"module="+module+"&unmdokt="+enmdokt,
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
                            
                            
                            <div class='col-sm-4'>
                                Nama Dokter
                                <div class="form-group">
                                    <input type='text' id='e_nmdokcari' name='e_nmdokcari' class='form-control col-md-7 col-xs-12' value='<?PHP echo $bnmdrcari; ?>'>
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
    
    
    
</div>

