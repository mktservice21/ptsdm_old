<?PHP

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];

        
$pcabangid="";

    $query ="SELECT a.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, 
        a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm, 
        a.icabangid as icabangid, a.areaid as areaid, a.jabatanid as jabatanid 
        FROM dbmaster.t_karyawan_posisi a 
        LEFT JOIN hrd.karyawan b on a.karyawanId=b.karyawanId 
        LEFT JOIN hrd.karyawan c on a.spv=c.karyawanId 
        LEFT JOIN hrd.karyawan d on a.dm=d.karyawanId 
        LEFT JOIN hrd.karyawan e on a.sm=e.karyawanId 
        LEFT JOIN hrd.karyawan f on a.gsm=f.karyawanId WHERE a.karyawanid='$pidcard'";
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
    $pareaid=$nrs['areaid'];
    $pjabatanid=$nrs['jabatanid'];


    $query = "select icabangid as icabangid, areaid as areaid, jabatanid as jabatanid from hrd.karyawan where karyawanid='$pidcard'";
    $tampil= mysqli_query($cnmy, $query);
    $rowx= mysqli_fetch_array($tampil);
    if (empty($pcabangid)) $pcabangid=$rowx['icabangid'];
    if (empty($pareaid)) $pareaid=$rowx['areaid'];
    if (empty($pjabatanid)) $pjabatanid=$rowx['jabatanid'];

    $picabidfil="";
    if ($pidjbt=="38" || (DOUBLE)$pidjbt==38) {
        $pcabangid="";
        $query = "select distinct karyawanid as karyawanid, icabangid as icabangid from hrd.rsm_auth where karyawanid='$pidcard'";
        $tampil= mysqli_query($cnmy, $query);
        while ($nro= mysqli_fetch_array($tampil)) {
            $pncab=$nro['icabangid'];
            if ($pncab=="0000000003" OR $pncab=="0000000114") {
                $pcabangid=$pncab;
            }else{
                if (empty($pcabangid)) $pcabangid=$pncab;
            }


            $picabidfil .="'".$pncab."',";
        }
        if (!empty($picabidfil)) {
            $picabidfil="(".substr($picabidfil, 0, -1).")";
        }else{
            $picabidfil="('nnzznnnn')";
        }

    }elseif ($pidjbt=="08" || (DOUBLE)$pidjbt==8) {
        $pcabangid="";
        $query = "select distinct karyawanid as karyawanid, icabangid as icabangid from MKT.idm0 where karyawanid='$pidcard'";
        $tampil= mysqli_query($cnmy, $query);
        while ($nro= mysqli_fetch_array($tampil)) {
            $pncab=$nro['icabangid'];
            if ($pncab=="0000000003" OR $pncab=="0000000114") {
                $pcabangid=$pncab;
            }else{
                if (empty($pcabangid)) $pcabangid=$pncab;
            }


            $picabidfil .="'".$pncab."',";
        }
        if (!empty($picabidfil)) {
            $picabidfil="(".substr($picabidfil, 0, -1).")";
        }else{
            $picabidfil="('nnzznnnn')";
        }

    }

    $pidcabang=$pcabangid;


    $query= "select DISTINCT a.karyawanid as karyawanid, b.nama as nama from MKT.idm0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " WHERE a.icabangid='$pcabangid' AND IFNULL(a.karyawanid,'')<>'' "
            . " AND (IFNULL(b.tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(b.tglkeluar,'')='')";
    $tampil= mysqli_query($cnmy, $query);
    $rowd= mysqli_fetch_array($tampil);
    $pnnkrydm=$rowd['karyawanid'];
    $pnnmkrydm=$rowd['nama'];
    if (!empty($pnnkrydm)) {
        $pkdspv=""; $pnamaspv="";
        $pkddm=$pnnkrydm;
        $pnamadm=$pnnmkrydm;
    }
    
    $query= "select DISTINCT a.karyawanid as karyawanid, b.nama as nama from MKT.ism0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " WHERE a.icabangid='$pcabangid' AND IFNULL(a.karyawanid,'')<>'' "
            . " AND (IFNULL(b.tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(b.tglkeluar,'')='')";
    $tampil= mysqli_query($cnmy, $query);
    $rowd2= mysqli_fetch_array($tampil);
    $pnnkrydm=$rowd2['karyawanid'];
    $pnnmkrydm=$rowd2['nama'];
    if (!empty($pnnkrydm)) {
        $pkdsm=$pnnkrydm;
        $pnamasm=$pnnmkrydm;
        $pkdgsm="";
        $pnamagsm="";
    }
    
    
    
    $query = "select a.gsm, b.nama as nama_gsm FROM dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.gsm=b.karyawanid WHERE a.karyawanid='$pkdsm'";
    $ptampil2= mysqli_query($cnmy, $query);
    $nrs2= mysqli_fetch_array($ptampil2);

    $pkdgsm=$nrs2['gsm'];
    $pnamagsm=$nrs2['nama_gsm'];

    if ($pcabangid=="0000000003") {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
    }

    if ($pcabangid=="00000000114") {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
        $pkdsm="";
        $pnamasm="";
    }

    if ($pidjbt=="08" || (DOUBLE)$pidjbt==8) {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
    }


