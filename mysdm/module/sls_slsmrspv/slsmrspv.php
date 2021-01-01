<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Sales Per MR</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        
        $pbukadarihp=$_SESSION['MOBILE'];
        $ptargetblank=" target=\"_blank\" ";
        if ($pbukadarihp=="Y") $ptargetblank="";
		
        $pmyidcard=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        $pmygroupid=$_SESSION['GROUP'];
        
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                $tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                $tgl_pertama = date('F Y', strtotime($hari_ini));
                ?>
                <script>
                    function disp_confirm(pText)  {
                        var ekryid = document.getElementById("cb_karyawanspv").value;
                        if (ekryid=="") {
                            alert("spv/am harus diisi....");
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
                    
                    function SelAllCheckBox(nmbuton, data){
                        var checkboxes = document.getElementsByName(data);
                        var button = document.getElementById(nmbuton);

                        if(button.value == 'select'){
                            for (var i in checkboxes){
                                checkboxes[i].checked = 'FALSE';
                            }
                            button.value = 'deselect'
                        }else{
                            for (var i in checkboxes){
                                checkboxes[i].checked = '';
                            }
                            button.value = 'select';
                        }
                    }
                    
                    function ShowDataAreaKaryawan() {
                        var eidkry = document.getElementById("cb_karyawanspv").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_slsmrspv/viewdata.php?module=cariareakaryawan",
                            data:"uidkry="+eidkry,
                            success:function(data){
                                $("#kotak-multi5").html(data);
                            }
                        });
                    }
                </script>
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>" enctype='multipart/form-data' <?PHP echo $ptargetblank; ?> >
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                                    <!--<button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>-->
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>SPV / AM <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_karyawanspv' id='cb_karyawanspv' onchange="ShowDataAreaKaryawan()">
                                                    <?PHP
                                                    echo "<option value=''>-- Pilih --</option>";
                                                    if ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
                                                        $query= "select b.karyawanid, b.nama from ms.karyawan b where karyawanid='$pmyidcard' ";
                                                    }elseif ($pmyjabatanid=="08") {
                                                        $query =" select DISTINCT a.karyawanid, b.nama from sls.ispv0 a join ms.karyawan b on a.karyawanid=b.karyawanid "
                                                                . " JOIN sls.idm0 c on a.icabangid=c.icabangid where c.karyawanid='$pmyidcard' ";
                                                    }elseif ($pmyjabatanid=="20") {
                                                        $query =" select DISTINCT a.karyawanid, b.nama from sls.ispv0 a join ms.karyawan b on a.karyawanid=b.karyawanid "
                                                                . " JOIN sls.ism0 c on a.icabangid=c.icabangid where c.karyawanid='$pmyidcard' ";
                                                        $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                . " and LEFT(b.nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                . " AND LEFT(b.nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                        $query .= " AND b.nama NOT IN ('ACCOUNTING')";
                                                    }else{
                                                        if ($pmyidcard=="0000000158" OR $pmyidcard=="0000000159") {
                                                            $query =" select DISTINCT a.karyawanid, b.nama from sls.ispv0 a join ms.karyawan b on a.karyawanid=b.karyawanid "
                                                                    . " JOIN sls.icabang c on a.icabangid=c.icabangid where 1=1 ";
                                                            $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                    . " and LEFT(b.nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                    . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                    . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                    . " AND LEFT(b.nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                            $query .= " AND b.nama NOT IN ('ACCOUNTING')";
                                                            if ($pmyidcard=="0000000158") {
                                                                $query .= " AND c.region='B' ";
                                                            }elseif ($pmyidcard=="0000000159") {
                                                                $query .= " AND c.region='T' ";
                                                            }
                                                        }else{
                                                            $query =" select DISTINCT a.karyawanid, b.nama from sls.ispv0 a join ms.karyawan b on a.karyawanid=b.karyawanid ";
                                                        }
                                                    }
                                                    $query .=" order by b.nama";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidkryspv=$rx['karyawanid'];
                                                        $nnmkryspv=$rx['nama'];
                                                        if ($pmyidcard==$nidkryspv)
                                                            echo "<option value='$nidkryspv' selected>$nnmkryspv</option>";
                                                        else
                                                            echo "<option value='$nidkryspv'>$nnmkryspv</option>";
                                                    }
                                                    ?>
                                                </select>
												<?PHP //echo $query; ?>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Area <input type="checkbox" id="chkbtnarea" value="deselect" onClick="SelAllCheckBox('chkbtnarea', 'chkbox_icabarea[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                
                                                <div id="kotak-multi5" class="jarak">

                                                    <?PHP
                                                    $query_area="";
                                                    if ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
                                                        $query_area="select distinct a.icabangid, a.areaid, b.nama nama_area, c.nama nama_cabang from sls.ispv0 a JOIN sls.iarea b on a.areaid=b.areaid and a.icabangid=b.icabangid LEFT JOIN sls.icabang c on b.icabangid=c.icabangid WHERE a.karyawanid='$pmyidcard'";
                                                    }elseif ($pmyjabatanid=="08") {
                                                        $query_area="select distinct a.icabangid, a.areaid, b.nama nama_area, c.nama nama_cabang from sls.ispv0 a JOIN sls.iarea b on a.areaid=b.areaid and a.icabangid=b.icabangid LEFT JOIN sls.icabang c on b.icabangid=c.icabangid WHERE "
                                                                . " a.icabangid in (select DISTINCT IFNULL(icabangid,'') from sls.idm0 WHERE karyawanid='$pmyidcard')";
                                                    }elseif ($pmyjabatanid=="15") {
                                                        $query_area="select distinct a.icabangid, a.areaid, b.nama nama_area, c.nama nama_cabang from sls.imr0 a JOIN sls.iarea b on a.areaid=b.areaid and a.icabangid=b.icabangid LEFT JOIN sls.icabang c on b.icabangid=c.icabangid WHERE a.karyawanid='$pmyidcard'";
                                                    }elseif ($pmyjabatanid=="20") {
                                                        $query_area="select distinct a.icabangid, a.areaid, b.nama nama_area, c.nama nama_cabang from sls.ispv0 a JOIN sls.iarea b on a.areaid=b.areaid and a.icabangid=b.icabangid LEFT JOIN sls.icabang c on b.icabangid=c.icabangid WHERE "
                                                                . " a.icabangid in (select DISTINCT IFNULL(icabangid,'') from sls.ism0 WHERE karyawanid='$pmyidcard')";
                                                    }else{
                                                        if ($pmyidcard=="0000000158" OR $pmyidcard=="0000000159") {
                                                            $query_area = "select distinct b.icabangid, b.areaid, b.nama nama_area, c.nama nama_cabang from sls.iarea b JOIN sls.icabang c on b.icabangid=c.icabangid where IFNULL(c.aktif,'')='Y' ";
                                                            //$query_area .=" AND LEFT(b.nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -') ";
                                                            if ($pmyidcard=="0000000158") {
                                                                $query_area .= " AND c.region='B' ";
                                                            }elseif ($pmyidcard=="0000000159") {
                                                                $query_area .= " AND c.region='T' ";
                                                            }
                                                        }else{
                                                            if ($pmygroupid=="1" OR $pmygroupid=="24" OR $pmygroupid=="24" OR $pmygroupid=="2" OR $pmygroupid=="44" OR $pmygroupid=="46") {
                                                                $query_area = "select distinct b.icabangid, b.areaid, b.nama nama_area, c.nama nama_cabang from sls.iarea b JOIN sls.icabang c on b.icabangid=c.icabangid where IFNULL(c.aktif,'')='Y' ";
                                                                //$query_area .=" AND LEFT(b.nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -') ";
                                                            }
                                                        }
                                                    }
                                                    
                                                    if (!empty($query_area)) {
                                                        $query_area .=" ORDER BY c.nama, b.nama";
                                                        
                                                        $tampil = mysqli_query($cnms, $query_area);
                                                        while ($rx= mysqli_fetch_array($tampil)) {
                                                            $nidcabang=$rx['icabangid'];
                                                            $nnmcabang=$rx['nama_cabang'];
                                                            $nidarea=$rx['areaid'];
                                                            $nnmarea=$rx['nama_area'];
                                                            
                                                            $picabidarea=$nidcabang."".$nidarea;
                                                            echo "&nbsp; <input type=checkbox name='chkbox_icabarea[]'  id='chkbox_icabarea[]' value='$picabidarea' checked> $nnmcabang - $nnmarea<br/>";
                                                        }
                                                        
                                                    }
                                                    ?>
                                                    
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

                
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>