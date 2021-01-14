<?php
    include "config/cek_akses_modul.php";
?>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Sales YTD Per Cabang</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        
        $pmyidcard=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        //$pmyidcard="0000002254";
        $pidcabangpil="";
        $piddivisipil="EAGLE";
        $filiddivisipil="";
        $filtercabangbyadmin="";
        $query = "select distinct icabangid from hrd.rsm_auth WHERE karyawanid='$pmyidcard'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($rs= mysqli_fetch_array($tampil)) {
                $picabid_=$rs['icabangid'];
                $filtercabangbyadmin .="'".$picabid_."',";
            }
            if (!empty($filtercabangbyadmin)) {
                $filtercabangbyadmin="(".substr($filtercabangbyadmin, 0, -1).")";
            }
        }
        
        $ilewat=false;
        if ($pmyidcard=="0000002297") {
            
        }else{
            
            if ($pmyjabatanid=="15") {
                $query_cab = "select distinct icabangid, divisiid FROM sls.imr0 WHERE karyawanid='$pmyidcard'";
                $ilewat=true;
            }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
                $query_cab = "select distinct icabangid, divisiid FROM sls.ispv0 WHERE karyawanid='$pmyidcard'";
                $ilewat=true;
            }elseif ($pmyjabatanid=="08") {
                $query_cab = "select distinct icabangid, '' as divisiid FROM sls.idm0 WHERE karyawanid='$pmyidcard'";
                $ilewat=true;
            }elseif ($pmyjabatanid=="20") {
                $query_cab = "select distinct icabangid, '' as divisiid FROM sls.ism0 WHERE karyawanid='$pmyidcard'";
                $ilewat=true;
            }elseif ($pmyjabatanid=="05") {
                if ($pmyidcard=="0000000158") {
                    $query_cab = "select distinct a.icabangid, '' as divisiid FROM sls.ism0 a JOIN sls.icabang b on a.icabangid=b.icabangid WHERE b.region='B'";
					$ilewat=true;
                }elseif ($pmyidcard=="0000000159") {
                    $query_cab = "select distinct a.icabangid, '' as divisiid FROM sls.ism0 a JOIN sls.icabang b on a.icabangid=b.icabangid WHERE b.region='T'";
					$ilewat=true;
                }
            }
        }
        
        if ($ilewat==true) {
            $filtercabangbyadmin="";
            
            $tampil= mysqli_query($cnms, $query_cab);
            while ($rs= mysqli_fetch_array($tampil)) {
                $picabid_=$rs['icabangid'];
                $pidcabangpil=$rs['icabangid'];
                $piddivi_=$rs['divisiid'];
                
                if (strpos($filtercabangbyadmin, $picabid_)==false) $filtercabangbyadmin .="'".$picabid_."',";
                
                if (!empty($piddivi_)) {
                    $piddivisipil=$rs['divisiid'];
                    
                    if (strpos($filiddivisipil, $piddivi_)==false) $filiddivisipil .="'".$piddivi_."',";
                }
                
            }
            
            
            if (!empty($filtercabangbyadmin)) {
                $filtercabangbyadmin="(".substr($filtercabangbyadmin, 0, -1).")";
            }
            
            if (!empty($filiddivisipil)) {
                $filiddivisipil="(".substr($filiddivisipil, 0, -1).")";
            }
            
        }
        
        
        if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="24") {
            $filtercabangbyadmin="";
        }else{
            if (!empty($filtercabangbyadmin)) $filtercabangbyadmin = " AND iCabangId IN $filtercabangbyadmin ";
            if (!empty($filiddivisipil)) $filiddivisipil = " AND DivProdId IN $filiddivisipil ";

        }
        
        
        
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
                            //alert("cabang harus diisi....");
                            //return false;
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
													<?PHP
													if ($_SESSION['IDCARD']=="0000000175") {
														echo "<option value=''>--Pilihan--</option>";
													}else{
													?>
                                                    <option value="" selected>--All--</option>
                                                    <option value="B">Barat</option>
                                                    <option value="T">Timur</option>
													<?PHP } ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Cabang <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_cabang' id='cb_cabang'>
                                                    <?PHP
													if ($_SESSION['IDCARD']=="0000000175") {
														echo "<option value=''>--Pilihan--</option>";
													}else{
														if ($pmyjabatanid!="15" AND $pmyjabatanid!="10" AND $pmyjabatanid!="18")  echo "<option value=''>-- Pilih --</option>";
														
														$query = "select iCabangId, nama from sls.icabang where "
																. " aktif='Y' $filtercabangbyadmin ";
														$query .=" order by nama";
														$tampil = mysqli_query($cnms, $query);
														while ($rx= mysqli_fetch_array($tampil)) {
															$nidcab=$rx['iCabangId'];
															$nnmcab=$rx['nama'];
															if ($pidcabangpil==$nidcab)
																echo "<option value='$nidcab' selected>$nnmcab</option>";
															else
																echo "<option value='$nidcab'>$nnmcab</option>";
														}
														
														$query = "select iCabangId, nama from sls.icabang where "
																. " IFNULL(aktif,'')<>'Y' ";
														$query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
														$query .=" order by nama";
														$tampil = mysqli_query($cnms, $query);
														$ketemunon=mysqli_num_rows($tampil);
														if ($ketemunon>0) {
															echo "<option value='NONAKTIFPL'></option>";
															echo "<option value='NONAKTIFPL'>-- Non Aktif --</option>";
															while ($rx= mysqli_fetch_array($tampil)) {
																$nidcab=$rx['iCabangId'];
																$nnmcab=$rx['nama'];
																if ($pidcabangpil==$nidcab)
																	echo "<option value='$nidcab' selected>$nnmcab</option>";
																else
																	echo "<option value='$nidcab'>$nnmcab</option>";
															}
														}
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
                            url:"module/sls_slsytdpercab/viewdata.php?module=caricabangregion",
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