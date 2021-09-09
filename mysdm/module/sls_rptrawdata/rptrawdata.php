<?PHP
include "config/cek_akses_modul.php";
?>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Raw Data Per Cabang</h3></div></div><div class="clearfix"></div>
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
        
        
        $filtericabexc="";
        $query ="select distinct icabangid from sls.exc_idm0 where karyawanid='$pmyidcard'";
        $tampilaa= mysqli_query($cnms, $query);
        $ketemux=mysqli_num_rows($tampilaa);
        if ($ketemux>0) {
            while ($nx= mysqli_fetch_array($tampilaa)) {
                $npicexc=$nx['icabangid'];
                $filtericabexc .="'".$npicexc."',";
            }
            if (!empty($filtericabexc)) $filtericabexc="(".substr($filtericabexc, 0, -1).")";
        }
		
		
		
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
            if (!empty($filtericabexc)) {
                
                if (!empty($filtercabangbyadmin)) {
                    $filtercabangbyadmin = " AND (iCabangId IN $filtercabangbyadmin OR icabangid IN $filtericabexc) ";
                }else{
                    $filtercabangbyadmin = " AND iCabangId IN $filtericabexc ";
                }
                
            }else{
                if (!empty($filtercabangbyadmin)) $filtercabangbyadmin = " AND iCabangId IN $filtercabangbyadmin ";
            }
            //if (!empty($filtercabangbyadmin)) $filtercabangbyadmin = " AND iCabangId IN $filtercabangbyadmin ";
            if (!empty($filiddivisipil)) $filiddivisipil = " AND DivProdId IN $filiddivisipil ";

        }
        
        $phiddenregion="";
        if ($pmyjabatanid=="15" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08" OR $pmyjabatanid=="20") {
            $phiddenregion="hidden";
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
                        var ecabid = document.getElementById("cbcabang").value;
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
                                                        <input type='text' id='cbln01' name='bulan1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cbln01'>s/d. <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='cbln02'>
                                                        <input type='text' id='cbln02' name='bulan2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div <?PHP echo $phiddenregion; ?> class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Region <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cbregion' id='cbregion' onchange="ShowDataCabangRegion()">
                                                    <?PHP
                                                    if ($pmyidcard=="0000001201" OR $pmyidcard=="0000001900" OR $pmyidcard=="0000001099" OR $pmyidcard=="0000002329") {
                                                        echo "<option value='B'>Barat</option>";
                                                    }else{
                                                        echo "<option value='' selected>--All--</option>";
                                                        echo "<option value='B'>Barat</option>";
                                                        echo "<option value='T'>Timur</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Cabang <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cbcabang' id='cbcabang'>
                                                    <?PHP
                                                    //if ($pmyjabatanid!="15" AND $pmyjabatanid!="10" AND $pmyjabatanid!="18")  echo "<option value=''>--Pilih--</option>";
                                                    $pno=0;
                                                    if ($pmyidcard=="0000001201" OR $pmyidcard=="0000001900") {
                                                        $query_pilih = "SELECT iCabangId, nama, aktif FROM sls.icabang WHERE 1=1 "
                                                                . " AND icabangid in (select distinct icabangid from sls.ism0 WHERE karyawanid='0000000017')";
                                                    }elseif ($pmyidcard=="0000001099") {
                                                        $query_pilih = "SELECT iCabangId, nama, aktif FROM sls.icabang WHERE 1=1 "
                                                                . " AND icabangid in (select distinct icabangid from sls.ism0 WHERE karyawanid='0000000031')";
                                                    }elseif ($pmyidcard=="0000002329") {
                                                        $query_pilih = "select iCabangId, nama, aktif from sls.icabang where "
                                                                . " 1=1 AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -') AND region='B' ";
                                                    }else{
                                                        $query_pilih = "select iCabangId, nama, aktif from sls.icabang where "
                                                                . " 1=1 AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -') $filtercabangbyadmin ";
                                                    }
                                                    $query =$query_pilih." AND aktif='Y' ";
                                                    $query .=" order by nama";
                                                    
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidcab=$rx['iCabangId'];
                                                        $nnmcab=$rx['nama'];
                                                        $nstsaktif=$rx['aktif'];
                                                        $pstsaktif="Aktif";
                                                        if ($nstsaktif!="Y") $pstsaktif="Non Aktif";
                                                        if ($pidcabangpil==$nidcab)
                                                            echo "<option value='$nidcab' selected>$nnmcab ($pstsaktif)</option>";
                                                        else
                                                            echo "<option value='$nidcab'>$nnmcab ($pstsaktif)</option>";
                                                        
                                                        $pno++;
                                                    }
                                                    
                                                    
                                                    $query =$query_pilih." AND aktif<>'Y' ";
                                                    $query .=" order by nama";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidcab=$rx['iCabangId'];
                                                        $nnmcab=$rx['nama'];
                                                        $nstsaktif=$rx['aktif'];
                                                        $pstsaktif="Aktif";
                                                        if ($nstsaktif!="Y") $pstsaktif="Non Aktif";
                                                        if ($pidcabangpil==$nidcab)
                                                            echo "<option value='$nidcab' selected>$nnmcab ($pstsaktif)</option>";
                                                        else
                                                            echo "<option value='$nidcab'>$nnmcab ($pstsaktif)</option>";
                                                        
                                                        $pno++;
                                                    }
                                                    
                                                    if ($pno==0) {
                                                        echo "<option value=''>--Pilih--</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
										
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Distributor <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cbdistributor' id='cbdistributor' onchange="">
                                                    <?PHP
                                                        echo "<option value=''>--Piihan--</option>";

                                                        $query_aktif ="select distid, nama from MKT.distrib0 ";
                                                        $query_aktif .=" order by nama";
                                                        $tampil= mysqli_query($cnms, $query_aktif);
                                                        while ($row= mysqli_fetch_array($tampil)) {
                                                            $piddis=$row['distid'];
                                                            $pnmdist=$row['nama'];
                                                            $pditint=(INT)$piddis;
                                                            if ($piddistpl==$piddis)
                                                                echo "<option value='$piddis' selected>$pnmdist ($pditint)</option>";
                                                            else
                                                                echo "<option value='$piddis'>$pnmdist ($pditint)</option>";
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
                
                
                <script>
                    function ShowDataCabangRegion() {
                        var eregion = document.getElementById("cbregion").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_rptrawdata/viewdata.php?module=caricabangregion",
                            data:"uregion="+eregion,
                            success:function(data){
                                $("#cbcabang").html(data);
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