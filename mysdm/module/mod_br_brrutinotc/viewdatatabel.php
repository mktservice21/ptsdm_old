<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $_SESSION['FINRUTTIPEOTC']="";
    //$_SESSION['FINRUTTGLTIPEOTC']=$_POST['utgltipe'];
    $_SESSION['FINRUTPERENTYOTC1']=$_POST['uperiode1'];
    $_SESSION['FINRUTPERENTYOTC2']=$_POST['uperiode2'];
    //$_SESSION['FINRUTDIVOTC']=$_POST['udivisi'];
    //$_SESSION['FINRUTCABOTC']=$_POST['uarea'];
    
    
    $tgltipe="";//$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m", strtotime($date1));
    $tgl2= date("Y-m", strtotime($date2));
    $divisi=$_SESSION['DIVISI'];//$_POST['udivisi'];
    $uidcard=$_SESSION['IDCARD'];//$_POST['uidc'];
    $cabang="";//$_POST['uarea'];
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pact=$_GET['act'];

?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $pmodule; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $pidmenu; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    <div class='x_content'>
        <table id='datatablerutotc' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='70px'>Aksi</th>
                    <th width='40px'>No ID</th>
                    <th width='80px'>Yang Membuat</th>
                    <th width='40px'>Tgl. Input</th>
                    <th width='40px'>Periode</th>
                    <th width='50px'>Jumlah</th>
                    <th width='40px'>Bukti</th>
                    <th width='50px'>Keterangan</th>
                    <th width='30px'>Approve HOS</th>
                    <th width='30px'>Proses Finance</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "SELECT
                    br.divi AS divi,
                    br.kode AS kode,
                    br.divisi AS divisi,
                    br.idrutin AS idrutin,
                    br.karyawanid AS karyawanid,
                    k.nama AS nama,
                    br.jabatanid AS jabatanid,
                    br.tgl AS tgl,
                    br.bulan AS bulan,
                    br.periode1 AS periode1,
                    br.periode2 AS periode2,
                    br.jumlah AS jumlah,
                    br.keterangan AS keterangan,
                    br.stsnonaktif AS stsnonaktif,
                    br.icabangid AS icabangid,
                    br.icabangid_o AS icabangid_o,
                    br.areaid AS areaid,
                    br.areaid_o AS areaid_o,
                    br.atasan1 AS atasan1,
                    br.atasan2 AS atasan2,
                    br.atasan3 AS atasan3,
                    br.atasan4 AS atasan4,
                    br.tgl_atasan1 AS tgl_atasan1,
                    br.tgl_atasan2 AS tgl_atasan2,
                    br.tgl_atasan3 AS tgl_atasan3,
                    br.tgl_atasan4 AS tgl_atasan4,
                    br.validate AS validate,
                    br.validate_date AS validate_date,
                    br.fin AS fin,
                    br.tgl_fin AS tgl_fin,
                    br.userid AS userid,
                    br.nama_karyawan AS nama_karyawan
                    FROM dbmaster.t_brrutin0 br JOIN hrd.karyawan k ON br.karyawanid = k.karyawanId ";
                
                
                $sql = "SELECT divisi, idrutin, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, DATE_FORMAT(bulan,'%M %Y') as bulan, "
                        . " DATE_FORMAT(periode1,'%d/%m/%Y') as periode1, "
                        . " DATE_FORMAT(periode2,'%d/%m/%Y') as periode2, "
                        . " divisi, karyawanid, nama, nama_karyawan, areaid, FORMAT(jumlah,0,'de_DE') as jumlah, "
                        . " keterangan, jabatanid, atasan1, atasan2, tgl_atasan1, tgl_atasan2, tgl_atasan3, tgl_atasan4, tgl_fin ";
                $sql.=" FROM ($query) as TBL WHERE 1=1 ";
                $sql.=" AND kode=1 AND IFNULL(stsnonaktif,'')<>'Y' AND divisi='OTC' ";
                $sql.=" AND Date_format(bulan, '%Y-%m') between '$tgl1' and '$tgl2' ";
                if ($_SESSION['GROUP']!=1) $sql .=" AND (karyawanid='$uidcard' OR userid='$uidcard')";
                $sql .=" ORDER BY idrutin DESC";
                    
                    
                $no=1;
                $tampil=mysqli_query($cnmy, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['idrutin'];
                    $pkaryawanid=$row['karyawanid'];
                    $nama=$row["nama"];
                    if ($_SESSION['KRYNONE']==$pkaryawanid) $nama=$row["nama_karyawan"];
                    $ptglinput=$row["tgl"];
                    
                    $periode = $row["periode1"]." - ".$row["periode2"];
                    $jumlah = $row["jumlah"];
                    $ket = $row["keterangan"];
                    
                    
                    $edit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$idno'>Edit</a>";
                    $hapus="<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesData('hapus', '$idno')\">";
                    $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=$pmodule&brid=$idno&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "Print</a>";
    
                    $t_ats1 = $row["tgl_atasan1"];
                    $t_ats2 = $row["tgl_atasan2"];
                    $g_ats2 = getfieldcnmy("select gbr_atasan2 as lcfields from dbmaster.t_brrutin0 where idrutin='$idno'");
                    $t_ats3 = $row["tgl_atasan3"];
                    $t_ats4 = $row["tgl_atasan4"];
                    $pjabatanid=$row['jabatanid'];
                    $lvlpengajuan = getfieldcnmy("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId='$pjabatanid'");
                    $allbutton="$edit $hapus $print";
                    
                    $warna="btn btn-success btn-xs";
                    $gambar = getfieldcnmy("select count(*) as lcfields from dbimages.img_brrutin1 where idrutin='$idno' LIMIT 1");
                    if (empty($gambar)) $gambar=0;
                    if ( (int)$gambar>0 ) $warna="btn btn-danger btn-xs";
                    $upload="<a class='$warna' href='?module=$pmodule&act=uploaddok&idmenu=$pidmenu&nmun=$pidmenu&id=$idno'>Upload</a>";
                    
                    $apv1="";
                    $apv2="";
                    $apv3="";
                    $apvfin="";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$allbutton</td>";
                    echo "<td nowrap>$idno</td>";
                    echo "<td nowrap>$nama</td>";
                    echo "<td nowrap>$ptglinput</td>";
                    echo "<td nowrap>$periode</td>";
                    echo "<td nowrap>$jumlah</td>";
                    
                    echo "<td nowrap>$upload</td>";
                    echo "<td nowrap>$ket</td>";
                    echo "<td nowrap>$apv1</td>";
                    echo "<td nowrap>$apvfin</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>

    </div>
</form>

<?PHP
    mysqli_close($cnmy);
?>

    
<script>
    $(document).ready(function() {
        var dataTable = $('#datatablerutotc').DataTable( {
            //"stateSave": true,
            //"order": [[ 2, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    

    function ProsesData(ket, noid){
        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var txt;
                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                    if (textket == null || textket == "") {
                        txt = textket;
                    } else {
                        txt = textket;
                    }
                }


                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                document.getElementById("d-form2").action = "module/mod_br_brrutinotc/aksi_brrutinotc.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
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
    #datatablerutotc th {
        font-size: 13px;
    }
    #datatablerutotc td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>