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

<?PHP
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpinptbrtnhodt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpinptbrtnhodt01_".$puserid."_$now ";
    
    $pabsenrutin=false;
    $query = "SELECT absen_rutin FROM dbmaster.t_karyawan_posisi WHERE karyawanId='$pkaryawanid' AND IFNULL(ho,'')='Y' AND IFNULL(absen_rutin,'')='Y'";
    $tampila=mysqli_query($cnmy, $query);
    $ketemua=mysqli_num_rows($tampila);
    
    if ((INT)$ketemua>0) $pabsenrutin=true;
    
    $query = "SELECT nobrid, nama, jumlah, qty as iqty, groupid FROM dbmaster.t_brid where kode=1 and aktif='Y' order by nobrid";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    

    $query = "ALTER TABLE $tmp00 ADD COLUMN rp_perperson DECIMAL(20,2)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp00 as a JOIN (select nobrid, rupiah from dbmaster.t_brrutin_rp_jbt WHERE jabatanid='$pjabatanid') as b 
        on a.nobrid=b.nobrid SET 
        a.rp_perperson=b.rupiah ";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    
    $query = "UPDATE $tmp00 as a JOIN (select nobrid, rupiah from dbmaster.t_brrutin_rp_person WHERE karyawanid='$pkaryawanid') as b 
        on a.nobrid=b.nobrid SET 
        a.rp_perperson=b.rupiah ";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


    $query = "select nourut, idrutin, nobrid, deskripsi, qty, rp, rptotal, notes, tgl1, tgl2, alasanedit_fin, km, obat_untuk, coa as coa_kode "
            . " from dbmaster.t_brrutin1 WHERE idrutin='$pidrutin'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "ALTER TABLE $tmp00 ADD COLUMN deskripsi varchar(15), "
                . " ADD COLUMN qty int(11), ADD COLUMN rp decimal(30,2), ADD COLUMN rptotal decimal(30,2), "
            . " ADD COLUMN notes varchar(300), ADD COLUMN tgl1 date, ADD COLUMN tgl2 date, "
            . " ADD COLUMN alasanedit_fin varchar(500), ADD COLUMN km decimal(20,2), ADD COLUMN obat_untuk varchar(1), "
            . " ADD COLUMN coa_kode VARCHAR(50)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "UPDATE $tmp00 a JOIN $tmp01 b on a.nobrid=b.nobrid SET a.deskripsi=b.deskripsi, a.qty=b.qty, a.rp=b.rp, "
            . " a.rptotal=b.rptotal, a.notes=b.notes, a.tgl1=b.tgl1, a.tgl2=b.tgl2, a.km=b.km, a.obat_untuk=b.obat_untuk, "
            . " a.alasanedit_fin=b.alasanedit_fin, a.coa_kode=b.coa_kode";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp00 a JOIN (select distinct nobrid, COA4 FROM dbmaster.posting_coa_rutin WHERE divisi='$pdivisi') as b "
            . " on a.nobrid=b.nobrid SET a.coa_kode=b.COA4 WHERE IFNULL(a.rptotal,0)=0";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    if ($pidact=="tambahbaru"){
        $query = "UPDATE $tmp00  SET rp=rp_perperson";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query = "UPDATE $tmp00 SET qty='$pjmlwfo_val' WHERE nobrid='04'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE $tmp00 SET rptotal=IFNULL(qty,0)*IFNULL(rp,0) WHERE nobrid='04'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }


    $no=1;
    $query = "select * from $tmp00 order by nobrid";
    $tampil=mysqli_query($cnmy, $query);
    while ($nrow= mysqli_fetch_array($tampil)){
        $pkodeidbr=$nrow['nobrid'];
        $pnmidbr=$nrow['nama'];
        $pkodeidcoa=$nrow['coa_kode'];
        $prptotal=$nrow['rptotal'];
        $pnotespldt=$nrow['notes'];
        $pkm=$nrow['km'];
        $pobatuntuk=$nrow['obat_untuk'];
        $ptgl01=$nrow['tgl1'];
        $ptgl02=$nrow['tgl2'];

        $pgrpid=$nrow['groupid'];
        $piqty_pil=$nrow['iqty'];
        if (empty($piqty_pil)) $piqty_pil=0;

        $prpjumlah=$nrow['qty'];
        $prpnilai=$nrow['rp'];
        
        $pnilaireadonly="";
        if ((DOUBLE)$prpnilai<>0) {
            $pnilaireadonly="Readonly";
        }

        $phiddentxt_km=" class='divnone' ";
        $phiddentgl=" class='divnone' ";
        $phiddenuntuk=" class='divnone' ";
        $phiddentxt=" class='divnone' ";
        
        $pfldtgl01="<div hidden><input type='date' class='input-xs' name='e_tglpilih01[$pkodeidbr]' id='e_tglpilih01[$pkodeidbr]' value='$ptgl01'></div>";
        $pfldtgl02="<div hidden><input type='date' class='input-xs' name='e_tglpilih02[$pkodeidbr]' id='e_tglpilih02[$pkodeidbr]' value='$ptgl02'></div>";

        $pcmbisi="<span hidden><select class='input-sm' id='cb_tkes[$pkodeidbr]' name='cb_tkes[$pkodeidbr]'>"
                . "<option value='' selected></option>"
                . "</select></span>";

        
        if ((INT)$pgrpid==1) {
            $phiddentxt_km="";
        }

        if ((int)$pgrpid==11) {
            $phiddentxt_tkes="";
            $pseltkes1="selected";
            $pseltkes2="";
            if ($pobatuntuk=="2"){
                $pseltkes1="";
                $pseltkes2="selected";
            }
            $pcmbisi="<select class='input-sm' id='cb_tkes[$pkodeidbr]' name='cb_tkes[$pkodeidbr]'>"
                    . "<option value='1' $pseltkes1>Istri</option><option value='2' $pseltkes2>Anak</option>"
                    . "</select>";
            $phiddenuntuk="";
        }elseif ((int)$pgrpid==18) {

            $pfldtgl01="Tgl. Kuitansi :<br/><input type='date' class='input-xs' name='e_tglpilih01[$pkodeidbr]' id='e_tglpilih01[$pkodeidbr]' value='$ptgl01'>";
            $phiddentgl="";
            
        }elseif ((int)$pgrpid==19) {
            $pseltkes0="selected";
            $pseltkes1="";
            $pseltkes2="";
            if ($pobatuntuk=="1"){
                $pseltkes0="";
                $pseltkes1="selected";
                $pseltkes2="";
            }elseif ($pobatuntuk=="2"){
                $pseltkes0="";
                $pseltkes1="";
                $pseltkes2="selected";
            }
            $pcmbisi="<select class='input-sm' id='cb_tkes[$pkodeidbr]' name='cb_tkes[$pkodeidbr]'>"
                    . "<option value='' $pseltkes0>Karyawan</option><option value='1' $pseltkes1>Istri</option><option value='2' $pseltkes2>Anak</option>"
                    . "</select>";
            $phiddenuntuk="";

            $pfldtgl01="<input type='date' class='input-xs' name='e_tglpilih01[$pkodeidbr]' id='e_tglpilih01[$pkodeidbr]' value='$ptgl01'>";
            $pfldtgl02="<input type='date' class='input-xs' name='e_tglpilih02[$pkodeidbr]' id='e_tglpilih02[$pkodeidbr]' value='$ptgl02'>";
            
            $phiddentgl="";

        }

        
        $prdonlytotal="";
        if ((INT)$piqty_pil<>0) {
            $phiddentxt="";
            $prdonlytotal="Readonly";
        }

        $preadonly_um="";
        if ($pkodeidbr=="04" AND $pabsenrutin==true) {
            $preadonly_um=" readonly ";
        }
                
        $pfldkilometer="<span><input type='text' size='10px' id='e_txtkm[$pkodeidbr]' name='e_txtkm[$pkodeidbr]'  class='input-sm inputmaskrp2' autocomplete='off' value='$pkm'><span>";

        $pfldjmlrp="<input type='text' size='10px' id='e_txtjmlrp[$pkodeidbr]' name='e_txtjmlrp[$pkodeidbr]' onblur=\"HitungTotalNilai('e_txtjmlrp[$pkodeidbr]', 'e_txtnilairp[$pkodeidbr]', 'e_txttotalrp[$pkodeidbr]')\" class='input-sm inputmaskrp2' autocomplete='off' value='$prpjumlah' $preadonly_um>";
        $pfldnilairp="<input type='text' size='10px' id='e_txtnilairp[$pkodeidbr]' name='e_txtnilairp[$pkodeidbr]' onblur=\"HitungTotalNilai('e_txtjmlrp[$pkodeidbr]', 'e_txtnilairp[$pkodeidbr]', 'e_txttotalrp[$pkodeidbr]')\" class='input-sm inputmaskrp2' autocomplete='off' value='$prpnilai' $pnilaireadonly>";
        $pfldtotalrp="<input type='text' size='10px' id='e_txttotalrp[$pkodeidbr]' name='e_txttotalrp[$pkodeidbr]' onblur='HitungTotalJumlahRp()' class='input-sm inputmaskrp2' autocomplete='off' value='$prptotal' $prdonlytotal>";


        $pfldnotes="<input type='text' size='50px' id='e_txtnotes[$pkodeidbr]' name='e_txtnotes[$pkodeidbr]' class='input-sm' value='$pnotespldt'>";

        $pfldcoa4="<input type='hidden' size='50px' id='e_txtcoa4[$pkodeidbr]' name='e_txtcoa4[$pkodeidbr]' class='input-sm' value='$pkodeidcoa'>";
        $chkbox = "<input type='checkbox' id='chk_kodeid[$pkodeidbr]' name='chk_kodeid[]' value='$pkodeidbr' checked>";
        
        echo "<div>";
            echo "<span hidden>$chkbox $pfldcoa4</span>";
            echo "<b>$pnmidbr</b>";
            echo "<table id='tabelnobr' class='datatable table nowrap table-striped table-bordered' width='100%'>";
            
                echo "<tr id='ikedaraan' $phiddentxt_km><td nowrap>KM</td><td nowrap>$pfldkilometer</td></tr>";
                echo "<tr id='ikedaraan' $phiddentgl><td nowrap>Tanggal</td><td nowrap>$pfldtgl01 s/d. $pfldtgl02</td></tr>";
                echo "<tr id='ikedaraan' $phiddenuntuk><td nowrap>Untuk</td><td nowrap>$pcmbisi</td></tr>";
                echo "<tr id='ikedaraan' $phiddentxt><td nowrap>Jumlah / Hari</td><td nowrap>$pfldjmlrp</td></tr>";
                echo "<tr id='ikedaraan' $phiddentxt><td nowrap>Nilai (Rp.)</td><td nowrap>$pfldnilairp</td></tr>";
                echo "<tr id='ikedaraan'><td nowrap>Total (Rp.)</td><td nowrap>$pfldtotalrp</td></tr>";
                echo "<tr id='ikedaraan'><td nowrap>Notes</td><td nowrap>$pfldnotes</td></tr>";
            
            echo "</table>";
        echo "</div>";
        
        /*
        echo "<tr>";
        echo "<td nowrap class='divnone'>$chkbox $pfldcoa4</td>";
        echo "<td nowrap>$no</td>";
        echo "<td nowrap>$pnmidbr</td>";
        echo "<td nowrap>$pfldjmlrp $pfldtgl01 $pfldtgl02 $pfldkilometer</td>";
        echo "<td nowrap>$pcmbisi $pfldnilairp</td>";
        echo "<td nowrap>$pfldtotalrp</td>";
        echo "<td nowrap>$pfldnotes</td>";
        echo "</tr>";
        */
        $no++;
    }

mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
?>
<script>
    function HitungTotalJumlahRp() {
        var chk_arr1 =  document.getElementsByName('chk_kodeid[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';

        var nTotal_="0";
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');    
                var anm_jml="e_txttotalrp["+fields[0]+"]";
                var ajml=document.getElementById(anm_jml).value;
                if (ajml=="") ajml="0";
                ajml = ajml.split(',').join(newchar);

                nTotal_ =parseFloat(nTotal_)+parseFloat(ajml);
            }
        }
        document.getElementById('e_totalsemua').value=nTotal_;
    }
    
    function HitungTotalNilai(iqty, irp, itotal) {
        var nqty=document.getElementById(iqty).value;
        var nrp=document.getElementById(irp).value;
        var newchar = '';
        var nTotal_="0";
        
        if (nqty=="") nqty="0";
        if (nrp=="") nrp="0";
        
        nqty = nqty.split(',').join(newchar);
        nrp = nrp.split(',').join(newchar);
        
        nTotal_ =parseFloat(nqty)*parseFloat(nrp);
        
        document.getElementById(itotal).value=nTotal_;
        HitungTotalJumlahRp();
        
    }
</script>

