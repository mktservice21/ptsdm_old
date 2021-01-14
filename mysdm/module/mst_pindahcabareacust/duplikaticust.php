<?php
include "config/koneksimysqli_it.php";

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];

$act="duplikaticust";


?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='form_data01' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data' >
                
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dari Cabang (Old) <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='form-control input-sm' id='cb_daricabang' name='cb_daricabang' onchange="showDataDariArea()" data-live-search="true">
                                            <?PHP 
                                            echo "<option value='' selected>--Pilih--</option>";
                                            $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE ifnull(aktif,'')<>'N'";
                                            $query .= " AND left(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
                                            $query .= " Order by nama";
                                            $tampil =mysqli_query($cnit, $query);
                                            while ($irow=mysqli_fetch_array($tampil)){
                                                $picabang=$irow['icabangid'];
                                                $pnamacab=$irow['nama'];
                                                $iidcab=(INT)$picabang;
                                                echo "<option value='$picabang'>$pnamacab ($iidcab)</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area (Old) <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='form-control input-sm' id='cb_dariarea' name='cb_dariarea' onchange="" data-live-search="true">
                                            <?PHP
                                            echo "<option value='' selected>--Pilih--</option>";
                                            
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <hr/>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pindah Ke Cabang (NEW) <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="showDataArea()" data-live-search="true">
                                            <?PHP
                                            echo "<option value='' selected>--Pilih--</option>";
                                            $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE ifnull(aktif,'')<>'N'";
                                            $query .= " AND left(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
                                            $query .= " Order by nama";
                                            $tampil =mysqli_query($cnit, $query);
                                            while ($irow=mysqli_fetch_array($tampil)){
                                                $picabang=$irow['icabangid'];
                                                $pnamacab=$irow['nama'];
                                                $iidcab=(INT)$picabang;
                                                echo "<option value='$picabang'>$pnamacab ($iidcab)</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area (NEW) <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='form-control input-sm' id='cb_area' name='cb_area' onchange="" data-live-search="true">
                                            <?PHP
                                            echo "<option value='' selected>--Pilih--</option>";
                                            
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                            </div>
                            
                        </div>
                    </div>
                    
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>

                        <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
                            <button type='button' class='btn btn-success btn-sm' onclick='LihatDataProses()'>Query Data</button>
                        </div>


                    </div>
                    
                    <div id='loading'></div>
                    <div id='c-data'>

                    </div>
                
                </form>
                
            </div>
            
            
        </div>
        
    </div>
    
</div>

<script>
    function showDataArea() {
        var icab = document.getElementById('cb_cabang').value;
        $.ajax({
            type:"post",
            url:"module/mst_pindahcabareacust/viewdatapindah.php?module=viewdataareacabang",
            data:"ucab="+icab,
            success:function(data){
                $("#cb_area").html(data);
            }
        });
    }
    
    function showDataDariArea() {
        var idrcab = document.getElementById('cb_daricabang').value;
        $.ajax({
            type:"post",
            url:"module/mst_pindahcabareacust/viewdatapindah.php?module=viewdatadariareacabang",
            data:"udrcab="+idrcab,
            success:function(data){
                $("#cb_dariarea").html(data);
            }
        });
    }
    
    function LihatDataProses() {
        var icab = document.getElementById('cb_cabang').value;
        var icabdari = document.getElementById('cb_daricabang').value;
        
        var iarea = document.getElementById('cb_area').value;
        var iareadari = document.getElementById('cb_dariarea').value;
        
        if (icab=="") {
            alert("Cabang harus diisi");
            return false;
        }
        
        if (iarea=="") {
            alert("Area harus diisi");
            return false;
        }
        
        if (icabdari=="") {
            alert("Cabang (Dari) harus diisi");
            return false;
        }
        
        if (iareadari=="") {
            alert("Area (Dari) harus diisi");
            return false;
        }
        
        
    }
    
    
    function LihatDataProses() {
        var icab = document.getElementById('cb_cabang').value;
        var icabdari = document.getElementById('cb_daricabang').value;
        
        var iarea = document.getElementById('cb_area').value;
        var iareadari = document.getElementById('cb_dariarea').value;

        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");

        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mst_pindahcabareacust/proseslihatdatapindah.php?module="+module+"&idmenu="+idmenu+"&act="+act,
            data:"uicab="+icab+"&uicabdari="+icabdari+"&uiarea="+iarea+"&uiareadari="+iareadari,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
    

</script>