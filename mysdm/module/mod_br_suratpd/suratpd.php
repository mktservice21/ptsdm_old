<?PHP
	include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $hari_ini2 = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    //$tgl_pertama = date('F Y', strtotime($hari_ini2));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['STPDPERENTY1'])) $tgl_pertama = $_SESSION['STPDPERENTY1'];
    if (!empty($_SESSION['STPDPERENTY2'])) $tgl_akhir = $_SESSION['STPDPERENTY2'];
    $ptipeinput = $_SESSION['STPDTIPE'];
    if (empty($ptipeinput)) $ptipeinput = "B";
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
	
    if (isset($_GET['act'])) {
        if ($_GET['act']!="editdata" AND $_GET['act']!="tambahbaru") {
            
            if ($fkaryawan=="0000000566") {
                mysqli_query($cnmy, "CALL dbmaster.proses_outstanding_br_eth_pertahun('PEACO')");
                mysqli_query($cnmy, "CALL dbmaster.proses_outstanding_br_eth_pertahun('PIGEO')");
            }elseif ($fkaryawan=="0000001043") {
                mysqli_query($cnmy, "CALL dbmaster.proses_outstanding_br_eth_pertahun('EAGLE')");
            }elseif ($fkaryawan=="0000000148") {
                mysqli_query($cnmy, "CALL dbmaster.proses_outstanding_br_eth_pertahun('HO')");
            }else{
                mysqli_query($cnmy, "CALL dbmaster.proses_outstanding_br_eth_otc_pertahun()");
            }
            
        }
    }
	
	
?>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Surat Permintaan Dana";
                if ($_GET['act']=="tambahbaru")
                    echo "Input Permintaan Dana";
                elseif ($_GET['act']=="editdata")
                    echo "Edit Permintaan Dana";
                elseif ($_GET['act']=="editdatanobbm")
                    echo "Isi Nomor BBM";
                elseif ($_GET['act']=="editdatanobbk")
                    echo "Isi Nomor BBK";
                else
                    echo "Data $judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_suratpd/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >
                    function ShowTombol() {
                        var etipe=document.getElementById('cb_tipeisi').value;
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_suratpd/viewdata.php?module=gantitombol",
                            data:"utipe="+etipe,
                            success:function(data){
                                $("#c_tombol").html(data);
                                if (etipe=="A") {
                                    $("#c-data").html("");
                                }else if (etipe=="B") {
                                    TampilDataPD();
                                }else if (etipe=="C") {
                                    $("#c-data").html("");
                                }else if (etipe=="D") {
                                    $("#c-data").html("");
                                }
                            }
                        });
                    }
                    

                    $(document).ready(function() {
                        ShowTombol();
                        var etipe=document.getElementById('cb_tipeisi').value;
                        if (etipe=="A") {
                            
                        }else if (etipe=="B") {
                            TampilDataPD();
                        }else if (etipe=="C") {
                            TampilDataNOSPD(1);
                        }else if (etipe=="D") {
                            TampilDataNOSPD(3);
                        }
                    } );

                    function TampilData(sinput) {
                        
                        var eaksi = "module/mod_br_suratpd/aksi_spd.php";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var etipe=document.getElementById('cb_tipeisi').value;
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_suratpd/viewdatatabel.php?module=viewdata",
                            data:"uinput="+sinput+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&uaksi="+eaksi+"&uisi="+etipe,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }


                    function TampilDataPD() {
                        
                        var eaksi = "module/mod_br_spd/aksi_spd.php";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_suratpd/viewdatatabelpd.php?module=viewdata",
                            data:"uperiode1="+etgl1+"&uperiode2="+etgl2+"&uaksi="+eaksi,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                        
                    }

                    function TampilDataNOSPD(sinput) {
                        var eaksi = "module/mod_br_spd/aksi_spd.php";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_suratpd/viewdatatabelnospd.php?module=viewdata",
                            data:"module="+module+"&idmenu="+idmenu+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&uaksi="+eaksi+"&uinputid="+sinput,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                        
                    }
                    
                </script>

                    
                
                <script>
                    function disp_confirm_print(pText)  {
                        
                        //KlikDataTabel();
                        
                        if (pText == "excel") {
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=suratpdpreview&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=suratpdpreview&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }
                        
                        
                    }
                </script>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <!--
                        <div class='x_title'>
                        
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        
                        </div>
                        --><br/>&nbsp;
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

                            <div class='col-sm-2'>
                                Type Proses
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_tipeisi" name="cb_tipeisi" onchange="ShowTombol()">
                                        <?PHP
                                        $sa=""; $sb=""; $sc=""; $sd="";
                                        if ($ptipeinput=="A") $sa=" selected";
                                        if ($ptipeinput=="B") $sb=" selected";
                                        if ($ptipeinput=="C") $sc=" selected";
                                        if ($ptipeinput=="D") $sd=" selected";
                                        ?>
                                        <option value="A" <?PHP echo $sa; ?>>Isi Nomor SPD</option>
                                        <option value="B" <?PHP echo $sb; ?>>Input Pengajuan Dana</option>
                                        <!--
                                        <option value="C" <?PHP //echo $sc; ?>>Input No. BBM</option>
                                        <option value="D" <?PHP //echo $sd; ?>>Input No. BBK</option>
                                        -->
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                Periode Permintaan
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01'>
                                        <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <small>s/d.</small>
                                <div class="form-group">
                                   <div class='input-group date' id='cbln02'>
                                        <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="c_tombol">
                                <!--
                                <div class='col-sm-3'>
                                    <small>&nbsp;</small>
                                   <div class="form-group">
                                       <input type='button' class='btn btn-success btn-xs' id="s-submit1" value="Belum Proses" onclick="RefreshDataTabel()">&nbsp;
                                       <input type='button' class='btn btn-info btn-xs' id="s-submit2" value="Sudah Proses" onclick="RefreshDataTabel()">&nbsp;
                                   </div>
                               </div>
                                -->
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

            case "editdatanobbm":
                include "tambahnomorbbm.php";
            break;

            case "editdatanobbk":
                include "tambahnomor.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

