<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    
    if (!empty($_SESSION['CLSETHPERIODE01'])) $tgl_pertama=$_SESSION['CLSETHPERIODE01'];
    
    $psts_cls="B";
    if (!empty($_SESSION['CLSETHSTS'])) $psts_cls=$_SESSION['CLSETHSTS'];
    
    $psts_clsdef1="";
    $psts_clsdef2="selected";
    if ($psts_cls=="C") {
        $psts_clsdef1="selected";
        $psts_clsdef2="";
    }
    
    
    $pca1_pros="0";
    if (!empty($_SESSION['CLSETHPILCA1'])) $pca1_pros=$_SESSION['CLSETHPILCA1'];
    $pca1_def1="selected";
    $pca1_def2="";
    $pca1_def3="";
    
    if ($pca1_pros=="1") {
        $pca1_def1="";
        $pca1_def2="selected";
        $pca1_def3="";
    }elseif ($pca1_pros=="2") {
        $pca1_def1="";
        $pca1_def2="";
        $pca1_def3="selected";
    }
    
    $pca2_pros="0";
    if (!empty($_SESSION['CLSETHPILCA2'])) $pca2_pros=$_SESSION['CLSETHPILCA2'];
    $pca2_def1="selected";
    $pca2_def2="";
    $pca2_def3="";
    
    if ($pca2_pros=="1") {
        $pca2_def1="";
        $pca2_def2="selected";
        $pca2_def3="";
    }elseif ($pca2_pros=="2") {
        $pca2_def1="";
        $pca2_def2="";
        $pca2_def3="selected";
    }
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Closing Biaya Luar Kota dan CA Per Bulan BARU";
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
        //$aksi="module/mod_br_closing_lkca_baru/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel(sKey) {
                        KlikDataTabel(sKey);
                    }

                    $(document).ready(function() {
                        var ests=document.getElementById('sts_rpt').value;
                        if (ests=="C") {
                            pilihSudahProses();
                            
                            <?PHP if ($_SESSION['CLSETHBTNPILIH']=="2" OR $_SESSION['CLSETHBTNPILIH']=="3") { ?>
                                    var sKey=<?PHP echo $_SESSION['CLSETHBTNPILIH']; ?> ;
                            <?PHP } ?>
                            
                        }
                    } );

                    function KlikDataTabel(sKey) {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var ket="";
                        var etgl1=document.getElementById('bulan1').value;
                        var ests=document.getElementById('sts_rpt').value;
                        var eprosid_sts=document.getElementById('sts_sudahprosesid').value;
                        var ecaperiode1=document.getElementById('sts_periodeca1').value;
                        var ecaperiode2=document.getElementById('sts_periodeca2').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_closing_lkca_baru/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module+
                                 "&utgl="+etgl1+"&usts="+ests+"&ucaperiode1="+ecaperiode1+"&ucaperiode2="+ecaperiode2+
                                 "&upilihjenis="+sKey+"&uprosid_sts="+eprosid_sts,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function pilihSudahProses(){
                        var ests=document.getElementById('sts_rpt').value;
                        var etgl1=document.getElementById('bulan1').value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_closing_lkca_baru/viewdata.php?module=tampilkansudahproses",
                            data:"usts="+ests+"&utgl="+etgl1,
                            success:function(data){
                                $("#div_pilihproses").html(data);
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
                        
                            <div class='col-sm-2' hidden>
                                Periode CA 1
                                <div class="form-group">
                                    <select class='form-control' id="sts_periodeca1" name="sts_periodeca1">
                                        <option value="0" <?PHP echo $pca1_def1; ?>>All Periode</option>
                                        <option value="1" <?PHP echo $pca1_def2; ?>>Periode 1</option>
                                        <option value="2" <?PHP echo $pca1_def3; ?>>Periode 2</option>
                                    </select>
                                </div>
                            </div>
                        
                            <div class='col-sm-2' hidden>
                                Periode CA 2
                                <div class="form-group">
                                    <select class='form-control' id="sts_periodeca2" name="sts_periodeca2">
                                        <option value="0" <?PHP echo $pca2_def1; ?>>All Periode</option>
                                        <option value="1" <?PHP echo $pca2_def2; ?>>Periode 1</option>
                                        <option value="2" <?PHP echo $pca2_def3; ?>>Periode 2</option>
                                    </select>
                                </div>
                            </div>
                        
                        
                            <div class='col-sm-2'>
                                Status
                                <div class="form-group">
                                    <select class='form-control' id="sts_rpt" name="sts_rpt" onchange="pilihSudahProses()">
                                        <option value="C" <?PHP echo $psts_clsdef1; ?>>Sudah Closing</option>
                                        <option value="B" <?PHP echo $psts_clsdef2; ?>>Belum Closing</option>
                                    </select>
                                </div>
                            </div>

                            
                            <div id="div_pilihproses">
                                <div class='col-sm-2' hidden>
                                    <div class="form-group">
                                        <select class='form-control' id="sts_sudahprosesid" name="sts_sudahprosesid">
                                            <option value="" selected></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        
                            <div class='col-sm-6'>
                                <small>&nbsp;</small>
                                <div class="form-group">
                                    <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel('1')">&nbsp;
                                    <input type='button' class='btn btn-dark btn-xs' id="s-submit" value="Permintaan Dana" onclick="RefreshDataTabel('2')">&nbsp;
                                    <input type='button' class='btn btn-warning btn-xs' id="s-submit" value="Input Bank (NO BBK)" onclick="RefreshDataTabel('3')">&nbsp;
                                </div>
                            </div>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatablelkcacls' class='table table-striped table-bordered' width='100%'>
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
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
                
                <script>
                    $('#cbln01').on('change dp.change', function(e){
                        pilihSudahProses();
                    });
                </script>
                
                
                <?PHP
                
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

