<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $pmodule="saldosuratdana";
    $pidmenu="204";
    $hari_ini = date("Y-m-d");
    
    $cket = $_POST['uinput'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $isitipe=$_POST['uisi'];
    
    
    $_SESSION['STPDTIPE'] = $isitipe;
    $_SESSION['STPDPERENTY1'] = $mytgl1;
    $_SESSION['STPDPERENTY2'] = $mytgl2;
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
    $ptanggal= date("d F Y", strtotime($hari_ini));
    
    if ($cket=="1") {
        
    }
    
?>
<script src="js/inputmask.js"></script>

<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
        
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <?PHP if ($cket=="1") { ?>
                    
                        <div class='col-sm-3'>
                            Tanggal
                           <div class="form-group">
                                <div class='input-group date' id='mytgl02x'>
                                    <input type="text" class="form-control" id='e_tglberlaku' name='e_tanggal' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo "$ptanggal"; ?>'>
                                    <span class='input-group-addon'>
                                        <span class='glyphicon glyphicon-calendar'></span>
                                    </span>
                                </div>
                           </div>
                       </div>

                        <div class='col-sm-3'>
                            No. SPD
                           <div class="form-group">
                                <input type='text' id='e_nomor' name='e_nomor' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo ""; ?>'>
                           </div>
                       </div>

                        <div class='col-sm-3'>
                            <button type='button' class='btn btn-info btn-xs' onclick='HitungTotalDariCekBox()'>Hitung Jumlah</button> <span class='required'></span>
                           <div class="form-group">
                                <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo ""; ?>' Readonly>
                           </div>
                       </div>


                        <div class='col-sm-3'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Save" onclick='disp_confirm("simpan", "chkbox_br[]")'>
                           </div>
                       </div>
                    <?PHP }elseif ($cket=="2") { ?>
                    
                        <div class='col-sm-3'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Hapus No. SPD" onclick='disp_confirm("hapus", "chkbox_br[]")'>
                           </div>
                       </div>
                    
                    <?PHP } ?>
                    
                </div>
            </div>
            
        
            <div class="title_left">
                <h4 style="font-size : 12px;">
                    <?PHP
                        $noteket = strtoupper($cket);
                        $text="Data Yang Belum Proses";
                        if ($noteket=="2") $text="Data Yang Sudah Proses";
                        echo "<b>$text</b>";
                    ?>
                </h4>
            </div><div class="clearfix">
        </div>
        <table id='datatableapvcaisi' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='7px'>No</th>
                    <th width='7px'></th>
                    <th width='40px'>Divisi</th>
                    <th width='50px'>No. BR</th>
                    <?PHP if ($cket=="1") { ?>
                        <th width='40px'>Tgl. Pengajuan</th>
                    <?PHP }else{ ?>
                        <th width='40px'>Tgl. SPD</th>
                    <?PHP } ?>
                    <th width='50px'>Jumlah</th>
                    <th width='80px'>No. SPD</th>
                    <th width='50px'>Kode</th>
                    <th width='30px'>Sub</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $finput = " AND IFNULL(userproses,'')='' ";
                if ($cket=="2") $finput = " AND IFNULL(userproses,'')<>'' ";
                $no=1;
                $sql = "SELECT a.idinput, a.tglinput, DATE_FORMAT(a.tgl,'%d/%m/%Y') as tgl, "
                        . " a.divisi, a.kodeid, b.nama, a.subkode, b.subnama, FORMAT(a.jumlah,0,'de_DE') as jumlah,"
                        . " a.nomor, a.nodivisi, a.pilih, a.karyawanid, a.jenis_rpt, DATE_FORMAT(a.tglspd,'%d/%m/%Y') as tglspd "
                        . " FROM dbmaster.t_suratdana_br a LEFT JOIN dbmaster.t_kode_spd b ON "
                        . " a.kodeid=b.kodeid AND a.subkode=b.subkode ";
                $sql.=" WHERE a.stsnonaktif <> 'Y' AND a.pilih='Y' $finput ";
                $sql.=" AND Date_format(a.tgl, '%Y-%m') between '$tgl1' and '$tgl2' ";
                $sql.=" ORDER BY a.tglinput ASC, a.divisi";
                $query = mysqli_query($cnmy, $sql);
                while( $row=mysqli_fetch_array($query) ) {
                    $nestedData=array();
                    $idno=$row['idinput'];
                    $pkaryawanid=$row['karyawanid'];
                    $pdivisi=$row['divisi'];
                    $pnama=$row['nama'];
                    $psubnama=$row['subnama'];
                    $pnomor=$row['nomor'];
                    
                    $ptgl=$row['tgl'];
                    if ($cket=="2") {
                        if (!empty($row['tglspd']))
                            $ptgl=$row['tglspd'];
                    }
                    
                    $pjumlah=$row['jumlah'];
                    $ndiviotc=$row["nodivisi"];
                    $pkode=$row["kodeid"];
                    $psubkode=$row["subkode"];
                    $pjenisrpt=$row["jenis_rpt"];
                    $nourut = "";

                    $pmystsyginput="";
                    if ($pkaryawanid=="0000000566") {
                        $pmystsyginput=1;
                    }elseif ($pkaryawanid=="0000001043") {
                        $pmystsyginput=2;
                    }else{

                        if ($pkode=="1" AND $psubkode=="03") {//ria
                            $pmystsyginput=3;
                        }elseif ($pkode=="2" AND $psubkode=="21") {//marsis
                            $pmystsyginput=4;
                        }

                    }

                    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$idno'>Edit</a>";
                    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">";

                    $plihat="<a class='btn btn-info btn-xs' href='?module=$pmodule&act=lihatdata&idmenu=$pidmenu&nmun=$pidmenu&id=$idno'>Lihat</a>";

                    $plihat="";
                    $plihatview="";
                    $plihatexcel="";

                    if ($pdivisi=="OTC") {
                        $plihat="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=lapbrotcpermo&act=input&idmenu=134&ket=bukan&ispd=$idno' target='_blank'>View</a>"
                                . " <a class='btn btn-info btn-xs' href='eksekusi3.php?module=lapbrotcpermo&act=input&idmenu=134&ket=excel&ispd=$idno' target='_blank'>Excel</a>";
                        if ($pjenisrpt=="L") $plihat="";
                        
                    }else{
                        if ($pmystsyginput==1) {
                            $pedit="";
                            $plihatview="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=rptsby&idmenu=$pidmenu&ket=bukan&ispd=$idno&iid=$pmystsyginput' target='_blank'>Rpt. SBY</a>
                                <a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=rptsby&idmenu=$pidmenu&ket=excel&ispd=$idno&iid=$pmystsyginput' target='_blank'>Excel Rpt. SBY</a><br/>";

                            $plihatexcel="<a class='btn btn-success btn-xs' href='eksekusi3.php?module=$pmodule&act=rekapbr&idmenu=$pidmenu&ket=bukan&ispd=$idno&iid=$pmystsyginput' target='_blank'>Rekap</a>
                                <a class='btn btn-success btn-xs' href='eksekusi3.php?module=$pmodule&act=rekapbr&idmenu=$pidmenu&ket=excel&ispd=$idno&iid=$pmystsyginput' target='_blank'>Excel Rekap</a>";
                        }elseif ($pmystsyginput==2) {
                            $pedit="";
                            if ($pjenisrpt=="D") {
                                $plihatview = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=viewbrklaim&idmenu=$pidmenu&ket=bukan&ispd=$idno&iid=$pmystsyginput' target='_blank'>View</a>";
                                $plihatexcel = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=viewbrklaim&idmenu=$pidmenu&ket=excel&ispd=$idno&iid=$pmystsyginput' target='_blank'>Excel</a>";
                            }else{
                                $plihatview = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=viewbr&idmenu=$pidmenu&ket=bukan&ispd=$idno&iid=$pmystsyginput' target='_blank'>View</a>";
                                $plihatexcel = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=viewbr&idmenu=$pidmenu&ket=excel&ispd=$idno&iid=$pmystsyginput' target='_blank'>Excel</a>";
                            }
                        }elseif ($pmystsyginput==3) {
                            $plihatview="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=rekapbiayarutin&act=input&idmenu=190&ket=bukan&ispd=$idno' target='_blank'>View</a>";
                            $plihatexcel="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=rekapbiayarutin&act=input&idmenu=190&ket=excel&ispd=$idno' target='_blank'>Excel</a>";
                        }elseif ($pmystsyginput==4) {
                            $plihatview="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=rekapbiayaluar&act=input&idmenu=187&ket=bukan&ispd=$idno' target='_blank'>View</a>";
                            $plihatexcel="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=rekapbiayaluar&act=input&idmenu=187&ket=excel&ispd=$idno' target='_blank'>Excel</a>";
                        }else{
                            if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {
                                $pedit="";
                                $plihatview = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=suratpd&act=viewbrho&idmenu=204&ket=bukan&ispd=$idno' target='_blank'>View</a>";
                                $plihatexcel = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=suratpd&act=viewbrho&idmenu=204&ket=excel&ispd=$idno' target='_blank'>Excel</a>";
                            }
                        }
                        $plihat="$plihatview $plihatexcel";
                    }


                    if ($pdivisi=="OTC") {
                        if ($row["pilih"]=="N") $ndiviotc="<div style='color:red;'>$ndiviotc</div>";
                    }
                    
                    
                    if ($cket=="2"){
                        $pnomor="<a title='Print / Cetak' href='#' class='btn btn-primary btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=suratpd&brid=$pnomor&iprint=print',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "$pnomor</a>";
                    }
                    
                    $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[] onclick=\"HitungTotalDariCekBox()\">";
                    
                    $plihat="";
                    echo "<tr>";
                    echo "<td nowrap>$cekbox</td>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$plihat</td>";
                    echo "<td nowrap>$pdivisi</td>";
                    echo "<td nowrap>$ndiviotc</td>";
                    echo "<td nowrap>$ptgl</td>";
                    echo "<td nowrap>$pjumlah</td>";
                    echo "<td nowrap>$pnomor</td>";
                    echo "<td>$pnama</td>";
                    echo "<td>$psubnama</td>";
                    
                    echo "</tr>";
                    
                    $no=$no+1;
                }
                
                ?>
            </tbody>
        </table>
    </div>
    
</form>

<script>
    $(document).ready(function() {
        <?PHP if ($cket=="1") { ?>
            ShowNoSPD();
        <?PHP } ?>
        var dataTable = $('#datatableapvcaisi').DataTable( {
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }/*,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true*/
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
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
        
        HitungTotalDariCekBox();
        
    }
    
    function disp_confirm(ket, cekbr){
        if (ket=="simpan") {
            var ijml =document.getElementById('e_jmlusulan').value;
            if(ijml==""){
                ijml="0";
            }
            if (ijml=="0") {
                alert("jumlah masih kosong...");
                return false;
            }
        }
        
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        if (ket=="simpan") {
            var itgl = document.getElementById('e_tglberlaku').value;
            var inospd = document.getElementById('e_nomor').value;
        }else if (ket=="hapus") {
            var itgl = "";
            var inospd = "";
        }
        
        var txt="";
        if (ket=="reject" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
        
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/aksi_suratpd.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket="+ket+"&unobr="+allnobr+"&utgl="+itgl+"&unospd="+inospd+"&ketrejpen="+txt,
            success:function(data){
                if (ket=="simpan") {
                    TampilData('1');
                }else{
                    TampilData('2');
                }
                alert(data);
            }
        });
        
    }
    

    $(function() {
        $('#e_tglberlaku').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                ShowNoSPD();
            } 
        });
    });
    
    function ShowNoSPD() {
        var itgl = document.getElementById('e_tglberlaku').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=viewnomorspd",
            data:"utgl="+itgl,
            success:function(data){
                document.getElementById('e_nomor').value=data;
            }
        });
    }
    
    
    function HitungTotalDariCekBox() {
        document.getElementById('e_jmlusulan').value="0";
        
        var chk_arr1 =  document.getElementsByName('chkbox_br[]');
        var chklength1 = chk_arr1.length; 

        var allnobr="";
        var TotalPilih=0;

        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');
                allnobr =allnobr + "'"+fields[0]+"',";
                TotalPilih++;
            }
        }
        
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            //alert("tidak ada data yang dipilih...");
            //return false;
        }
        //$("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=hitungtotalcekboxspd",
            data:"unoidbr="+allnobr,
            success:function(data){
                //$("#loading2").html("");
                document.getElementById('e_jmlusulan').value=data;
            }
        });

    }
            
</script>

<style>
    .divnone {
        display: none;
    }
    #datatableapvcaisi th {
        font-size: 13px;
    }
    #datatableapvcaisi td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

