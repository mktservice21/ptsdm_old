<?PHP
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpinptkscbdt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpinptkscbdt01_".$puserid."_$now ";
    
    $query = "select * from dbmaster.t_kode_kascab WHERE IFNULL(divisi,'') NOT IN ('OTC', 'CHC') AND IFNULL(aktif,'')<>'N' order by kode";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select * from dbmaster.t_kaskecilcabang_d WHERE idkascab='$pidkodeinput'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "ALTER TABLE $tmp00 ADD COLUMN jumlahrp DECIMAL(20,2), ADD COLUMN tglpilih date, ADD COLUMN notes VARCHAR(200)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "UPDATE $tmp00 a JOIN $tmp01 b on a.kode=b.kode SET a.jumlahrp=b.jumlahrp, a.tglpilih=b.tglpilih, a.notes=b.notes";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
?>
<div class='tbldata'>
    <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='2%px' class='divnone'></th>
                <th width='5%px'>No</th>
                <th width='30%' >Akun</th>
                <th width='5%' align="right">Jumlah Rp.</th>
                <th width='5%' class="divnone">Tanggal (bln/tgl/thn)</th>
                <th width='40%'>Note</th>
            </tr>
        </thead>
        <tbody class='inputdatauc'>
            <?PHP
            $no=1;
            $query = "select * from $tmp00 order by urutan, kode";
            $tampil=mysqli_query($cnmy, $query);
            while ($nrow= mysqli_fetch_array($tampil)){
                $pkodeidbr=$nrow['kode'];
                $pnmidbr=$nrow['nama'];
                $pkodeidcoa=$nrow['coa_kode'];
                $pjmldtrp=$nrow['jumlahrp'];
                $pnotespldt=$nrow['notes'];
                $ptglpldt=$nrow['tglpilih'];
                
                
                $pfldjmlrp="<input type='text' size='10px' id='e_txtrp[$pkodeidbr]' name='e_txtrp[$pkodeidbr]' onblur='HitungTotalJumlahRp()' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmldtrp'>";
                $pfldtgl="<input type='date' class='input-xs' name='e_tglpilih[$pkodeidbr]' id='e_tglpilih[$pkodeidbr]' value='$ptglpldt'>";
                $pfldnotes="<input type='text' size='50px' id='e_txtnotes[$pkodeidbr]' name='e_txtnotes[$pkodeidbr]' class='input-sm' value='$pnotespldt'>";
                
                $pfldcoa4="<input type='hidden' size='50px' id='e_txtcoa4[$pkodeidbr]' name='e_txtcoa4[$pkodeidbr]' class='input-sm' value='$pkodeidcoa'>";
                $chkbox = "<input type='checkbox' id='chk_kodeid[$pkodeidbr]' name='chk_kodeid[]' value='$pkodeidbr' checked>";
                
                echo "<tr>";
                echo "<td nowrap class='divnone'>$chkbox $pfldcoa4</td>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pnmidbr</td>";
                echo "<td nowrap>$pfldjmlrp</td>";
                echo "<td nowrap class='divnone'>$pfldtgl</td>";
                echo "<td nowrap>$pfldnotes</td>";
                echo "</tr>";
                
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>

<?PHP
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
                var anm_jml="e_txtrp["+fields[0]+"]";
                var ajml=document.getElementById(anm_jml).value;
                if (ajml=="") ajml="0";
                ajml = ajml.split(',').join(newchar);

                nTotal_ =parseFloat(nTotal_)+parseFloat(ajml);
            }
        }
        document.getElementById('e_jml').value=nTotal_;
        HitungSaldoAkhir();
    }
</script>