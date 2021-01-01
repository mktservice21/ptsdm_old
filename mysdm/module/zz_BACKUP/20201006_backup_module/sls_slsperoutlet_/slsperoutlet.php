<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Sales Per Outlet</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        /*
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
        if ($pmyidcard=="0000002297") {//JIMMY
            
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
        
        
        $ppilihregion="";
        $query = "select distinct region from sls.icabang where icabangid='$pidcabangpil'";
        $tampil= mysqli_query($cnms, $query);
        $reg= mysqli_fetch_array($tampil);
        $ppilihregion=$reg['region'];
        
        if ($pmyidcard=="0000002297") {//JIMMY
            $ppilihregion="B";
            $pidcabangpil="0000000107";
        }
        
        //untuk dm dan sm dibuka bisa lihat semua cabang tgl 19/02/2020 bpk. yakub
        if ($pmyjabatanid=="08" OR $pmyjabatanid=="20" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18") {
            $filtercabangbyadmin="";
        }
        */
        
        $pmyidcard=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        //$pmyidcard="0000000649";
        //$pmyjabatanid="08";
        
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cbln01'>Periode <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='cbln01'>
                                                        <input type='text' id='cbln01' name='bulan' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    
                                                    <div class='input-group date' id='cbln02'>
                                                        <input type='text' id='cbln02' name='bulan2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Cabang <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                
                                                <input type="hidden" id="txt_idcard" name="txt_idcard" value="<?PHP echo $pmyidcard;?>">
                                                <input type="hidden" id="txt_jbt" name="txt_jbt" value="<?PHP echo $pmyjabatanid;?>">
                                                <input type="hidden" id="txt_cabang" name="txt_cabang" value="<?PHP echo $ptextcabang;?>">
                                                <input type="hidden" id="txt_cabarea" name="txt_cabarea" value="<?PHP echo $ptextcabarea;?>">
                                                
                                                <select class='form-control' name='cb_cabang' id='cb_cabang' onchange="ShowDataArea()">
                                                    <?PHP
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
                                                    
                                                    /*
                                                    if ($pmyjabatanid!="15" AND $pmyjabatanid!="10" AND $pmyjabatanid!="18" AND $pmyjabatanid!="08")  echo "<option value=''>-- Pilih --</option>";
                                                    
                                                    $query = "select iCabangId, nama, aktif from sls.icabang where 1=1 AND aktif='Y' ";
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
                                                        $nnmaktif=$rx['aktif'];
                                                        
                                                        $namaaktif="Aktif";
                                                        if ($nnmaktif!="Y") $namaaktif="Non Aktif";
                                                        
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
                                                <select class='form-control' name='cb_area' id='cb_area'>
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
                                                        $query = "select * from sls.iarea where 1=1  ";
                                                        if ($pmyjabatanid=="15") {
                                                            $query .= " AND CONCAT(icabangid, areaid) IN (select distinct CONCAT(icabangid,areaid) FROM sls.imr0 WHERE karyawanid='$pmyidcard')";
                                                        }else{
                                                            $query .=" AND icabangid='$pidcabangpil' ";
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
                                                    }else{
                                                        if ($pmyidcard=="0000002297") {
                                                            $query = "select * from sls.iarea where 1=1 $filtercabangbyadmin ";
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
                                                        }
                                                    }
                                                     */   
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Divisi <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>

                                                <select class='form-control' name='cb_divisi' id='cb_divisi' onchange="ShowDataProduk()">
                                                <?PHP
                                                
                                                    if ($pjmldivisicover==1 AND $pmyjabatanid=="15") {
                                                    }else{
                                                        echo "<option value=''>-- All --</option>";
                                                    }
                                                    
                                                    $query="select DivProdId divisiId, nama from MKT.divprod where IFNULL(br,'')='Y' AND DivProdId NOT IN ('OTHER', 'OTC', 'HO', 'CAN') ";
                                                    if (!empty($pfilterdivisipilih)) {
                                                        if ($pmyjabatanid=="15") $query .=" AND DivProdId IN $pfilterdivisipilih ";
                                                    }
                                                    $query .=" order by divisiId";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $niddiv=$rx['divisiId'];
                                                        $nnmdiv=$rx['nama'];
                                                        if ($piddivisipil==$niddiv)
                                                            echo "<option value='$niddiv' selected>$nnmdiv</option>";
                                                        else
                                                            echo "<option value='$niddiv'>$nnmdiv</option>";
                                                    }
                                                    
                                                /*
                                                    echo "<option value=''>-- All --</option>";
                                                    $query = "select DivProdId divisiId, nama from MKT.divprod where "
                                                            . " IFNULL(br,'')='Y' AND DivProdId NOT IN ('OTHER', 'OTC', 'HO', 'CAN') $filiddivisipil "
                                                            . " order by divisiId";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $niddiv=$rx['divisiId'];
                                                        $nnmdiv=$rx['nama'];
                                                        if ($piddivisipil==$niddiv)
                                                            echo "<option value='$niddiv' selected>$nnmdiv</option>";
                                                        else
                                                            echo "<option value='$niddiv'>$nnmdiv</option>";
                                                    }
                                                    */
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Produk <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>

                                                <select class='form-control' name='cb_prod' id='cb_prod'>
                                                    <?PHP
                                                    echo "<option value=''>-- All --</option>";
                                                    $query = "select iprodid, nama from sls.iproduk where divprodid NOT IN ('OTC') ";
                                                    if (!empty($pfilterdivisipilih)) {
                                                        if ($pmyjabatanid=="15") $query .=" AND divprodid IN $pfilterdivisipilih ";
                                                    }
                                                    $query .=" order by nama";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidprod=$rx['iprodid'];
                                                        $nnmprod=$rx['nama'];
                                                        echo "<option value='$nidprod'>$nnmprod</option>";
                                                    }
                                                    
                                                    
                                                    /*
                                                    echo "<option value=''>-- All --</option>";
                                                    $query = "select iprodid, nama from sls.iproduk where divprodid NOT IN ('OTC') AND divprodid='$piddivisipil' order by nama";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidprod=$rx['iprodid'];
                                                        $nnmprod=$rx['nama'];
                                                        echo "<option value='$nidprod'>$nnmprod</option>";
                                                    }
                                                     * 
                                                     */
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Report Type <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <div class='btn-group' data-toggle='buttons'>
                                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_rpttipe' id='rb_rptby1' value='D' checked> Detail </label>
                                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_rpttipe' id='rb_rptby2' value='S'> Summary </label>
                                                </div>
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
                
                
                <script>
                    function ShowDataArea() {
                        var ecabid = document.getElementById("cb_cabang").value;
                        var txtcab = document.getElementById("txt_cabang").value;
                        var txtcabarea = document.getElementById("txt_cabarea").value;
                        
                        var txtidcard = document.getElementById("txt_idcard").value;
                        var txtjbt = document.getElementById("txt_jbt").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_slsperoutlet/viewdata.php?module=cariareacabang",
                            data:"ucabid="+ecabid+"&utxtcab="+txtcab+"&utxtcabarea="+txtcabarea+"&utxtidcard="+txtidcard+"&utxtjbt="+txtjbt,
                            success:function(data){
                                $("#cb_area").html(data);
                            }
                        });
                    }
                    
                    function ShowDataProduk() {
                        var edivi = document.getElementById("cb_divisi").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_slsperoutlet/viewdata.php?module=caridataproduk",
                            data:"udivi="+edivi,
                            success:function(data){
                                $("#cb_prod").html(data);
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