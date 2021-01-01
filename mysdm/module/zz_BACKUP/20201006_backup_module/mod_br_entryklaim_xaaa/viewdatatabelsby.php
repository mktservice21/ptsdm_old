    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />

    <script src="js/inputmask.js"></script>
    
<?PHP
    session_start();
    
    $_SESSION['FINKLMTIPE']=$_POST['utipeproses'];
    $_SESSION['FINKLMTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['FINKLMPERENTY1']=$_POST['uperiode1'];
    $_SESSION['FINKLMPERENTY2']=$_POST['uperiode2'];
    
    
    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    $divisi=$_POST['udivisi'];
    $uidcard=$_SESSION['USERID'];
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('d/mm/Y', strtotime($hari_ini));
    
    include "../../config/koneksimysqli_it.php";
    include "../../config/koneksimysqli.php";

?>

<form method='POST' action='<?PHP echo "?module='$_GET[module]'&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='entrybrklaim' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='106' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'>
        
        
        <table id='datatableklaim1' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th>Jumlah</th>
                    <th>Rpt. SBY</th>
                    <th width='60px'>Tgl. Rpt. SBY</th>
                    <th width='50px'>Noslip</th>
                    <th width='100px'>Supplier</th>
                    <th width='80px'>Yg Membuat</th>
                    <th width='50px'>Realisasi</th>
                    <th nowrap>Keterangan</th>
                    <th></th>
                    <th>ID</th>

                </tr>
            </thead>
            <tbody>
                <?PHP
                $sql = "select a.DIVISI divisi, a.klaimId, a.karyawanid, c.nama nama_karyawan,
                    a.distid, b.nama nama_dist, a.aktivitas1, a.jumlah, a.tgl, a.tgltrans, 
                    a.realisasi1, a.noslip, a.COA4, d.NAMA4, a.sby, a.tglrpsby 
                    from hrd.klaim a LEFT JOIN MKT.distrib0 b on a.distid=b.distid 
                    LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanId 
                    LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4
                    WHERE a.klaimId NOT IN (select DISTINCT IFNULL(klaimId,'') FROM hrd.klaim_reject) ";
                
                $filtipe="Date_format(a.tgl, '%Y-%m-%d')";
                if ($tgltipe=="2") $filtipe="Date_format(a.tgltrans, '%Y-%m-%d')";
                if ($tgltipe=="3") $filtipe="Date_format(a.tglrpsby, '%Y-%m-%d')";
                $sql.=" and $filtipe between '$tgl1' and '$tgl2' ";
                if (!empty($divisi)) $sql.=" and a.divprodid='$divisi' ";
               

                //echo $sql;
                $userid=$_SESSION['USERID'];
                $now=date("mdYhis");
                $tmp01 =" dbtemp.DSETHSBY01_".$userid."_$now ";
                
                $query = "create TEMPORARY table $tmp01 ($sql)"; 
                mysqli_query($cnmy, $query);
                
                $ntno=1;
                $gtotal=0;
                $query ="select distinct IFNULL(tgltrans,'0000-00-00') tgltrans from $tmp01 ORDER BY tgltrans";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $ntgltrans=$row1['tgltrans'];
                    
                    $ptgltrans = "";
                    if (!empty($row1['tgltrans']) AND $row1['tgltrans']<> "0000-00-00")
                        $ptgltrans =date("d-M-Y", strtotime($row1['tgltrans']));
                    
                    $nnamaid="chk_sby".$ntno."[]";
                    $ptxtsbyall="<input type='checkbox' id='chk_ntsby$ntno' name='chk_ntsby$ntno' class='input' value='select' onclick=\"SelAllCheckBox('chk_ntsby$ntno', '$nnamaid')\">";
                    
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b>Tgl. Trans : </b></td>";
                    echo "<td nowrap><b>$ptgltrans $ptxtsbyall</b></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "</tr>";
                    
                    $query ="select * from $tmp01 where IFNULL(tgltrans,'0000-00-00')='$ntgltrans' ORDER BY tgltrans";
                    $no=1;
                    $ptotjumlah=0;
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $dok=$row["nama_dist"];

                        $ptglsby = "";
                        if (!empty($row['tglrpsby']) AND $row['tglrpsby']<> "0000-00-00")
                            $ptglsby =date("d-M-Y", strtotime($row['tglrpsby']));

                        $ptgltrans = "";
                        if (!empty($row['tgltrans']) AND $row['tgltrans']<> "0000-00-00")
                            $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                        $ptglinput =date("d-M-Y", strtotime($row['tgl']));
                        
                        $tojumlah = $row["jumlah"];
                        $ptotjumlah=$ptotjumlah+$tojumlah;
                        
                        $pjumlah = $row["jumlah"];
                        $pjumlah=number_format($pjumlah,0,",",",");
                        
                        $paktivitas = $row["aktivitas1"];
                        $pnmrealisasi = $row["realisasi1"];
                        $pnoslip = $row["noslip"];
                        $pnmkaryawan = $row["nama_karyawan"];
                        $psby = $row["sby"];
                        $chkrptsby="";
                        $ndisable="";
                        
                        $pbrid = $row["klaimId"];

                        $ptxtnobrid="<input type='hidden' size='10px' id='e_nobrid$no' name='e_nobrid$no' class='input-sm' autocomplete='off' value='$pbrid'>";
                        
                        if ($psby=="Y") {
                            $chkrptsby="checked";
                            $ndisable="disabled";
                            $nnamaid="";
                        }else{
                            $nnamaid="chk_sby".$ntno."[]";
                        }
                        $ptxtsby="<input type='checkbox' id='$nnamaid' name='$nnamaid' class='input' value='$pbrid' $chkrptsby $ndisable>";

                        $fsimpan="'e_nobrid$no', 'e_tglsby$no', 'chk_sby$no'";
                        $simpandata= "<input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save Real' onclick=\"SimpanData('input', $fsimpan)\">";
                        $pedit = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$pbrid'>Edit</a>";
                        $phapus = "<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesData('hapus', '$pbrid')\">";
                        
                        echo "<tr>";
                        echo "<td nowrap>$no $ptxtnobrid</td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        echo "<td nowrap>$ptxtsby</td>";
                        echo "<td nowrap>$ptglsby</td>";

                        echo "<td nowrap>$pnoslip</td>";
                        echo "<td>$dok</td>";
                        echo "<td nowrap>$pnmkaryawan</td>";
                        echo "<td nowrap>$pnmrealisasi</td>";
                        echo "<td >$paktivitas</td>";
                        echo "<td nowrap>$pedit $phapus</td>";
                        echo "<td nowrap>$pbrid</td>";
                        echo "</tr>";

                        $no++;
                    }
                    $gtotal=$gtotal+$ptotjumlah;
                    $ptotjumlah=number_format($ptotjumlah,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b>Total : </b></td>";
                    echo "<td nowrap><b>$ptotjumlah</b></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "</tr>";
                    
                    $ntno++;
                }

                $gtotal=number_format($gtotal,0,",",",");
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'><b>Grand Total :</b></td>";
                echo "<td nowrap><b>$gtotal</b></b></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "</tr>";
                
                ?>
            </tbody>
        </table>
        <?PHP
        $ntno=$ntno;
        $ptxttotrec="<input type='hidden' size='10px' id='e_totrec' name='e_totrec' class='input-sm' autocomplete='off' value='$ntno'>";
        echo $ptxttotrec;
        ?>
        <br/>&nbsp;<br/>&nbsp;
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                <div class='col-sm-3'>
                    Tgl. Report SBY.
                   <div class="form-group">
                        <div class='input-group date' for='mytgl02'>
                            <input type="text" class="form-control" id='mytgl02' name='e_tgltrans' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl_pertama; ?>'>
                            <span class='input-group-addon'>
                                <span class='glyphicon glyphicon-calendar'></span>
                            </span>
                        </div>
                   </div>
               </div>

                <div class='col-sm-3'>
                    <small>&nbsp;</small>
                   <div class="form-group">
                       <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Save" onclick='disp_confirm("Simpan ?")'>
                   </div>
               </div>
                
            </div>
        </div>
        
    </div>
    
    
    
