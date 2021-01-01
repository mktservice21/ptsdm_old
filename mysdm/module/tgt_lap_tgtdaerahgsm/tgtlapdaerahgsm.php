<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Target Per Daerah (SM / GSM)</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        
        $pmyidcard=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        $pmygroupid=$_SESSION['GROUP'];
        $pidcabangpil="";
        
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                //$tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                $tgl_pertama = date('F Y', strtotime($hari_ini));
                ?>
                <script>
                    function disp_confirm(pText)  {
                        var ecabid = document.getElementById("cb_cabang").value;
                        if (ecabid=="") {
                            alert("cabang harus diisi....");
                            return false;
                        }
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
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>" enctype='multipart/form-data' target="_blank">
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                                    <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                                    <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                                </h2>
                                <div class='clearfix'></div>
                            </div>

                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cbln01'>Bulan <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='cbln01'>
                                                        <input type='text' id='cbln01' name='bulan' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Region <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_region' id='cb_region' onchange="ShowDataCabangRegion()">
                                                    <option value="" selected>--All--</option>
                                                    <?PHP
                                                        if ($pmyidcard=="0000000158") echo "<option value='B' selected>Barat</option>";
                                                        elseif ($pmyidcard=="0000000159") echo "<option value='T' selected>Timur</option>";
                                                        else{
                                                            echo "<option value='B'>Barat</option><option value='T'>Timur</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Cabang <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_cabang' id='cb_cabang'>
                                                    <?PHP
                                                    echo "<option value=''>-- Pilih --</option>";
                                                    $query = "SELECT distinct idcabang, nama from ms.cbgytd WHERE "
                                                            . " aktif='Y' ";
                                                    if ($pmygroupid=="1" OR $pmygroupid=="24") {
                                                    }else{
                                                        if ($pmyjabatanid=="20") $query .=" AND id_sm='$pmyidcard' ";
                                                        if ($pmyjabatanid=="08") $query .=" AND id_dm='$pmyidcard' ";
                                                        
                                                        if ($pmyidcard=="0000000158") $query .=" AND region='B' ";
                                                        if ($pmyidcard=="0000000159") $query .=" AND region='T' ";
                                                        
                                                    }
                                                    
                                                    $query .=" order by nama";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidcab=$rx['idcabang'];
                                                        $nnmcab=$rx['nama'];
                                                        if ($pidcabangpil==$nidcab)
                                                            echo "<option value='$nidcab' selected>$nnmcab</option>";
                                                        else
                                                            echo "<option value='$nidcab'>$nnmcab</option>";
                                                    }
                                                    ?>
                                                </select>
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
                    function ShowDataCabangRegion() {
                        var eregion = document.getElementById("cb_region").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/tgt_lap_tgtdaerahgsm/viewdata.php?module=caricabangregion",
                            data:"uregion="+eregion,
                            success:function(data){
                                $("#cb_cabang").html(data);
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