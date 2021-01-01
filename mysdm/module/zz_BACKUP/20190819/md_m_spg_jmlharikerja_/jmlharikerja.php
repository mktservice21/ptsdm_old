<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('Y', strtotime($hari_ini));
?>

<div class="">
    
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h2>
                Jumlah Hari Kerja SPG Per Bulan
            </h2>
        </div>
    </div>
    <div class="clearfix"></div>
    
    <div class="row">
        
        <?php
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

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
                        var etahun=document.getElementById('e_periode01').value;
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/md_m_spg_jmlharikerja/viewdatatabel.php?module="+module+"&idmenu="+idmenu,
                            data:"utahun="+etahun,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                            <div class='col-sm-2'>
                                Tahun
                                <div class="form-group">
                                    <div class='input-group date' id='thn01'>
                                        <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                               </div>
                           </div>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='10px'>No</th>
                                        <th width='300px' align="center">Bulan</th>
                                        <th width='200px' align="center">Jumlah</th>
                                        <th width='80px'></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
                <?PHP
            break;
        }
        ?>
    </div>
    
    
    
</div>

<style>
    #datatableuc th {
        font-size: 12px;
    }
    #datatableuc td { 
        font-size: 12px;
        padding: 3px;
        margin: 1px;
    }
</style>