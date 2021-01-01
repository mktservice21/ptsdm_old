<div class='tbldata'>
    <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
        <thead>
            <tr><th width='5%px'>No</th>
            <th width='30%' >Akun</th>
            <th width='4%' align="right">Jumlah / Hari</th>
            <th width='10%' align="right">Nilai (Rp.)</th>
            <th width='15%' align="right">Total (Rp.)</th>
            <th width='40%' align="right">Note</th>
            </tr>
        </thead>
        <tbody class='inputdatauc'>
        <?PHP
        $no=1;
        $tampil = mysqli_query($cnmy, "SELECT nobrid, nama, jumlah, qty, groupid FROM dbmaster.t_brid where kode=1 and aktif='Y' order by nobrid");
        while ($uc=mysqli_fetch_array($tampil)){
            $ada=0;
            $tjml=1;
            if (!empty($uc['jumlah'])) $tjml=$uc['jumlah'];
            $tgpid=$uc['groupid'];
            
            if ($_GET['act']=="editdata"){
                $cari = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin1 where idrutin='$_GET[id]' and nobrid='$uc[nobrid]'");
                $ada = mysqli_num_rows($cari);
            }

            if ($ada>0) {
                $xx=0;
                while ($c=mysqli_fetch_array($cari)){
                    $qtyhide="hidden";
                    $qtyreadony="";
                    $qtyjml=1;
                    if (!empty($uc['qty']) AND $uc['qty']==1) { $qtyhide="text"; $qtyjml=""; $qtyreadony="Readonly"; }
                    $uqyu="";$unilai="";$unote="";$unote="";$total="";
                    $nmidbl="e_idbl".$no; $nmnama="e_blnama".$no; $nmqty="e_qty".$no; $nmnilai="e_nilai".$no;
                    $nmtotal="e_total".$no; $nmnote="e_note".$no;

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


                    $upkmdetail=$c['km'];
                    $nmkmdetail="e_kmdetail".$no;
                    $kmdetailisi="";
                    if ((int)$tgpid==1) {
                        $kmdetailisi="KM : <input type='text' class='input-sm inputmaskrp2' name='$nmkmdetail' id='$nmkmdetail' size='10px' value='$upkmdetail'>";
                    }
                    
                    $kmdetailisi="<div id='ikedaraan'>$kmdetailisi</div";
                    
                    $uptglkuitansi=$c['tgl1'];
                    $nmisitgl1="e_1isitgl".$no;
                    $tglisi="";
                    //if ((int)$tgpid==10 OR (int)$tgpid==11 OR (int)$tgpid==16 OR (int)$tgpid==17 OR (int)$tgpid==18) {
                    if ((int)$tgpid==18) {
                        $tglisi="Tgl. Kuitansi :<br/><input type='date' class='input-xs' name='$nmisitgl1' id='$nmisitgl1' value='$uptglkuitansi'>";
                    }

                    $pisidetail=$tglisi;
                    if ((int)$tgpid==1)$pisidetail=$kmdetailisi;
                    
                    $upkes_kel=$c['obat_untuk'];
                    $nmisicmb="cb_isi".$no;
                    $cmbisi="";
                    if ((int)$tgpid==11) {
                        $seltkes1="selected";
                        $seltkes2="";
                        if ($upkes_kel=="2"){
                            $seltkes1="";
                            $seltkes2="selected";
                        }
                        $cmbisi="<select class='input-sm' id='$nmisicmb' name='$nmisicmb'><option value=1 $seltkes1>Istri</option><option value=2 $seltkes2>Anak</option></select>";
                    }
                    
                    echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'></div>";
                    echo "<tr scope='row'><td>$no</td>";
                    echo "<td>$uc[nama]<input type='hidden' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'></td>";
                    echo "<td align='right'>$pisidetail<input type='$qtyhide' size='5px' id='$nmqty' name='$nmqty' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$uqyu'></td>";
                    echo "<td>$cmbisi<input type='$qtyhide' size='10px' id='$nmnilai' name='$nmnilai' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$unilai'></td>";
                    echo "<td><input type='text' size='10px' id='$nmtotal' name='$nmtotal' onblur='findTotal()' class='input-sm inputmaskrp2' autocomplete='off' value='$total' $qtyreadony></td>";
                    echo "<td><input type='text' size='35px' id='$nmnote' name='$nmnote' class='input-sm' autocomplete='off' value='$unote'></td>";
                    $no++;
                    $xx++;
                }
                $tjml=(int)$tjml-(int)$xx;
            }

            for ($i=1; $i <=$tjml; $i++) {

                $qtyhide="hidden";
                $qtyreadony="";
                $qtyjml=1;
                if (!empty($uc['qty']) AND $uc['qty']==1) { $qtyhide="text"; $qtyjml=""; $qtyreadony="Readonly"; }

                $uqyu="";$unilai="";$unote="";$unote="";$total="";
                $nmidbl="e_idbl".$no; $nmnama="e_blnama".$no; $nmqty="e_qty".$no; $nmnilai="e_nilai".$no;
                $nmtotal="e_total".$no; $nmnote="e_note".$no;
                $uqyu=$qtyjml;

                
                
                $nmkmdetail="e_kmdetail".$no;
                $kmdetailisi="";
                if ((int)$tgpid==1) {
                    $kmdetailisi="KM : <input type='text' class='input-sm inputmaskrp2' name='$nmkmdetail' id='$nmkmdetail' size='10px' value=''>";
                }
                $kmdetailisi="<div id='ikedaraan'>$kmdetailisi</div";
                
                $nmisitgl1="e_1isitgl".$no;
                $tglisi="";
                //if ((int)$tgpid==10 OR (int)$tgpid==11 OR (int)$tgpid==16 OR (int)$tgpid==17 OR (int)$tgpid==18) {
                if ((int)$tgpid==18) {
                    $tglisi="Tgl. Kuitansi :<br/><input type='date' class='input-xs' name='$nmisitgl1' id='$nmisitgl1' value=''>";
                }
                
                $pisidetail=$tglisi;
                if ((int)$tgpid==1)$pisidetail=$kmdetailisi;
                
                $nmisicmb="cb_isi".$no;
                $cmbisi="";
                if ((int)$tgpid==11) {
                    $cmbisi="<select class='input-sm' id='$nmisicmb' name='$nmisicmb'><option value=1 selected>Istri</option><option value=2>Anak</option></select>";
                }
                
                
                //$coainput="<input type='text' size='5px' id='i_coa$no' name='$nmqty' value=''>";

                echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'></div>";
                echo "<tr scope='row'><td>$no</td>";
                echo "<td>$uc[nama] <input type='hidden' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'></td>";
                echo "<td align='right'>$pisidetail<input type='$qtyhide' size='5px' id='$nmqty' name='$nmqty' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$uqyu'></td>";
                echo "<td>$cmbisi<input type='$qtyhide' size='10px' id='$nmnilai' name='$nmnilai' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$unilai'></td>";
                echo "<td><input type='text' size='10px' id='$nmtotal' name='$nmtotal' onblur='findTotal()' class='input-sm inputmaskrp2' autocomplete='off' value='$total' $qtyreadony></td>";
                echo "<td><input type='text' size='35px' id='$nmnote' name='$nmnote' class='input-sm' autocomplete='off' value='$unote'></td>";
                $no++;

            }

        }
        echo "<input type='hidden' name='num_records' id='num_records' value='$no'>";
        ?>
        <?PHP
        /*
        $no=1;
        $tampil = mysqli_query($cnmy, "SELECT nobrid, nama, jumlah, qty FROM dbmaster.t_brid where kode=1 and aktif='Y' order by nobrid");
        while ($uc=mysqli_fetch_array($tampil)){
            $qtyhide="hidden";
            $qtyreadony="";
            $qtyjml=1;
            if (!empty($uc['qty']) AND $uc['qty']==1) { $qtyhide="text"; $qtyjml=""; $qtyreadony="Readonly"; }

            $uqyu="";
            $unilai="";
            $unote="";
            $unote="";
            $total="";
            $nmidbl="e_idbl".$no;//$uc['nobrid'];
            $nmnama="e_blnama".$no;//$uc['nobrid'];
            $nmqty="e_qty".$no;//$uc['nobrid'];
            $nmnilai="e_nilai".$no;//$uc['nobrid'];
            $nmtotal="e_total".$no;//$uc['nobrid'];
            $nmnote="e_note".$no;//$uc['nobrid'];

            if ($_GET['act']=="editdata"){
                $uqyu=  getfield("select qty as lcfields from dbmaster.t_brrutin1 where idrutin='$_GET[id]' and nobrid='$uc[nobrid]'");
                $unilai=  getfield("select rp as lcfields from dbmaster.t_brrutin1 where idrutin='$_GET[id]' and nobrid='$uc[nobrid]'");
                $unote=  getfield("select notes as lcfields from dbmaster.t_brrutin1 where idrutin='$_GET[id]' and nobrid='$uc[nobrid]'");
                if (!empty($uc['qty']) AND $uc['qty']==1){
                    $total=floatval($uqyu)*floatval($unilai);
                }else{
                    $total=  getfield("select rptotal as lcfields from dbmaster.t_brrutin1 where idrutin='$_GET[id]' and nobrid='$uc[nobrid]'");
                    $unilai="";
                    $uqyu=$qtyjml;
                }
            }else{
                $uqyu=$qtyjml;
            }
            echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'></div>";
            echo "<tr scope='row'><td>$no</td>";
            echo "<td>$uc[nama]<input type='hidden' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'></td>";
            echo "<td><input type='$qtyhide' size='5px' id='$nmqty' name='$nmqty' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$uqyu'></td>";
            echo "<td><input type='$qtyhide' size='10px' id='$nmnilai' name='$nmnilai' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$unilai'></td>";
            echo "<td><input type='text' size='10px' id='$nmtotal' name='$nmtotal' class='input-sm inputmaskrp2' autocomplete='off' value='$total' $qtyreadony></td>";
            echo "<td><input type='text' size='35px' id='$nmnote' name='$nmnote' class='input-sm' autocomplete='off' value='$unote'></td>";
            $no++;
        }
        echo "<input type='hidden' name='num_records' id='num_records' value='$no'>";
         * 
         */
        
        ?>
        </tbody>
        </table>

</div>