<?PHP 
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    ini_set('display_errors', '0');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
?>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<?PHP
    $hari_ini = date("Y-m-d");
    
    $pperiodepilih= date("d F Y", strtotime($hari_ini));
    
    
    include "../../config/koneksimysqli.php";
    
    $userid=$_SESSION['USERID'];
    
    $ptipe=$_POST['utipe'];
    $pbln=$_POST['ubulan'];
    
    $_SESSION['SBPJSINPTIPE']=$ptipe;
    $_SESSION['SBPJSINPBLN01']=$pbln;
    
    $pbulan= date("Ym", strtotime($pbln));
    $pbulanpilih= date("F Y", strtotime($pbln));
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBPJSA01_".$userid."_$now ";
    $tmp02 =" dbtemp.DTBPJSA02_".$userid."_$now ";
    $tmp03 =" dbtemp.DTBPJSA03_".$userid."_$now ";
    
    
    $query ="select * from dbmaster.t_spd_bpjs WHERE periode='$pbulan'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $psudahada=false;
    $query = "select * from $tmp02";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $psudahada=true;
        if (!empty($_SESSION['SBPJSINPTGLAJU'])) $pperiodepilih = $_SESSION['SBPJSINPTGLAJU'];
    }
    
    
    
    $query ="select karyawanid, nama, CAST('A' as CHAR(1)) as istatus, CAST('T' as CHAR(1)) as istatus2, CAST(0 as DECIMAL(20,2)) as ngp, "
            . " CAST(0 as DECIMAL(20,2)) as potongan_pt, CAST(0 as DECIMAL(20,2)) as potongan_kry, "
            . " CAST(0 as DECIMAL(20,2)) as bayar, CAST(0 as DECIMAL(5)) as kelas "
            . " from hrd.karyawan where 1=1 ";
    if ($psudahada==true AND $ptipe!="A") {
        $query .=" AND karyawanId IN (select distinct IFNULL(karyawanid,'') from $tmp02) ";
    }else{
        $query .=" AND ";
        if ($psudahada==true AND $ptipe=="A") {
            $query .=" ( ";
        }
        $query .=" aktif='Y' ";
        $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
        $query .=" AND karyawanId not in (select distinct IFNULL(karyawanId,'') from dbmaster.t_karyawanadmin) ";
        $query .=" AND karyawanId not in ('0000002200', '0000002083')";
        
        if ($psudahada==true AND $ptipe=="A") {
            $query .=" ) OR karyawanId IN (select distinct IFNULL(karyawanid,'') from $tmp02) ";
        }
        
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    if ($psudahada==false) {
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
        
        $query ="select * from dbmaster.t_spd_bpjs a WHERE a.karyawanid IN "
                . " (select distinct IFNULL(karyawanid,'') FROM $tmp01) AND "
                . " a.periode=(select max(periode) from dbmaster.t_spd_bpjs b WHERE a.karyawanid=b.karyawanid)";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
        
        $query ="select karyawanid from $tmp02 WHERE karyawanId NOT IN (select distinct karyawanid from $tmp01)";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


        $query = "INSERT INTO $tmp01 (karyawanid, nama, istatus) "
                . " select karyawanid, nama, 'T' as istatus from hrd.karyawan WHERE karyawanId IN "
                . " (select distinct IFNULL(karyawanid,'') FROM $tmp03)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    $query = "UPDATE $tmp01 a JOIN $tmp02 b on a.karyawanid=b.karyawanid SET "
            . " a.ngp=b.ngp, a.potongan_pt=b.potongan_pt, a.potongan_kry=b.potongan_kry, "
            . " a.bayar=b.bayar, a.kelas=b.kelas, a.istatus2='Y'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 SET istatus2='T' WHERE IFNULL(istatus,'')='T'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $pjmlusulan=0;
    $query = "select sum(bayar) as totbayar from $tmp01 WHERE IFNULL(istatus2,'')='Y'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nrow= mysqli_fetch_array($tampil);
        $pjmlusulan=$nrow['totbayar'];
        
        if (empty($_SESSION['SBPJSINPTGLAJU']) AND $psudahada==true) {
            $query = "select tanggal from $tmp02 WHERE IFNULL(tanggal,'')<>'' AND IFNULL(tanggal,'0000-00-00')<>'0000-00-00' LIMIT 1";
            $tampilt= mysqli_query($cnmy, $query);
            $ketemut= mysqli_num_rows($tampilt);
            if ($ketemut>0) {
                $trow= mysqli_fetch_array($tampilt);
                $ntglaju=$trow['tanggal'];
                if (!empty($ntglaju) AND $ntglaju<>"0000-00-00") {
                    $pperiodepilih= date("d F Y", strtotime($ntglaju));
                }
                
            }
        }
    }
    
    
?>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatablebpjsspdx').DataTable( {
            "processing": true,
            //"stateSave": true,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [4,6,7,8] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6,7,8] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
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
        HitungJumlahTotalCexBox();
    }
    
    function hilangkanTanda(sText){
        var newchar = '';
        var aText=document.getElementById(sText).value;
        aText = aText.split(',').join(newchar);
        
        if (aText=="") { aText="0"; }
        
        return aText;
    }
    
    
    function HitungJumlahTotalCexBox() {
        var chk_arr1 =  document.getElementsByName('chkbox_br[]');
        var chklength1 = chk_arr1.length;
        var apilih_text="";
        var ajml_rpnya="";
        var nTotalBayar="0";
        
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');
                apilih_text="txt_ntotal["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                
                nTotalBayar =parseInt(nTotalBayar)+parseInt(ajml_rpnya);
                
                /*
                apilih_text="txt_ngp["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                
                ntotalpt=parseInt(ajml_rpnya)*4/100;
                ntotalkry=parseInt(ajml_rpnya)*1/100;
                ntotjml=parseInt(ntotalpt)+parseInt(ntotalkry);
                
                apilih_textpt="txt_npotpt["+fields[0]+"]";
                apilih_textkry="txt_npotkry["+fields[0]+"]";
                apilih_texttotal="txt_ntotal["+fields[0]+"]";
                document.getElementById(apilih_textpt).value=ntotalpt;
                document.getElementById(apilih_textkry).value=ntotalkry;
                document.getElementById(apilih_texttotal).value=ntotjml;
                */
                
            }
        }
        document.getElementById('e_jmlusulan').value=nTotalBayar;
    }
    
    function HitungJumlahPersentase(agp, atotpt, atotkry, atotal) {
        var egp =  document.getElementById(agp).value;
        var newchar = '';
        
        var ajml_rpnya = egp.split(',').join(newchar);
        ntotalpt=parseInt(ajml_rpnya)*4/100;
        ntotalkry=parseInt(ajml_rpnya)*1/100;
        ntotjml=parseInt(ntotalpt)+parseInt(ntotalkry);
        
        
        document.getElementById(atotpt).value=ntotalpt;
        document.getElementById(atotkry).value=ntotalkry;
        document.getElementById(atotal).value=ntotjml;
        
        HitungJumlahTotalCexBox();
    }
    
    $(function() {
        $('#e_tglberlaku').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
            } 
        });
    });
    
    function disp_confirm(ket, cekbr){
        if (ket=="simpan") {
            var ijml =document.getElementById('e_jmlusulan').value;
            if(ijml==""){
                ijml="0";
            }
            if (ijml=="0") {
                alert("jumlah masih kosong...");
                return false;
            }
        }
        
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");

        //document.write("You pressed OK!")
        document.getElementById("d-form2").action = "module/mod_br_spdbpjs/aksi_spdbpjs.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket;
        document.getElementById("d-form2").submit();
        return 1;
        
    }
