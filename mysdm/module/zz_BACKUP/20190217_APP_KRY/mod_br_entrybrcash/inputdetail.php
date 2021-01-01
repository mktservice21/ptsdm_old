<div class='col-md-12 col-xs-12'>
    <div class='x_panel'>
        <div class='x_content form-horizontal form-label-left'>
            <div class='tbldata'>
                <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                    <thead>
                        <tr><th width='5%px'>No</th>
                        <th width='30%' >Akun</th>
                        <th width='4%' align="right">Tanggal</th>
                        <th width='10%' align="right">s.d</th>
                        <th width='15%' align="right">Total (Rp.)</th>
                        <th width='40%' align="right">Kota / Note</th>
                        </tr>
                    </thead>
                    <tbody class='inputdatauc'>
                    <?PHP
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT nobrid, nama, jumlah, qty FROM dbmaster.t_brid where kode=3 and aktif='Y' order by nobrid");
                    while ($uc=mysqli_fetch_array($tampil)){
                        $ada=0;
                        $tjml=1;
                        if (!empty($uc['jumlah'])) $tjml=$uc['jumlah'];
                        
                        if ($_GET['act']=="editdata"){
                            $cari = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_ca1 where idca='$_GET[id]' and nobrid='$uc[nobrid]'");
                            $ada = mysqli_num_rows($cari);
                        }
                        
                        if ($ada>0) {
                            $xx=0;
                            while ($c=mysqli_fetch_array($cari)){
                                $qtyhide="hidden";
                                $qtyreadony="";
                                $qtyjml=1;
                                if (!empty($uc['qty']) AND $uc['qty']==1) { $qtyhide="date"; $qtyjml=""; $qtyreadony=""; }
                                $uqyu="";$unilai="";$unote="";$unote="";$total="";
                                $nmidbl="e_idbl".$no; $nmnama="e_blnama".$no; $nmqty="e_qty".$no; $nmnilai="e_nilai".$no;
                                $nmtotal="e_total".$no; $nmnote="e_note".$no;
                                
                                $uqyu = date('Y-m-d', strtotime($c['tgl1']));
                                $unilai = date('Y-m-d', strtotime($c['tgl2']));
                                
                                if (empty($c['tgl1']) OR $c['tgl1']=="0000-00-00") $uqyu="";
                                if (empty($c['tgl2']) OR $c['tgl2']=="0000-00-00") $unilai="";
                                
                                //$uqyu=$c['tgl1'];
                                //$unilai=$c['tgl2'];
                                $unote=$c['notes'];
                                $total=  $c['rptotal'];
                                
                                echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'></div>";
                                echo "<tr scope='row'><td>$no</td>";
                                echo "<td>$uc[nama]<input type='hidden' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'></td>";
                                echo "<td><input type='$qtyhide' size='15px' id='$nmqty' name='$nmqty' class='input-sm' autocomplete='off'  value='$uqyu'></td>";
                                echo "<td><input type='$qtyhide' size='15px' id='$nmnilai' name='$nmnilai' class='input-sm' autocomplete='off'  value='$unilai'></td>";
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
                            $qtyjml="";
                            if (!empty($uc['qty']) AND $uc['qty']==1) { $qtyhide="date"; $qtyjml=""; $qtyreadony=""; }

                            $uqyu="";$unilai="";$unote="";$unote="";$total="";
                            $nmidbl="e_idbl".$no; $nmnama="e_blnama".$no; $nmqty="e_qty".$no; $nmnilai="e_nilai".$no;
                            $nmtotal="e_total".$no; $nmnote="e_note".$no;
                            $uqyu=$qtyjml;
                            
                            
                            echo "<div hidden><input type='text' name='$nmidbl' id='$nmidbl' value='$uc[nobrid]'></div>";
                            echo "<tr scope='row'><td>$no</td>";
                            echo "<td>$uc[nama] <input type='hidden' id='$nmnama' name='$nmnama' class='input-sm' autocomplete='off' value='$uc[nama]'></td>";
                            echo "<td><input type='$qtyhide' size='15px' id='$nmqty' name='$nmqty' class='input-sm' autocomplete='off' value='$uqyu'></td>";
                            echo "<td><input type='$qtyhide' size='15px' id='$nmnilai' name='$nmnilai' class='input-sm' autocomplete='off' value='$unilai'></td>";
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


        </div>
    </div>
</div>