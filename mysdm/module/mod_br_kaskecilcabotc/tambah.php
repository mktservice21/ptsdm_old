<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
?>
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
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
</style>

<script>    


$(document).ready(function() {
    var element = document.getElementById("div_atasan");
    //element.classList.remove("disabledDiv");
    element.classList.add("disabledDiv");
} );

function ShowCOA(udiv, ucoa) {
    var icar = "";
    var idiv = document.getElementById(udiv).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_kaskecilcabotc/viewdata.php?module=viewcoadivisi",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#"+ucoa).html(data);
        }
    });
}

function ShowNamaRealsasi() {
    var ikary = document.getElementById('cb_karyawan').value;
    $.ajax({
        type:"post",
        url:"module/mod_br_kaskecilcabotc/viewdata.php?module=carinamarealisasi",
        data:"ukary="+ikary,
        success:function(data){
            document.getElementById('e_nmreal').value=data;
        }
    });
}


function ShowDataKaryawan() {
    ShowDataCabang();
    ShowDataAtasan();
    ShowNamaRealsasi();
}

function ShowDataCabang() {
    var ikry = document.getElementById('cb_karyawan').value;
    $.ajax({
        type:"post",
        url:"module/mod_br_kaskecilcabotc/viewdata.php?module=caricabang",
        data:"ukry="+ikry,
        success:function(data){
            $("#cb_cabang").html(data);
            ShowDataJumlah();
            ShowDataArea();
            //ShowDataPC();
            //ShowDataOTS();
        }
    });
}

function ShowDataArea() {
    var ikry = document.getElementById('cb_karyawan').value;
    var icab = document.getElementById('cb_cabang').value;
    $.ajax({
        type:"post",
        url:"module/mod_br_kaskecilcabotc/viewdata.php?module=caridataarea",
        data:"ukry="+ikry+"&ucab="+icab,
        success:function(data){
            $("#cb_area").html(data);
        }
    });
}

function ShowDataAtasan() {
    var ikry = document.getElementById('cb_karyawan').value;
    $.ajax({
        type:"post",
        url:"module/mod_br_kaskecilcabotc/dataatasanotc.php?module=caridataatasan",
        data:"ukry="+ikry,
        success:function(data){
            $("#div_atasan").html(data);
        }
    });
}

function ShowDataJumlah() {
    var ikry = document.getElementById('cb_karyawan').value;
    var icab = document.getElementById('cb_cabang').value;
    var iuntuk = document.getElementById('cb_untuk').value;
    $.ajax({
        type:"post",
        url:"module/mod_br_kaskecilcabotc/datajumlah.php?module=caridatajumlah",
        data:"ukry="+ikry+"&ucab="+icab+"&uuntuk="+iuntuk,
        success:function(data){
            $("#div_jumlah").html(data);
            //ShowDataOTS();
            HitungSaldoAkhir();
        }
    });
}


function ShowDataPengajuan() {
    ShowDataAtasan();
    ShowDataJumlah();
    ShowDataArea();
    /*
    ShowDataPC();
    ShowDataOTS();
        */
}

function ShowDataPC() {
    var icab = document.getElementById('cb_cabang').value;
    var iuntuk = document.getElementById('cb_untuk').value;
    $.ajax({
        type:"post",
        url:"module/mod_br_kaskecilcabotc/viewdata.php?module=caridatapettycashcab",
        data:"ucab="+icab+"&uuntuk="+iuntuk,
        success:function(data){
            document.getElementById('e_pcrp').value=data;
            HitungSaldoAkhir();
        }
    });
}
function ShowDataOTS() {
    var icab = document.getElementById('cb_cabang').value;
    var iuntuk = document.getElementById('cb_untuk').value;
    $.ajax({
        type:"post",
        url:"module/mod_br_kaskecilcabotc/viewdata.php?module=caridataoustanding",
        data:"ucab="+icab+"&uuntuk="+iuntuk,
        success:function(data){
            document.getElementById('e_otsrp').value=data;
            HitungSaldoAkhir();
        }
    });
}