</script>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                  
                        <div hidden class='col-sm-3'>
                            <button type='button' class='btn btn-default btn-xs'>Bulan</button> <span class='required'></span>
                           <div class="form-group">
                                <div class='input-group date' id=''>
                                    <input type="text" class="form-control" id='e_tglbulan' name='e_tglbulan' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $pbulanpilih; ?>' Readonly>
                                    <span class='input-group-addon'>
                                        <span class='glyphicon glyphicon-calendar'></span>
                                    </span>
                                </div>
                           </div>
                       </div>
                  
                        <div class='col-sm-3'>
                            <button type='button' class='btn btn-default btn-xs'>Tgl. Pengajuan</button> <span class='required'></span>
                           <div class="form-group">
                                <div class='input-group date' id='mytgl02x'>
                                    <input type="text" class="form-control" id='e_tglberlaku' name='e_tanggal' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $pperiodepilih; ?>'>
                                    <span class='input-group-addon'>
                                        <span class='glyphicon glyphicon-calendar'></span>
                                    </span>
                                </div>
                           </div>
                       </div>
                    
                    
                        <div class='col-sm-3'>
                            <button type='button' class='btn btn-info btn-xs' onclick='HitungTotalDariCekBox()'>Jumlah</button> <span class='required'></span>
                           <div class="form-group">
                                <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlusulan; ?>' Readonly>
                           </div>
                       </div>
                    
                        <div class='col-sm-3'>
                            <p>&nbsp;</p>
                           <div class="form-group">
                               <input type='button' class='btn btn-dark btn-sm' id="s-submit" value="Save" onclick='disp_confirm("simpan", "chkbox_br[]")'>
                               <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Hapus" onclick='disp_confirm("hapus", "chkbox_br[]")'>
                           </div>
                       </div>
                    
                    
                </div>
            </div>
        
        <?PHP
        $pchkall = "<input type='checkbox' id='chkbtnbr' name='chkbtnbr' value='select' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\"/>";
        ?>
        <table id='datatablebpjsspd' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'><?PHP echo $pchkall; ?></th>
                    <th width='50px'>Karyawan ID</th>
                    <th width='50px'>Nama</th>
                    <th width='50px'>Gaji Pokok</th>
                    <th width='10px'>Kelas</th>
                    <th width='40px'>Potongan <br/>Perusahaan(4%)</th>
                    <th width='40px'>Potongan <br/>Karyawan(1%)</th>
                    <th width='40px'>Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $pgp=0;
                $ppotpt=0;
                $ppotkry=0;
                $pjmltotal=0;
                $no=1;
                $query = "select * from $tmp01 order by nama";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pkaryawanid=$row['karyawanid'];
                    $pkaryawannm=$row['nama'];
                    $pistatus=$row['istatus2'];
                    
                    $pkeals=$row['kelas'];
                    $pgp=$row['ngp'];
                    $ppotpt=$row['potongan_pt'];
                    $ppotkry=$row['potongan_kry'];
                    $pjmltotal=$row['bayar'];
                    
                    $nstyle_text=" style='text-align:right; background-color: transparent; border: 0px solid;' ";
                    $pnreadonly=" Readonly ";
                    
                    $chkck="";
                    $pkelas="";
                    
                    $pcb_kelas="";
                    for ($xi=1;$xi<=2;$xi++) {
                        if ($pkeals==$xi)
                            $pcb_kelas .="<option value='$xi' selected>$xi</option>";
                        else
                            $pcb_kelas .="<option value='$xi'>$xi</option>";
                    }
                    
                    $ppilih_cbkelas="<select id='txt_kelas[$pkaryawanid]' name='txt_kelas[$pkaryawanid]'>$pcb_kelas</select>";
                    
                    $ptxt_jmlgp="<input style='text-align:right;' type='text' value='$pgp' id='txt_ngp[$pkaryawanid]' name='txt_ngp[$pkaryawanid]' class='inputmaskrp2 inputbaya' size='8px' onblur=\"HitungJumlahPersentase('txt_ngp[$pkaryawanid]', 'txt_npotpt[$pkaryawanid]', 'txt_npotkry[$pkaryawanid]', 'txt_ntotal[$pkaryawanid]')\">";
                    $ptxt_jmlpotpt="<input type='text' value='$ppotpt' id='txt_npotpt[$pkaryawanid]' name='txt_npotpt[$pkaryawanid]' class='inputmaskrp2 inputbaya' size='8px' $pnreadonly $nstyle_text>";
                    $ptxt_jmlpotkry="<input type='text' value='$ppotkry' id='txt_npotkry[$pkaryawanid]' name='txt_npotkry[$pkaryawanid]' class='inputmaskrp2 inputbaya' size='8px' $pnreadonly $nstyle_text>";
                    $ptxt_jmltotal="<input type='text' value='$pjmltotal' id='txt_ntotal[$pkaryawanid]' name='txt_ntotal[$pkaryawanid]' class='inputmaskrp2 inputbaya' size='8px' Readonly $nstyle_text>";
                    
                    if ($pistatus=="Y") $chkck= "checked";
                    $ceklisnya = "<input type='checkbox' value='$pkaryawanid' onclick=\"HitungJumlahPersentase('txt_ngp[$pkaryawanid]', 'txt_npotpt[$pkaryawanid]', 'txt_npotkry[$pkaryawanid]', 'txt_ntotal[$pkaryawanid]')\" name='chkbox_br[]' id='chkbox_br[$pkaryawanid]' class='cekbr' $chkck>";
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ceklisnya</td>";
                    echo "<td nowrap>$pkaryawanid</td>";
                    echo "<td nowrap>$pkaryawannm</td>";
                    echo "<td nowrap>$ptxt_jmlgp</td>";
                    echo "<td nowrap>$ppilih_cbkelas</td>";
                    echo "<td nowrap>$ptxt_jmlpotpt</td>";
                    echo "<td nowrap>$ptxt_jmlpotkry</td>";
                    echo "<td nowrap>$ptxt_jmltotal</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                
                ?>
            </tbody>
        </table>

    </div>
    
</form>


<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>


<style>
    .divnone {
        display: none;
    }
    #datatablebpjsspd th {
        font-size: 13px;
    }
    #datatablebpjsspd td { 
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

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>