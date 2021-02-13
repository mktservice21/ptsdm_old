<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    //include "config/koneksimysqli_it.php";
    $icabang="";
    if (!empty($_SESSION['SPGMSTPRSMCAB'])) $icabang=$_SESSION['SPGMSTPRSMCAB'];
    if (!empty($_SESSION['SPGMSTPRSMTGL'])) $tgl_pertama=$_SESSION['SPGMSTPRSMTGL'];
    $ptipeinput = $_SESSION['SPGMSTPRSMTIPE'];
    if (empty($ptipeinput)) $ptipeinput = "A";
    
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Proses Finance Data SPG Per Bulan";
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
        //$aksi="module/md_m_spg_prosesfinspv/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >
                    function ShowTombol() {
                        var etipe=document.getElementById('cb_tipeisi').value;
                        $.ajax({
                            type:"post",
                            url:"module/md_m_spg_prosesfinspv/viewdata.php?module=gantitombol",
                            data:"utipe="+etipe,
                            success:function(data){
                                $("#c_tombol").html(data);
                                if (etipe=="A") {
                                    $("#c-data").html("");
                                }else if (etipe=="B") {
                                    $("#c-data").html("");
                                }
                            }
                        });
                    }
                    
                    function RefreshDataTabel(sts) {
                        KlikDataTabel(sts);
                    }

                    $(document).ready(function() {
                        ShowTombol();
                        var ecabang=document.getElementById('e_cabangid').value;
                        if (ecabang != "") {
                            //KlikDataTabel();
                        }
                    } );

                    function KlikDataTabel(sts) {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var ket="";
                        var etipe=document.getElementById('cb_tipeisi').value;
                        var ecabang=document.getElementById('e_cabangid').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/md_m_spg_prosesfinspv/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&ucabang="+ecabang+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module+"&utgl="+etgl1+"&usts="+sts+"&utipe="+etipe,
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
                                Type Proses
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_tipeisi" name="cb_tipeisi" onchange="ShowTombol()">
                                        <?PHP
                                        $sa=""; $sb=""; $sc="";
                                        if ($ptipeinput=="A") $sa=" selected";
                                        if ($ptipeinput=="B") $sb=" selected";
                                        if ($ptipeinput=="C") $sc=" selected";
                                        ?>
                                        <option value="A" <?PHP echo $sa; ?>>Proses</option>
                                    </select>
                                </div>
                            </div>
                        
                            <div class='col-sm-2'>
                                Periode
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01x'>
                                        <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_cabangid' name='e_cabangid' onchange="Kosongkan()">
                                        <?PHP
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            //$query = "select icabangid_o, nama from MKT.icabang_o WHERE aktif='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ORDER BY nama";
                                            $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE aktif='Y' AND i_spg='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ORDER BY nama";
                                            $tampil= mysqli_query($cnmy, $query);
                                            while($s= mysqli_fetch_array($tampil)) {
                                                $pcabangid=$s['icabangid_o'];
                                                $pnmcabang=$s['nama'];
                                                if ($pcabangid==$icabang)
                                                    echo "<option value='$pcabangid' selected>$pnmcabang</option>";
                                                else
                                                    echo "<option value='$pcabangid'>$pnmcabang</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div id="c_tombol"></div>
                        
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
    <!--end row-->
</div>

<script>
    function Kosongkan(){
        $("#c-data").html("");
    }
    $(function() {
        $('#tgl1').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'MM yy',
            onSelect: function(dateStr) {
                
            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                Kosongkan();
            },

            beforeShow: function() {
                if ((selDate = $(this).val()).length > 0) 
                {
                    iYear = selDate.substring(selDate.length - 4, selDate.length);
                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                }
            }
        });
    });    
</script>

<style>
    .ui-datepicker-calendar {
        display: none;
    }
    
    .divnone {
        display: none;
    }
    #datatableuc th {
        font-size: 12px;
    }
    #datatableuc td { 
        font-size: 12px;
        padding: 3px;
        margin: 1px;
    }
</style>