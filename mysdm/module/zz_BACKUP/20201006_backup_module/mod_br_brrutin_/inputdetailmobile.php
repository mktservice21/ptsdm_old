<?PHP
$nourutid = "";
$noidurut = "";
$no=1;
$tampil = mysqli_query($cnmy, "SELECT nobrid, nama, jumlah, qty, groupid FROM dbmaster.t_brid where kode=1 and aktif='Y' order by nobrid");
while ($uc=mysqli_fetch_array($tampil)){
    $ada=0;
    $tjml=1;
    if (!empty($uc['jumlah'])) $tjml=$uc['jumlah'];
    $tgpid=$uc['groupid'];
    $namaakun=$uc['nama'];
    
    for ($i=1; $i <=$tjml; $i++) {
        $nakunnya=$namaakun;
        if ($tjml>1) $nakunnya =$nakunnya." #".$i;
        
        $qtyhide="hidden";
        $qtyreadony="";
        $qtyjml=1;
        if (!empty($uc['qty']) AND $uc['qty']==1) { $qtyhide="text"; $qtyjml=""; $qtyreadony="Readonly"; }
        
        $nmisitgl1="e_1isitgl".$no;
        $nmisitgl2="e_1isitgl2".$no;
        $nmisitgl3="e_2isitgl".$no;
        $tglisi="";
        $tglisi1="";
        $tglisi2="";
                                                
        $uqyu="";$unilai="";$unote="";$unote="";$total="";
        $nmidbl="e_idbl".$no; $nmnama="e_blnama".$no; $nmqty="e_qty".$no; $nmnilai="e_nilai".$no;
        $nmtotal="e_total".$no; $nmnote="e_note".$no;
        $uqyu=$qtyjml;
        
        
        $upkmdetail="";
        $uptglkuitansi="";
        $uptglkuitansi2="";
        $upkes_kel="";
        
        if ($_GET['act']=="editdata"){
            if (!empty($nourutid)) {
                $noidurut = "AND nourut not in ($nourutid)";
            }
            $cari = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin1 where idrutin='$_GET[id]' and nobrid='$uc[nobrid]' $noidurut order by nourut");
            $ada = mysqli_num_rows($cari);
            if ($ada>0) {
                $c = mysqli_fetch_array($cari);
                $enour=$c['nourut'];
                $uqyu=$c['qty'];
                $unilai=$c['rp'];
                $unote=$c['notes'];
                if (!empty($uc['qty']) AND $uc['qty']==1){
                    $total=floatval($uqyu)*floatval($unilai);
                }else{
                    $total=  $c['rptotal'];
                    $unilai="";
                    $uqyu=$qtyjml;
                }

                $tglisi1="";
                $tglisi2="";
                if (!empty($c['tgl1']) AND $c['tgl1'] <> "0000-00-00")
                    $tglisi1 =  date("Y-m-d", strtotime($c['tgl1']));

                if (!empty($c['tgl2']) AND $c['tgl2'] <> "0000-00-00")
                    $tglisi2 =  date("Y-m-d", strtotime($c['tgl2']));
                
                
                //
                $upkmdetail=$c['km'];
                $uptglkuitansi=$c['tgl1'];
                $uptglkuitansi2=$c['tgl2'];
                $upkes_kel=$c['obat_untuk'];
                //
                
                if (!empty($nourutid))
                    $nourutid = $nourutid.",".$enour;
                else
                    $nourutid = $enour;
            }
        }
        
        echo "<div>";
        echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'>"
                . "<input type='text' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'>"
                . "</div>";
        echo "<b>$nakunnya</b>";
        echo "<table id='tabelnobr' class='datatable table nowrap table-striped table-bordered' width='100%'>";
        if ($tgpid==21) {
            $tglisi="<input type='date' class='input-sm' name='$nmisitgl1' id='$nmisitgl1' size='10px' value='$tglisi1'> s/d. "
                    . "<input type='date' class='input-sm' name='$nmisitgl3' id='$nmisitgl3' size='10px' value='$tglisi2'>";
            echo "<tr><td nowrap>Periode</td><td nowrap>$tglisi</td></tr>";
        }
        
                if ((int)$tgpid==1) {
                    $nmkmdetail="e_kmdetail".$no;
                    $kmdetailisi="<input type='text' class='input-sm inputmaskrp2' name='$nmkmdetail' id='$nmkmdetail' size='10px' value='$upkmdetail'>";
                    echo "<tr id='ikedaraan'><td nowrap>KM</td><td nowrap>$kmdetailisi</td></tr>";
                }



                //if ((int)$tgpid==10 OR (int)$tgpid==11 OR (int)$tgpid==16 OR (int)$tgpid==17 OR (int)$tgpid==18) {
                if ((int)$tgpid==18) {
                    $nmisitgl1="e_1isitgl".$no;
                    $tglisi="<input type='date' class='input-xs' name='$nmisitgl1' id='$nmisitgl1' size='10px' value='$uptglkuitansi'>";
                    echo "<tr><td nowrap>Tgl. Kuitansi</td><td nowrap>$tglisi</td></tr>";
                }
                
        
        
                if ((int)$tgpid==11) {
                    $nmisicmb="cb_isi".$no;
                    
                    $seltkes1="selected";
                    $seltkes2="";
                    if ($upkes_kel=="2"){
                        $seltkes1="";
                        $seltkes2="selected";
                    }
                    $cmbisi="<select class='input-sm' id='$nmisicmb' name='$nmisicmb'><option value=1 $seltkes1>Istri</option><option value=2 $seltkes2>Anak</option></select>";
                    echo "<tr><td nowrap>Untuk</td><td nowrap>$cmbisi</td></tr>";
                }
                    
                    
                if ((int)$tgpid==19) {
                    $nmisitgl1="e_1isitgl".$no;
                    $nmisitgl2="e_1isitgl2".$no;
                    $tglisi="<input type='date' class='input-xs' name='$nmisitgl1' id='$nmisitgl1' size='10px' value='$uptglkuitansi'>";
                    $tglisi2_="<input type='date' class='input-xs' name='$nmisitgl2' id='$nmisitgl2' size='10px' value='$uptglkuitansi2'>";
                    echo "<tr><td nowrap>Tgl.</td><td nowrap>$tglisi s/d. $tglisi2_</td></tr>";
                    
                    $nmisicmb="cb_isi".$no;
                    
                    $seltkes0="selected";
                    $seltkes1="";
                    $seltkes2="";
                    if ($upkes_kel=="1"){
                        $seltkes0="";
                        $seltkes1="selected";
                        $seltkes2="";
                    }elseif ($upkes_kel=="2"){
                        $seltkes0="";
                        $seltkes1="";
                        $seltkes2="selected";
                    }
                    $cmbisi="<select class='input-sm' id='$nmisicmb' name='$nmisicmb'><option value='' $seltkes1>Karyawan</option><option value=1 $seltkes1>Istri</option><option value=2 $seltkes2>Anak</option></select>";
                    echo "<tr><td nowrap>Untuk</td><td nowrap>$cmbisi</td></tr>";
                    
                }
                
                
        $inputqty = "<input type='text' size='5px' id='e_qty$no' name='e_qty$no' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$uqyu'>";
        $inputnilai = "<input type='$qtyhide' size='10px' id='$nmnilai' name='$nmnilai' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$unilai'>";
        if ($qtyhide!="hidden") {
            echo "<tr><td nowrap>Jumlah / Hari</td><td>$inputqty</td></tr>";
            echo "<tr><td nowrap>Nilai (Rp.)</td><td>$inputnilai</td></tr>";
        }else{
            echo "<div hidden>$inputqty &nbsp; $inputnilai</div>";
        }
        $inputtotal = "<input type='text' size='10px' id='$nmtotal' name='$nmtotal' onblur='findTotal()' class='input-sm inputmaskrp2' autocomplete='off' value='$total' $qtyreadony>";
        $inputnote = "<input type='text' size='35px' id='$nmnote' name='$nmnote' class='input-sm' autocomplete='off' value='$unote'>";
        echo "<tr><td nowrap>Total (Rp.)</td><td>$inputtotal</td></tr>";
        echo "<tr><td nowrap>Note</td><td>$inputnote</td></tr>";
        
        echo "</table>";
        echo "</div>";
        
        $no++;
    }
}
echo "<input type='hidden' name='num_records' id='num_records' value='$no'>";
?>