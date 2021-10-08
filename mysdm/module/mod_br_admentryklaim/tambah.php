<!--<script src="module/mod_br_entrydcc/mytransaksi.js"></script>-->
<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
</style>


<?PHP
$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];

$iduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD']; 
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];
$padmkhusus=$_SESSION['ADMINKHUSUS'];
$pigroup=$_SESSION['GROUP'];


$hari_ini = date("Y-m-d");
$tglinput = date('d F Y', strtotime($hari_ini));
$tglinput = date('d/m/Y', strtotime($hari_ini));

$pbulan = date('F Y', strtotime($hari_ini));
$pblnsusulan = date('F Y', strtotime($hari_ini));
$pharinya = date('d', strtotime($hari_ini));
if ((DOUBLE)$pharinya>=17) {
    $pperiode1 = date('d F Y', strtotime($hari_ini));
    $pperiode2 = date('t F Y', strtotime($hari_ini));
}else{
    $pperiode1 = date('d F Y', strtotime($hari_ini));
    $pperiode2 = date('t F Y', strtotime($hari_ini));
}

$pdistid="";
$pnamadiv="Ethical";
$pnamareg="Reg I";
if ($pigroup=="40") $pnamareg="Reg II";
//spp = 2
$pbulan=date('F Y', strtotime($hari_ini));
if ((DOUBLE)$pdistid==2 || $pdistid=="0000000002") {
    $pbulan=date('F Y', strtotime($hari_ini))." ".date('d/m/Y', strtotime($hari_ini))." s/d. ".date('d/m/Y', strtotime($hari_ini));
}
$paktivitas1="Biaya Kerjasama Promosi Produk $pnamadiv $pnamareg $pbulan";
    


$tgltrans="";

$pidklaim="";
$piddistrb="";
//$paktivitas1="";
$paktivitas2="";

$pselectall="";
$pselectbarat="selected";
$pselecttimur="";
if ($pigroup=="40") {
    $pselectall="";
    $pselectbarat="";
    $pselecttimur="selected";    
}
                                            
$pjumlah=0;
$prealisasi="";
$pnoslip="";
$plampiran="";

$pkodeid="";
$pcoa="755-31";//canary
//$pcoa="751-31";//EAGLE
//$pcoa="752-31";//PIGEO
//$pcoa="753-31";//PEACO
//$pcoa="754-31";//OTC
$pdivpengajuan="CAN";



$pjnspajak = "N";
$pjmldpp=0;
$pjmlppn=10;
$pjmlrpppn=0;
$pjnspph="";
$pjmlpph=2;
$pjmlrppph=0;
$pjmlbulat=0;
$ptglfakturpajak = "";
$pnoseripajak="";
$pkenapajak="";

$pjenisonline="";



$ptotrpklaim=0;
$ptotrpreal=0;
$ptotrptolak=0;

$pppnrpklaim=0;
$pppnrpreal=0;
$pppnrptolak=0;

$ptotppnrpklaim=0;
$ptotppnrpreal=0;
$ptotppnrptolak=0;

$ppphrpklaim=0;
$ppphrpreal=0;
$ppphrptolak=0;

$pgrdrpklaim=0;
$pgrdrpreal=0;
$pgrdrptolak=0;

$pbulatklaim=0; $pbulatreal=0; $pbulattolak=0;
            
$pjmlkuranglebih=0;
$pketkuranglebih="";
$sudahapv="";


