<?PHP include "config/cek_akses_modul.php"; ?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Laporan Data Stock</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        
        $fkaryawan=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        $pmygroupid=$_SESSION['GROUP'];
        $pidcabangpil="";
        include ("config/koneksimysqli_ms.php");
        
        $pdivisipm="";
        $query = "SELECT DISTINCT divprodid FROM ms.penempatan_pm where karyawanid='$fkaryawan'";
        $tampil= mysqli_query($cnms, $query);
        $nrow= mysqli_fetch_array($tampil);
        $pdivisipm=$nrow['divprodid'];
        
        if ($pmygroupid=="48" OR $pmygroupid=="51" OR $pmygroupid=="38") $pdivisipm= "OTC";
        
        $pmobilepilih=$_SESSION['MOBILE'];
                
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('d F Y', strtotime($hari_ini));
                
                $tgl_pertama = date('F Y', strtotime('+2 year', strtotime($hari_ini)));
                
                ?>
                <script>
                    function disp_confirm(pText)  {
                        if (pText == "excel") {
                            document.getElementById("form1").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("form1").submit();
                            return 1;
                        }else{
                            document.getElementById("form1").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("form1").submit();
                            return 1;
                        }
                    }
                </script>
                
                <style>
                    .grp-periode, .input-periode, .control-periode {
                        margin-bottom:2px;
                    }
                </style>
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>" enctype='multipart/form-data' target="_blank">
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                                    <?PHP if ($pmobilepilih!="Y") { ?>
                                    <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                                    <?PHP } ?>
                                    <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                                </h2>
                                <div class='clearfix'></div>
                            </div>

                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                            <div class='col-xs-8'>
                                                <select class='form-control' name='cb_divisi' id='cb_divisi' style='width: 100%;' onchange="ShowDataProduk()">
                                                    <?PHP
                                                    if (empty($pdivisipm)) echo "<option value='' selected>--All--</option>";
                                                    $query = "select divprodid, nama from MKT.divprod WHERE 1=1 ";
                                                    if (!empty($pdivisipm)) $query .=" AND divprodid='$pdivisipm' ";
                                                    else $query .=" AND br='Y' and divprodid NOT IN ('HO', 'CAN') ";
                                                    $query .="order by divprodid";
                                                    $tampil= mysqli_query($cnmy, $query);
                                                    while($na= mysqli_fetch_array($tampil)) {
                                                        $niprodid=$na['divprodid'];
                                                        $niprodnm=$na['nama'];
                                                        if ($niprodid==$pdivisipm)
                                                            echo "<option value='$niprodid' selected>$niprodnm</option>";
                                                        else
                                                            echo "<option value='$niprodid'>$niprodnm</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Produk <span class='required'></span></label>
                                            <div class='col-xs-8'>
                                                <select class='form-control' name='cb_produk' id='cb_produk' style='width: 100%;' onchange="">
                                                    <option value='' selected>--All--</option>
                                                    <?PHP
                                                    $query = "select divprodid, iprodid, nama from sls.iproduk WHERE aktif='Y' ";
                                                    if (!empty($pdivisipm)) $query .= " AND divprodid='$pdivisipm' ";
                                                    $query .= " ORDER BY divprodid, nama";
                                                    
                                                    $query = "select a.id, a.kdproduk, a.nmproduk, b.divprodid, a.iprodid from "
                                                            . " sls.imaping_produk a "
                                                            . " LEFT JOIN sls.iproduk b on a.iprodid=b.iprodid WHERE 1=1 ";
                                                    if (!empty($pdivisipm)) $query .= " AND (b.divprodid='$pdivisipm' OR IFNULL(a.iprodid,'')='') ";
                                                    $query .= " ORDER BY b.divprodid, a.nmproduk";
                                                    
                                                    $tampil= mysqli_query($cnms, $query);
                                                    while($na= mysqli_fetch_array($tampil)) {
                                                        $nidprod=$na['kdproduk'];
                                                        $nnmprod=$na['nmproduk'];
                                                        $nnmdivisi=$na['divprodid'];
                                                        if (empty($nnmdivisi)) $nnmdivisi="uncategorized";
                                                        
                                                        echo "<option value='$nidprod'>$nnmprod ($nnmdivisi)</option>";
                                                        
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Expired Date &nbsp;<input type="checkbox" id="chk_ed" name="chk_ed" onclick="cekBoxPilihExpDate()" value="Y"><span class='required'></span></label>
                                            <div class='col-xs-8'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='cbln01'>
                                                        <input type='hidden' id='e_periode01_' name='e_periode01_' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo ""; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <!--end kiri-->
                            
                            
                        </form>
                    </div><!--end xpanel-->
                </div>
                
                
                <script>
                    function ShowDataProduk() {
                        var edivisi =document.getElementById('cb_divisi').value;

                        $.ajax({
                            type:"post",
                            url:"module/laporan/stc_lapdatastock/viewdata.php?module=caridataproduk",
                            data:"udivisiid="+edivisi,
                            success:function(data){
                                $("#cb_produk").html(data);
                            }
                        });

                    }
                </script>

                
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>