<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }

    $pidcardapv=$_SESSION['IDCARD'];
    $pidcabang=$_POST['ucabang'];
    $pidarea=$_POST['uarea'];
    $pbulanawal="202102";

    $_SESSION['LSTCUSTNEWICAB']=$pidcabang;
    $_SESSION['LSTCUSTNEWIARE']=$pidarea;

    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    include "../../../config/koneksimysqli_ms.php";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpcustsnewdm01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpcustsnewdm02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpcustsnewdm03_".$puserid."_$now ";


    $query = "select icabangid, areaid, icustid, nama, alamat1, alamat2, kodepos, 
        kota, tglinput from mkt.icust where DATE_FORMAT(tglinput,'%Y%m')>='$pbulanawal' 
        AND icabangid='$pidcabang' ";
    if (!empty($pidarea)) $query .=" AND areaid='$pidarea'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "alter table $tmp01 add column tvalue DECIMAL(20,2), add column isudah varchar(1)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "select distinct icabangid, areaid, icustid FROM mkt.new_icust WHERE 
        icabangid='$pidcabang'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    $query = "UPDATE $tmp01 as a JOIN $tmp02 as b 
        on a.icabangid=b.icabangid and a.areaid=b.areaid and a.icustid=b.icustid SET 
        isudah='Y'";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    //mysqli_query($cnms, "DELETE FROM $tmp01 WHERE IFNULL(isudah,'')='Y'");
    //$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "select a.icabangid, a.areaid, a.icustid, DATE_FORMAT(tgljual,'%Y%m') as bulan, 
        sum(a.qty*a.hna) as tvalue 
        from mkt.mr_sales2 as a 
        JOIN $tmp01 as b 
        on a.icabangid=b.icabangid and a.areaid=b.areaid and a.icustid=b.icustid
        group by 1,2,3,4";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "DELETE $tmp03, $tmp01  FROM $tmp03 JOIN $tmp01 
        on $tmp03.icabangid=$tmp01.icabangid and $tmp03.areaid=$tmp01.areaid and $tmp03.icustid=$tmp01.icustid 
        WHERE $tmp03.bulan<'$pbulanawal'"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "UPDATE $tmp01 as a JOIN (select icabangid, areaid, icustid, 
        sum(tvalue) as tvalue from $tmp03 GROUP BY 1,2,3) as b 
        on a.icabangid=b.icabangid and a.areaid=b.areaid and a.icustid=b.icustid SET 
        a.tvalue=b.tvalue";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp03");
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "select a.*, b.nama as nama_cabang, c.nama as nama_area from $tmp01 as a LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangid 
        LEFT JOIN mkt.iarea as c on a.icabangid=c.icabangid AND a.areaid=c.areaid";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' 
    data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>

        <?PHP
        echo "<input type='hidden' value='$pidcardapv' id='e_idcard' name='e_idcard' readonly>";
        ?>
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='50px'>No</th>
                    <th width='50px'>
                    <?PHP
                    $chkall = "<input type='checkbox' id='chkbtnbr' value='select' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" />";
                    echo "$chkall";
                    ?>
                    </th>
                    <th width='100px'>Nama Customer</th>
                    <th width='100px'>Alamat 1</th>
                    <th width='100px'>Alamat 2</th>
                    <th width='50px'>Kota</th>
                    <th width='100px'>Cabang</th>
                    <th width='100px'>Area</th>
                    <th width='100px'>Value</th>
                    <th width='100px'>Tgl. Input</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $query = "select * from $tmp03 order by nama, icustid, nama_cabang, nama_area";
                $tampil=mysqli_query($cnms, $query);
                while ($row=mysqli_fetch_array($tampil)) {
                    $pidcabang=$row['icabangid'];
                    $pnmcabang=$row['nama_cabang'];
                    $pidarea=$row['areaid'];
                    $pnmarea=$row['nama_area'];
                    $pidcust=$row['icustid'];
                    $pnmcust=$row['nama'];
                    $palamat1=$row['alamat1'];
                    $palamat2=$row['alamat2'];
                    $pkota=$row['kota'];
                    $ptglinput=$row['tglinput'];
                    $psudah=$row['isudah'];
                    
                    $pidcusttomer=(INT)$pidcust;
                    $pidcab=(INT)$pidcabang;
                    $pidar=(INT)$pidarea;

                    $pvalue=$row['tvalue'];

                    $pvalue=number_format($pvalue,0,",",",");

                    $pidno=$pidcabang."".$pidarea."".$pidcust;

                    $txt_idcab="<input type='hidden' value='$pidcabang' id='txtidcab[$pidno]' name='txtidcab[$pidno]' class='' autocomplete='off' size='8px' Readonly>";
                    $txt_idarea="<input type='hidden' value='$pidarea' id='txtidarea[$pidno]' name='txtidarea[$pidno]' class='' autocomplete='off' size='8px' Readonly>";
                    $txt_idcust="<input type='hidden' value='$pidcust' id='txtidcust[$pidno]' name='txtidcust[$pidno]' class='' autocomplete='off' size='8px' Readonly>";
                    $txt_value="<input type='hidden' value='$pvalue' id='txtvalue[$pidno]' name='txtvalue[$pidno]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly>";

                    $cekbox = "<input type=checkbox value='$pidno' id='chkbox_br[$pidno]' name='chkbox_br[]' onclick=\"\">";
                    $phapus = "<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"disp_confirm('hapus', 'hapuslistcust', '$pidno')\">";
    
                    if ($psudah=="Y") {
                        $cekbox="";
                        $cekbox=$phapus;
                    }

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$cekbox $txt_idcab $txt_idarea $txt_idcust $txt_value</td>";
                    echo "<td nowrap>$pnmcust ($pidcusttomer)</td>";
                    echo "<td nowrap>$palamat1</td>";
                    echo "<td nowrap>$palamat2</td>";
                    echo "<td nowrap>$pkota</td>";
                    echo "<td nowrap>$pnmcabang ($pidcab)</td>";
                    echo "<td nowrap>$pnmarea ($pidar)</td>";
                    echo "<td nowrap align='right'>$pvalue</td>";
                    echo "<td nowrap>$ptglinput</td>";
                    echo "</tr>";
                    
                    $no++;
                    
                }
                ?>
            </tbody>
        </table>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
            <div class='col-md-4'>
                <button type='button' class='btn btn-dark' onclick='disp_confirm("simpan", "simpandatalitcust", "")'>Simpan</button>
            </div>
        </div>

    </div>

