<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['OTCPERENTY1'])) $tgl_pertama = $_SESSION['OTCPERENTY1'];
    if (!empty($_SESSION['OTCPERENTY2'])) $tgl_akhir = $_SESSION['OTCPERENTY2'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    //if (!empty($_SESSION['OTCKARYAWAN'])) 
        $fkaryawan = $_SESSION['OTCKARYAWAN'];
?>
<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                if ($_GET['act']=="tambahbaru")
                    echo "Tambah Baru Budget Request OTC";
                elseif ($_GET['act']=="editdata")
                    echo "Edit Data Budget Request OTC";
                elseif ($_GET['act']=="editterima")
                    echo "Edit Data Terima / Realisasi BR OTC";
                elseif ($_GET['act']=="edittransfer")
                    echo "Edit Data Transfer / Realisasi BR OTC";
                else
                    echo "Input Budget Request OTC";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_br_entryotc/aksi_entrybrotc.php";
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
                        var ket="";
                        var ekryid=document.getElementById('cb_karyawan').value;
                        var etgltipe=document.getElementById('cb_tgltipe').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var eisi=document.getElementById('cb_tipeisi').value;
                        var edivisi="";//document.getElementById('cb_divisi').value;
                        
                        
                        if (eisi=="D") {
                            
                            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                            $.ajax({
                                type:"post",
                                url:"module/mod_br_entryotc/viewdatatabelrealisasi.php?module="+ket,
                                data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uisi="+eisi+"&ukryid="+ekryid,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
                            
                        }else{
                        
                            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                            $.ajax({
                                type:"post",
                                url:"module/mod_br_entryotc/viewdatatabel.php?module="+ket,
                                data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uisi="+eisi+"&ukryid="+ekryid,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
                            
                        }
                    }
                    
                    
                    function TambahDataInputPajak(eidbr){
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entryotc/tambah_pajak.php?module=viewisipajak",
                            data:"uidbr="+eidbr,
                            success:function(data){
                                $("#myModal").html(data);
                            }
                        });
                    }
                    
                    
                    function ShowDataBR(skey) {
                        
                    }
                    
                </script>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        
                        
                        <div class='col-sm-3'>
                            Yang membuat
                            <div class="form-group">
                                <select class='form-control' id="cb_karyawan" name="cb_karyawan" onchange="">
                                    <?PHP
                                        //if ($pidgroup=="1" OR $pidgroup=="24") {
                                            $query = "select karyawanId as karyawanid, nama as nama_karyawan from hrd.karyawan WHERE 1=1 ";
                                            $query .=" AND jabatanid NOT IN ('15', '10', '18', '08', '20', '05')";
                                        //}else{
                                        //    $query = "select karyawanId as karyawanid, nama as nama_karyawan from hrd.karyawan WHERE karyawanId='$fkaryawan' ";
                                        //}
                                        $query .= " Order by nama, karyawanId";
                                        $tampilket= mysqli_query($cnmy, $query);
                                        $ketemu=mysqli_num_rows($tampilket);
                                        //if ((INT)$ketemu<=0) 
                                        echo "<option value='' selected>-- Pilih --</option>";

                                        while ($du= mysqli_fetch_array($tampilket)) {
                                            $nidkry=$du['karyawanid'];
                                            $nnmkry=$du['nama_karyawan'];
                                            $nidkry_=(INT)$nidkry;
                                            if ($nidkry==$fkaryawan)
                                                echo "<option value='$nidkry' selected>$nnmkry ($nidkry_)</option>";
                                            else
                                                echo "<option value='$nidkry'>$nnmkry ($nidkry_)</option>";

                                        }

                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        

                        <div class='col-sm-2'>
                            Type Input
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_tipeisi" name="cb_tipeisi">
                                    <option value="" selected>--Pilihan--</option>
                                    <?PHP
                                    $sa=""; $sb=""; $sc=""; $sd="";
                                    if ($_SESSION['OTCTIPE']=="A") $sa=" selected";
                                    if ($_SESSION['OTCTIPE']=="B") $sb=" selected";
                                    if ($_SESSION['OTCTIPE']=="C") $sc=" selected";
                                    if ($_SESSION['OTCTIPE']=="D") $sd=" selected";
                                    ?>
                                    <option value="A" <?PHP echo $sa; ?>>Isi Noslip</option>
                                    <option value="B" <?PHP echo $sb; ?>>Isi Transfer</option>
                                    <option value="C" <?PHP echo $sc; ?>>Isi Noslip & Transfer</option>
                                    <option value="D" <?PHP echo $sd; ?>>Isi Realisasi</option>
                                </select>
                            </div>
                        </div>
                        

                        <div class='col-sm-2'>
                            Periode By
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                    <?PHP
                                    $sa=""; $sb=""; $sc="";
                                    if ($_SESSION['OTCTGLTIPE']=="2") $sb=" selected";
                                    if ($_SESSION['OTCTGLTIPE']=="1") $sa=" selected";
                                    if ($_SESSION['OTCTGLTIPE']=="3") $sc=" selected";
                                    if (empty($_SESSION['OTCTGLTIPE'])) $sb="selected"
                                    ?>
                                    <option value="1" <?PHP echo $sa; ?>>Tanggal Input</option>
                                    <option value="2" <?PHP echo $sb; ?>>Tanggal Transfer</option>
                                    <option value="3" <?PHP echo $sc; ?>>Belum Transfer</option>
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
                                    //ComboSelectIsiDivisiProdFilter("", "","", "$fstsadmin", "$flvlposisi", "$fdivisi", "");
                                ?>
                            </div>
                        </div>
                        -->
                        
                        <div class='x_panel'>
                            <div class='col-sm-8'>
                                <small>&nbsp;</small>
                                <div class="form-group">
                                    <input type='button' class='btn btn-success  btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp; &nbsp; 
                                    <input type='button' class='btn btn-dark  btn-xs' id="s-submit" value="View Data PPH Via SBY" onclick="ShowDataBR('2')">
                                </div>
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
        
            case "editterima":
                include "terima.php";
            break;
        
            case "edittransfer":
                include "transfer.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