function HitungSaldoAkhir() {
    var newchar = '';
    //var ipc=document.getElementById('e_pcrp').value;
    var ipc=document.getElementById('e_rppc').value;
    var ijml=document.getElementById('e_jml').value;
    var ijmlots=document.getElementById('e_otsrp').value;
    var isldawal=document.getElementById('e_sldawal').value;
    var itambahn=document.getElementById('e_tambahanrp').value;
    var ipclalu=document.getElementById('e_pcblnlalu').value;
    
    if (ipc=="") ipc="0";
    if (ijml=="") ijml="0";
    if (ijmlots=="") ijmlots="0";
    if (isldawal=="") isldawal="0";
    if (itambahn=="") itambahn="0";
    if (ipclalu=="") ipclalu="0";
    
    ipc = ipc.split(',').join(newchar);
    ijml = ijml.split(',').join(newchar);
    ijmlots = ijmlots.split(',').join(newchar);
    isldawal = isldawal.split(',').join(newchar);
    itambahn = itambahn.split(',').join(newchar);
    ipclalu = ipclalu.split(',').join(newchar);
    
    var isaldo="0";
    //isaldo =parseFloat(ipc)-parseFloat(ijmlots)-parseFloat(ijml)+parseFloat(isldawal);
    isaldo =parseFloat(isldawal)+parseFloat(ipclalu)-parseFloat(ijml);//-parseFloat(ijmlots)
    
    var isaldo_tbh="0";
    //isaldo_tbh =parseFloat(ipc)+parseFloat(itambahn)-parseFloat(ijmlots)-parseFloat(ijml)+parseFloat(isldawal);
    isaldo_tbh =parseFloat(isldawal)+parseFloat(ipclalu)-parseFloat(ijml)+parseFloat(itambahn);//-parseFloat(ijmlots)
    
    document.getElementById('e_saldorp').value=isaldo;
    document.getElementById('e_saldorp_tambah').value=isaldo_tbh;
    
    
}

function disp_confirm(pText_,ket)  {
    var iid = document.getElementById('e_id').value;
    var itgl = document.getElementById('e_tglberlaku').value;
    var ibulan = document.getElementById('e_bulan').value;
    var ikry = document.getElementById('cb_karyawan').value;
    var icabid = document.getElementById('cb_cabang').value;
    var iareaid = document.getElementById('cb_area').value;
    var icoap = document.getElementById('cb_coa').value;
    var ijml = document.getElementById('e_jml').value;
    var iket = document.getElementById('e_ket').value;
    var isaldo=document.getElementById('e_saldorp').value;
    var isaldo_tbh=document.getElementById('e_saldorp_tambah').value;
    var irppcm=document.getElementById('e_pcrp').value;
    
    if (ikry=="") {
        alert("Pembuat masih kosong...");
        return false;
    }
    if (icabid=="") {
        alert("Cabang harus diisi...");
        return false;
    }
    if (icoap=="") {
        //alert("COA harus dipilih...");
        //return false;
    }
    if (ijml=="" || ijml=="0") {
        alert("Jumlah Permintaan Masih kosong...");
        return false;
    }
    if (iket=="") {
        //alert("keterangan harus diisi...");
        //return false;
    }

    var newchar = '';
    
    if (irppcm=="") irppcm="0";
    irppcm = irppcm.split(',').join(newchar);
    
    if (parseFloat(irppcm)<=0) {
        alert("Petty Cash NOL...");
        return false;
    }
    
    
    if (isaldo=="") isaldo="0";
    if (isaldo_tbh=="") isaldo_tbh="0";
    if (ijml=="") ijml="0";
    
    
    isaldo = isaldo.split(',').join(newchar);
    isaldo_tbh = isaldo_tbh.split(',').join(newchar);
    ijml = ijml.split(',').join(newchar);
    
    //alert(parseFloat(ijml));
    //alert(parseFloat(irppcm));

    //if (parseFloat(isaldo_tbh)<0) {
    if (parseFloat(ijml)>parseFloat(irppcm)) {
        alert("Total Rp. tidak boleh melebihi Petty Cash...\n\
Jika Saldo Minus, silakan minta tambahan saldo pc untuk dibuka.");
        return false;
    }
    
    $.ajax({
        type:"post",
        url:"module/mod_br_kaskecilcabotc/viewdata.php?module=cekdatasudahada",
        data:"utgl="+itgl+"&uid="+iid+"&ukry="+ikry+"&ucabid="+icabid+"&ucoap="+icoap+"&ubulan="+ibulan+"&uareaid="+iareaid,
        success:function(data){
            //var tjml = data.length;
            //alert(data);
            //return false;

            if (data=="boleh") {

                ok_ = 1;
                if (ok_) {
                    var r=confirm(pText_)
                    if (r==true) {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        //document.write("You pressed OK!")
                        document.getElementById("demo-form2").action = "module/mod_br_kaskecilcabotc/aksi_kaskecilcabotc.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                        document.getElementById("demo-form2").submit();
                        return 1;
                    }
                } else {
                    //document.write("You pressed Cancel!")
                    return 0;
                }

            }else{
                alert(data);
            }
        }
    });
        
}


