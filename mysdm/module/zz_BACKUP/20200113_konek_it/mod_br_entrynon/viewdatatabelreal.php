    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />

    <script src="js/inputmask.js"></script>
    
<?PHP
    session_start();
    
    $_SESSION['FINNONTIPE']=$_POST['utipeproses'];
    $_SESSION['FINNONTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['FINNONPERENTY1']=$_POST['uperiode1'];
    $_SESSION['FINNONPERENTY2']=$_POST['uperiode2'];
    $_SESSION['FINNONDIV']=$_POST['udivisi'];
    
    
    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    $divisi=$_POST['udivisi'];
    $uidcard=$_SESSION['USERID'];
    
    
    include "../../config/koneksimysqli_it.php";
    $sql="select distinct COA4 from dbmaster.v_coa_wewenang where karyawanId=$uidcard and (br <> '') and (br<>'N')";//DCC & DSS
    $tampil=mysqli_query($cnit, $sql);
    $ketemu=mysqli_num_rows($tampil);
    $filcoa="";
    if ($ketemu>0) {
        while ($r=  mysqli_fetch_array($tampil)) {
            $filcoa .= $r['COA4'].",";
        }
        if (!empty($filcoa)) {
            $filcoa=substr($filcoa, 0, -1);
        }
    }
?>

<form method='POST' action='<?PHP echo "?module='$_GET[module]'&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='entrybrdcc' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='88' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'>
        <table id='datatabledcc2' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th>Jumlah</th>
                    <th width='60px'>Realisasi</th><th width='60px'>Tgl. Terima</th><th>Lain-Lain</th><th>Batal</th>
                    <th></th>
                    <th width='50px'>Noslip</th>
                    <th width='100px'>Dokter / Realisasi</th>
                    <th width='80px'>Yg Membuat</th>
                    <th width='50px'>Realisasi</th>
                    <th nowrap>Tgl. Transfer</th>
                    <th nowrap>Tgl. Rpt. SBY</th>
                    <th nowrap>Tgl. Input</th>
                    <th nowrap>Keterangan</th>
                    <th>Kode</th>
                    <th>ID</th>

                </tr>
            </thead>
            <tbody>
                <?PHP
                $sql = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
                    a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.tgltrm, a.kode, d.nama nama_kode,  
                    a.icabangid, e.nama nama_cabang, a.lain2, a.tglrpsby, a.lampiran, a.ca, a.batal 
                    from hrd.br0 a 
                    LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
                    LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
                    LEFT JOIN hrd.br_kode d on a.kode=d.kodeid 
                    LEFT JOIN MKT.icabang e on a.icabangid=e.iCabangId 
                    WHERE a.brid NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject)";
                
                $filtipe="Date_format(a.MODIFDATE, '%Y-%m-%d')";
                if ($tgltipe=="2") $filtipe="Date_format(a.tgltrans, '%Y-%m-%d')";
                if ($tgltipe=="3") $filtipe="Date_format(a.tgltrm, '%Y-%m-%d')";
                if ($tgltipe=="4") $filtipe="Date_format(a.tgl, '%Y-%m-%d')";
                if ($tgltipe=="5") $filtipe="Date_format(a.tglrpsby, '%Y-%m-%d')";
                $sql.=" and $filtipe between '$tgl1' and '$tgl2' ";
                $sql.=" and (d.br = '' and d.br<>'N') ";
                if (!empty($divisi)) $sql.=" and a.divprodid='$divisi' ";
                
                if (!empty($filcoa)) {
                    $ucoa=$filcoa;
                    $fcoa="";
                    $arr_coa= explode(",", $ucoa);
                    $jml=count($arr_coa);
                    for ($i=0;$i<$jml;$i++) {
                        $fcoa .="'".$arr_coa[$i]."',";

                    }
                    $filcoa=" a.COA4 in (".substr($fcoa, 0, -1).")";
                }
                
                //id input
                $filidi=" a.user1=$uidcard ";
                if ($_SESSION['ADMINKHUSUS']=="N") $filidi="";


                if (!empty($filcoa) AND !empty($filidi))
                    $sql.=" and ($filcoa OR $filidi) ";
                elseif (!empty($filcoa))
                    $sql.=" and $filcoa ";
                elseif (!empty($filidi)) {
                    //$sql.=" and $filidi ";
                }
                //============================
                
                $no=1;
                $tampil=mysqli_query($cnit, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $dok="";
                    if (!empty($row['dokterId'])) $dok=$row["nama_dokter"];//." <small>(".(int)$row['dokterId'].")</small>"
                    if (empty($dok)) $dok=$row["realisasi1"];
                    
                    $ptgltrm = "";
                    if (!empty($row['tgltrm']) AND $row['tgltrm']<> "0000-00-00")
                        $ptgltrm =$row['tgltrm'];
                    
                    $ptgltrans = "";
                    if (!empty($row['tgltrans']) AND $row['tgltrans']<> "0000-00-00")
                        $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));
                    
                    $ptglinput =date("d-M-Y", strtotime($row['tgl']));
                    
                    $ptglsby = "";
                    if (!empty($row['tglrpsby']) AND $row['tglrpsby']<> "0000-00-00")
                        $ptglsby =date("d-M-Y", strtotime($row['tglrpsby']));
                            
                    $pjumlah = $row["jumlah"];
                    $pjumlah=number_format($pjumlah,0,",",",");
                    
                    $pjmlreal = $row["jumlah1"];
                    //$pjmlreal=number_format($prealisasi,0,",",",");
                    
                    
                    $paktivitas = $row["aktivitas1"];
                    $pnmrealisasi = $row["realisasi1"];
                    $pnoslip = $row["noslip"];
                    $pnmkode = $row["nama_kode"];
                    $pnmkaryawan = $row["nama_karyawan"];
                    $pnmcab = $row["nama_cabang"];
                    $plain = $row["lain2"];
                    $pbatal = $row["batal"];
                    $chkbatal="";
                    if ($pbatal=="Y") $chkbatal="checked";
                    
                    $pbrid = $row["brId"];
                    
                    $ptxtnobrid="<input type='hidden' size='10px' id='e_nobrid$no' name='e_nobrid$no' class='input-sm' autocomplete='off' value='$pbrid'>";
                    $ptxtjmlreal="<input type='text' size='10px' id='e_jmlreal$no' name='e_jmlreal$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlreal'>";
                    $ptxttglterima="<input type='date' size='5px' id='e_tglterima$no' name='e_tglterima$no' class='input' autocomplete='off' value='$ptgltrm'>";
                    $ptxtlain="<input type='text' size='10px' id='e_lain$no' name='e_lain$no' class='input' autocomplete='off' value='$plain'>";
                    $ptxtbatal="<input type='checkbox' id='chk_batal$no' name='chk_batal$no' class='input' value='$pbatal' $chkbatal>";
                    
                    $fsimpan="'e_nobrid$no', 'e_jmlreal$no', 'e_tglterima$no', 'e_lain$no', 'chk_batal$no'";
                    $simpandata= "<input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save Real' onclick=\"SimpanData('input', $fsimpan)\">";
                    $pedit = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$pbrid'>Edit</a>";
                    $phapus = "<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesData('hapus', '$pbrid')\">";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no $ptxtnobrid</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap align='right'>$ptxtjmlreal</td>";
                    echo "<td nowrap>$ptxttglterima</td>";
                    echo "<td nowrap>$ptxtlain</td>";
                    echo "<td nowrap>$ptxtbatal</td>";
                    echo "<td nowrap>$simpandata $pedit $phapus</td>";
                    
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap>$dok</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap>$pnmrealisasi</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$ptglsby</td>";
                    echo "<td nowrap>$ptglinput</td>";
                    echo "<td nowrap>$paktivitas</td>";
                    echo "<td nowrap>$pnmkode</td>";
                    echo "<td nowrap>$pbrid</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>

    </div>