// END CABANG & ATASAN



$pidinput="";


$hari_ini = date("Y-m-01");
$tgl_pertama = date('F Y', strtotime($hari_ini));
$tgl_kedua = date('F Y', strtotime('+1 month', strtotime($hari_ini)));


$pjeniscuti="01";//Tahunan
$pkeperluan="";
$ctglpilih="";

$act="input";
if ($pidact=="editdata"){
    $act="update";

    include "config/fungsi_ubahget_id.php";
    
    $pidinput_ec=$_GET['id'];
    $pidinput = decodeString($pidinput_ec);
    
    $edit = mysqli_query($cnmy, "SELECT * FROM hrd.t_cuti0 WHERE idcuti='$pidinput'");
    $jmlrw0=mysqli_num_rows($edit);
    if ((INT)$jmlrw0>0) {
        $r    = mysqli_fetch_array($edit);
        $pjeniscuti=$r['id_jenis'];
        $pkeperluan=$r['keperluan'];
        $pbln1=$r['bulan1'];
        $pbln2=$r['bulan2'];
        
        $tgl_pertama = date('F Y', strtotime($pbln1));
        $tgl_kedua = date('F Y', strtotime($pbln2));
        
        
        $query = "select distinct tanggal from hrd.t_cuti1 WHERE idcuti='$pidinput' order by tanggal";
        $tampil1=mysqli_query($cnmy, $query);
        $ketemu1=mysqli_num_rows($tampil1);
        if ((INT)$ketemu1>0) {
            while ($row1=mysqli_fetch_array($tampil1)) {
                $tgl_p=$row1['tanggal'];
                if (!empty($tgl_p)) {
                    $tgl_p = date('Y-m-d', strtotime($tgl_p));

                    $ctglpilih .="'".$tgl_p."',";
                }
            }
        }
    }



}

$ptglpilih = date('Y-m-d', strtotime($tgl_pertama));
$ptglpilih02 = date('Y-m-d', strtotime($tgl_kedua));

$query = "select nama from hrd.jabatan where jabatanId='$pidjbt'";
$ntampil=mysqli_query($cnmy, $query);
$nr=mysqli_fetch_array($ntampil);
$pnamajabatan=$nr['nama'];