$act="input";
if ($pact=="editdata"){
    $act="update";
    $pidklaim=$_GET['id'];
    
    $edit = mysqli_query($cnmy, "SELECT * FROM hrd.klaim WHERE klaimId='$pidklaim'");
    $r    = mysqli_fetch_array($edit);
    
    $tglinput = date('d F Y', strtotime($r['tgl']));
    $tglinput = date('d/m/Y', strtotime($r['tgl']));
    if (empty($r['tgltrans']) OR $r['tgltrans']=="0000-00-00"){
        
    }else{
        $tgltrans = date('d F Y', strtotime($r['tgltrans']));
        $tgltrans = date('d/m/Y', strtotime($r['tgltrans']));
    }
    
    $nbulan=$r['bulan'];
    $nper1=$r['periode1'];
    $nper2=$r['periode2'];
    $pbulan = date('F Y', strtotime($nbulan));
    $pperiode1 = date('d F Y', strtotime($nper1));
    $pperiode2 = date('d F Y', strtotime($nper2));
    
    
    $idajukan=$r['karyawanid'];
    $piddistrb=$r['distid']; 
    $pjumlah=$r['jumlah'];
    $prealisasi=$r['realisasi1'];
    $pnoslip=$r['noslip'];
    $paktivitas1=$r['aktivitas1'];
    $paktivitas2=$r['aktivitas2'];
    
    
    $pjmlkuranglebih=$r['jmlkuranglebih'];
    $pketkuranglebih=$r['ketkuranglebih'];
    
    if ($r['lampiran']=="Y") $plampiran="checked";
    
    
    $pdivpengajuan=$r['pengajuan'];
    $pcoa=$r['COA4'];
    
    if ($pdivpengajuan=="ETH") {   
        $pdivpengajuan="CAN";
    }
    
    
    $pjnspajak=$r['pajak'];
    $pkenapajak=$r['nama_pengusaha'];
    $pnoseripajak=$r['noseri'];
    if (!empty($r['tgl_fp']) AND $r['tgl_fp']<>"0000-00-00")
        $ptglfakturpajak = date('d/m/Y', strtotime($r['tgl_fp']));

    if ($r['tgl_fp']=="0000-00-00") $ptglfakturpajak = "";

    $pjmldpp=$r['dpp'];
    $pjmlppn=$r['ppn'];
    $pjmlrpppn=$r['ppn_rp'];

    $pjnspph=$r['pph_jns'];
    $pjmlpph=$r['pph'];
    $pjmlrppph=$r['pph_rp'];
    $pjmlbulat=$r['pembulatan'];
    
    $pbulatreal=$r['pembulatan'];
    $pjenisonline=$r['jenisklaim'];
    
    $ppilregion=$r['region'];
    if ($ppilregion=="B") {
        $pselectall="";
        $pselectbarat="selected";
        $pselecttimur="";
    }elseif ($ppilregion=="T") {
        $pselectall="";
        $pselectbarat="";
        $pselecttimur="selected";
    }else{
        $pselectall="selected";
        $pselectbarat="";
        $pselecttimur="";
    }
    
    $pblnsusulan=$r['blnkuranglebih'];
    if ($pblnsusulan=="0000-00-00") $pblnsusulan="";
    
    if (!empty($pblnsusulan)) {
        $pblnsusulan = date('F Y', strtotime($pblnsusulan));
    }
    
}
?>