</form>

<?PHP
    mysqli_close($cnit);
?>
    
<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledcc2').DataTable( {
            fixedHeader: false,
            "stateSave": true,
            "ordering": false,
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false,
            rowReorder: {
                selector: 'td:nth-child(7)'
            },
            responsive: true
        } );
    } );
    
    //$fsimpan="'e_nobrid$no', 'e_jmlreal$no', 'e_tglterima$no', 'e_lain$no', '$ptxtbatal'";
    function SimpanData(eact, idbr, ajmlreal,  atglterima, alain, abatal)  {
        var eidbr =document.getElementById(idbr).value;
        var ejmlreal =document.getElementById(ajmlreal).value;
        var etglterima =document.getElementById(atglterima).value;
        var elain =document.getElementById(alain).value;
        var ebatal =document.getElementById(abatal).checked;

        if (eidbr==""){
            alert("id kosong....");
            return 0;
        }

        //alert(eidbr+", "+ejmlreal+", "+etglterima+", "+elain+", "+ebatal); return 0;
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";

        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/mod_br_entrynon/aksi_simpanreal.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidbr="+eidbr+"&ujmlreal="+ejmlreal+"&ulain="+elain+"&utglterima="+etglterima+"&ubatal="+ebatal,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                        }
                        if (eact=="hapus" && data.length <= 1) {
                            //document.getElementById(enoslip).value="";
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
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
                document.getElementById("demo-form2").action = "module/mod_br_entrynon/aksi_entrybrdcc.php?kethapus="+txt+"&ket="+ket+"&id="+noid;
                document.getElementById("demo-form2").submit();
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
    #datatabledcc2 th {
        font-size: 12px;
    }
    #datatabledcc2 td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>
