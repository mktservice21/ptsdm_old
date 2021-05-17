<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);

    session_start();
    include("../../../config/koneksimysqli.php");
    
    $_SESSION['BNKDANATIPE']="viewdatabankkeluar";
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pact="bankkeluar";

    $pket_input=$_POST['uketinput'];
    $ptgl01=$_POST['uperiode1'];
    $ptgl02=$_POST['uperiode2'];

    $_SESSION['DBKENTRY1']=$ptgl01;
    $_SESSION['DBKENTRY2']=$ptgl02;

    $periode1= date("Y-m-d", strtotime($ptgl01));
    $periode2= date("Y-m-t", strtotime($ptgl02));

    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];

    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmprkpbnkkeluar01_".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.tmprkpbnkkeluar02_".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.tmprkpbnkkeluar03_".$_SESSION['USERID']."_$now ";
    $tmp04 =" dbtemp.tmprkpbnkkeluar04_".$_SESSION['USERID']."_$now ";
    $tmp05 =" dbtemp.tmprkpbnkkeluar05_".$_SESSION['USERID']."_$now ";
    $tmp06 =" dbtemp.tmprkpbnkkeluar06_".$_SESSION['USERID']."_$now ";
    $tmp07 =" dbtemp.tmprkpbnkkeluar07_".$_SESSION['USERID']."_$now ";
    $tmp08 =" dbtemp.tmprkpbnkkeluar08_".$_SESSION['USERID']."_$now ";


    $n_filterkaryawan="";

    if ($pses_grpuser=="1" OR $pses_grpuser=="24" OR $pses_grpuser=="25") {
        if ($pket_input=="2" AND $pses_grpuser=="25") {
            $n_filterkaryawan=" AND divisi<>'OTC' AND ( karyawanid='$pses_idcard' OR subkode='25' ) ";
        } 
    }else{
        if ($pses_divisi=="OTC") {
            $n_filterkaryawan=" AND divisi='OTC' ";
        }else{
            $n_filterkaryawan=" AND divisi<>'OTC' AND karyawanid='$pses_idcard' ";
        }
    }

    $fil_keluar_ = "(
        (tglspd between '$periode1' AND '$periode2')
            OR
            (
            (tgl between '$periode1' AND '$periode2') 
            AND ( subkode IN ('22', '23', '36') OR pilih='N')
            )
        )";
        


    $query = "select karyawanid, userid, pilih, idinput, tgl, 
        CASE WHEN IFNULL(tglspd,'0000-00-00')='0000-00-00' THEN tgl ELSE tglspd END as tglspd, 
        divisi, kodeid, subkode, 
        nodivisi, CASE WHEN IFNULL(nodivisi,'')='' THEN idinput ELSE nodivisi END as nodivisi2, 
        nomor, jumlah, jumlah2, jumlah3 from dbmaster.t_suratdana_br 
        WHERE IFNULL(stsnonaktif,'')<>'Y' $n_filterkaryawan AND 
        $fil_keluar_ ";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "select a.*, b.nama, b.subnama, b.ibank, b.igroup, b.inama  
        from $tmp01 as a LEFT JOIN
        dbmaster.t_kode_spd as b on a.subkode=b.subkode";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "DELETE FROM $tmp02 WHERE ( IFNULL(nomor,'')='' AND IFNULL(igroup,'') IN ('1') ) OR ( subkode IN ('29') )";//IFNULL(igroup,'')='3' AND IFNULL(ibank,'')='Y'
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "ALTER TABLE $tmp02 ADD COLUMN sdh varchar(1)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN 
        ( select distinct idinput from dbmaster.t_suratdana_bank WHERE IFNULL(stsnonaktif,'')<>'Y' AND  IFNULL(stsinput,'')='K' ) as b 
        on a.idinput=b.idinput SET a.sdh='1'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "update $tmp02 set igroup='99', inama='PC-M' WHERE pilih='N' AND IFNULL(nomor,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<br/><br/>