<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<div class='modal fade' id='myModal2' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_klaimid").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
        
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                  
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-9'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidklaim; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $iduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Tanggal </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='mytgl01' name='e_tglinput' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglinput; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idkaryawan' name='e_idkaryawan'>
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $query = "SELECT DISTINCT karyawanId as karyawanid, nama as nama FROM dbmaster.karyawan WHERE 1=1 ";
                                                
                                                if ($pigroup=="1" OR $pigroup=="3" OR $pigroup=="24") {
                                                    $query .= " AND IFNULL(tglkeluar,'0000-00-00') = '0000-00-00' ";
                                                    $query .= " AND IFNULL(aktif,'')='Y' ";
                                                    $query .=" AND (IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00') ";
                                                    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S', 'BKS-')  "
                                                            . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                            . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                            . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                            . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY', 'LOGIN ', 'SMGTO-') ";
                                                    $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                    $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                                }else{
                                                    $query .= " AND (karyawanId='$pidcard' OR karyawanId='$idajukan') ";
                                                }
                                                
                                                $query .=" order by nama, karyawanId";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    $pkryid=$a['karyawanid'];
                                                    $pkrynm=$a['nama'];
                                                    if ($pkryid==$idajukan)
                                                        echo "<option value='$pkryid' selected>$pkrynm ($pkryid)</option>";
                                                    else
                                                        echo "<option value='$pkryid'>$pkrynm ($pkryid)</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Region <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_region' name='cb_region' onchange="ShowData()">
                                            <?PHP
                                            echo "<option value='' $pselectall>-- ALL --</option>";
                                            echo "<option value='B' $pselectbarat>Barat</option>";
                                            echo "<option value='T' $pselecttimur>Timur</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pengajuan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_divpengajuan' name='cb_divpengajuan' onchange="ShowDataDariPengajuan()">
                                            <?PHP //$pdivpengajuan
                                            $query = "SELECT DivProdId as divprodid, nama as nama FROM dbmaster.divprod where br='Y' AND DivProdId NOT IN ('HO') ";//AND DivProdId NOT IN ('OTHER', 'OTHERS')
                                            $query .=" order by nama";
                                            $tampil=mysqli_query($cnmy, $query);
                                            if ($pdivpengajuan=="ETH") {
                                                //echo "<option value='ETH' selected>ETHICAL</option>";
                                            }
                                            while($et=mysqli_fetch_array($tampil)){
                                                $netdivprod=$et['divprodid'];
                                                $netdivnm=$et['nama'];
                                                if ($netdivprod=="CAN") $netdivnm="CANARY / ETHICAL";
                                                if ($netdivprod==$pdivpengajuan)
                                                    echo "<option value='$netdivprod' selected>$netdivnm</option>";
                                                else
                                                    echo "<option value='$netdivprod'>$netdivnm</option>";
                                                
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_iddist'>Distributor <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_iddist' name='e_iddist' onchange="ShowDataFromDist()">
                                              <?PHP
                                                $plewat=false;
                                                $pbolehlewat=true;
                                                
                                                if ($pigroup=="43") $pbolehlewat=false;
                                                
                                                //$pinsel="('0000000002', '0000000003', '0000000005', '0000000006', '0000000010', "
                                                //        . " '0000000011', '0000000016', '0000000023', '0000000030', '0000000031')";
                                                if ($pigroup=="40") {
                                                    $pinsel="('0000000002', '0000000003', '0000000005', '0000000010', '0000000011', "
                                                            . "'0000000012', '0000000015', '0000000024', '0000000025', '0000000029', '0000000031')";
                                                }else{
                                                    $pinsel="('0000000002', '0000000003', '0000000005', '0000000010', '0000000011', "
                                                            . " '0000000015', '0000000017', '0000000029', '0000000031')";
                                                }
                                                
                                                $sql=mysqli_query($cnmy, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 WHERE"
                                                        . " Distid IN $pinsel order by Distid, nama");
                                                echo "<option value=''>--Pilih--</option>";
                                                while ($Xt=mysqli_fetch_array($sql)){
                                                    $pdisid=$Xt['Distid'];
                                                    $pdisnm=$Xt['nama'];
                                                    $cidcek=(INT)$pdisid;
                                                    if ($pdisid==$piddistrb){
                                                        echo "<option value='$pdisid' selected>$cidcek - $pdisnm</option>";
                                                        
                                                        $plewat=true;
                                                    }else
                                                        echo "<option value='$pdisid'>$cidcek - $pdisnm</option>";
                                                    
                                                }
                                                
                                                if ($plewat == false AND $pact=="editdata") $pbolehlewat=true;
                                                
                                                if ($pbolehlewat==true) {
                                                    $sql=mysqli_query($cnmy, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 WHERE"
                                                            . " Distid NOT IN $pinsel order by Distid, nama");
                                                    echo "<option value=''></option>";
                                                    while ($Xt=mysqli_fetch_array($sql)){
                                                        $pdisid=$Xt['Distid'];
                                                        $pdisnm=$Xt['nama'];
                                                        $cidcek=(INT)$pdisid;
                                                        if ($pdisid==$piddistrb)
                                                            echo "<option value='$pdisid' selected>$cidcek - $pdisnm</option>";
                                                        else
                                                            echo "<option value='$pdisid'>$cidcek - $pdisnm</option>";
                                                    }
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_bulan' name='e_bulan' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pbulan; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='tgl01'>
                                            <input type="text" class="form-control" id='e_periode1' name='e_periode1' required='required' placeholder='dd MMMM yyyy'  value='<?PHP echo $pperiode1; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>s/d </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='tgl02'>
                                            <input type="text" class="form-control" id='e_periode2' name='e_periode2' required='required' placeholder='dd MMMM yyyy' value='<?PHP echo $pperiode2; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                

                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:red;"><u>Reklaim</u> <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        &nbsp;
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:red;">Reklaim / Susulan (Rp.) <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_jmlkuranglebihX' name='e_jmlkuranglebihX' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' placeholder="jumlah rp kekurangan / kelebihan" value='<?PHP echo $pjmlkuranglebih; ?>' onblur="HitungTotalJumlahRp()">
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:red;">Keterangan <span class='required'></span></label>
                                        <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_ketkuranglebih' name='e_ketkuranglebih' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketkuranglebih; ?>' maxlength="150">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:red;">Bulan Reklaim </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='cbln02'>
                                            <input type="text" class="form-control" id='e_bulansusulan' name='e_bulansusulan' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pblnsusulan; ?>' >
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                

                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>
                                        <button type='button' class='btn btn-warning btn-xs' onclick='ShowAktifitasiTombo()'>Aktivitas</button> 
                                        <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $paktivitas1; ?></textarea>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas2'> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas2' name='e_aktivitas2' rows='3' placeholder='Keterangan Detail'><?PHP echo $paktivitas2; ?></textarea>
                                    </div>
                                </div>
                              
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">PPN (%) <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_jmlppn' name='e_jmlppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlppn; ?>' onblur="" Readonly>
                                    </div><!--disabled='disabled'-->
                                </div>
                              
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">PPH (%) <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_jmlpph' name='e_jmlpph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlpph; ?>' onblur="" Readonly>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div id="inputanpajak">
                                    
                                    <div  class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Pengusaha Kena Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_kenapajak' name='e_kenapajak' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pkenapajak; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">No Seri Faktur Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_noserifp' name='e_noserifp' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pnoseripajak; ?>'>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Tgl Faktur Pajak </label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div class='input-group date' id='mytgl01'>
                                                <input type="text" class="form-control" id='mytgl05' name='e_tglpajak' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglfakturpajak; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            <button type='button' class='btn btn-warning btn-xs' onclick='ShowRealisasiTombo()'>Nama Realisasi</button> 
                                            <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_realisasi' name='e_realisasi' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $prealisasi; ?>'>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                                
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis Klaim (Online) <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_jenisklaim' name='cb_jenisklaim' onchange="">
                                            <?PHP //$pdivpengajuan
                                                if ($pjenisonline=="S") {
                                                    echo "<option value=''>&nbsp;</option>";
                                                    echo "<option value='S' selected>SDM ONLINE</option>";
                                                    echo "<option value='D'>SKS ONLINE</option>";
                                                }elseif ($pjenisonline=="D") {
                                                    echo "<option value=''>&nbsp;</option>";
                                                    echo "<option value='S'>SDM ONLINE</option>";
                                                    echo "<option value='D' selected>SKS ONLINE</option>";
                                                }else{
                                                    echo "<option value='' selected>&nbsp;</option>";
                                                    echo "<option value='S'>SDM ONLINE</option>";
                                                    echo "<option value='D'>SKS ONLINE</option>";
                                                }
                                                
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                
                            </div>
                        </div>
                    </div>
                    
                    
                    <div id="div_detail">

                        <style>
                            .form-group, .input-group, .control-label {
                                margin-bottom:2px;
                            }
                            .control-label {
                                font-size:11px;
                            }
                            #datatable input[type=text], #tabelnobr input[type=text] {
                                box-sizing: border-box;
                                color:#000;
                                font-size:11px;
                                height: 25px;
                            }
                            select.soflow {
                                font-size:12px;
                                height: 30px;
                            }
                            .disabledDiv {
                                pointer-events: none;
                                opacity: 0.4;
                            }

                            table.datatable, table.tabelnobr {
                                color: #000;
                                font-family: Helvetica, Arial, sans-serif;
                                width: 100%;
                                border-collapse:
                                collapse; border-spacing: 0;
                                font-size: 11px;
                                border: 0px solid #000;
                            }

                            table.datatable td, table.tabelnobr td {
                                border: 1px solid #000; /* No more visible border */
                                height: 10px;
                                transition: all 0.1s;  /* Simple transition for hover effect */
                            }

                            table.datatable th, table.tabelnobr th {
                                background: #DFDFDF;  /* Darken header a bit */
                                font-weight: bold;
                            }

                            table.datatable td, table.tabelnobr td {
                                background: #FAFAFA;
                            }

                            /* Cells in even rows (2,4,6...) are one color */
                            tr:nth-child(even) td { background: #F1F1F1; }

                            /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
                            tr:nth-child(odd) td { background: #FEFEFE; }

                            tr td:hover.biasa { background: #666; color: #FFF; }
                            tr td:hover.left { background: #ccccff; color: #000; }

                            tr td.center1, td.center2 { text-align: center; }

                            tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
                            tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
                            /* Hover cell effect! */
                            tr td {
                                padding: -10px;
                            }
                            .divnone {
                                display: none;
                            }
                        </style>

                        <script>
                            $(document).ready(function() {
                                var dataTable = $('#datatable').DataTable( {
                                    "ordering": false,
                                    bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                                    "bPaginate": false
                                } );
                            });
                            
                            function KosongkanDataCabang() {
                                $("#c-datacabang").html("");
                            }
                            function TampilkanDataCabang() {
								ShowAktifitasiTombo();
								ShowRealisasiTombo();
                                var eid=document.getElementById('e_id').value;
                                var edivisi=document.getElementById('cb_divpengajuan').value;
                                var edistid=document.getElementById('e_iddist').value;
                                var eregion=document.getElementById('cb_region').value;
                                var eppn=document.getElementById('e_jmlppn').value;
                                var epph=document.getElementById('e_jmlpph').value;
                                
                                if (edistid=="0000000011" || edistid=="11") {
                                    eppn="0";
                                    document.getElementById('e_jmlppn').value=eppn;
                                }else{
                                    eppn="10";
                                    document.getElementById('e_jmlppn').value=eppn;
                                }
                                
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var act = urlku.searchParams.get("act");
                                var idmenu = urlku.searchParams.get("idmenu");
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mod_br_admentryklaim/inputdetail.php?module=tmapilkancabang"+"&act="+act,
                                    data:"uid="+eid+"&udivisi="+edivisi+"&udistid="+edistid+"&uregion="+eregion+"&uppn="+eppn+"&upph="+epph,
                                    success:function(data){
                                        $("#c-datacabang").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                        </script>
                        
                        <button type='button' class='btn btn-info btn-xs' onclick='TampilkanDataCabang()'>Tampilkan Data Cabang</button> <span class='required'></span>
                        
                        <div id='loading'></div>
                        <div id='c-datacabang'>
                        
                        </div>
                        <?PHP //include "module/mod_br_admentryklaim/inputdetail.php"; ?>   
                        
                        

                    </div>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                            if (empty($sudahapv)) {
                                if ($pact=="editdata") {
                                    ?><button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Update</button><?PHP
                                }else{
                                    echo "<div class='col-sm-6'>";
                                    include "module/mod_br_admentryklaim/ttd_klaimdiscadm.php";
                                    echo "</div>";
                                }
                            ?>
                            <?PHP
                            }elseif ($sudahapv=="reject") {
                                echo "data sudah hapus";
                            }else{
                                echo "tidak bisa diedit, sudah approve";
                            }
                            ?>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    <div class='col-md-12 col-xs-12'>
                        <div class='x_panel'>
                            
                            
                        </div>
                    </div>
                    
                    
                </div>
            </div>
            
        </form>
        
    </div>
    
    
</div>



<script>
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        if (act=="editdata") {
            TampilkanDataCabang();
        }
        
    } );
    
    function disp_confirm(pText_, ket)  {
        var iid = document.getElementById('e_id').value;
        var ikry = document.getElementById('e_idkaryawan').value;
        var idistid = document.getElementById('e_iddist').value;
        var ipengajuan = document.getElementById('cb_divpengajuan').value;

        var ijml = document.getElementById('e_txtgrdreal').value;


        if (ikry=="") {
            alert("Pembuat masih kosong...");
            return false;
        }
        if (idistid=="") {
            alert("distributor harus diisi...");
            return false;
        }
        if (ipengajuan=="") {
            alert("pengajuan / divisi harus diisi...");
            return false;
        }

        if (ijml=="" || ijml=="0") {
            alert("Jumlah Permintaan Masih kosong...");
            return false;
        }

        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                        
                        
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_admentryklaim/aksi_admentryklaim.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
</script>



<script>
    function ShowAktifitasiTombo() {
        ShowAktivitas();
        ShowNamaRealisasi();
    }
    
    function ShowRealisasiTombo() {
        ShowNamaRealisasi();
    }
    
    
    function ShowData() {
        KosongkanDataCabang();
        ShowAktivitas();
        ShowNamaRealisasi();
        ShowDataPPH();
    }
    
    function ShowDataDariPengajuan() {
        ShowAktivitas();
        ShowNamaRealisasi();
    }
    
    function ShowDataFromDist() {
        KosongkanDataCabang();
        ShowNamaRealisasi();
        TentukanPeriode();
        ShowAktivitas();
        ShowDataPPH();
    }

    function ShowDataPPH() {
        var eregion=document.getElementById('cb_region').value;
        if (eregion=="T") {
            document.getElementById('e_jmlpph').value="2";
        }else{
            var edistid=document.getElementById('e_iddist').value;
            if (edistid=="0000000002" || edistid=="0000000031" || edistid=="0000000015" || edistid=="0000000017") {
                document.getElementById('e_jmlpph').value="0";
            }else{
                document.getElementById('e_jmlpph').value="2";
            }
        }
    }
    
    function ShowAktivitas() {
        var eregion=document.getElementById('cb_region').value;
        var edivisi=document.getElementById('cb_divpengajuan').value;
        var edistid=document.getElementById('e_iddist').value;
        var ebln=document.getElementById('e_bulan').value;
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_admentryklaim/viewdata.php?module=caridataaktivitas",
            data:"uregion="+eregion+"&udivisi="+edivisi+"&udistid="+edistid+"&ubln="+ebln+"&uper1="+eper1+"&uper2="+eper2,
            success:function(data){
                document.getElementById('e_aktivitas').value=data;
            }
        });
    }
    
    function ShowNamaRealisasi() {
        var edistid=document.getElementById('e_iddist').value
        $.ajax({
            type:"post",
            url:"module/mod_br_admentryklaim/viewdata.php?module=caridatarealisasi",
            data:"udistid="+edistid,
            success:function(data){
                document.getElementById('e_realisasi').value=data;
            }
        });
    }
</script>


<script>
    
    function TentukanPeriode() {
        var edist =document.getElementById('e_iddist').value;
        var idate1=document.getElementById('e_bulan').value;
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
        
        
        document.getElementById('e_periode1').value="01 "+nbulan1+" "+ntahun1;
        if (edist=="0000000002X" || edist=="299999") {
            document.getElementById('e_periode2').value="15 "+nbulan1+" "+ntahun1;
        }else{
            document.getElementById('e_periode2').value=ntgl1+" "+nbulan1+" "+ntahun1;
        }
    }
    
    $('#tgl01, #tgl02').on('change dp.change', function(e){
        ShowAktivitas();
    });
    
    $('#cbln01').on('change dp.change', function(e){
        TentukanPeriode();
        ShowAktivitas();
    });
</script>