<?PHP
include "config/cek_akses_modul.php";
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Laporan Sales Per Sektor</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        
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
        
        
        if ($pmyjabatanid=="15" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08" OR $pmyjabatanid=="20") {
            if ($pmyjabatanid=="15") {
                $query_cab = "select distinct icabangid, areaid, divisiid FROM sls.imr0 WHERE karyawanid='$pmyidcard'";
            }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
                $query_cab = "select distinct icabangid, areaid, divisiid FROM sls.ispv0 WHERE karyawanid='$pmyidcard'";
            }elseif ($pmyjabatanid=="08") {
                $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM sls.idm0 WHERE karyawanid='$pmyidcard'";
            }elseif ($pmyjabatanid=="20") {
                $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM sls.ism0 WHERE karyawanid='$pmyidcard'";
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

        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                //$tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                $tgl_pertama = date('F Y', strtotime($hari_ini));
                ?>
                <script>
                    function disp_confirm(pText)  {
                        var eidcab = document.getElementById("cb_cabang").value;
                        var eam = document.getElementById("cb_am").value;
                        
                        if (eidcab=="") {
                            alert("cabang harus diisi...!!!");
                            return false;
                        }
                        
                        if (eam=="") {
                            alert("AM harus diisi...!!!");
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
                
                <style>
                    .grp-periode, .input-periode, .control-periode {
                        margin-bottom:2px;
                    }
                </style>
                
                
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
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                
                                                
                                                <select class='form-control' name='cb_cabang' id='cb_cabang' onchange="ShowDataPilihDataCab()">
                                                    <?PHP
                                                    
                                                    $query = "select iCabangId, nama, aktif from sls.icabang where 1=1 ";
                                                    $query .=" AND aktif='Y' ";
                                                    if ($pmyidcard=="0000000158" OR $pmyidcard=="0000002329" OR $pmyidcard=="0000000159" OR $pmyidcard=="0000002073") {
                                                        if ($pmyidcard=="0000000158" OR $pmyidcard=="0000002329") $query .=" And region='B' ";
                                                        elseif ($pmyidcard=="0000000159" OR $pmyidcard=="0000002073") $query .=" And region='T' ";
                                                    }else{
                                                    
                                                        if(!empty($pfilterregionpilih)) $query .=" AND region IN $pfilterregionpilih ";
                                                        if (!empty($pfiltercabpilih)) {
                                                            if ($pmyjabatanid=="15" OR $pmyjabatanid=="38" OR $pmyjabatanid=="39" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08" OR $pmyjabatanid=="20") $query .=" AND iCabangId IN $pfiltercabpilih ";
                                                        }

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
                                                    $query .=" AND aktif<>'Y' ";
                                                    if ($pmyidcard=="0000000158" OR $pmyidcard=="0000002329" OR $pmyidcard=="0000000159" OR $pmyidcard=="0000002073") {
                                                        if ($pmyidcard=="0000000158" OR $pmyidcard=="0000002329") $query .=" And region='B' ";
                                                        elseif ($pmyidcard=="0000000159" OR $pmyidcard=="0000002073") $query .=" And region='T' ";
                                                    }else{
                                                        if(!empty($pfilterregionpilih)) $query .=" AND region IN $pfilterregionpilih ";
                                                        
                                                        if (!empty($pfiltercabpilih)) {//OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08" OR $pmyjabatanid=="20"
                                                            if ($pmyjabatanid=="15" OR $pmyjabatanid=="38" OR $pmyjabatanid=="39") $query .=" AND iCabangId IN $pfiltercabpilih ";
                                                        }
                                                    }
                                                    $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
                                                    $query .=" order by aktif DESC, nama";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    $ketemunon=mysqli_num_rows($tampil);
                                                    if ($ketemunon>0) {
                                                        //echo "<option value='NONAKTIFPL'></option>";
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
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>AM <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_am' id='cb_am' onchange="">
                                                    <option value="">--Pilih--</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_divisi' id='cb_divisi' onchange="">
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
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for='cbln01'>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='cbln01'>
                                                        <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    <!--
                                                    <div class='input-group date' id='cbln02'>
                                                        <input type='text' id='e_periode02' name='e_periode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    -->
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for='cbln02'>s/d. <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    
                                                    <div class='input-group date' id='cbln02'>
                                                        <input type='text' id='e_periode02' name='e_periode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    
                                                </div>
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
                                                            echo "<option value='$piddis'>$pnmdist ($pditint)</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Report By <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="radio" id="rd_rptny" name="rd_rptny" value="J" checked>&nbsp;<b>Jenis Sektor</b> &nbsp; &nbsp;
                                                <input type="radio" id="rd_rptny" name="rd_rptny" value="G">&nbsp;<b>Group Sektor</b>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="radio" id="rd_rptjns" name="rd_rptjns" value="Q" checked>&nbsp;<b>qty</b> &nbsp; &nbsp;
                                                <input type="radio" id="rd_rptjns" name="rd_rptjns" value="V">&nbsp;<b>value</b>
                                                <span hidden><input type="radio" id="rd_rptjns" name="rd_rptjns" value="A">&nbsp;<b>All</b></span>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>&nbsp; <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="checkbox" id="chkboth" name="chkboth" value="Y" /> Include Produk Other Peacock
                                                <br/>( produk other peacock tidak masuk achievement MR, SPV/AM, DM )
                                            </div>
                                        </div>
                                        
                                        
                                        <hr/>
                                        <div class='form-group'>
                                        
                                                <div class='x_content' style="overflow-x:auto; max-height: 200px;">
                                                    <table id="ndatatable1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Jenis Sektor</th>
                                                                <th>Group Sektor</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?PHP
                                                            $no_m=1;
                                                            $query = "select nama as jenisektor, nama_pvt as groupsektor from MKT.isektor where aktif = 'Y' ";
                                                            $query .=" order by 2, 1 asc";
                                                            $tampil = mysqli_query($cnms, $query);
                                                            while ($rx= mysqli_fetch_array($tampil)) {
                                                                $njenissektor=$rx['jenisektor'];
                                                                $ngrpsektor=$rx['groupsektor'];
                                                                echo "<tr>";
                                                                echo "<td nowrap>$no_m</td>";
                                                                echo "<td nowrap>$njenissektor</td>";
                                                                echo "<td nowrap>$ngrpsektor</td>";
                                                                echo "</tr>";

                                                                $no_m++;
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                
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
                        ShowDataAM();
                        /*
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var iact = urlku.searchParams.get("act");
                        if (iact=="editdata") {
                            CariDataBarang();
                        }
                        */
                    } );
                    
                    function ShowDataPilihDataCab() {
                        ShowDataAM();
                    }
                    
                    function ShowDataAM() {
                        var eidcab = document.getElementById("cb_cabang").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_lapslspersektor/viewdata.php?module=caridataam",
                            data:"uidcab="+eidcab,
                            success:function(data){
                                $("#cb_am").html(data);
                            }
                        });
                    }
                    
                </script>

                
                <style>

                    .divnone {
                        display: none;
                    }
                    #ndatatable1 {
                        color:#000;
                        font-family: "Arial";
                    }
                    #ndatatable1 th {
                        font-size: 12px;
                        border: 1px solid;
                        text-align: center;
                    }
                    #ndatatable1 td { 
                        font-size: 11px;
                        border: 1px solid;
                    }
                </style>
                
                
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>
</div>