<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
    id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>

        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='20px' align='center'>Divisi</th>
                    <th width='40px' align='center'>Nomor</th>
                    <th width='40px' align='center'>No Divisi</th>
                    <th width='40px' align='center'>Tanggal</th>
                    <th width='50px' align='center'>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "select distinct igroup, inama from $tmp02 order by igroup";
                $tampil0=mysqli_query($cnmy, $query);
                while ($row0=mysqli_fetch_array($tampil0)) {
                    $pigroup=$row0['igroup'];
                    $pinmgroup=$row0['inama'];

                    echo "<tr class='trtotal'>";
                    echo "<td nowrap>$pinmgroup</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "</tr>";

                    $query = "select * from $tmp02 WHERE igroup='$pigroup' order by igroup, tglspd, divisi";
                    $tampil1=mysqli_query($cnmy, $query);
                    while ($row1=mysqli_fetch_array($tampil1)) {
                        $pidinput=$row1['idinput'];
                        $pidnomor=$row1['nomor'];
                        $pdivisi=$row1['divisi'];
                        $pkodeid=$row1['kodeid'];
                        $pnsubdiv=$row1['subkode'];
                        $psubnama=$row1['subnama'];
                        $pnospd=$row1['nomor'];
                        $pnodivisi=$row1['nodivisi'];
                        $ptglspd=$row1['tglspd'];
                        $pjumlah1=$row1['jumlah'];
                        $pjumlah2=$row1['jumlah2'];
                        $pjumlah3=$row1['jumlah3'];

                        $psdh=$row1['sdh'];

                        $pjumlah=$pjumlah1;

                        $pnmdivisi=$pdivisi;
                        $n_div=$pdivisi;
                        if ($pkodeid=="2" AND $pnsubdiv=="21") $n_div="LK";
                        if ($pkodeid=="1" AND $pnsubdiv=="03") $n_div="RUTIN";
                        if ($pkodeid=="2" AND $pnsubdiv=="22") $n_div="KAS";
                        if ($pkodeid=="2" AND $pnsubdiv=="23") $n_div="KASCOR";
                        if ($pdivisi=="OTC") $n_div=$pdivisi;

                        $ptglspd= date('d F Y', strtotime($ptglspd));


                        $pjumlah=number_format($pjumlah,0,",",",");

                        $pnmbtn="btn btn-default btn-xs";
                        if ($psdh=="1") $pnmbtn="btn btn-info btn-xs";

                        $pbtnnodivisi=$pnodivisi;
                        if (!empty($pnodivisi)) {
                            $pbtnnodivisi="<a href='eksekusi3.php?module=glreportspddetail&act=input&idmenu=258&ket=bukan&divisi=$n_div&nodivisi=$pnodivisi&idinspd=$pidinput' target='_blank'>$pnodivisi</a>";
                        }


                        
                        $pbtninputjumlah="<button type='button' class='$pnmbtn' title='' data-toggle='modal' "
                                . " data-target='#myModal' "
                                . " onClick=\"InputBankKeluar('$pidinput', '$pidnomor', '$pnodivisi', '$pjumlah')\">$pjumlah</button>";
                                
                        if (empty($pnodivisi)) {
                            //$pbtninputjumlah=$pjumlah;
                        }

                        echo "<tr>";
                        echo "<td nowrap>$psubnama - $pnmdivisi</td>";
                        echo "<td nowrap>$pnospd</td>";
                        echo "<td nowrap>$pbtnnodivisi</td>";
                        echo "<td nowrap>$ptglspd</td>";
                        echo "<td nowrap align='right'>$pbtninputjumlah</td>";
                        echo "</tr>";
                    }


                }
                ?>
            </tbody>
        </table>


    </div>
</form>
    <script>
        function InputBankKeluar(didinput, dnospd, dnodiv, djumlah){
            $.ajax({
                type:"post",
                url:"module/mod_br_danabank/input_bank_keluar.php?module=viewdatabankkeluar",
                data:"uidinput="+didinput+"&unospd="+dnospd+"&unodiv="+dnodiv+"&ujumlah="+djumlah,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
    </script>

    <style>
        .divnone {
            display: none;
        }
        
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

        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            z-index:1;
        }
		
        .trtotal {
            font-weight: bold;
        }
    </style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_close($cnmy);
?>