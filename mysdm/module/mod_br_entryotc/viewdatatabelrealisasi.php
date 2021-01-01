<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

<?PHP 
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    
    $_SESSION['OTCTIPE']=$_POST['uisi'];
    $_SESSION['OTCTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['OTCPERENTY1']=$_POST['uperiode1'];
    $_SESSION['OTCPERENTY2']=$_POST['uperiode2'];
    
    
    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    $divisi=$_POST['udivisi'];
    $isitipe=$_POST['uisi'];
    
    
    $userid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPOTRLINP01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPOTRLINP02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPOTRLINP03_".$userid."_$now ";

    $sql = "SELECT brotcid, icabangid_o, subpost, kodeid, tglbr, keterangan1, keterangan2, 
        jumlah, realisasi, tglreal, tgltrans, noslip, real1, lampiran, ca, via, tglrpsby, sby, batal  
        from hrd.br_otc where IFNULL(batal,'')<>'Y' AND ( IFNULL(tglreal,'')='' OR IFNULL(tglreal,'0000-00-00')='0000-00-00' ) ";
    
    $filtipe="Date_format(tglbr, '%Y-%m-%d')";
    if ($tgltipe=="2") $filtipe="Date_format(tgltrans, '%Y-%m-%d')";
    if ($tgltipe=="3") {
        $sql.=" and ifnull(tgltrans,'0000-00-00') in ('0000-00-00', '') ";
    }else
        $sql.=" and $filtipe between '$tgl1' and '$tgl2' ";
    
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.*, b.nama nama_cabang, c.nama AS nama_kode, c.nmsubpost from $tmp01 a "
            . " LEFT JOIN dbmaster.v_icabang_o b on a.icabangid_o=b.icabangid_o "
            . " LEFT JOIN hrd.brkd_otc c on a.kodeid = c.kodeid AND a.subpost = c.subpost";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
?>

<form method='POST' action='<?PHP echo "?module='entrybrotc'&act=isidatarealisasipilih&idmenu=87"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        
        <table id='datatableotc' class='table table-striped table-bordered' width="100%" border="1px solid black">
        
            <thead>
                <tr><!-- <input type='checkbox' id='chkbtnall' value='select' onClick="SelAllCheckBox('chkbtnall', 'chkbox_id[]')"/> -->
                    <th width='7px'>No</th>
                    <th width='50px'></th>
                    <th width='50px'></th>
                    <th width='50px'>Jml. Real</th>
                    <th width='50px'>Tgl. Real</th>
                    <th width='20px'>No Slip</th>
                    <th width='20px'>Jumlah</th>
                    <th width='20px'>ID</th>
                    <th width='20px'>Tanggal</th>
                    <th width='20px'>Tgl. Trans</th>
                    <th width='20px'>Cabang</th>
                    <th width='20px'>Alokasi</th>
                    <th>Keterangan</th>
                    
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp02 order by nama_cabang, brotcid";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pbrid=$row['brotcid'];
                    $pnmcabang=$row['nama_cabang'];
                    $ptglbr=$row['tglbr'];
                    $ptgltrans=$row['tgltrans'];
                    
                    $pnmkode=$row['nama_kode'];
                    $pnmsubkode=$row['nmsubpost'];
                    $pket1=$row['keterangan1'];
                    $pket2=$row['keterangan2'];
                    $pnoslip=$row['noslip'];
                    $ptglreal=$row['tglreal'];
                    if ($ptglreal=="0000-00-00") $ptglreal="";
                    
                    $pjumlah=$row['jumlah'];
                    $pjmlreal=$row['realisasi'];
                    
                    $ptglbr= date("d/m/Y", strtotime($ptglbr));
                    if ($ptgltrans=="0000-00-00") $ptgltrans="";
                    if (!empty($ptgltrans)) $ptgltrans= date("d/m/Y", strtotime($ptgltrans));
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    
                    $kettipeisi = "<input type='checkbox' value='$pbrid' name='chkbox_id[]' id='chkbox_id[$pbrid]' class='cekbr'>";
                    $kettipeisi="";
                    
                    $ptxt_jmlreal="<input style='text-align:right;' type='text' value='$pjmlreal' id='txt_njmlreal[$pbrid]' name='txt_njmlreal[$pbrid]' class='inputmaskrp2 inputbaya' size='10px' >";
                    $ptxt_tglreal="<input style='text-align:right;' type='date' value='$ptglreal' id='txt_ntglreal[$pbrid]' name='txt_ntglreal[$pbrid]' class='inputbaya' size='8px'>";
                    $ptxt_noslip="<input style='text-align:right;' type='text' value='$pnoslip' id='txt_nnoslip[$pbrid]' name='txt_nnoslip[$pbrid]' class='inputbaya' size='8px' Readonly>";
                    $ptxt_jumlah="<input style='text-align:right;' type='text' value='$pjumlah' id='txt_njumlah[$pbrid]' name='txt_njumlah[$pbrid]' class='inputmaskrp2 inputbaya' size='10px' Readonly>";
                    
                    $pchksama = "<input type='checkbox' value='$pbrid' name='chkbox_sama[]' id='chkbox_sama[$pbrid]' class='cekbr' onClick=\"SamakanJumlahReal('txt_njmlreal[$pbrid]', 'txt_njumlah[$pbrid]')\">";
                    
                    $btnsave = "<input type='button' class='btn btn-info btn-xs' value='Save Real' onClick=\"ProsesSimpanRealSatu('simpan', '$pbrid', 'txt_njmlreal[$pbrid]', 'txt_ntglreal[$pbrid]')\">";
                    $btndelete = "<input type='button' class='btn btn-danger btn-xs' value='Delete Real' onClick=\"ProsesSimpanRealSatu('hapus', '$pbrid', 'txt_njmlreal[$pbrid]', 'txt_ntglreal[$pbrid]')\">";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$kettipeisi</td>";
                    echo "<td nowrap>$btnsave $btndelete</td>";
                    echo "<td nowrap align='right'>$pchksama $ptxt_jmlreal</td>";
                    echo "<td nowrap>$ptxt_tglreal</td>";
                    echo "<td nowrap>$ptxt_noslip</td>";
                    echo "<td nowrap align='right'>$ptxt_jumlah</td>";
                    echo "<td nowrap>$pbrid</td>";
                    echo "<td nowrap>$ptglbr</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    echo "<td nowrap>$pnmkode</td>";
                    echo "<td nowrap>$pket1</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
        
        
        
    </div>
</form>

<style>

    .divnone {
        display: none;
    }
    #datatableotc {
        color:#000;
        font-family: "Arial";
    }
    #datatableotc th {
        font-size: 12px;
    }
    #datatableotc td { 
        font-size: 11px;
    }
