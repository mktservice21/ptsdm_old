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
        $tampil = mysqli_query($cnmy, "SELECT nobrid, nama, jumlah, qty, groupid FROM dbmaster.t_brid where kode=2 and aktif='Y' order by nobrid");
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

                    $tglisi1="";
                    $tglisi2="";
                    if (!empty($c['tgl1']) AND $c['tgl1'] <> "0000-00-00")
                        $tglisi1 =  date("Y-m-d", strtotime($c['tgl1']));

                    if (!empty($c['tgl2']) AND $c['tgl2'] <> "0000-00-00")
                        $tglisi2 =  date("Y-m-d", strtotime($c['tgl2']));

                    $nmisitgl1="e_1isitgl".$no;
                    $nmisitgl2="e_2isitgl".$no;
                    $tglisi="";
                    if ($tgpid==21) {
                        $tglisi="<input type='date' class='input-sm' name='$nmisitgl1' id='$nmisitgl1' size='10px' value='$tglisi1'> s/d. "
                                . "<input type='date' class='input-sm' name='$nmisitgl2' id='$nmisitgl2' size='10px' value='$tglisi2'>";
                    }

                    $upkmdetail=$c['km'];
                    $nmkmdetail="e_kmdetail".$no;
                    $kmdetailisi="";
                    if ((int)$tgpid==24) {
                        $kmdetailisi="&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; .... <span align='right'> <b>KM : </b><input type='text' class='input-sm inputmaskrp2' name='$nmkmdetail' id='$nmkmdetail' size='10px' value='$upkmdetail'></span>";
                        $tglisi=$kmdetailisi;
                    }
                    
                    echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'></div>";
                    echo "<tr scope='row'><td>$no</td>";
                    echo "<td>$uc[nama]<input type='hidden' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'> $tglisi</td>";
                    echo "<td><input type='$qtyhide' size='5px' id='$nmqty' name='$nmqty' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$uqyu'></td>";
                    echo "<td><input type='$qtyhide' size='10px' id='$nmnilai' name='$nmnilai' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$unilai'></td>";
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

                $nmisitgl1="e_1isitgl".$no;
                $nmisitgl2="e_2isitgl".$no;
                $tglisi="";
                if ($tgpid==21) {
                    $tglisi="<input type='date' class='input-sm' name='$nmisitgl1' id='$nmisitgl1' size='10px'> s/d. "
                            . "<input type='date' class='input-sm' name='$nmisitgl2' id='$nmisitgl2' size='10px'>";
                }

                
                $nmkmdetail="e_kmdetail".$no;
                $kmdetailisi="";
                if ((int)$tgpid==24) {
                    $kmdetailisi="&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; .... <span align='right'> <b>KM : </b><input type='text' class='input-sm inputmaskrp2' name='$nmkmdetail' id='$nmkmdetail' size='10px' value=''></span>";
                    $tglisi=$kmdetailisi;
                }

                echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'></div>";
                echo "<tr scope='row'><td>$no</td>";
                echo "<td>$uc[nama]<input type='hidden' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'> $tglisi</td>";
                echo "<td><input type='$qtyhide' size='5px' id='$nmqty' name='$nmqty' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$uqyu'></td>";
                echo "<td><input type='$qtyhide' size='10px' id='$nmnilai' name='$nmnilai' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$unilai'></td>";
                echo "<td><input type='text' size='10px' id='$nmtotal' name='$nmtotal' onblur='findTotal()' class='input-sm inputmaskrp2' autocomplete='off' value='$total' $qtyreadony></td>";
                echo "<td><input type='text' size='35px' id='$nmnote' name='$nmnote' class='input-sm' autocomplete='off' value='$unote'></td>";
                $no++;

            }

        }
        echo "<input type='hidden' name='num_records' id='num_records' value='$no'>";
        ?>
        </tbody>
        </table>

</div>