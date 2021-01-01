<?PHP
    $hari_ini = date("Y-m-d");
	$tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    //$tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['FINRUTPERENTY1'])) $tgl_pertama = $_SESSION['FINRUTPERENTY1'];
    if (!empty($_SESSION['FINRUTPERENTY2'])) $tgl_akhir = $_SESSION['FINRUTPERENTY2'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $cabhide=" class='col-sm-2' ";
    $cabhide="hidden";
    if ($flvlposisi=="FF1" OR $flvlposisi=="FF2" OR $flvlposisi=="FF3" OR $flvlposisi=="FF4") $cabhide="hidden";
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Biaya Rutin";
                if (isset($_GET['ca'])) {
                    echo "Input $judul dari CA";
                }else{
                    if ($_GET['act']=="tambahbaru")
                        echo "Input $judul";
                    elseif ($_GET['act']=="editdata")
                        echo "Edit $judul";
                    elseif ($_GET['act']=="uploaddok")
                        echo "Upload Bukti $judul";
                    else
                        echo "Data $judul";
                }
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_brrutin/laporanbrbulan.php";
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
                        var ket="";
                        var etgltipe=document.getElementById('cb_tgltipe').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi=document.getElementById('cb_divisi').value;
                        var earea=document.getElementById('e_idarea').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_brrutin/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uidc="+eidc+"&uarea="+earea,
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
                
                
                <script>
                    function ProsesData(ket, noid){

                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                            if (r==true) {

                                var txt;
                                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                                    if (textket == null || textket == "") {
                                        txt = textket;
                                    } else {
                                        txt = textket;
                                    }
                                }

                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("demo-form2").action = "module/mod_br_brrutin/aksi_brrutin.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+txt+"&ket="+ket+"&id="+noid;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
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
                                        if ($_SESSION['FINRUTTGLTIPE']=="1") $sa=" selected";
                                        if ($_SESSION['FINRUTTGLTIPE']=="2") $sb=" selected";
                                        if ($_SESSION['FINRUTTGLTIPE']=="3") $sc=" selected";
                                        if ($_SESSION['FINRUTTGLTIPE']=="4") $sd=" selected";
                                        if (empty($_SESSION['FINRUTTGLTIPE'])) $sb="selected"
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


                            <div <?PHP echo $cabhide; ?>>
                                Divisi
                                <div class="form-group">
                                    <?PHP
                                    $pilih=$_SESSION['FINRUTDIV'];
                                    include "config/koneksimysqli_it.php";
                                    $query="SELECT DivProdId, nama FROM dbmaster.divprod where br='Y' ";
                                    if ($_SESSION['ADMINKHUSUS']=="Y") {
                                        if (!empty($_SESSION['KHUSUSSEL'])) $query .=" AND DivProdId in $_SESSION[KHUSUSSEL]";
                                    }
                                    $query .=" order by nama";
                                    $sql=mysqli_query($cnit, $query);
                                    echo "<select class='form-control input-sm' id='cb_divisi' name='cb_divisi'>";
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

                            <div <?PHP echo $cabhide; ?>>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_idarea' name='e_idarea'>
                                      <option value='' selected>-- Pilihan --</option>
                                      <?PHP
                                        include "config/koneksimysqli_it.php";
                                        $query = "select cabang.iCabangId, cabang.nama from dbmaster.icabang as cabang  order by nama"; 
                                        $result = mysqli_query($cnit, $query); 
                                        $record = mysqli_num_rows($result);
                                        for ($i=0;$i < $record;$i++) {
                                            $row = mysqli_fetch_array($result); 
                                            $kodeid  = $row['iCabangId'];
                                            $nama = $row['nama'];
                                            if ($_SESSION['FINRUTCAB']==$kodeid)
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
                                   
                                   <input type='hidden' class='btn btn-default btn-xs' id="s-print" value="List Data" onclick="disp_confirm('bukan')">
                                   <input type='hidden' class='btn btn-danger btn-xs' id="s-excel" value="Excel" onclick="disp_confirm('excel')">
                                   
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

            case "uploaddok":
                include "uploaddok.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

