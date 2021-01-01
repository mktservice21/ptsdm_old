<?PHP
include "config/cek_akses_modul.php";
include "config/koneksimysqli_ms.php";
?>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Laporan Sales Per Customer Ethical</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('t F Y', strtotime($hari_ini));
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
                    
                    
                    function ShowDataCabang() {
                        var eidreg = document.getElementById("e_region").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_lapbycusteth/viewdata.php?module=caridatacabang",
                            data:"uidreg="+eidreg,
                            success:function(data){
                                $("#cb_cabang").html(data);
                                $("#cb_area").html("<option value=''>~ Pilih ~</option>");
                            }
                        });
                    }
                    
                    
                    function ShowDataArea() {
                        var eidcab = document.getElementById("cb_cabang").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_lapbycusteth/viewdata.php?module=caridataarea",
                            data:"uidcab="+eidcab,
                            success:function(data){
                                $("#cb_area").html(data);
                            }
                        });
                    }
                    
                    
                    function ShowDataProduk() {
                        var eiddivisi = document.getElementById("cb_divisi").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_lapbycusteth/viewdata.php?module=caridataproduk",
                            data:"uiddivisi="+eiddivisi,
                            success:function(data){
                                $("#cb_produk").html(data);
                            }
                        });
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
                                    <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                                    <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                                </h2>
                                <div class='clearfix'></div>
                            </div>

                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for='tgl01'>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='tgl01'>
                                                        <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    <div class='input-group date' id='tgl02'>
                                                        <input type='text' id='e_periode02' name='e_periode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Regional <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>

                                                <select class='form-control' name='region' id='e_region' onchange="ShowDataCabang()">
                                                    <option value='A' selected>~ All ~</option>
                                                    <option value='B'>B - Barat</option>
                                                    <option value='T'>T - Timur</option>
                                                </select>

                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Cabang <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_cabang' id='cb_cabang' onchange="ShowDataArea()">
                                                    <option value='' selected>~ All ~</option>
                                                    <?PHP
                                                    $query = "select icabangid, nama from sls.icabang WHERE IFNULL(aktif,'')='Y' AND "
                                                            . " LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -', 'OTH -') order by nama";
                                                    $tampil=mysqli_query($cnms, $query);
                                                    while ($nr1= mysqli_fetch_array($tampil)) {
                                                        $nidcab=$nr1['icabangid'];
                                                        $nnmcab=$nr1['nama'];
                                                        
                                                        echo "<option value='$nidcab'>$nnmcab</option>";
                                                    }
                                                    echo "<option value='ZAAZZA'>&nbsp;</option>";
                                                    echo "<option value='NON'>--Non Aktif--</option>";
                                                    echo "<option value='ZAAZZA'>&nbsp;</option>";
                                                    $query = "select icabangid, nama from sls.icabang WHERE IFNULL(aktif,'')<>'Y' AND "
                                                            . " LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -', 'OTH -') order by nama";
                                                    $tampil=mysqli_query($cnms, $query);
                                                    while ($nr1= mysqli_fetch_array($tampil)) {
                                                        $nidcab=$nr1['icabangid'];
                                                        $nnmcab=$nr1['nama'];
                                                        
                                                        echo "<option value='$nidcab'>$nnmcab</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_area' id='cb_area'>
                                                    <option value='' selected>~ Pilih ~</option>
                                                    <?PHP
                                                    
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Divisi <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_divisi' id='cb_divisi' onchange="ShowDataProduk()">
                                                    <option value='' selected>~ All ~</option>
                                                    <?PHP
                                                    $query="select DivProdId divisiId, nama from MKT.divprod where IFNULL(br,'')='Y' AND DivProdId NOT IN ('OTHER', 'OTC', 'HO', 'CAN') ";
                                                    $query .=" order by divisiId";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $niddiv=$rx['divisiId'];
                                                        $nnmdiv=$rx['nama'];
                                                        echo "<option value='$niddiv'>$nnmdiv</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Produk <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_produk' id='cb_produk'>
                                                    <option value='' selected>~ All ~</option>
                                                    <?PHP
                                                    
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Sektor <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_sektor' id='cb_sektor' onchange="">
                                                    <option value='' selected>~ All ~</option>
                                                    <?PHP
                                                    $query="select isektorid, nama from ms.isektor ";
                                                    $query .=" order by nama";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidsektor=$rx['isektorid'];
                                                        $nnmsektor=$rx['nama'];
                                                        echo "<option value='$nidsektor'>$nnmsektor</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Distributor <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control input-sm' name='distibutor' id='distibutor'>
                                                <?PHP
                                                    $pinsel="('0000000002', '0000000003', '0000000005', '0000000006', '0000000010', "
                                                            . " '0000000011', '0000000016', '0000000023', '0000000030', '0000000031')";
                                                    //cComboDistibutorHanya('', '', $pinsel);

                                                    //cComboDistibutor('', '');

                                                    $sql=mysqli_query($cnmy, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 WHERE"
                                                            . " Distid IN $pinsel order by Distid, nama");
                                                    echo "<option value=''>~ All ~</option>";
                                                    while ($Xt=mysqli_fetch_array($sql)){
                                                        $pdisid=$Xt['Distid'];
                                                        $pdisnm=$Xt['nama'];
                                                        $cidcek=(INT)$pdisid;
                                                        echo "<option value='$pdisid'>$cidcek - $pdisnm</option>";
                                                    }

                                                    $sql=mysqli_query($cnmy, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 WHERE"
                                                            . " Distid NOT IN $pinsel order by Distid, nama");
                                                    echo "<option value=''></option>";
                                                    while ($Xt=mysqli_fetch_array($sql)){
                                                        $pdisid=$Xt['Distid'];
                                                        $pdisnm=$Xt['nama'];
                                                        $cidcek=(INT)$pdisid;
                                                        echo "<option value='$pdisid'>$cidcek - $pdisnm</option>";
                                                    }


                                                ?>
                                                </select>
                                            </div>
                                        </div>

                                        
                                        <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang Dist. <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_cabdist' id='cb_cabdist'>
                                                    <option value='' selected>~ All ~</option>
                                                    <?PHP
                                                    
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

                    function TentukanPeriode() {
                        var idate1=document.getElementById('e_periode01').value;
                        var ndate1 = new Date(idate1+" 01");
                        
                        var lastDay = new Date(ndate1.getFullYear(), ndate1.getMonth() + 1, 0);
                        
                        var month = new Array();
                        month[0] = "January";
                        month[1] = "February";
                        month[2] = "March";
                        month[3] = "April";
                        month[4] = "May";
                        month[5] = "June";
                        month[6] = "July";
                        month[7] = "August";
                        month[8] = "September";
                        month[9] = "October";
                        month[10] = "November";
                        month[11] = "December";
                        
                        var ntgl1 = lastDay.getDate();
                        var nbln1 = ndate1.getMonth();
                        var nbulan1 = month[ndate1.getMonth()];
                        var ntahun1 = ndate1.getFullYear();


                        document.getElementById('e_periode02').value=ntgl1+" "+nbulan1+" "+ntahun1;
                    }
                    

                    $('#tgl01').on('change dp.change', function(e){
                        TentukanPeriode();
                    });
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