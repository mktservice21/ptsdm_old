<?PHP
	include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['FINKLMPERENTY1'])) $tgl_pertama = $_SESSION['FINKLMPERENTY1'];
    if (!empty($_SESSION['FINKLMPERENTY2'])) $tgl_akhir = $_SESSION['FINKLMPERENTY2'];
    
    $pgroupid=$_SESSION['GROUP'];
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $pnmactid="";
    if (isset($_GET['act'])) $pnmactid=$_GET['act'];
?>
<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                if ($pnmactid=="tambahbaru") {
                    echo "Tambah Klaim Discount By Admin";
                }elseif ($pnmactid=="editdata") {
                    echo "Edit Klaim Discount By Admin";
                }elseif ($pnmactid=="editdataperiode") {
                    echo "Edit Periode Klaim Discount By Admin";
                }else{
                    echo "Data Klaim Discount By Admin";
                }
                ?>
            </h3>
        </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_br_admentryklaim/aksi_admentryklaim.php";
        switch($_GET['act']){
            default:
                ?>
        
                <div class='modal fade' id='myModal' role='dialog'></div>
                
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        $("#c-data").html("");
                        $("#loading").html("");
                        PilihData1();
                    }
                    
                    
                    function PilihData1() {
                        var ket="";
                        var etgltipe=document.getElementById('cb_tgltipe').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi="";//document.getElementById('cb_divisi').value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var act = urlku.searchParams.get("act");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_admentryklaim/viewdatatabel.php?module="+module+"&act="+act+"&idmenu="+idmenu+"&nmun="+idmenu,
                            data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function TambahDataInputPajak(eidbr){
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_admentryklaim/tambah_pajak.php?module=viewisipajak",
                            data:"uidbr="+eidbr,
                            success:function(data){
                                $("#myModal").html(data);
                            }
                        });
                    }
                    
                    function SelAllCheckBox(nmbuton, data){
                        var checkboxes = document.getElementsByName(data);
                        var button = document.getElementById(nmbuton);
                        if(button.value == 'select'){
                            for (var i in checkboxes){
                                checkboxes[i].checked = 'FALSE';
                            }
                            button.value = 'deselect'
                        }else{
                            for (var i in checkboxes){
                                checkboxes[i].checked = '';
                            }
                            button.value = 'select';
                        }
                        
                    }
                    
                    function PreviewData()  {
                        document.getElementById("demo-form10").action = "<?PHP echo "eksekusi3.php?module=$_GET[module]&brid=input&iprint=allprev"; ?>";
                        document.getElementById("demo-form10").submit();
                        return 1;
                    }
                </script>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <?PHP
                        if ($pgroupid=="25") {
                            
                        }else{
                        ?>
                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        <?PHP } ?>

                        <div class='col-sm-2'>
                            Periode By
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                    <?PHP
                                    $sa=""; $sb=""; $sc=""; $sk="";
                                    if ($_SESSION['FINKLMTGLTIPE']=="1") $sa=" selected";
                                    if ($_SESSION['FINKLMTGLTIPE']=="2") $sb=" selected";
                                    if ($_SESSION['FINKLMTGLTIPE']=="4") $sk=" selected";
                                    ?>
                                    <option value="1" <?PHP echo $sa; ?>>Tanggal Input</option>
                                    <option value="2" <?PHP echo $sb; ?>>Tanggal Transfer</option>
                                    <option value="4" <?PHP echo $sk; ?>>Bulan Klaim</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            Periode
                            <div class="form-group">
                                <div class='input-group date' id='tgl01'>
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
                               <div class='input-group date' id='tgl02'>
                                   <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                   <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                   </span>
                               </div>
                           </div>
                       </div>
                        
                        <!--
                        <div class='col-sm-2'>
                            Divisi
                            <div class="form-group">
                                <?PHP
                                    ComboSelectIsiDivisiProdFilter("", "",
                                            "", "$fstsadmin", "$flvlposisi", "$fdivisi", "");
                                ?>
                            </div>
                        </div>
                        -->
                        <div class='col-sm-4'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-success  btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()"> &nbsp; 
                               <input type='button' class='btn btn-warning  btn-xs' id="s-submit" value="Preview" onclick="PreviewData()">
                               
                                <?PHP
                                if ($pgroupid=="25") {

                                }else{
                                ?>
                                    &nbsp; &nbsp; &nbsp; &nbsp; 
                                    <input type='button' class='btn btn-dark  btn-xs' id="s-submit" value="Approve Direktur" 
                                          onclick="window.location.href='<?PHP echo "?module=approvedirekturklaimadm&idmenu=361&act=approvebyadmin"; ?>';">
                                <?PHP } ?>
                           </div>
                       </div>
                        
                        
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

            case "editdatattd":
                include "editttd.php";
            break;

            case "editdataperiode":
                include "editperiode.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