</form>

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
		
    </style>

    <script>
        function SelAllCheckBox(nmbuton, data){
            var checkboxes = document.getElementsByName(data);
            var button = document.getElementById(nmbuton);

            if(button.value == 'select'){
                for (var i in checkboxes){
                    checkboxes[i].checked = 'FALSE';
                }
                button.value = 'deselect'
            }else{
                for (var i in checkboxes){
                    checkboxes[i].checked = '';
                }
                button.value = 'select';
            }
        }

        function disp_confirm(pText, ket, iid)  {
            
            var eidcard=document.getElementById('e_idcard').value;
            if (eidcard=="") {
                alert("ID Approve Kosong...!!!"); return false;
            }

            var pText_="";
            if (pText=="hapus") {
                if (iid=="") {
                    alert("Tidak ada data yang akan diproses...!!!"); return false;
                }
                pText_ = "Apakah akan melakukan proses hapus...???";
            }else if (pText=="simpan") {
                pText_ = "Apakah akan melakukan proses simpan...???";
            }

            if (pText_=="") {
                return 0;
            }else{
                ok_ = 1;
                if (ok_) {
                    var r=confirm(pText_)
                    if (r==true) {
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                                    
                        //document.write("You pressed OK!")
                        document.getElementById("d-form2").action = "module/master/mst_listcustomernew/aksi_listcustomernew.php?module="+module+"&act="+ket+"&idmenu="+idmenu+"&uid="+iid;
                        document.getElementById("d-form2").submit();
                        return 1;
                    }
                } else {
                    //document.write("You pressed Cancel!")
                    return 0;
                }
            }
        }

    </script>

<?PHP
hapusdata:
    mysqli_query($cnms, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_query($cnms, "drop TEMPORARY table IF EXISTS $tmp02");
    mysqli_query($cnms, "drop TEMPORARY table IF EXISTS $tmp03");
    mysqli_close($cnms);
?>