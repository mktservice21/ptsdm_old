<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    
    $_SESSION['DBTIPE']="";
    $_SESSION['DBTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['DBKENTRY1']=$_POST['uperiode1'];
    $_SESSION['DBKENTRY2']=$_POST['uperiode2'];
    
    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
    
    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    
    echo "<input type='hidden' name='cb_tgltipe' id='cb_tgltipe' value='$tgltipe'>";
    echo "<input type='hidden' name='xtgl1' id='xtgl1' value='$tgl1'>";
    echo "<input type='hidden' name='xtgl2' id='xtgl2' value='$tgl2'>";
    
    
    $tgl1= date("Y-m", strtotime($date1));
    $tgl2= date("Y-m", strtotime($date2));
    
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSETHZR01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHZR02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHZR03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSETHZR04_".$userid."_$now ";
    $tmp05 =" dbtemp.DSETHZR05_".$userid."_$now ";
    
    
    //FORMAT(realisasi1,2,'de_DE') as 
    // getting total number records without any search
    $sql = "SELECT a.nodivisi, a.nomor, a.stsinput, a.idinputbank, DATE_FORMAT(a.tanggal,'%d %M %Y') as tanggal, a.kodeid, a.subkode, b.subnama, b.ibank, "
            . " a.divisi, a.nobukti, FORMAT(a.jumlah,0,'de_DE') as jumlah, "
            . " a.keterangan, a.userid, a.brid, a.noslip ";
    $sql.=" FROM dbmaster.t_suratdana_bank a LEFT JOIN dbmaster.t_kode_spd b on a.subkode=b.subkode ";
    $sql.=" WHERE IFNULL(a.stsnonaktif,'') <> 'Y' ";// AND IFNULL(stsinput,'')<>'K'
    $sql.=" AND Date_format(a.tanggal, '%Y-%m') between '$tgl1' and '$tgl2' ";

    if ($pses_grpuser=="1" OR $pses_grpuser=="24" OR $pses_grpuser=="25") {// OR $pses_grpuser=="25" anne
    }else{
        $sql.=" AND CONCAT(IFNULL(a.nomor,''),IFNULL(a.nodivisi,'')) IN (SELECT CONCAT(IFNULL(nomor,''),IFNULL(nodivisi,'')) FROM dbmaster.t_suratdana_br WHERE "
                . " karyawanid='$pses_idcard')";
    }
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "SELECT a.brOtcId brid, a.noslip, a.tgltrans, a.jumlah, a.realisasi, a.keterangan1, a.real1, a.icabangid_o, b.nama nama_cabang 
        from hrd.br_otc a LEFT JOIN mkt.icabang_o b on a.icabangid_o=b.icabangid_o WHERE 
        a.brOtcId IN (select distinct brid From $tmp01 WHERE IFNULl(brid,'')<>'' AND divisi='OTC')";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query="UPDATE $tmp02 SET nama_cabang=icabangid_o WHERE IFNULL(nama_cabang,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "SELECT a.brid, a.noslip, a.tgltrans, a.jumlah, a.jumlah1, a.aktivitas1, a.realisasi1, a.icabangid, b.nama nama_cabang, a.dokterid 
        from hrd.br0 a LEFT JOIN mkt.icabang b on a.icabangid=b.icabangid WHERE 
        a.brid IN (select distinct brid From $tmp01 WHERE IFNULl(brid,'')<>'' AND divisi<>'OTC')";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select dokterid, nama nama_dokter from hrd.dokter WHERE dokterid IN (select distinct dokterid from $tmp03)";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "select a.*, b.tgltrans, b.aktivitas1, b.realisasi1 nmrealisasi, b.nama_cabang, b.dokterid, c.nama_dokter, d.nama nama_user "
            . " from $tmp01 a LEFT JOIN $tmp03 b on a.brid=b.brid LEFT JOIN $tmp04 c on b.dokterid=c.dokterid LEFT JOIN hrd.karyawan d on a.userid=d.karyawanId";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    //OTC
    $query = "UPDATE $tmp05 a SET a.tgltrans=(select b.tgltrans FROM $tmp02 b WHERE a.brid=b.brid) WHERE IFNULL(a.brid,'')<>'' AND a.divisi='OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp05 a SET a.aktivitas1=(select b.keterangan1 FROM $tmp02 b WHERE a.brid=b.brid) WHERE IFNULL(a.brid,'')<>'' AND a.divisi='OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp05 a SET a.nmrealisasi=(select b.real1 FROM $tmp02 b WHERE a.brid=b.brid) WHERE IFNULL(a.brid,'')<>'' AND a.divisi='OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp05 a SET a.nama_cabang=(select b.nama_cabang FROM $tmp02 b WHERE a.brid=b.brid) WHERE IFNULL(a.brid,'')<>'' AND a.divisi='OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    //END OTC
    

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/mod_br_danabank/aksi_danabank.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etgltipe=document.getElementById('cb_tgltipe').value;
        var etgl1 = document.getElementById("xtgl1").value;
        var etgl2 = document.getElementById("xtgl2").value;
        
        //alert(etgl1);
        var dataTable = $('#datatablednbank').DataTable( {
            //"processing": true,
            //"serverSide": true,
            "stateSave": true,
            //"order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                //{ "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [7,8] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9,11,13,14,15] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 400,
            "scrollX": true/*,

            "ajax":{
                url :"module/mod_br_danabank/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2, // json datasource
                type: "post",  // method  , by default get
                data:"uperiode1="+etgl1+"&uperiode2="+etgl2,
                error: function(){  // error handling
                    $(".data-grid-error").html("");
                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#data-grid_processing").css("display","none");

                }
            }*/
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
</script>

<script>
    function ProsesData(ket, noid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/mod_br_danabank/aksi_danabank.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+"&ket="+ket+"&id="+noid;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }



    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablednbank th {
        font-size: 13px;
    }
    #datatablednbank td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='saldosuratdana'&act=input&idmenu=258"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='brdanabank' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='258' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'>
        <table id='datatablednbank' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>NO</th>
                    <th width='50px'></th>
                    <th width='20px'>ID</th>
                    <th width='30px'>TGL. TRANSAKSI</th>
                    <th width='20px'>JENIS</th>
                    <th width='50px'>PENGAJUAN</th>
                    <th width='50px'>NO DIVISI/BR</th>
                    <th width='50px'>BUKTI</th>
                    <th width='20px'>DEBIT</th>
                    <th width='20px'>KREDIT</th>
                    <th width='200px'>KETERANGAN</th>
                    <th width='20px'>IDBR</th>
                    <th width='20px'>NOSLIP</th>
                    <th width='20px'>REALISASI</th>
                    <th width='20px'>DOKTER/CUSTOMER</th>
                    <th width='20px'>AKTIVITAS</th>
                    <th width='20px'>USER</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $query = "select * from $tmp05 order by idinputbank";
                $query=mysqli_query($cnmy, $query) or die("mydata.php: get data");
                while( $row=mysqli_fetch_array($query) ) {  // preparing an array
                    $nestedData=array();
                    $idno=$row['idinputbank'];
                    $puserid=$row['userid'];
                    $pstsinput=$row['stsinput'];
                    $ptgl_t=$row["tanggal"];
                    
                    $pdivisi = $row["divisi"];
                    $pnobukti = $row["nobukti"];
                    $pnmdokter = $row["nama_dokter"];
                    $pket = $row["keterangan"];
                    
                    $pnobrid = $row["brid"];
                    $pnoslip = $row["noslip"];
                    $paktivitas = $row["aktivitas1"];
                    $pnmuser = $row["nama_user"];
                    $pnmrealisasi = $row["nmrealisasi"];
                    
                    $psubkode = $row["subkode"];
                    $psubnamakode = $row["subnama"];
                    $psubkodeibank = $row["ibank"];
                    
                    $pnospd= $row["nomor"];
                    $pnodivisi= $row["nodivisi"];
                    
                    $nbutton = ""
                            . "<a class='btn btn-success btn-xs' href='?module=brdanabank&act=editdata&idmenu=258&nmun=258&id=$idno'>Edit</a> "
                            . "<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">
                    ";


                    $nkodeid="Advance";
                    if ($row["kodeid"]=="2") $nkodeid="Klaim";
                    if ($row["kodeid"]=="5") $nkodeid="Bank";


                    $pjumlah=$row["jumlah"];
                    $pjmld=$pjumlah;
                    $pjmlk="";
                    if ($pstsinput=="K") {
                        $pjmld="";
                        $pjmlk=$pjumlah; 
                    }
                    
                    if ($row["kodeid"]!="5") {
                        $nkodeid=$psubnamakode;
                    }
                    
                    if ($psubkodeibank=="Y" AND $puserid==$pses_idcard) {
                    }else{
                        if ($pstsinput=="K" OR $pstsinput=="M") {
                            $nbutton="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">";
                            $nbutton="";//hanya bisa hapus diinput keluar atau masuk
                        }
                    }
                    
                    if ($pstsinput=="M") {
                        //if ($puserid<>$pses_idcard) {
                            $nbutton = "";
                        //}
                    }
                    
                    if ($pses_grpuser=="25") {
                        if ($puserid<>$pses_idcard) {
                            $nbutton = "";
                        }
                    }
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$nbutton</td>";
                    echo "<td>$idno</td>";
                    echo "<td>$ptgl_t</td>";
                    echo "<td>$nkodeid</td>";
                    echo "<td>$pdivisi</td>";
                    echo "<td>$pnodivisi</td>";
                    echo "<td>$pnobukti</td>";
                    echo "<td>$pjmld</td>";
                    echo "<td>$pjmlk</td>";
                    echo "<td>$pket</td>";
                    echo "<td>$pnobrid</td>";
                    echo "<td>$pnoslip</td>";
                    echo "<td>$pnmrealisasi</td>";
                    echo "<td>$pnmdokter</td>";
                    echo "<td nowrap>$paktivitas</td>";
                    echo "<td nowrap>$pnmuser</td>";
                    echo "</tr>";

                    $no++;
                }
            ?>
            </tbody>
        </table>

    </div>
    
</form>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_query($cnmy, "drop temporary table $tmp02");
    mysqli_query($cnmy, "drop temporary table $tmp03");
    mysqli_query($cnmy, "drop temporary table $tmp04");
    mysqli_query($cnmy, "drop temporary table $tmp05");
?>