</script>

<?PHP
$idbr="";
$pidkodeinput="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$pbulanpilih = date('F Y', strtotime($hari_ini));

$pidgroup=$_SESSION['GROUP'];
                
$pidcardpl=$_SESSION['IDCARD'];
$idajukan=$_SESSION['IDCARD'];
$nmajukan=$_SESSION['NAMALENGKAP']; 
$keterangan="";
$pdivisi="OTC";
if ($_SESSION['DIVISI']=="OTC") $pdivisi="OTC";


$ptxthidden="hidden";
//if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26" OR $pidgroup=="1") $ptxthidden="";
if ($pidgroup=="1") $ptxthidden="";

$ptxthidden2="hidden";
if ($pidgroup=="1") $ptxthidden2="";



$untukpil1="";
$untukpil2="selected";
        
$pjumlah="";
$jumlahk="";
$coa="";
$pnamauntuk="";
$pketerangan="";
$idkdoepilih="";

$pcabangid="";
$pcabangid_o="";
$pareaid="";
$ppilcoa="";

$pjabatanid="";


$pkdspv="";
$pnamaspv="";
$pkddm="";
$pnamadm="";
$pkdsm="";
$pnamasm="";
$pkdgsm="";
$pnamagsm="";

$query ="SELECT a.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, 
    a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm, 
    a.icabangid as icabangid, a.areaid as areaid, a.jabatanid as jabatanid, a.b_norek, b.b_norek as norek_2
    FROM dbmaster.t_karyawan_posisi a 
    LEFT JOIN hrd.karyawan b on a.karyawanId=b.karyawanId 
    LEFT JOIN hrd.karyawan c on a.spv=c.karyawanId 
    LEFT JOIN hrd.karyawan d on a.dm=d.karyawanId 
    LEFT JOIN hrd.karyawan e on a.sm=e.karyawanId 
    LEFT JOIN hrd.karyawan f on a.gsm=f.karyawanId WHERE a.karyawanid='$idajukan'";
$ptampil= mysqli_query($cnmy, $query);
$nrs= mysqli_fetch_array($ptampil);
$pkdspv=$nrs['spv'];
$pnamaspv=$nrs['nama_spv'];
$pkddm=$nrs['dm'];
$pnamadm=$nrs['nama_dm'];
$pkdsm=$nrs['sm'];
$pnamasm=$nrs['nama_sm'];
$pkdgsm=$nrs['gsm'];
$pnamagsm=$nrs['nama_gsm'];

$pcabangid=$nrs['icabangid'];
$pcabangid_o=$nrs['icabangid'];
$pareaid=$nrs['areaid'];
$pjabatanid=$nrs['jabatanid'];

$pnorekening=$nrs['norek_2'];
$pnorekening2=$nrs['b_norek'];
if (empty($pnorekening)) $pnorekening=$pnorekening2;

    $query = "select icabangid as icabangid, areaid as areaid, jabatanid as jabatanid from hrd.karyawan where karyawanid='$idajukan'";
    $tampil= mysqli_query($cnmy, $query);
    $rowx= mysqli_fetch_array($tampil);
    if (empty($pcabangid)) $pcabangid=$rowx['icabangid'];
    if (empty($pcabangid_o)) $pcabangid_o=$rowx['icabangid'];
    if (empty($pareaid)) $pareaid=$rowx['areaid'];
    if (empty($pjabatanid)) $pjabatanid=$rowx['jabatanid'];
    
    //khusus
    if ($idajukan=="0000000515") {
        $pcabangid_o="JKT_RETAIL";
        $pareaid="0000000001";
    }
    
$pidcabang=$pcabangid_o;

$prpjumlah=0;
$prppc=0;
$prpsaldo=0;
$prpsaldo_tambah=0;
$prpots=0;