</style>

<script>
    $(document).ready(function() {
        
        var dataTable = $('#datatableotc').DataTable({
            fixedHeader: false,
            "ordering": false,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "order": [[ 0, "asc" ]],
            "columnDefs": [
                { "visible": false },
                { className: "text-right", "targets": [3, 6] },//right
                { className: "text-nowrap", "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12] }//nowrap

            ],
            bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
            "bPaginate": true,
            "scrollY": 400,
            "scrollX": true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
</script>


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
</script>


<script>
    function ProsesSimpanRealSatu(iket, pid, pnmreal, pnmtglreal) {
        var ijmlreal=document.getElementById(pnmreal).value;
        var itglreal=document.getElementById(pnmtglreal).value;
        var itext="Hapus Data Realisasi...???";
        if (iket=="simpan") {
            itext="Simpan Data Realisasi...???";
            
            if (ijmlreal=="" || ijmlreal=="0") {
                alert("jumlah realisasi masih kosong...");
                return false;
            }

            if (itglreal=="") {
                alert("Tgl realisasi masih kosong...");
                return false;
            }
            
        }
        
        ok_ = 1;
        if (ok_) {
            var r = confirm(itext);
            if (r==true) {
                
                $.ajax({
                    type:"post",
                    url:"module/mod_br_entryotc/simpanrealisasiterima.php?module=simpanrealisasiterimaotc&act="+iket,
                    data:"uidbrotc="+pid+"&ujmlreal="+ijmlreal+"&utglreal="+itglreal,
                    success:function(data){
                        if (data.length>1) {
                            alert(data);
                        }
                        
                        if (iket=="hapus") {
                            //document.getElementById(pnmreal).value="";
                            //document.getElementById(pnmtglreal).value="";
                        }
                        
                    }
                });
                return 1;
                
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
            
        //alert(itext+" : "+pid+", "+ijmlreal+", "+itglreal);
        
    }
    function SamakanJumlahReal(pnmreal, pnmjml) {
        document.getElementById(pnmreal).value=document.getElementById(pnmjml).value;
    }
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>