<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['FINBLPERENTY1'])) $tgl_pertama = $_SESSION['FINBLPERENTY1'];
    if (!empty($_SESSION['FINBLPERENTY2'])) $tgl_akhir = $_SESSION['FINBLPERENTY2'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Budget Request Per Bulan";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_entrybrbulan/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >
                    function ShowCabangDivisi() {
                        var idiv = document.getElementById('cb_divisi').value;
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entrybrbulan/viewdata.php?module=viewareadivisi",
                            data:"udivi="+idiv,
                            success:function(data){
                                $("#e_idcabang").html(data);
                            }
                        });
                    }
                    
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var ket="";
                        var etgltipe=document.getElementById('cb_tgltipe').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi=document.getElementById('cb_divisi').value;
                        var ecabang=document.getElementById('e_idcabang').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entrybrbulan/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uidc="+eidc+"&ucabang="+ecabang,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                
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

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

                            <div hidden>
                                Periode By
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                        <?PHP
                                        $sa=""; $sb=""; $sc=""; $sd="";
                                        if ($_SESSION['FINBLTGLTIPE']=="1") $sa=" selected";
                                        if ($_SESSION['FINBLTGLTIPE']=="2") $sb=" selected";
                                        if ($_SESSION['FINBLTGLTIPE']=="3") $sc=" selected";
                                        if ($_SESSION['FINBLTGLTIPE']=="4") $sd=" selected";
                                        if (empty($_SESSION['FINBLTGLTIPE'])) $sb="selected"
                                        ?>
                                        <option value="1" <?PHP echo $sa; ?>>Last Input / Update</option>
                                        <option value="2" <?PHP echo $sb; ?>>Tanggal Transfer</option>
                                        <option value="3" <?PHP echo $sc; ?>>Tanggal Terima</option>
                                        <option value="4" <?PHP echo $sd; ?>>Tanggal Pengajuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Periode
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


                            <div class='col-sm-2'>
                                Divisi
                                <div class="form-group">
                                    <?PHP
                                    $pilih=$_SESSION['FINBLDIV'];
                                    include "config/koneksimysqli_ms.php";
                                    $query="SELECT DivProdId, nama FROM mkt.divprod where br='Y' ";
                                    if ($_SESSION['ADMINKHUSUS']=="Y") {
                                        //if (!empty($_SESSION['KHUSUSSEL'])) $query .=" AND DivProdId in $_SESSION[KHUSUSSEL]";
                                    }
                                    $query .=" order by nama";
                                    $sql=mysqli_query($cnms, $query);
                                    echo "<select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange=\"ShowCabangDivisi()\">";
                                    echo "<option value=''>-- Pilihan --</option>";
                                    while ($Xt=mysqli_fetch_array($sql)){
                                        if ($Xt['DivProdId']==$pilih)
                                            echo "<option value='$Xt[DivProdId]' selected>$Xt[DivProdId]</option>";
                                        else
                                            echo "<option value='$Xt[DivProdId]'>$Xt[DivProdId]</option>";
                                    }
                                    echo "</select>";
                                    ?>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_idcabang' name='e_idcabang'>
                                      <option value='' selected>-- Pilihan --</option>
                                      <?PHP
                                        
                                        if (trim($pilih)=="OTC") {
                                            $query = "select icabangid_o iCabangId, nama from dbmaster.v_icabang_o where "
                                                    . " (aktif='Y' and "
                                                    . " icabangid_o not in ('JKT_MT', 'JKT_RETAIL', 'MD', 'PM_ACNEMED', 'PM_CARMED', 'PM_LANORE', 'PM_MELANOX', 'PM_PARASOL') )"
                                                    . " order by nama"; 
                                        }else{
                                            $query = "select cabang.iCabangId, cabang.nama from dbmaster.icabang as cabang  order by nama";
                                        }
                                        $result = mysqli_query($cnmy, $query); 
                                        $record = mysqli_num_rows($result);
                                        for ($i=0;$i < $record;$i++) {
                                            $row = mysqli_fetch_array($result); 
                                            $kodeid  = $row['iCabangId'];
                                            $nama = $row['nama'];
                                            if ($_SESSION['FINBLCAB']==$kodeid)
                                                echo "<option value=\"$kodeid\" selected>$nama</option>";
                                            else
                                                echo "<option value=\"$kodeid\">$nama</option>";
                                        }
                                      ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                                   <input type='button' class='btn btn-default btn-xs' id="s-print" value="List Data" onclick="disp_confirm('bukan')">
                                   <input type='button' class='btn btn-danger btn-xs' id="s-excel" value="Excel" onclick="disp_confirm('excel')">
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

