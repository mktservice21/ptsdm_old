<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    
    $pinput = $_POST['uinputid'];
    if ((INT)$pinput==1 OR (INT)$pinput==2) $_SESSION['STPDTIPE']="C";
    if ((INT)$pinput==3 OR (INT)$pinput==4) $_SESSION['STPDTIPE']="D";
    
    $_SESSION['STPDPERENTY1']=$_POST['uperiode1'];
    $_SESSION['STPDPERENTY2']=$_POST['uperiode2'];
    
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m", strtotime($date1));
    $tgl2= date("Y-m", strtotime($date2));
    
    $paksi=$_POST['uaksi'];
    $pmodule=$_POST['module'];
    $pidmenu=$_POST['idmenu'];
    
    echo "<input type='hidden' name='e_tgl1' id='e_tgl1' value='$tgl1'>";
    echo "<input type='hidden' name='e_tgl2' id='e_tgl2' value='$tgl2'>";
    echo "<input type='hidden' name='e_aksi' id='e_aksi' value='$paksi'>";
    
?>

<form method='POST' action='<?PHP echo "?module='suratpd'&act=input&idmenu=204"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        <table id='datatablespd' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='2px'>No</th>
                    <th width='100px'>No. SPD</th>
                    <th width='100px'>Jumlah</th>
                    <th width='50px'></th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $fsudahada = "";
                if ($pinput==2) {
                    $fsudahada = " AND idinput IN (select distinct IFNULL(idinput,'') FROM dbmaster.t_suratdana_br1 WHERE IFNULL(nobbm,'')<>'')";
                }elseif ($pinput==4) {
                    $fsudahada = " AND idinput IN (select distinct IFNULL(idinput,'') FROM dbmaster.t_suratdana_br1 WHERE IFNULL(nobbk,'')<>'')";
                }
                $no=1;
                $query = "select nomor, FORMAT(sum(jumlah),0,'de_DE') jumlah from dbmaster.t_suratdana_br where IFNULL(stsnonaktif,'')<>'Y' 
                    AND IFNULL(nomor,'')<>'' 
                    AND Date_format(tgl, '%Y-%m') between '$tgl1' and '$tgl2' $fsudahada 
                    group by 1 order by 1";
                
                $tampil=mysqli_query($cnmy, $query) or die("error");
                while( $row=mysqli_fetch_array($tampil) ) {
                    $pnomor=$row['nomor'];
                    $pjumlah=$row['jumlah'];
                    
                    $pedit="";
                    if ((INT)$pinput==1 OR (INT)$pinput==2) {
                        $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdatanobbm&idmenu=$pidmenu&nmun=$pidmenu&id=$pnomor'>Edit No. BBM</a>";
                    }elseif ((INT)$pinput==3 OR (INT)$pinput==4) {
                        $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdatanobbk&idmenu=$pidmenu&nmun=$pidmenu&id=$pnomor'>Edit No. BBK</a>";
                    }
                    echo "<tr>";
                    echo "<td>$no<t/d>";
                    echo "<td nowrap>$pnomor</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap>$pedit</td>";
                    echo "</tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>

    </div>
</form>