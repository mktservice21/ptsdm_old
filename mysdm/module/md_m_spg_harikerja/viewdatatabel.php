<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>
<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG....!!!";
        exit;
    }
    
    
    //include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    $pidcabang=$_POST['ucabang'];
    
    $_SESSION['SPGMSTHKCAB']=$pidcabang;
    $_SESSION['SPGMSTHKTGL']=date("F Y", strtotime($date1));
    
    $fcabang = " AND icabangid = '$pidcabang' ";
    if (empty($pidcabang)) $fcabang = " AND IFNULL(icabangid,'') = '' ";
    
    if ($pidcabang=="JKT_MT") {
        $fcabang = " AND IFNULL(icabangid,'') = '0000000007' AND alokid='001' ";
    }elseif ($pidcabang=="JKT_RETAIL") {
        $fcabang = " AND IFNULL(icabangid,'') = '0000000007' AND alokid='002' ";
    }
    
    $jmlkerja = 0;
    $jmlkerja_aspr = 0;
    $query = "select * from dbmaster.t_spg_jmlharikerja WHERE DATE_FORMAT(periode,'%Y%m')='$bulan'";
    $tampilnp = mysqli_query($cnmy, $query);
    while ($np= mysqli_fetch_array($tampilnp)) {
        if (!empty($np['jumlah'])) $jmlkerja=$np['jumlah'];
        if (!empty($np['jml_aspr'])) $jmlkerja_aspr=$np['jml_aspr'];
    }
    
    $n_cabangid=$pidcabang;
    if ($pidcabang=="JKT_MT") {
        $n_cabangid="0000000007";
    }elseif ($pidcabang=="JKT_RETAIL") {
        $n_cabangid="0000000007";
    }
    
    
    $sudah_validate=false;
    $query ="SELECT * FROM dbmaster.t_spg_validate WHERE icabangid='$pidcabang' AND DATE_FORMAT(bulan,'%Y%m')='$bulan'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nr= mysqli_fetch_array($tampil);
        $nbulan=$nr['bulan'];
        if (!empty($nbulan)) $sudah_validate=true;
    }
  
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSPGSELHKMS01_".$puserid."_$now ";
    $tmp02 =" dbtemp.DSPGSELHKMS02_".$puserid."_$now ";
    $tmp03 =" dbtemp.DSPGSELHKMS03_".$puserid."_$now ";
    
    //$query = "select * from MKT.spg WHERE IFNULL(aktif,'') = 'Y' $fcabang";
    $query = "SELECT * FROM dbmaster.t_spg_data WHERE 1=1 AND DATE_FORMAT(periode,'%Y%m')='$bulan' $fcabang";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "SELECT a.*, CAST('' as CHAR(1)) as stransaksi, b.nama nama_cabang, c.nama nama_area, d.id_zona, e.nama_zona, f.jabatanid, f.nama_jabatan "
            . " FROM $tmp02 a LEFT JOIN MKT.icabang_o b on a.icabangid=b.icabangid_o "
            . " LEFT JOIN MKT.iarea_o c on a.icabangid=c.icabangid_o AND a.areaid=c.areaid_o "
            . " LEFT JOIN dbmaster.t_spg_gaji_area_zona d on a.icabangid=d.icabangid AND a.areaid=d.areaid "
            . " LEFT JOIN dbmaster.t_zona e on d.id_zona=e.id_zona "
            . " LEFT JOIN dbmaster.t_spg_jabatan f on a.jabatid=f.jabatid";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    
    $query="select * from dbmaster.t_spg_gaji_br0 WHERE DATE_FORMAT(periode,'%Y%m')='$bulan' AND IFNULL(stsnonaktif,'')<>'Y' $fcabang";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "SELECT a.*, CAST('' as CHAR(1)) as stransaksi, b.nama nama_cabang, c.nama nama_area, e.nama_zona, f.jabatanid, f.nama_jabatan "
            . " FROM $tmp03 a LEFT JOIN MKT.icabang_o b on a.icabangid=b.icabangid_o "
            . " LEFT JOIN MKT.iarea_o c on a.icabangid=c.icabangid_o AND a.areaid=c.areaid_o "
            . " LEFT JOIN dbmaster.t_spg_gaji_area_zona d on a.icabangid=d.icabangid AND a.areaid=d.areaid "
            . " LEFT JOIN dbmaster.t_zona e on d.id_zona=e.id_zona "
            . " LEFT JOIN dbmaster.t_spg_jabatan f on a.jabatid=f.jabatid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_cabangid' name='u_cabangid' value='<?PHP echo $pidcabang; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    
    <div class='x_content'>
        <b>Standar Hari Kerja SPG : <?PHP echo $jmlkerja; ?> Hari</b><br/>
        <b>Standar Hari Kerja ASPR : <?PHP echo $jmlkerja_aspr; ?> Hari</b><br/>
        <!--
        <span style='color:red;'><b>*) Pastikan Area / Zona terisi.<br/>
                *) Geser Scroll ke kanan untuk menyesuaikan Area / Zona.<br/>
                *) Apabila ada perubahan area, pastikan pilih Area, klik tombol UPDATE. 
                Dan pada Hari Kerja harus disimpan ulang (klik SAVE)</b></span>-->
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='300px' align="center">Nama SPG</th>
                    <th width='50px' align="center">Jabatan</th>
                    <th width='100px' align="center" nowrap>Jml. Hari Kerja</th>
                    <th width='100px' align="center" nowrap>Sakit</th>
                    <th width='100px' align="center" nowrap>Izin</th>
                    <th width='100px' align="center" nowrap>Alpa</th>
                    <th width='100px' align="center" nowrap>UC</th>
                    <th width='200px' align="center" nowrap>Keterangan</th>
                    <th width='50px'></th>
                    <th width='100px' align="center">Area</th>
                    <th width='200px' align="center">Penempatan</th>
                    <th width='50px'>Simpan Data SPG</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $query = "select id_spg, nama, penempatan, icabangid, areaid, nama_area, id_zona, jabatid, nama_jabatan from $tmp01 ";
                    $query .=" ORDER BY nama";
                    
                    $tampil = mysqli_query($cnmy, $query);
                    while ($sp= mysqli_fetch_array($tampil)) {
                        $pidspg=$sp['id_spg'];
                        $pnmspg=$sp['nama'];
                        $ptempatspg=$sp['penempatan'];
                        
                        
                        $pareaidspg=$sp['areaid'];
                        $pjabatanid=$sp['jabatid'];
                        $pnmarea=$sp['nama_area'];
                        $pidzona=$sp['id_zona'];
                        $pnmjabatan=$sp['nama_jabatan'];
                        
                        $queryg="select * from $tmp02 WHERE id_spg='$pidspg'";
                        $tampilg= mysqli_query($cnmy, $queryg);
                        $ketemugj= mysqli_num_rows($tampilg);
                        $gj= mysqli_fetch_array($tampilg);
                        
                        if ($ketemugj>0) {
                            $pareaidspg=$gj['areaid'];
                            $pjabatanid=$gj['jabatid'];
                            $pnmarea=$gj['nama_area'];
                            $pidzona=$gj['id_zona'];
                            $pnmjabatan=$gj['nama_jabatan'];
                        }
                        
                        if ($pidzona=="0") $pidzona="";
                        
                        $pjmlhk=$gj['jml_harikerja'];
                        $pjmlsakit=$gj['jml_sakit'];
                        $pjmlizin=$gj['jml_izin'];
                        $pjmlalpa=$gj['jml_alpa'];
                        $pjmluc=$gj['jml_uc'];
                        $pketerangan=$gj['keterangan'];
                        $psts=$gj['sts'];
                        
                        $pstatusapv="";
                        $papvtgl1=$gj['apvtgl1'];
                        $sudahapv=false;
                        if (!empty($papvtgl1) AND $papvtgl1<>'0000-00-00 00:00:00') {
                            $sudahapv=true;
                            $pstatusapv="on process";
                        }
                        
                        $papvtgl2=$gj['apvtgl2'];
                        if (!empty($papvtgl2) AND $papvtgl2<>'0000-00-00 00:00:00') {
                            $sudahapv=true;
                            $pstatusapv="process finance";
                        }
                        
                        $papvtgl4=$gj['apvtgl4'];
                        if (!empty($papvtgl4) AND $papvtgl4<>'0000-00-00 00:00:00') {
                            $sudahapv=true;
                            $pstatusapv="process manager";
                        }
                        
                        if ($psts=="P") $pstatusapv="pending";
                        
                        $finidspg="<input type='hidden' id='txtidspg$no' name='txtidspg$no' class='input-sm' value='$pidspg'>";
                        $finbulan="<input type='hidden' id='txtbulan$no' name='txtbulan$no' class='input-sm' value='$tgl1'>";
                        $fincabang="<input type='hidden' id='txtcabang$no' name='txtcabang$no' class='input-sm' value='$pidcabang'>";
                        
                        
                        $finharikerja="<input type='text' size='10px' id='txtjmlhk$no' name='txtjmlhk$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlhk'>";
                        $finuket="<input type='text' size='20px' id='txtketerangan$no' name='txtketerangan$no' class='input' autocomplete='off' value='$pketerangan'>";
                        
                        $finharisakit="<input type='text' size='5px' id='txtjmlsakit$no' name='txtjmlsakit$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlsakit'>";
                        $finhariizin="<input type='text' size='5px' id='txtjmlizin$no' name='txtjmlizin$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlizin'>";
                        $finharialpa="<input type='text' size='5px' id='txtjmlalpa$no' name='txtjmlalpa$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlalpa'>";
                        $finhariuc="<input type='text' size='5px' id='txtjmluc$no' name='txtjmluc$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmluc'>";
                        
                        $n_jml_hari_sis=$jmlkerja;
                        if ($pjabatanid=="003") { //ASPR
                            $n_jml_hari_sis=$jmlkerja_aspr;
                        }
                        $finjhksistem="<input type='hidden' size='5px' id='txtjkhsis$no' name='txtjkhsis$no' class='input-sm inputmaskrp2' autocomplete='off' value='$n_jml_hari_sis'>";
                        
                        $finjabatanid="<input type='hidden' size='27px' id='txtjbtid$no' name='txtjbtid$no' class='input' autocomplete='off' value='$pjabatanid'>";
                        $finareaid="<input type='hidden' size='27px' id='txtareaid$no' name='txtareaid$no' class='input' autocomplete='off' value='$pareaidspg'>";
                        $finzonaid="<input type='hidden' size='27px' id='txtzonaid$no' name='txtzonaid$no' class='input' autocomplete='off' value='$pidzona'>";
                        
                        $finpenempatan="<input type='text' size='27px' id='txtpenempatan$no' name='txtpenempatan$no' class='input' autocomplete='off' onkeyup=\"this.value = this.value.toUpperCase();\" value='$ptempatspg'>";

                        
                        $fsimpan="'txtidspg$no', 'txtbulan$no', 'txtcabang$no', 'txtjmlhk$no', 'txtketerangan$no', 'txtjmlsakit$no', 'txtjmlizin$no', 'txtjmlalpa$no', 'txtjkhsis$no', 'txtjmluc$no', 'txtjbtid$no', 'txtareaid$no', 'txtzonaid$no', 'txtpenempatan$no'";
                        $simpandata= "<input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save' onclick=\"SimpanData('input', $fsimpan)\">";
                        $hapusdata= "<input type='button' class='btn btn-danger btn-xs' id='s-hapus' value='Hapus' onclick=\"SimpanData('hapus', $fsimpan)\">";
                        
                        $rincian="<a title='Lihat Rincian Gaji' href='#' class='btn btn-default btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=$_POST[module]&idspg=$pidspg&bulan=$bulan&idcab=$pidcabang&idarea=$pareaidspg',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Rincian Gaji</a>";
                        
                        
                        
                        $ppilih_nm_areazona=$pnmarea." (ZONA ".$pidzona.")";
                        
                        
                        if ($_SESSION['GROUP']=="37"){
                            
                        }else{
                            
                            //sudah validate
                            if ($sudah_validate==true) {
                                $simpandata="sudah validasi";
                                $hapusdata="";
                            }
                        }
                        
                        if ($sudahapv==true){
                            $simpandata="";
                            $hapusdata="$pstatusapv";
                        }
                        
                        
                        echo "<tr>";
                        echo "<td>$no $finjhksistem</td>";
                        echo "<td>$pnmspg $finidspg $finbulan $fincabang $finjabatanid $finareaid $finzonaid</td>";
                        echo "<td>$pnmjabatan</td>";
                        echo "<td>$finharikerja</td>";
                        echo "<td>$finharisakit</td>";
                        echo "<td>$finhariizin</td>";
                        echo "<td>$finharialpa</td>";
                        echo "<td>$finhariuc</td>";
                        echo "<td>$finuket</td>";
                        echo "<td align='right' nowrap></td>";
                        echo "<td nowrap>$ppilih_nm_areazona</td>";
                        echo "<td>$finpenempatan</td>";
                        echo "<td nowrap>$simpandata $rincian $hapusdata</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                ?>
            </tbody>
        </table>

    </div>
    
    
    
    
    <div class='col-md-12 col-sm-12 col-xs-12'>

        <div id="div_jumlah">
            <div class='x_panel'>
                <div class='x_content'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>


                        
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                            <div class='col-xs-9'>
                                <div class="checkbox">
                                    <?PHP
                                    if ($sudah_validate==false) {
                                        echo "<button type='button' class='btn btn-danger' onclick=\"ValidasiDataInput()\">Submit</button>";
                                    }else{
                                        if ($_SESSION['GROUP']=="37"){
                                            echo "<button type='button' class='btn btn-warning' onclick=\"HapusValidate()\">Hapus Posisi Submit</button>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
            
        </div>

    </div>
    
    
    
</form>

<?php
hapusdata:
    
    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_query($cnmy, "drop temporary table $tmp02");
    mysqli_query($cnmy, "drop temporary table $tmp03");
    mysqli_close($cnmy);
?>


<style>
    .divnone {
        display: none;
    }
    #datatablespggj th {
        font-size: 12px;
    }
    #datatablespggj td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

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

</style>
<?PHP //$fsimpan="txtidspg$no, txtbulan$no, txtcabang$no, txtjmlhk$no, txtketerangan$no, txtsewa$no, txtpulsa$no, txtparkir$no"; ?>                            
<script>
    
    $(document).ready(function() {
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false,
            "scrollY": 440,
            "scrollX": true
        } );
    });
    
    function SimpanData(eact, aidspg, abulan, acabang, aharikerja, aketerangan, asakit, aizin, aalpa, ahksistem, auc, ajbtid, aareaid, aidzona, atempat)  {
        var newchar = '';
        
        
        var espg =document.getElementById(aidspg).value;
        var ebulan =document.getElementById(abulan).value;
        var ecabang =document.getElementById(acabang).value;
        
        var ejbtid =document.getElementById(ajbtid).value;
        var eareaid =document.getElementById(aareaid).value;
        var eidzona =document.getElementById(aidzona).value;
        var etempat =document.getElementById(atempat).value;

        var ehk =document.getElementById(aharikerja).value;
        var eket =document.getElementById(aketerangan).value;
        
        var esakit =document.getElementById(asakit).value;
        var eizin =document.getElementById(aizin).value;
        var ealpa =document.getElementById(aalpa).value;
        var ehksistem =document.getElementById(ahksistem).value;
        var euc =document.getElementById(auc).value;
        
        if (espg==""){
            alert("id kosong....");
            return 0;
        }
        
        
        if (eact=="hapus"){
            
        }else{
            
            if (ejbtid=="") {
                alert("jabatan kosong....");
                return 0;
            }
        
            if (eareaid==""){
                alert("Area masih kosong...");
                return 0;
            }
            
            if (eidzona=="") {
                alert("ZONA kosong....");
                return 0;
            }
            
            if (etempat=="") {
                alert("Pemempatan SPG harus diisi....");
                return 0;
            }
            
        }
        
        
        var nhksistem = ehksistem; 
        nhksistem = nhksistem.split(',').join(newchar);
        
        var nhk = ehk; 
        nhk = nhk.split(',').join(newchar);
        var nsakit = esakit; 
        nsakit = nsakit.split(',').join(newchar);
        var nizin = eizin; 
        nizin = nizin.split(',').join(newchar);
        var nalpa = ealpa; 
        nalpa = nalpa.split(',').join(newchar);
        var nuc = euc; 
        nuc = nuc.split(',').join(newchar);
        
        if (nhk=="") nhk="0";
        if (nsakit=="") nsakit="0";
        if (nizin=="") nizin="0";
        if (nalpa=="") nalpa="0";
        if (nuc=="") nuc="0";
        
        var njmlh="0";
        njmlh=parseInt(nhk)+parseInt(nsakit)+parseInt(nizin)+parseInt(nalpa)+parseInt(nuc);
        
        if (eact=="hapus"){
            
        }else{
            if (parseInt(njmlh) < parseInt(nhksistem)) {
                alert("Total Jumlah Hari Kerja "+njmlh+" hari, Masih kurang dari "+nhksistem+" hari, GAGAL SIMPAN...");
                return false;
            }else if (parseInt(njmlh) > 30) {
                alert("Total Jumlah Hari Kerja "+njmlh+" hari, melebihi batas maksimal (30 hari), GAGAL SIMPAN...");
                return false;
            }
        }
        

        //alert(espg+", "+ebulan+", "+ecabang+", "+ehk+", "+eket+", "+euc); return 0;
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/md_m_spg_harikerja/aksi_spgharikerja.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidspg="+espg+"&ubulan="+ebulan+"&ucabang="+ecabang+"&uharikerja="+ehk+
                            "&uketerangan="+eket+"&usakit="+esakit+"&uizin="+eizin+"&ualpa="+ealpa+
                            "&uhksistem="+ehksistem+"&uuc="+euc+
                            "&utempat="+etempat+"&ujbtid="+ejbtid+"&uareaid="+eareaid+"&uidzona="+eidzona,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                        }
                        if (eact=="hapus" && data.length <= 1) {
                            document.getElementById(aharikerja).value="";
                            document.getElementById(aketerangan).value="";
                            
                            document.getElementById(asakit).value="";
                            document.getElementById(aizin).value="";
                            document.getElementById(aalpa).value="";
                            
                            document.getElementById(auc).value="";
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function SimpanDataAreaPenempatan(eact, aidspg, acabang, aarea, apenempatan, abulan, areapilih)  {
        var espg =document.getElementById(aidspg).value;
        var ecabang =document.getElementById(acabang).value;
        var earea =document.getElementById(aarea).value;
        var epenempatan =document.getElementById(apenempatan).value;
        var ebulan =document.getElementById(abulan).value;
        
        if (espg==""){
            alert("id kosong....");
            return 0;
        }
        if (ecabang==""){
            alert("cabang kosong....");
            return 0;
        }
        if (earea==""){
            alert("area kosong....");
            return 0;
        }
        
        document.getElementById(areapilih).value=earea;
        
        //alert(espg+", "+earea+", "+ecabang+", "+epenempatan); return 0;
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/md_m_spg_harikerja/aksi_spgtempat.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidspg="+espg+"&ucabang="+ecabang+"&uarea="+earea+"&upenempatan="+epenempatan+"&ubulan="+ebulan,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
    }
</script>