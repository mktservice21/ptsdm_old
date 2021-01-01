<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    include "config/koneksimysqli_it.php";
    if (!empty($_SESSION['SPGMSTGJTGLCAB'])) $tgl_pertama=$_SESSION['SPGMSTGJTGLCAB'];
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Closing Biaya Luar Kota Per Bulan";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "$judul";
                ?>
            </h2>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_closing_lkca/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        
                    } );

                    function KlikDataTabel() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var ket="";
                        var etgl1=document.getElementById('bulan1').value;
                        var ests=document.getElementById('sts_rpt').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_closing_lkca/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module+"&utgl="+etgl1+"&usts="+ests,
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
                                Periode
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01'>
                                        <input type='text' id='bulan1' name='bulan1' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        
                            <div class='col-sm-2'>
                                Status
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01'>
                                        <select class='form-control' id="sts_rpt" name="sts_rpt">
                                            <option value="S">Sudah Closing</option>
                                            <option value="B" selected>Belum Closing</option>
                                        </select>
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
                                    <th width="30px" align="center" nowrap></th>
                                    <th align="center" nowrap>NAMA</th>
                                    <th align="center" nowrap>No LK</th>
                                    <th align="center" nowrap>Credit</th>
                                    <th align="center" nowrap>Saldo REAL</th>
                                    <th align="center" nowrap>CA </th>
                                    <th align="center" nowrap>Selisih</th>
                                    <th align="center" >CA  </th>
                                    <th align="center" >JUML TRSF</th>
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
    <!--end row-->
</div>

