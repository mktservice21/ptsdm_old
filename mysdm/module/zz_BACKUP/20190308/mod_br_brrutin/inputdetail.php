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
        $tampil = mysqli_query($cnmy, "SELECT nobrid, nama, jumlah, qty FROM dbmaster.t_brid where kode=1 and aktif='Y' order by nobrid");
        while ($uc=mysqli_fetch_array($tampil)){
            $ada=0;
            $tjml=1;
            if (!empty($uc['jumlah'])) $tjml=$uc['jumlah'];

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

                    echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'></div>";
                    echo "<tr scope='row'><td>$no</td>";
                    echo "<td>$uc[nama]<input type='hidden' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'></td>";
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

                //$coainput="<input type='text' size='5px' id='i_coa$no' name='$nmqty' value=''>";

                echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'></div>";
                echo "<tr scope='row'><td>$no</td>";
                echo "<td>$uc[nama] <input type='hidden' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'></td>";
                echo "<td><input type='$qtyhide' size='5px' id='$nmqty' name='$nmqty' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$uqyu'></td>";
                echo "<td><input type='$qtyhide' size='10px' id='$nmnilai' name='$nmnilai' onblur=hit_total('$nmnilai','$nmqty','$nmtotal') class='input-sm inputmaskrp2' autocomplete='off' value='$unilai'></td>";
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