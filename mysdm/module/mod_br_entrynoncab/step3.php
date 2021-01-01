<!--bawah-->
<div class='col-md-12 col-xs-12'>
    <div class='x_panel'>
        <div class='x_content form-horizontal form-label-left'>
            <div class='tbldata'>
                <table id='datatableuc' class='table table-striped table-bordered' width="50%">
                    <thead>
                        <tr><th width='5%px'>No</th>
                        <th>Akun</th>
                        <th width='17%' align="right">Rp. (Limit)</th>
                        <th width='10%' align="right">Jumlah/Hari</th>
                        <th width='15%' align="right">Total</th>
                        <th width='40%' align="right">Note</th>
                        </tr>
                    </thead>
                    <tbody class='inputdatauc'>
                    <?PHP
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT NOBUD, NAMA_BUD, FORMAT(RP,2,'de_DE') as RP FROM dbbudget.br_uc_budget order by NOBUD");
                    while ($uc=mysqli_fetch_array($tampil)){
                        $jmhr="";
                        $total="";
                        $note="";
                        if ($_GET['act']=="editdata"){
                            $jmhr=  getfield("select JML as lcfields from dbbudget.t_br_u where NOID='$_GET[id]' and NOBUD='$uc[NOBUD]'");
                            $total=  getfield("select TOTAL as lcfields from dbbudget.t_br_u where NOID='$_GET[id]' and NOBUD='$uc[NOBUD]'");
                            $note=  getfield("select KET as lcfields from dbbudget.t_br_u where NOID='$_GET[id]' and NOBUD='$uc[NOBUD]'");
                        }
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td>$uc[NAMA_BUD]</td>";
                        echo "<td align='right'>$uc[RP]</td>";
                        echo "<td><input type='text' id='e_hr$uc[NOBUD]' name='e_hr$uc[NOBUD]' class='form-control input-sm inputmaskrp2' autocomplete='off' value='$jmhr'></td>";
                        echo "<td><input type='text' id='e_rphr$uc[NOBUD]' name='e_rphr$uc[NOBUD]' class='form-control input-sm inputmaskrp2' autocomplete='off' value='$total'></td>";
                        echo "<td><input type='text' id='e_note$uc[NOBUD]' name='e_note$uc[NOBUD]' class='form-control input-sm' autocomplete='off' value='$note'></td>";
                        $no++;
                    }
                    ?>
                    </tbody>
                    </table>
                
            </div>


        </div>
    </div>
</div>