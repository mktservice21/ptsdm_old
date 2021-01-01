<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Evaluasi Sales Outlet</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        /*
        $pmyidcard=$_SESSION['IDCARD'];
        $pidkrypilih=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        
        $pidcabangpil="";
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
            $query_cab_kry = "select DISTINCT karyawanid, icabangid from sls.imr0 WHERE CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) IN "
                    . " (select distinct CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) FROM sls.ispv0 WHERE karyawanid='$pmyidcard')";
        }elseif ($pmyjabatanid=="08") {
            $query_cab_kry = "select DISTINCT karyawanid, icabangid from sls.imr0 WHERE IFNULL(icabangid,'') IN "
                    . " (select distinct IFNULL(icabangid,'') FROM sls.idm0 WHERE karyawanid='$pmyidcard')";
        }elseif ($pmyjabatanid=="20") {
            $query_cab_kry = "select DISTINCT karyawanid, icabangid from sls.imr0 WHERE IFNULL(icabangid,'') IN "
                    . " (select distinct IFNULL(icabangid,'') FROM sls.ism0 WHERE karyawanid='$pmyidcard')";
        }elseif ($pmyjabatanid=="15") {
            $query_cab_kry = "select DISTINCT karyawanid, icabangid from sls.imr0 WHERE karyawanid='$pmyidcard' ";
        }else{
            $query_cab_kry = "select DISTINCT karyawanid, icabangid from sls.imr0 WHERE 1=1 ";
            if (!empty($filtercabangbyadmin)) $query_cab_kry .= " AND IFNULL(icabangid,'') IN $filtercabangbyadmin ";
        }
        
        if (!empty($query_cab_kry)) {
            $filtercabangbyadmin="";
            $tampil= mysqli_query($cnms, $query_cab_kry);
            while ($rs= mysqli_fetch_array($tampil)) {
                $pikryid_=$rs['karyawanid'];
                $pidcabangpil=$rs['icabangid'];
                
                $picabid_=$rs['icabangid'];
                
                $filter_karyawan .="'".$pikryid_."',";
                
                if (strpos($filtercabangbyadmin, $picabid_)==false) $filtercabangbyadmin .="'".$picabid_."',";
                
            }
            
            if (!empty($filtercabangbyadmin)) {
                $filtercabangbyadmin=" AND IFNULL(icabangid,'') IN (".substr($filtercabangbyadmin, 0, -1).")";
            }
            
            if (!empty($filter_karyawan)) {
                $filter_karyawan="(".substr($filter_karyawan, 0, -1).")";
            }            
        }
        
        
        $ppilihregion="";
        $query = "select distinct region from sls.icabang where icabangid='$pidcabangpil'";
        $tampil= mysqli_query($cnms, $query);
        $reg= mysqli_fetch_array($tampil);
        $ppilihregion=$reg['region'];
        
        //buka dulu semua cabang
        if ($pmyjabatanid=="08" OR $pmyjabatanid=="20" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18") {
            $filtercabangbyadmin="";
        }
        //end buka dulu semua cabang
        */
        
        $pbukadarihp=$_SESSION['MOBILE'];
        $ptargetblank=" target=\"_blank\" ";
        if ($pbukadarihp=="Y") $ptargetblank="";
		
        $pmyidcard=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        //$pmyidcard="0000000649";
        //$pmyjabatanid="08";
        
        $filter_karyawan="('$pmyidcard')";
                
        $pidcabangpil="";
        $pidareapil="";
        $piddivisipil="";
        $pfilterregionpilih="";
        
        $ptextcabang="";
        $ptextcabarea="";
        
        $pnviddivisipil="";
        $pfiltercabpilih="";
        $pfilterareapilih="";
        $pfilterdivisipilih="";
        $pfiltercabarea="";
        $pjmldivisicover=0;
        
        
        if ($pmyjabatanid=="15" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08") {
            if ($pmyjabatanid=="15") {
                $query_cab = "select distinct icabangid, areaid, divisiid FROM sls.imr0 WHERE karyawanid='$pmyidcard'";
            }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
                $query_cab = "select distinct icabangid, areaid, divisiid FROM sls.ispv0 WHERE karyawanid='$pmyidcard'";
            }elseif ($pmyjabatanid=="08") {
                $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM sls.idm0 WHERE karyawanid='$pmyidcard'";
            }
            $tampil= mysqli_query($cnms, $query_cab);
            while ($rs= mysqli_fetch_array($tampil)) {
                $vbicabangid=$rs['icabangid'];
                $vbareaid=$rs['areaid'];
                $vbdivisi=$rs['divisiid'];
                
                if (!empty($vbicabangid)) $pidcabangpil=$vbicabangid;
                
                if (strpos($pfiltercabpilih, $vbicabangid)==false) $pfiltercabpilih .="'".$vbicabangid."',";
                if (!empty($vbareaid)) {
                    if (strpos($pfilterareapilih, $vbareaid)==false) $pfilterareapilih .="'".$vbareaid."',";
                }
                
                if (!empty($vbdivisi)) {
                    if (strpos($pfilterdivisipilih, $vbdivisi)==false) {
                        $pfilterdivisipilih .="'".$vbdivisi."',";

                        $pjmldivisicover++;
                        $pnviddivisipil=$vbdivisi;

                    }
                }
                
                if (strpos($pfiltercabarea, $vbicabangid.$vbareaid)==false) $pfiltercabarea .="'".$vbicabangid.$vbareaid."',";
                
            }
            
        }else{
            
            $query_cab = "select distinct icabangid from hrd.rsm_auth WHERE karyawanid='$pmyidcard'";
            $tampil= mysqli_query($cnmy, $query_cab);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($rs= mysqli_fetch_array($tampil)) {
                    $vbicabangid=$rs['icabangid'];
                    
                    if (!empty($vbicabangid)) $pidcabangpil=$vbicabangid;
                    if (strpos($pfiltercabpilih, $vbicabangid)==false) $pfiltercabpilih .="'".$vbicabangid."',";
                    
                }
            }
            
        }
        
        
        
        $pkaryawanareakosong=false;
        $pcaridarikry=true;
        if ($pmyjabatanid=="15" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="38" OR $pmyjabatanid=="39") {
            $pcaridarikry=false;
            if (empty($pfiltercabpilih)) {
                $pcaridarikry=true;
                $pkaryawanareakosong=true;
            }
        }
        
        if ($pcaridarikry==true) {
            $queryk = "select icabangid, areaId, divisiid from ms.karyawan where karyawanid='$pmyidcard'";
            $tampilk= mysqli_query($cnms, $queryk);
            $nk= mysqli_fetch_array($tampilk);
            if (!empty($nk['icabangid'])) {
                $pidcabangpil=$nk['icabangid'];
            }
            if (!empty($nk['areaId'])) {
                $pidareapil=$nk['areaId'];
            }
            $sdivid_divisi="";
            if (!empty($nk['divisiid'])) {
                $sdivid_divisi=$nk['divisiid'];
            }
            
            if ($pkaryawanareakosong==true) {
                $pfiltercabpilih="'$pidcabangpil',";
                $pfilterareapilih="'$pidareapil',";
                $pfilterdivisipilih="'$sdivid_divisi',";
                $pfiltercabarea="'".$pidcabangpil.$pidareapil."',";
            }
                
        }
        
        $ptextcabang=$pfiltercabpilih;
        if (!empty($pfiltercabpilih)) $pfiltercabpilih="(".substr($pfiltercabpilih, 0, -1).")";
        if (!empty($pfilterareapilih)) $pfilterareapilih="(".substr($pfilterareapilih, 0, -1).")";
        if (!empty($pfilterdivisipilih)) $pfilterdivisipilih="(".substr($pfilterdivisipilih, 0, -1).")";
        $ptextcabarea=$pfiltercabarea;
        if (!empty($pfiltercabarea)) $pfiltercabarea="(".substr($pfiltercabarea, 0, -1).")";
        
        if ($pjmldivisicover==1 AND $pmyjabatanid=="15") $piddivisipil=$pnviddivisipil;
        
        
        if (!empty($pfiltercabpilih)) {
            $query = "select distinct region from sls.icabang where icabangid IN $pfiltercabpilih";
            $tampil= mysqli_query($cnms, $query);
            while ($nr= mysqli_fetch_array($tampil)) {
                if (!empty($nr['region'])) $pvbregion=$nr['region'];
                if (strpos($pfilterregionpilih, $pvbregion)==false) $pfilterregionpilih .="'".$pvbregion."',";
            }
            if (!empty($pfilterregionpilih)) $pfilterregionpilih="(".substr($pfilterregionpilih, 0, -1).")";
        }
        
        //echo "$pfilterregionpilih<br/>$pidcabangpil<br/>$pfiltercabpilih<br/>$pfilterareapilih<br/>$pfilterdivisipilih<br/>$pfiltercabarea<br/>";
        
        
        
        
        
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                //$tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                $tgl_pertama = date('Y', strtotime($hari_ini));
                ?>
                <script>
                    function disp_confirm(pText)  {
                        var eoutcb = document.getElementById("cb_outlet").value;
                        if (eoutcb=="") {
                            alert("outlet harus diisi....");
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tahun <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='thn01'>
                                                        <input type='text' id='tahun' name='tahun' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    
                                    <?PHP 
                                    $hiddenmr="";
                                    $hiddennotmr="hidden";
                                    if ($pmyjabatanid=="15") { 
                                        $hiddenmr="";
                                        $hiddennotmr="hidden";
                                    }
                                    ?>
                                    <?PHP echo "<div $hiddenmr>"; ?>
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Cabang <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                
                                                <input type="hidden" id="txt_idcard" name="txt_idcard" value="<?PHP echo $pmyidcard;?>">
                                                <input type="hidden" id="txt_jbt" name="txt_jbt" value="<?PHP echo $pmyjabatanid;?>">
                                                <input type="hidden" id="txt_cabang" name="txt_cabang" value="<?PHP echo $ptextcabang;?>">
                                                <input type="hidden" id="txt_cabarea" name="txt_cabarea" value="<?PHP echo $ptextcabarea;?>">
                                                
                                                
                                                <select class='form-control' name='cb_cabang' id='cb_cabang' onchange="ShowDataPilihArea()">
                                                    <?PHP
                                                    if ($_SESSION['IDCARD']=="0000000175") {
														echo "<option value=''>--Pilihan--</option>";
													}else{
														$query = "select iCabangId, nama, aktif from sls.icabang where 1=1 ";
														$query .=" AND aktif='Y' ";
														if(!empty($pfilterregionpilih)) $query .=" AND region IN $pfilterregionpilih ";
														
														if (!empty($pfiltercabpilih)) {
															if ($pmyjabatanid=="15" OR $pmyjabatanid=="38" OR $pmyjabatanid=="39") $query .=" AND iCabangId IN $pfiltercabpilih ";
														}
														$query .=" order by aktif DESC, nama";
														$tampil = mysqli_query($cnms, $query);
														while ($rx= mysqli_fetch_array($tampil)) {
															$nidcab=$rx['iCabangId'];
															$nnmcab=$rx['nama'];
															$nnmaktif=$rx['aktif'];
															
															$namaaktif="Aktif";
															if ($nnmaktif!="Y") $namaaktif="Non Aktif";
															
															if ($pidcabangpil==$nidcab)
																echo "<option value='$nidcab' selected>$nnmcab</option>";
															else
																echo "<option value='$nidcab'>$nnmcab</option>";
														}
														
														
														$query = "select iCabangId, nama, aktif from sls.icabang where 1=1 ";
														$query .=" AND IFNULL(aktif,'')<>'Y' ";
														$query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
														if(!empty($pfilterregionpilih)) $query .=" AND region IN $pfilterregionpilih ";
														
														if (!empty($pfiltercabpilih)) {
															if ($pmyjabatanid=="15" OR $pmyjabatanid=="38" OR $pmyjabatanid=="39") $query .=" AND iCabangId IN $pfiltercabpilih ";
														}
														$query .=" order by aktif DESC, nama";
														$tampil = mysqli_query($cnms, $query);
														$ketemunon=mysqli_num_rows($tampil);
														if ($ketemunon>0) {
															echo "<option value='NONAKTIFPL'></option>";
															echo "<option value='NONAKTIFPL'>-- Non Aktif --</option>";
															while ($rx= mysqli_fetch_array($tampil)) {
																$nidcab=$rx['iCabangId'];
																$nnmcab=$rx['nama'];
																$nnmaktif=$rx['aktif'];
																
																$namaaktif="Aktif";
																if ($nnmaktif!="Y") $namaaktif="Non Aktif";
																
																if ($pidcabangpil==$nidcab)
																	echo "<option value='$nidcab' selected>$nnmcab</option>";
																else
																	echo "<option value='$nidcab'>$nnmcab</option>";
															}
														}
													
                                                    }
                                                    /*
                                                    if ($pmyjabatanid!="15" AND $pmyjabatanid!="10" AND $pmyjabatanid!="18" AND $pmyjabatanid!="08")  echo "<option value=''>-- Pilih --</option>";
                                                    
                                                    $query = "select iCabangId, nama from sls.icabang where 1=1 AND aktif='Y' ";
                                                    
                                                    if ($pmyjabatanid=="08" OR $pmyjabatanid=="20" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18") {
                                                        $query .=" AND region='$ppilihregion' ";
                                                    }else{
                                                        $query .=" $filtercabangbyadmin ";
                                                    }
                                                    $query .=" order by aktif DESC, nama";
                                                    
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidcab=$rx['iCabangId'];
                                                        $nnmcab=$rx['nama'];
                                                        if ($pidcabangpil==$nidcab)
                                                            echo "<option value='$nidcab' selected>$nnmcab</option>";
                                                        else
                                                            echo "<option value='$nidcab'>$nnmcab</option>";
                                                    }
                                                     * 
                                                     */
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_area'>Area <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_area' id='cb_area' onchange="ShowDataCusId()">
                                                    <?PHP
                                                    
                                                    echo "<option value=''>-- All --</option>";
                                                    
                                                    $query = "select areaid, nama, aktif from sls.iarea where 1=1 AND icabangid='$pidcabangpil' ";
                                                    
                                                    //$query .=" AND aktif='Y' ";
                                                    
                                                    if (!empty($pfiltercabarea)) {
                                                        if ($pmyjabatanid=="15") $query .=" AND CONCAT(icabangid, areaid) IN $pfiltercabarea ";
                                                        if ($pmyjabatanid=="10" OR $pmyjabatanid=="18") $query .=" AND (CONCAT(icabangid, areaid) IN $pfiltercabarea OR IFNULL(aktif,'')<>'Y') ";
                                                    }
                                                    $query .=" order by aktif DESC, nama";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidarea=$rx['areaid'];
                                                        $nnmarea=$rx['nama'];
                                                        $nnmaktif=$rx['aktif'];

                                                        $namaaktif="Aktif";
                                                        if ($nnmaktif!="Y") $namaaktif="Non Aktif";

                                                        echo "<option value='$nidarea'>$nnmarea ($namaaktif)</option>";
                                                    }
                                                    
                                                    
                                                    /*
                                                    echo "<option value=''>-- All --</option>";
                                                    if (!empty($pidcabangpil)) {
                                                        $query = "select * from sls.iarea where aktif='Y' AND icabangid='$pidcabangpil' order by nama";
                                                        $tampil = mysqli_query($cnms, $query);
                                                        while ($rx= mysqli_fetch_array($tampil)) {
                                                            $nidarea=$rx['areaid'];
                                                            $nnmarea=$rx['nama'];
                                                            
                                                            echo "<option value='$nidarea'>$nnmarea</option>";
                                                        }
                                                    }else{
                                                        if ($pmyidcard=="0000002297") {
                                                            $query = "select * from sls.iarea where aktif='Y' $filtercabangbyadmin order by nama";
                                                            $tampil = mysqli_query($cnms, $query);
                                                            while ($rx= mysqli_fetch_array($tampil)) {
                                                                $nidarea=$rx['areaid'];
                                                                $nnmarea=$rx['nama'];

                                                                echo "<option value='$nidarea'>$nnmarea</option>";
                                                            }    
                                                        }
                                                    }
                                                     */   
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    <?PHP echo "</div>"; ?>
                                        
                                    <?PHP echo "<div $hiddennotmr>"; ?>
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>MR <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>

                                                <select class='form-control' name='cb_mr' id='cb_mr' onchange="ShowDataCusId()">
                                                    <?PHP
                                                    $query_kry="";
                                                    if ($pmyjabatanid=="15") {
                                                        $query_kry = "select b.karyawanId, b.nama from ms.karyawan b WHERE karyawanId='$pmyidcard' order by b.nama";
                                                    }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08" OR $pmyjabatanid=="20") {
                                                        $query_kry = "select b.karyawanId, b.nama from ms.karyawan b WHERE b.karyawanid IN $filter_karyawan  order by b.nama";
                                                    }else{
                                                        $query_kry = "select b.karyawanId, b.nama from ms.karyawan b WHERE b.karyawanid IN $filter_karyawan  "
                                                                . " AND IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='' ";
                                                        $query_kry .=" ORDER BY b.nama";
                                                    }
                                                    
                                                    $no=1;
                                                    if (!empty($query_kry)) {
                                                        $tampil = mysqli_query($cnms, $query_kry);
                                                        $ketemu= mysqli_num_rows($tampil);
                                                        if ($ketemu==0) echo "<option value=''>-- Pilih --</option>";
                                                        while ($rx= mysqli_fetch_array($tampil)) {
                                                            $nidkry=$rx['karyawanId'];
                                                            $nnmkry=$rx['nama'];
                                                            if ($no==1) {
                                                                $pidkrypilih=$nidkry;
                                                                echo "<option value='$nidkry' selected>$nnmkry</option>";
                                                            }else{
                                                                echo "<option value='$nidkry'>$nnmkry</option>";
                                                            }
                                                            
                                                            $no++;
                                                        }
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>
                                        
                                    <?PHP echo "</div>"; ?>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Outlet <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>

                                                <select class='form-control' name='cb_outlet' id='cb_outlet'>
                                                    <?PHP
                                                    echo "<option value=''>-- Pilih --</option>";
                                                    /*
                                                    $query = "SELECT iCabangId, areaId, iCustId, nama from sls.icust WHERE CONCAT(iCabangId,areaId) IN "
                                                            . " (SELECT DISTINCT CONCAT(iCabangId,areaId) FROM sls.imr0 WHERE karyawanId='$pidkrypilih') "
                                                            . " order by CASE WHEN IFNULL(nama,'')='' then 'zzzz' else LTRIM(nama) end";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    $ketemu= mysqli_num_rows($tampil);
                                                    if ($ketemu==0) echo "<option value=''>-- Pilih --</option>";
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidcab=$rx['iCabangId'];
                                                        $nidarea=$rx['areaId'];
                                                        $nidcustid=$rx['iCustId'];
                                                        $nidcustnm=$rx['nama'];
                                                        
                                                        $pigrpkode=$nidcab.$nidarea.$nidcustid;
                                                        
                                                        echo "<option value='$pigrpkode'>$nidcustnm</option>";
                                                        
                                                    }
                                                     * 
                                                     */
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
                    $(document).ready(function() {
                        ShowDataArea();
                    } );
                    
                    function ShowDataPilihArea() {
                        ShowDataArea();
                    }
                    function ShowDataArea() {
                        var ecabid = document.getElementById("cb_cabang").value;
                        var txtcab = document.getElementById("txt_cabang").value;
                        var txtcabarea = document.getElementById("txt_cabarea").value;
                        
                        var txtidcard = document.getElementById("txt_idcard").value;
                        var txtjbt = document.getElementById("txt_jbt").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_evalslsout/viewdata.php?module=cariareacabang",
                            data:"ucabid="+ecabid+"&utxtcab="+txtcab+"&utxtcabarea="+txtcabarea+"&utxtidcard="+txtidcard+"&utxtjbt="+txtjbt,
                            success:function(data){
                                $("#cb_area").html(data);
                                ShowDataCusId();
                            }
                        });
                    }
                    
                    function ShowDataCusId() {
                        var emr = document.getElementById("cb_mr").value;
                        var ecabid = document.getElementById("cb_cabang").value;
                        var eareaid = document.getElementById("cb_area").value;
                        if (eareaid=="") {
                            $("#cb_outlet").html("<option value=''>--Pilih--</option>");
                            return false;
                        }
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_evalslsout/viewdata.php?module=caridatacustid",
                            data:"umr="+emr+"&ucabid="+ecabid+"&uareaid="+eareaid,
                            success:function(data){
                                $("#cb_outlet").html(data);
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