?>
<div class="">

    
    <!--row-->
    <div class="row">


        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>

                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                        id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                        
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>



                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='text' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type='text' id='e_namauser' name='e_namauser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamalengkap; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jabatan <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type='text' id='e_namajbt' name='e_namajbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamajabatan; ?>' Readonly>
                                    </div>
                                </div>


                                <hr/>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow' name='cb_jeniscuti' id='cb_jeniscuti' onchange="">
                                            <?php
                                            $query = "select id_jenis, nama_jenis From hrd.jenis_cuti order by id_jenis";
                                            $tampilket= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampilket)) {
                                                $nidjns=$du['id_jenis'];
                                                $nnmjns=$du['nama_jenis'];

                                                if ($nidjns==$pjeniscuti) 
                                                    echo "<option value='$nidjns' selected>$nnmjns</option>";
                                                else
                                                    echo "<option value='$nidjns'>$nnmjns</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div id='div_akv'>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keperluan <span class='required'></span></label>
                                        <div class='col-md-9'>
                                        <textarea class='form-control' id="e_keperluan" name='e_keperluan' maxlength="300"><?PHP echo $pkeperluan; ?></textarea>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_bulan01' name='e_bulan01' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $tgl_pertama; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>

                                        </div>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div id="div_tgl">
                                            <?PHP
                                                $p_tgl = date('d', strtotime($ptglpilih));
                                                $p_akh = date('t', strtotime($ptglpilih));
                                                
                                                $pchkpilih="";
                                                if (strpos($ctglpilih, $ptglpilih)==true) $pchkpilih="checked";
                                                echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih' $pchkpilih> $p_tgl &nbsp; &nbsp; ";

                                                $nom=2;
                                                for ($ix=1;$ix<(INT)$p_akh;$ix++) {
                                                    $ptglpilih = date('Y-m-d', strtotime('+1 days', strtotime($ptglpilih)));
                                                    
                                                    $pchkpilih="";
                                                    if (strpos($ctglpilih, $ptglpilih)==true) $pchkpilih="checked";
                                                    
                                                    $p_tgl = date('d', strtotime($ptglpilih));
                                                    echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih' $pchkpilih> $p_tgl &nbsp; &nbsp; ";
                                                    if ($nom>5) {echo "<br/>"; $nom=0;}
                                                    $nom++;
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>s/d. Bulan <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class='input-group date' id='cbln02'>
                                            <input type="text" class="form-control" id='e_bulan02' name='e_bulan02' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $tgl_kedua; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>

                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div id="div_tgl2">
                                            <?PHP
                                                $p_tgl = date('d', strtotime($ptglpilih02));
                                                $p_akh = date('t', strtotime($ptglpilih02));
                                                
                                                $p_b01 = date('Ym', strtotime($ptglpilih));
                                                $p_b02 = date('Ym', strtotime($ptglpilih02));
                                                
                                                $pchkpilih="";
                                                if (strpos($ctglpilih, $ptglpilih02)==true AND $p_b01<>$p_b02) $pchkpilih="checked";
                                                echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih02' $pchkpilih> $p_tgl &nbsp; &nbsp; ";

                                                $nom=2;
                                                for ($ix=1;$ix<(INT)$p_akh;$ix++) {
                                                    $ptglpilih02 = date('Y-m-d', strtotime('+1 days', strtotime($ptglpilih02)));
                                                    
                                                    $pchkpilih="";
                                                    if (strpos($ctglpilih, $ptglpilih02)==true AND $p_b01<>$p_b02) $pchkpilih="checked";
                                                    
                                                    $p_tgl = date('d', strtotime($ptglpilih02));
                                                    echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih02' $pchkpilih> $p_tgl &nbsp; &nbsp; ";
                                                    if ($nom>5) {echo "<br/>"; $nom=0;}
                                                    $nom++;
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>

                    
                    <div class='clearfix'></div>
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                    </div>
                                </div>

                                

                                <br/>
                                <div hidden id="div_atasan">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kdspv' name='e_kdspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdspv; ?>'>
                                            <input type='text' id='e_namaspv' name='e_namaspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamaspv; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kddm' name='e_kddm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkddm; ?>'>
                                            <input type='text' id='e_namadm' name='e_namadm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadm; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kdsm' name='e_kdsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdsm; ?>'>
                                            <input type='text' id='e_namasm' name='e_namasm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamasm; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>GSM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kdgsm' name='e_kdgsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdgsm; ?>'>
                                            <input type='text' id='e_namagsm' name='e_namagsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamagsm; ?>'>
                                        </div>
                                    </div>
                                    
                                </div>

                            </div>

                        </div>
                    </div>


                </form>

            </div>

        </div>


    </div>

</div>




<script>
    $(document).ready(function() {
        $('#cbln01').on('change dp.change', function(e){
            ShowTanggalPilih();
        });
        
        $('#cbln02').on('change dp.change', function(e){
            ShowTanggalPilih2();
        });
    });
    function ShowTanggalPilih() {
        var etgl =document.getElementById('e_bulan01').value;
        
        $.ajax({
            type:"post",
            url:"module/marketing/viewdatamkt.php?module=viewdatatanggal",
            data:"utgl="+etgl,
            success:function(data){
                $("#div_tgl").html(data);
            }
        });
    }
    
    function ShowTanggalPilih2() {
        var etgl =document.getElementById('e_bulan02').value;
        
        $.ajax({
            type:"post",
            url:"module/marketing/viewdatamkt.php?module=viewdatatanggal",
            data:"utgl="+etgl,
            success:function(data){
                $("#div_tgl2").html(data);
            }
        });
    }
</script>



<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

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

<style>
    .divnone {
        display: none;
    }
    #dtabel th {
        font-size: 13px;
    }
    #dtabel td { 
        font-size: 11px;
    }
</style>

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>


<script type="text/javascript">
    function disp_confirm(pText_,ket)  {
        var iid = document.getElementById('e_id').value;
        var ikeperluan = document.getElementById('e_keperluan').value;
        var ijenis = document.getElementById('cb_jeniscuti').value;
        var ikry = document.getElementById('e_idcarduser').value;
        var ibln1 = document.getElementById('e_bulan01').value;
        var ibln2 = document.getElementById('e_bulan02').value;
        

        if (ikry=="") {
            alert("Anda harus login ulang...");
            return false;
        }
        

        var chk_arr =  document.getElementsByName("chktgl[]");
        var chklength = chk_arr.length;             
        var itglpilih="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                //itglpilih =itglpilih + "'"+chk_arr[k].value+"',";
                itglpilih =itglpilih + chk_arr[k].value+",";
            }
        }
        
        if (ijenis=="02") {//melahirkan
            
        }else{
            if (ikeperluan=="") {
                alert("Keperluan harus diisi...");
                return false;
            }
            
            if (itglpilih.length > 0) {
                var lastIndex = itglpilih.lastIndexOf(",");
                //itglpilih = "("+itglpilih.substring(0, lastIndex)+")";
                itglpilih = itglpilih.substring(0, lastIndex);
            }else{
                alert("Tidak ada tanggal yang dipilih...!!!");
                return false;
            }
        }
            
        //alert(itglpilih); return false;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var iact = urlku.searchParams.get("act");
        //alert(iact);
        $.ajax({
            type:"post",
            url:"module/marketing/viewdatamkt.php?module=cekdatasudahada",
            data:"uact="+iact+"&uid="+iid+"&ukry="+ikry+"&utglpilih="+itglpilih+"&ujenis="+ijenis+"&ubln1="+ibln1+"&ubln2="+ibln2,
            success:function(data){
                //var tjml = data.length;
                //alert(data);
                //return false;

                if (data=="boleh") {

                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            //document.write("You pressed OK!")
                            document.getElementById("form_data1").action = "module/marketing/mkt_formcutieth/aksi_formcutieth.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                            document.getElementById("form_data1").submit();
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