$prpsldawal=0;
$prptambah=0;
$prpblnlalu=0;//pc_bln_lalu


    $query = "select * from dbmaster.t_uangmuka_kascabang WHERE icabangid_o='$pidcabang'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prpjumlah=$pr['jumlah'];
    if (empty($prpjumlah)) $prpjumlah=0;
    $prppc=$pr['pcm'];
    if (empty($prppc)) $prppc=0;
    //$prpsldawal=$pr['saldoawal'];
    //if (empty($prpsldawal)) $prpsldawal=0;
    $prptambah=$pr['jmltambahan'];
    if (empty($prptambah)) $prptambah=0;
    
    
    
    $query = "select * from dbmaster.t_outstanding_kaskecilcab WHERE icabangid='$pidcabang' AND pengajuan='$pdivisi'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prpots=$pr['jmlsisa'];
    if (empty($prpots)) $prpots=0;

    $prpblnlalu=$pr['saldobln_jalan'];
    if (empty($prpblnlalu)) $prpblnlalu=0;
    
    $prpsldawal=$pr['saldo_awal'];
    if (empty($prpsldawal)) $prpsldawal=0;
    
    
    $prpjumlah=(DOUBLE)$prpsldawal+(DOUBLE)$prpblnlalu+(DOUBLE)$prptambah;
    
	if ((DOUBLE)$prpots>0) {
		$prpjumlah=(DOUBLE)$prpjumlah-(DOUBLE)$prpots;
		
		$prpsldawal=(DOUBLE)$prpsldawal-(DOUBLE)$prpots;
	}





$pfinid="";
$ptglfin="";

$pnmreal=$nmajukan;


$philangkanhapus=false;

$sudahapv="";

$pmyact=$_GET['act'];

        
$pact=$_GET['act'];

