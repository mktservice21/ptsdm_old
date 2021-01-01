<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Sales MR</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        
        $pbukadarihp=$_SESSION['MOBILE'];
        $ptargetblank=" target=\"_blank\" ";
        if ($pbukadarihp=="Y") $ptargetblank="";
        
        $pmyidcard=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        
        
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
        
        if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="24") {
            $filtercabangbyadmin="";
        }
        
        $filter_karyawan="";
        $query_cab_kry = "";
        if ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
            $query_cab_kry = "select DISTINCT karyawanid from sls.imr0 WHERE CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) IN "
                    . " (select distinct CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) FROM sls.ispv0 WHERE karyawanid='$pmyidcard')";
        }elseif ($pmyjabatanid=="08") {
            $query_cab_kry = "select DISTINCT karyawanid from sls.imr0 WHERE IFNULL(icabangid,'') IN "
                    . " (select distinct IFNULL(icabangid,'') FROM sls.idm0 WHERE karyawanid='$pmyidcard')";
        }elseif ($pmyjabatanid=="20") {
            $query_cab_kry = "select DISTINCT karyawanid from sls.imr0 WHERE IFNULL(icabangid,'') IN "
                    . " (select distinct IFNULL(icabangid,'') FROM sls.ism0 WHERE karyawanid='$pmyidcard')";
        }else{
            $query_cab_kry = "select DISTINCT karyawanid from sls.imr0 WHERE 1=1 ";
            if (!empty($filtercabangbyadmin)) $query_cab_kry .= " AND IFNULL(icabangid,'') IN $filtercabangbyadmin ";
        }
        
        if (!empty($query_cab_kry)) {
            $tampil= mysqli_query($cnms, $query_cab_kry);
            while ($rs= mysqli_fetch_array($tampil)) {
                $pikryid_=$rs['karyawanid'];
                
                $filter_karyawan .="'".$pikryid_."',";
                
            }
            
            if (!empty($filter_karyawan)) {
                $filter_karyawan="(".substr($filter_karyawan, 0, -1).")";
            }            
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
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>" enctype='multipart/form-data' <?PHP echo $ptargetblank; ?> >
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>MR <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>

                                                <select class='form-control' name='cb_mr' id='cb_mr'>
                                                    <?PHP
													if ($_SESSION['IDCARD']=="0000000175") {
														echo "<option value=''>-- Pilihan --</option>";
													}else{
														$query_kry="";
														if ($pmyjabatanid=="15") {
															$query_kry = "select b.karyawanId, b.nama from ms.karyawan b WHERE karyawanId='$pmyidcard' order by b.nama";
														}elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08" OR $pmyjabatanid=="20") {
															$query_kry = "select b.karyawanId, b.nama from ms.karyawan b WHERE b.karyawanid IN $filter_karyawan  ";
															/*
															$query_kry .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
																	. " and LEFT(b.nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
																	. " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
																	. " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
																	. " AND LEFT(b.nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
															$query_kry .= " AND b.nama NOT IN ('ACCOUNTING')";
															*/
															$query_kry .=" ORDER BY b.nama";
														}else{
															$query_kry = "select b.karyawanId, b.nama from ms.karyawan b WHERE b.karyawanid IN $filter_karyawan  "
																	. " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
																	/*
															$query_kry .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
																	. " and LEFT(b.nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
																	. " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
																	. " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
																	. " AND LEFT(b.nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
																	*/
															$query_kry .= " AND b.nama NOT IN ('ACCOUNTING')";
															$query_kry .=" ORDER BY b.nama";
														}
														
														if (!empty($query_kry)) {
															$tampil = mysqli_query($cnms, $query_kry);
															$ketemu= mysqli_num_rows($tampil);
															if ($ketemu==0) echo "<option value=''>-- Pilihan --</option>";
															while ($rx= mysqli_fetch_array($tampil)) {
																$nidkry=$rx['karyawanId'];
																$nnmkry=$rx['nama'];
																echo "<option value='$nidkry'>$nnmkry</option>";
															}
														}
													}
                                                    ?>
                                                </select>
                                                
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>&nbsp; <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="checkbox" id="chkboth" name="chkboth" value="Y" /> Include Produk Other Peacock
                                                <br/>( produk other peacock tidak masuk achievement MR, SPV/AM, DM )
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <!--end kiri-->

                        </form>
                    </div><!--end xpanel-->
                </div>
                

                
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>