</form>
    
<?PHP
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
?>

<?PHP
    mysqli_close($cnmy);
    mysqli_close($cnit);
?>
    
<script>
    $(document).ready(function() {
        var dataTable = $('#datatableklaim1').DataTable( {
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
            "bPaginate": false/*,
            rowReorder: {
                selector: 'td:nth-child(5)'
            },
            responsive: true*/
        } );
    } );
    
    
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
                    url:"module/mod_br_entryklaim/aksi_simpanreal.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
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
    
    function disp_confirm(pText_){
        var pText_="Simpan";
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                var tglsby=document.getElementById('mytgl02').value;
                var jmlrec=document.getElementById('e_totrec').value;
                var chk_arr =  "";//document.getElementsByName('chk_sby1[]');
                var chklength = "";//chk_arr.length;
                var allnobr="";
                
                for(x=0;x< jmlrec;x++) {
                    var nm="chk_sby"+x+"[]";
                    chk_arr =  document.getElementsByName(nm);
                    chklength = chk_arr.length;
                    
                    for(k=0;k< chklength;k++)
                    {
                        if (chk_arr[k].checked == true) {
                            var kata = chk_arr[k].value;
                            var fields = kata.split('-');
                            allnobr =allnobr + "'"+fields[0]+"',";
                        }
                    }
                }
                
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var act = "inputsby";
                var idmenu = urlku.searchParams.get("idmenu");
                
                $.ajax({
                    type:"post",
                    url:"module/mod_br_entryklaim/aksi_simpansby.php?module="+module+"&act="+act+"&idmenu="+idmenu,
                    data:"uidbr="+allnobr+"&utglsby="+tglsby,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                        }else{
                            PilihData3();
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
                document.getElementById("demo-form2").action = "module/mod_br_entryklaim/aksi_entryklaim.php?kethapus="+txt+"&ket="+ket+"&id="+noid;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    }
    
    $('#mytgl01, #mytgl02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
    
</script>

<style>
    .divnone {
        display: none;
    }
    #datatableklaim1 th {
        font-size: 12px;
    }
    #datatableklaim1 td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>
