<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    include "config/koneksimysqli_it.php";
    if (!empty($_SESSION['SPGMSTGJTGLCAB'])) $tgl_pertama=$_SESSION['SPGMSTGJTGLCAB'];
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Outstanding Biaya Luar Kota dan CA Per Bulan (OTC)";
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
        //$aksi="module/mod_br_otsdlkca_otc/laporanbrbulan.php";
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
                            url:"module/mod_br_otsdlkca_otc/viewdatatabel.php?module="+ket,
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

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
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
                        
                            <div hidden class='col-sm-2'>
                                Status
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01'>
                                        <select class='form-control' id="sts_rpt" name="sts_rpt">
                                            <option value="C" selected>Sudah Closing</option>
                                            <option value="S">Susulan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        
                            <div class='col-sm-7'>
                                <small>&nbsp;</small>
                                <div class="form-group">
                                    <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                                    <button type='button' class='btn btn-info btn-xs' onclick="disp_confirm('')">Preview</button>
                                    <?PHP
                                    if ($_SESSION['MOBILE']!="Y") {
                                        echo "<button type='button' class='btn btn-danger btn-xs' onclick=\"disp_confirm('excel')\">Excel</button>";
                                    }
                                    ?>
                                    <input class='btn btn-default btn-xs' type=button value='Tambah'
                                        onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                </div>
                            </div>
                            
                        </form>
                        
                        
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
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
                
                <script>
                    function disp_confirm(pText)  {
                        if (pText == "excel") {
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }
                    }
                </script>
                

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
    <!--end row-->
</div>

