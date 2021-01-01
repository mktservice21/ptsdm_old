<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<?PHP
include "config/koneksimysqli.php";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tglawal = date('d/m/Y', strtotime($hari_ini));
$tglakhir = date('d/m/Y', strtotime($hari_ini));
$ptglstnk="";

$ptglnopol1 = date('d/m/Y', strtotime($hari_ini));
$ptglnopol2 = date('d/m/Y', strtotime($hari_ini));

$pnomorid="";
$platnomor="";
$jenis="02";
$merk="";
$tipe="";
$pnorangka="";
$pnomesin="";

$idpakai="";
$idajukan=$_SESSION['IDCARD'];
$pkaryawnidpemakai="";

$stskendaraan="";
$pwarna="";

$chktgl ="";
$tglakhhidden="hidden";

$pdivisiid="";
$pcabangid="0000000001";

$pjnsasuransi="";
$pnmasuransi="";
$pnopolisasuransi="";

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    $pnomorid=$_GET['id'];
    $query = "select * from dbmaster.t_kendaraan where noid='$pnomorid'";
    $tampil= mysqli_query($cnmy, $query);
    $r= mysqli_fetch_array($tampil);
    $platnomor=$r['nopol'];
    $jenis=$r['jenis'];
    $merk=$r['merk'];
    $tipe=$r['tipe'];
    $pnorangka=$r['norangka'];
    $pnomesin=$r['nomesin'];
    $ptglst=$r['tgltempostnk'];
    
    $tgl1 = date('d/m/Y', strtotime($r['tglbeli']));
    
    if ($ptglst=="0000-00-00") $ptglst="";
    if (!empty($ptglst)) $ptglstnk = date('d/m/Y', strtotime($ptglst));
    
    $stskendaraan=$r['statuskendaraan'];
    
    $pwarna=$r['warna'];
    
    $pjnsasuransi=$r['jenis_asuransi'];
    $pnmasuransi=$r['nama_asuransi'];
    $pnopolisasuransi=$r['nopolis_asuransi'];
    
    $ptglnopol1=$r['polis_periode1'];
    $ptglnopol2=$r['polis_periode2'];
    
    if ($ptglnopol1=="0000-00-00") $ptglnopol1="";
    if ($ptglnopol2=="0000-00-00") $ptglnopol2="";
    
    
    if (!empty($ptglnopol1)) $ptglnopol1 = date('d/m/Y', strtotime($ptglnopol1));
    if (!empty($ptglnopol2)) $ptglnopol2 = date('d/m/Y', strtotime($ptglnopol2));

    $pemakai= mysqli_query($cnmy, "select * from dbmaster.t_kendaraan_pemakai where noid='$pnomorid' and stsnonaktif<>'Y'");
    $p= mysqli_fetch_array($pemakai);
    $idpakai=$p['nourut'];
    $pkaryawnidpemakai=$p['karyawanid'];
    $pcabangid=$p['icabangid'];
    $pdivisiid=$p['divisi'];
    
    
    
    
    if (!empty($p['tglawal']) AND $p['tglawal'] <> "0000-00-00")
        $tglawal = date('d/m/Y', strtotime($p['tglawal']));
    
    if (!empty($p['tglakhir']) AND $p['tglakhir'] <> "0000-00-00") {
        $tglakhir = date('d/m/Y', strtotime($p['tglakhir']));
        $chktgl="checked";
        $tglakhhidden="";
    }
    
    
}
?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' 
              enctype='multipart/form-data'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='x_panel'>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <!-- ISI INPUT -->
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PLAT NOMOR <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnomorid; ?>' Readonly>
                                        <input type='text' id='e_nopolid' name='e_nopolid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $platnomor; ?>'>
                                        <input type='hidden' id='e_nopollama' name='e_nopollama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $platnomor; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>JENIS <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <select class='form-control input-sm' id='e_jenis' name='e_jenis'>
                                        <?PHP
                                            $query="SELECT DISTINCT jenis, nama_jenis FROM dbmaster.t_kendaraan_jenis";
                                            $query .=" order by nama_jenis, jenis";
                                            $ketemu=  mysqli_num_rows(mysqli_query($cnmy, $query));
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['jenis']==$jenis)
                                                    echo "<option value='$a[jenis]' selected>$a[nama_jenis]</option>";
                                                else
                                                    echo "<option value='$a[jenis]'>$a[nama_jenis]</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>MERK <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_merk' name='e_merk' class='form-control col-md-7 col-xs-12' value='<?PHP echo $merk; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TIPE <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_tipe' name='e_tipe' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tipe; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>WARNA <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_warna' name='e_warna' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pwarna; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TGL. BELI <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tgl' name='e_tgl' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>NO. RANGKA <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_norangka' name='e_norangka' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorangka; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>NO. MESIN <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_nomesin' name='e_nomesin' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnomesin; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TGL. JATUH TEMPO STNK <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <div class='input-group date' id='mytgl03'>
                                            <input type="text" class="form-control" id='e_tglstnk' name='e_tglstnk' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglstnk; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>STATUS KENDARAAN <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <select class='form-control input-sm' id='e_ststkendaraan' name='e_ststkendaraan'>
                                        <?PHP
                                            $sselect1="";
                                            $sselect2="";
                                            $sselect3="";
                                            if (empty($stskendaraan) OR $stskendaraan=="AKTIF") $sselect1="selected";
                                            if ($stskendaraan=="JUAL") $sselect2="selected";
                                            if ($stskendaraan=="TIDAKTERPAKAI") $sselect3="selected";
                                            
                                            echo "<option value='AKTIF' $sselect1>Aktif</option>";
                                            echo "<option value='JUAL' $sselect2>Di Jual</option>";
                                            echo "<option value='TIDAKTERPAKAI' $sselect3>Tidak Terpakai</option>";
                                            
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>JENIS ASURANSI <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input list="lstjenisasuransi" id="lst_jenisasuransi" name="lst_jenisasuransi" class='form-control col-md-7 col-xs-12' value="<?PHP echo $pjnsasuransi; ?>">
                                            <datalist id="lstjenisasuransi">
                                                <?PHP
                                                
                                                    $query = "select distinct jenis_asuransi from dbmaster.t_kendaraan WHERE IFNULL(jenis_asuransi,'')<>'' order by jenis_asuransi";
                                                    $tampild= mysqli_query($cnmy, $query);
                                                    while ($nrd= mysqli_fetch_array($tampild)) {
                                                        $ijnsasuransi=$nrd['jenis_asuransi'];

                                                        echo "<option value='$ijnsasuransi'>";
                                                    }
                                                
                                                ?>
                                        </datalist>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>NAMA ASURANSI <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input list="lstnamaasuransi" id="lst_nmasuransi" name="lst_nmasuransi" class='form-control col-md-7 col-xs-12' value="<?PHP echo $pnmasuransi; ?>">
                                            <datalist id="lstnamaasuransi">
                                                <?PHP
                                                
                                                    $query = "select distinct nama_asuransi from dbmaster.t_kendaraan WHERE IFNULL(nama_asuransi,'')<>'' order by nama_asuransi";
                                                    $tampild= mysqli_query($cnmy, $query);
                                                    while ($nrd= mysqli_fetch_array($tampild)) {
                                                        $inmasuransi=$nrd['nama_asuransi'];

                                                        echo "<option value='$inmasuransi'>";
                                                    }
                                                
                                                ?>
                                        </datalist>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>NO. POLIS <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input list="lstnopolisasuransi" id="lst_nopolisasuransi" name="lst_nopolisasuransi" class='form-control col-md-7 col-xs-12' value="<?PHP echo $pnopolisasuransi; ?>">
                                            <datalist id="lstnopolisasuransi">
                                                <?PHP
                                                
                                                    $query = "select distinct nopolis_asuransi from dbmaster.t_kendaraan WHERE IFNULL(nopolis_asuransi,'')<>'' order by nopolis_asuransi";
                                                    $tampild= mysqli_query($cnmy, $query);
                                                    while ($nrd= mysqli_fetch_array($tampild)) {
                                                        $inopolasuransi=$nrd['nopolis_asuransi'];

                                                        echo "<option value='$inopolasuransi'>";
                                                    }
                                                
                                                ?>
                                        </datalist>
                                    </div>
                                </div>

                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PERIODE <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <div class='input-group date' id='mytgl04'>
                                            <input type="text" class="form-control" id='e_tglper01' name='e_tglper01' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglnopol1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>S/D. <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <div class='input-group date' id='mytgl04'>
                                            <input type="text" class="form-control" id='e_tglper02' name='e_tglper02' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglnopol2; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                                
                                
                                
                                <br/>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PEMAKAI SEKARANG : <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_idpakai' name='e_idpakai' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idpakai; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>KARYAWAN <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_pemakai' name='e_pemakai' onchange=""><!--showAreaEmp('', 'e_idkaryawan', 'e_idarea')-->
                                            <?PHP
                                                $query = "select b.karyawanId as karyawanid, b.nama as nama from hrd.karyawan as b WHERE 1=1 ";
                                                $query .=" AND ( IFNULL(b.nama,'')<>'' ";
                                                $query .=" AND b.karyawanId NOT IN (select distinct karyawanId from dbmaster.t_karyawanadmin)";
                                                $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ')  "
                                                        . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                                        . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-') "
                                                        . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";
                                                $query .=" ) OR b.karyawanId='$pkaryawnidpemakai' ";
                                                $query .=" order by b.nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                echo "<option value='' selected>--Pilihan--</option>";
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pnidkry=$z['karyawanid'];
                                                    $pnamakry=$z['nama'];
                                                    if ($pnidkry==$pkaryawnidpemakai)
                                                        echo "<option value='$pnidkry' selected>$pnamakry ($pnidkry)</option>";
                                                    else
                                                        echo "<option value='$pnidkry'>$pnamakry ($pnidkry)</option>";
                                                }
                                            //PilihKaryawanAktif("", "-- Pilihan --", $pkaryawnidpemakai, "Y", $_SESSION['STSADMIN'], "", $_SESSION['LVLPOSISI'], "", $_SESSION['IDCARD'], "", $_SESSION['AKSES_REGION'], "", "", "");
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DIVISI <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_divisiid' name='e_divisiid' onchange="ShowDataCabang()">
                                            <?PHP
                                                echo "<option value='' selected>--Pilih--</option>";
                                                $query = "SELECT DivProdId as divprodid, nama as nama "
                                                        . " FROM dbmaster.divprod where br='Y' AND DivProdId NOT IN ('OTHERS', 'OTHER') ";
                                                $query .=" order by nama";
                                                $tampil=mysqli_query($cnmy, $query);
                                                while($et=mysqli_fetch_array($tampil)){
                                                    $netdivprod=$et['divprodid'];
                                                    $netdivnm=$et['nama'];
                                                    if ($netdivprod=="CAN") $netdivnm="CANARY";
                                                    if ($netdivprod=="OTC") $netdivnm="CHC";
                                                    
                                                    if ($netdivprod==$pdivisiid)
                                                        echo "<option value='$netdivprod' selected>$netdivnm</option>";
                                                    else
                                                        echo "<option value='$netdivprod'>$netdivnm</option>";

                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CABANG <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_cabid' name='e_cabid' onchange="">
                                            <?PHP
                                                if ($pdivisiid=="CHC" OR $pdivisiid=="OTC")
                                                    $query = "select icabangid_o as icabangid, nama as nama from dbmaster.v_icabang_o WHERE aktif='Y' ";
                                                else
                                                    $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE aktif='Y' ";
                                                
                                                $query .=" ORDER BY nama";
                                                $tampil= mysqli_query($cnmy, $query);
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $npidcab=$row['icabangid'];
                                                    $npnmcab=$row['nama'];

                                                    if ($npidcab==$pcabangid)
                                                          echo "<option value='$npidcab' selected>$npnmcab</option>";
                                                    else
                                                        echo "<option value='$npidcab'>$npnmcab</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TANGGAL <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='e_tglawal' name='e_tglawal' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglawal; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TANGGAL AKHIR 
                                        <span class='required'><input type="checkbox" id="chktgl" name="chktgl" <?PHP echo $chktgl; ?> onclick="myShowHide()"></span></label>
                                    <div class='col-md-4'
                                         <div id="divtglakhir">
                                            <div class='input-group date' id='mytgl02'>
                                                <input type="text" class="form-control" id='e_tglakhir' name='e_tglakhir' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglakhir; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <br/>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                        <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                    </div>
                                </div>
                                
                                
                                
                                <!-- END ISI INPUT -->
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            
            
        </form>
    </div>
    
</div>

<script>
            
function disp_confirm(pText_, ket)  {
    var eidplat =document.getElementById('e_nopolid').value;
    
    if (eidplat==""){
        alert("PLAT NOMOR TIDAK BOLEH KOSONG....");
        document.getElementById('e_nopolid').focus();
        return 0;
    }
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            
            document.getElementById("demo-form2").action = "module/md_m_kendaraan/aksi_kendaraan.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}

function myShowHide() {
    var xchec=$("#chktgl").is(":checked");
    var x = document.getElementById("divtglakhir");
    if (xchec==false) {
        x.style.display = "none";
    }else{
        x.style.display = "block";
    }
    

}

$(document).ready(function() {
    
    $('#mytgl03, #mytgl04, #mytgl05').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });

    var xchec=$("#chktgl").is(":checked");
    if (xchec==false) {
        var x = document.getElementById("divtglakhir");
        x.style.display = "none";
    }
} );


function ShowDataCabang() {
    var edivsi =document.getElementById('e_divisiid').value;

    var myurl = window.location;
    var urlku = new URL(myurl);
    var iact = urlku.searchParams.get("act");
    if (iact=="editdata") {
        //esdhtmpl="";
    }


    $.ajax({
        type:"post",
        url:"module/md_m_kendaraan/viewdata.php?module=viewdatacabang",
        data:"udivsi="+edivsi,
        success:function(data){
            $("#e_cabid").html(data);
        }
    });
}
</script>