$act="input";
if ($pact=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_kaskecilcabang WHERE idkascab='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idkascab'];
    $pidkodeinput=$r['idkascab'];
    $tglberlku = date('d/m/Y', strtotime($r['tanggal']));
    $tgl1 = date('d/m/Y', strtotime($r['tanggal']));
    $pbulanpilih = date('F Y', strtotime($r['bulan']));
    $idajukan=$r['karyawanid']; 
    $keterangan=$r['keterangan'];
    $pjumlah=$r['jumlah'];
    $pdivisi=$r['divisi'];
    $pcabangid=$r['icabangid'];
    $pcabangid_o=$r['icabangid_o'];
    $pareaid=$r['areaid_o'];
    $ppilcoa=$r['coa4'];
    $pjabatanid=$r['jabatanid'];
    $pnmreal=$r['nmrealisasi'];
    $pnorekening=$r['norekening'];

    
    $ppengajuan=$r['pengajuan'];

    if ($ppengajuan=="OTC" OR $ppengajuan=="CHC") {
        $untukpil1="";
        $untukpil2="selected";
    }

    

    $pnmfieldcab=" icabangid_o ";
    $pidcabang=$pcabangid_o;
    
    
    $query = "select * from dbmaster.t_uangmuka_kascabang WHERE $pnmfieldcab='$pidcabang'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prpjumlah=$pr['jumlah'];
    if (empty($prpjumlah)) $prpjumlah=0;
    $prppc=$pr['pcm'];
    if (empty($prppc)) $prppc=0;
    $prpsldawal=$pr['saldoawal'];
    if (empty($prpsldawal)) $prpsldawal=0;
    $prptambah=$pr['jmltambahan'];
    if (empty($prptambah)) $prptambah=0;
    
    
            $query = "select * from dbmaster.t_kaskecilcabang_rpdetail WHERE idkascab='$idbr'";
            $tampilp= mysqli_query($cnmy, $query);
            $pr= mysqli_fetch_array($tampilp);

            $prpjumlah=$pr['jumlah'];
            if (empty($prpjumlah)) $prpjumlah=0;
            $prppc=$pr['pcm'];
            if (empty($prppc)) $prppc=0;
            $prpsldawal=$pr['saldoawal'];
            if (empty($prpsldawal)) $prpsldawal=0;
            $prptambah=$pr['jmltambahan'];
            if (empty($prptambah)) $prptambah=0;
            
            $prpblnlalu=$pr['pc_bln_lalu'];
            if (empty($prpblnlalu)) $prpblnlalu=0;
    
    
        $prpsaldo=(DOUBLE)$prppc-(DOUBLE)$pjumlah+(DOUBLE)$prpsldawal;
        $prpsaldo_tambah=(DOUBLE)$prppc+(DOUBLE)$prptambah-(DOUBLE)$pjumlah+(DOUBLE)$prpsldawal;

    $pfinid=$r['fin'];
    $patasan1=$r['atasan1'];
    $patasan2=$r['atasan2'];
    $patasan3=$r['atasan3'];
    $patasan4=$r['atasan4'];
    
    
    $ptglfin=$r['tgl_fin'];
    $ptglatasan1=$r['tgl_atasan1'];
    $ptglatasan2=$r['tgl_atasan2'];
    $ptglatasan3=$r['tgl_atasan3'];
    $ptglatasan4=$r['tgl_atasan4'];
    
    if ($ptglfin=="0000-00-00 00:00:00") $ptglfin="";
    if ($ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
    if ($ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
    if ($ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
    if ($ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
    
    
    $philangkanhapus=true;
    if (empty($patasan1) AND empty($patasan2) AND empty($patasan3) AND empty($patasan4)) {
        $philangkanhapus=false;
    }elseif (empty($patasan1) AND empty($patasan2) AND empty($patasan3) AND !empty($patasan4)) {
        if (empty($ptglatasan4)) $philangkanhapus=false;
    }elseif (empty($patasan1) AND empty($patasan2) AND !empty($patasan3)) {
        if (empty($ptglatasan3)) $philangkanhapus=false;
        if (!empty($patasan4) AND !empty($ptglatasan4)) $philangkanhapus=true;
    }elseif (empty($patasan1) AND !empty($patasan2)) {
        if (empty($ptglatasan2)) $philangkanhapus=false;
        if (!empty($patasan3) AND !empty($ptglatasan3)) $philangkanhapus=true;
    }elseif (!empty($patasan1)) {
        if (empty($ptglatasan1)) $philangkanhapus=false;
        if (!empty($patasan2) AND !empty($ptglatasan2)) $philangkanhapus=true;
    }
    
    $query ="SELECT cb.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm 
        FROM dbmaster.t_kaskecilcabang cb
        LEFT JOIN dbmaster.t_karyawan_posisi a on cb.karyawanid=a.karyawanid
        LEFT JOIN hrd.karyawan b on cb.karyawanId=b.karyawanId 
        LEFT JOIN hrd.karyawan c on a.spv=c.karyawanId 
        LEFT JOIN hrd.karyawan d on a.dm=d.karyawanId 
        LEFT JOIN hrd.karyawan e on a.sm=e.karyawanId 
        LEFT JOIN hrd.karyawan f on a.gsm=f.karyawanId WHERE cb.idkascab='$idbr'";
    $ptampil= mysqli_query($cnmy, $query);
    $nrs= mysqli_fetch_array($ptampil);
    $pkdspv=$nrs['spv'];
    $pnamaspv=$nrs['nama_spv'];
    $pkddm=$nrs['dm'];
    $pnamadm=$nrs['nama_dm'];
    $pkdsm=$nrs['sm'];
    $pnamasm=$nrs['nama_sm'];
    $pkdgsm=$nrs['gsm'];
    $pnamagsm=$nrs['nama_gsm'];
     
    $query = "select * from dbmaster.t_outstanding_kaskecilcab WHERE icabangid='$pidcabang' AND pengajuan='$ppengajuan'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prpots=(DOUBLE)$pr['jmlsisa']-(DOUBLE)$pjumlah;
    
    
    //ADMIN BR
    if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26") {
        if (empty($ptglfin)) $philangkanhapus=false;
    }
    
}
    
?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                
                <div class='x_panel'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan (Periode PC / Kas Kecil) </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type='text' class='form-control' id='e_bulan' name='e_bulan' autocomplete='off' value='<?PHP echo $pbulanpilih; ?>' />
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pengajuan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_untuk' name='cb_untuk' onchange="ShowDataPengajuan();" data-live-search="true">
                                            <?PHP
                                                //echo "<option value='ETH' $untukpil1>Ethical</option>";
                                                echo "<option value='OTC' $untukpil2>CHC</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pembuat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="ShowDataKaryawan();" data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP 
                                                    $query = "select karyawanId, nama From hrd.karyawan
                                                        WHERE 1=1 ";
                                                    if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26" OR $pidgroup=="1") {
                                                        
                                                        $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                        $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                        $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                        $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                                        $query .= " AND (divisiId='OTC' OR karyawanid='$idajukan') ";
                                                    }else{
                                                        $query .= " AND karyawanid ='$pidcardpl' ";
                                                    }
                                                    $query .= " ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pkaryid=$z['karyawanId'];
                                                        $pkarynm=$z['nama'];
                                                        $pkryid=(INT)$pkaryid;
                                                        if ($z['karyawanId']==$idajukan)
                                                            echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                        else
                                                            echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="ShowDataPengajuan()">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            //if ($pact=="editdata"){
                                                
                                                $query = "select distinct icabangid_o as iCabangId, nama as nama from MKT.icabang_o WHERE 1=1 ";
                                                if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26" OR $pidgroup=="1") {
                                                    $query .=" AND (IFNULL(aktif,'')<>'N' OR icabangid_o='$pidcabang') ";
                                                }else{
                                                    $query .=" AND icabangid_o='$pidcabang' ";
                                                }
                                                $query .=" order by nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pidcab=$z['iCabangId'];
                                                    $pnmcab=$z['nama'];
                                                    if ($pidcab==$pidcabang)
                                                        echo "<option value='$pidcab' selected>$pnmcab</option>";
                                                    else
                                                        echo "<option value='$pidcab'>$pnmcab</option>";
                                                }
                                                
                                                $pidcab="JKT_RETAIL";
                                                $pnmcab="JAKARTA RETAIL";
                                                if ($idajukan=="0000000515") {
                                                    echo "<option value=\"$pidcab\" selected>$pnmcab</option>";
                                                }
                                            //}
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_area' name='cb_area' onchange="">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            //if ($pact=="editdata"){
                                                $pidcabfil=$pidcabang;
                                                if ($pidcabang=="JKT_RETAIL") $pidcabfil="0000000007";
                                                
                                                $query = "select distinct icabangid_o as iCabangId, areaid_o as areaid_o, nama as nama from MKT.iarea_o WHERE icabangid_o='$pidcabfil' order by nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $npidarea=$z['areaid_o'];
                                                    $npnmarea=$z['nama'];
                                                    if ($npidarea==$pareaid)
                                                        echo "<option value='$npidarea' selected>$npnmarea</option>";
                                                    else
                                                        echo "<option value='$npidarea'>$npnmarea</option>";
                                                }
                                            //}
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowCOAKode('cb_divisi', 'cb_kdoepil', 'cb_coa');">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER') ";
                                            if ($_SESSION['DIVISI']=="OTC") {
                                                $query .=" AND DivProdId = 'OTC' ";
                                            }
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $piddiv=$z['DivProdId'];
                                                $pnmdiv=$z['DivProdId'];
                                                if ($piddiv==$pdivisi)
                                                    echo "<option value='$piddiv' selected>$pnmdiv</option>";
                                                else
                                                    echo "<option value='$piddiv'>$pnmdiv</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Akun <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_coa' name='cb_coa' data-live-search="true">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                /*
                                                //$fil = " AND ( c.DIVISI2 = '$pdivisi' OR RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '') )";
                                                $fil = " AND ( c.DIVISI2 = '$pdivisi' )";
                                                if (empty($pdivisi)) $fil = " AND RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '')";

                                                $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                                                    LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
                                                    LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
                                                    WHERE 1=1 $fil ";//OR a.COA4='$ppilcoa'
                                                $query .= " ORDER BY a.COA4";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pcoa=$z['COA4'];
                                                    $pnmcoa=$z['NAMA4'];
                                                    if ($pcoa==$ppilcoa)
                                                        echo "<option value='$pcoa' selected>$pcoa - $pnmcoa</option>";
                                                    else
                                                        echo "<option value='$pcoa'>$pcoa - $pnmcoa</option>";
                                                }
                                                */
                                                ?>
                                            </select>
                                    </div>
                                </div>
                                
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Realisasi <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='text' id='e_nmreal' name='e_nmreal' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmreal; ?>' maxlength="50">
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Rekening <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='text' id='e_norek' name='e_norek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekening; ?>' maxlength="50">
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_ket' name='e_ket' class='form-control col-md-7 col-xs-12' value='<?PHP echo $keterangan; ?>'>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type="button" class='btn btn-warning btn-xs'  name="btn_refresh" id="btn_refresh" onclick="ShowDataAtasan('spv')" value="Refresh Atasan.."><!--refresh_atasan()-->
                                    </div>
                                </div>
                            
                                
                                <div id="div_atasan">
                                    
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kdspv' name='e_kdspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdspv; ?>'>
                                            <input type='text' id='e_namaspv' name='e_namaspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamaspv; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kddm' name='e_kddm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkddm; ?>'>
                                            <input type='text' id='e_namadm' name='e_namadm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadm; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kdsm' name='e_kdsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdsm; ?>'>
                                            <input type='text' id='e_namasm' name='e_namasm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamasm; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>HOS <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kdgsm' name='e_kdgsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdgsm; ?>'>
                                            <input type='text' id='e_namagsm' name='e_namagsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamagsm; ?>'>
                                        </div>
                                    </div>
                                    
                                </div>


                                <div id="div_jumlah">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            <span style="color:red;">Saldo Awal Rp.</span>
                                        </label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_sldawal' name='e_sldawal' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpsldawal; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            <span style="color:red;">Isi PC Bulan Lalu</span>
                                        </label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_pcblnlalu' name='e_pcblnlalu' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpblnlalu; ?>' Readonly>
                                        </div>
                                    </div>
                                    

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            <span style="color:red;">Petty Cash</span>
                                        </label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_rppc' name='e_rppc' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prppc; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div <?PHP echo $ptxthidden; ?> class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            <span style="color:red;">Tambahan LIMIT PC</span>
                                        </label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_tambahanrp' name='e_tambahanrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prptambah; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div <?PHP echo $ptxthidden; ?> class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            <span style="color:red;">Total Petty Cash</span>
                                        </label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_pcrp' name='e_pcrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpjumlah; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            <span style="color:red;">Outstanding</span>
                                        </label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_otsrp' name='e_otsrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpots; ?>' Readonly>
                                        </div>
                                    </div>
                                
                                    
                                    
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Total Biaya Rp.
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jml' name='e_jml' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>' Readonly>
                                    </div>
                                </div>


                                <div <?PHP echo $ptxthidden; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Saldo Akhir tambah
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_saldorp_tambah' name='e_saldorp_tambah' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpsaldo_tambah; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Saldo Akhir
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_saldorp' name='e_saldorp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpsaldo; ?>' Readonly>
                                    </div>
                                </div>
                                
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
                            </script>
                            
                            
                            <?PHP include "module/mod_br_kaskecilcabotc/inputdetailotc.php"; ?>    
                                
                            
                            
                            
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                            if (empty($sudahapv)) {
                                if ($pmyact=="editdata") {
                                    ?><button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Update</button><?PHP
                                }else{
                                    echo "<div class='col-sm-6'>";
                                    include "module/mod_br_kaskecilcabotc/ttd_kkcabotc.php";
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
                            
                            
                            <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <?PHP
                                            if ($philangkanhapus == false) {
                                                //echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm('Simpan ?', '$act')\" >Save</button>";
                                            }
                                            ?>
                                            <!--<button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP //echo $act; ?>")'>Save</button>-->
                            <!--
                                            <a class='btn btn-default' href="<?PHP //echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                        </div>
                                    </div>
                                </div>
                            -->
                            
                            
                            
                            </div>
                            
                        </div>
                    </div>
                    

                </div>
            </div>
            

        </form>
        
    </div>
    <!--end row-->
</div>


    <script type="text/javascript">
        $(function() {
            $('#tglfrom').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var min = $(this).datepicker('getDate');
                    $('#tglto').datepicker('option', 'minDate', min || '0');
                    datepicked();
                } 
            });
            $('#tglto').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                minDate: '0',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var max = $(this).datepicker('getDate');
                    $('#tglfrom').datepicker('option', 'maxDate', max || '+2Y');
                    datepicked();
                } 
            });
        });
        var datepicked = function() {
            var from = $('#from');
            var to = $('#to');
            var nights = $('#nights');
            var fromDate = from.datepicker('getDate')
            var toDate = to.datepicker('getDate')
            if (toDate && fromDate) {
                var difference = 0;
                var oneDay = 1000 * 60 * 60 * 24;
                var difference = Math.ceil((toDate.getTime() - fromDate.getTime()) / oneDay);
                nights.val(difference);
            }
        }
    </script>
    
    
    
<script>
                                    
    $(document).ready(function() {

        $('#e_bulan').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'MM yy',
            minDate: '-1M',
            onSelect: function(dateStr) {
                
            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                //showPeriode();
            },

            beforeShow: function() {
                if ((selDate = $(this).val()).length > 0) 
                {
                    iYear = selDate.substring(selDate.length - 4, selDate.length);
                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                }
            }

        });
    });

</script>

<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>