<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />


<?PHP

$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$eperiode1 = date('01 F Y', strtotime($hari_ini));
$eperiode2 = date('t F Y', strtotime($hari_ini));
$idbr=$_GET['id'];
$noterakhir ="";
$act="input";


$query = "SELECT max(tgl) tgl FROM dbmaster.t_suratdana_br WHERE nomor='$idbr'";
$tampil= mysqli_query($cnmy, $query);
$ketemu= mysqli_num_rows($tampil);
$s= mysqli_fetch_array($tampil);
if (!empty($s['tgl'])) { $tgl1=date('d F Y', strtotime($s['tgl']));}



$query = "CALL dbmaster.spd_proses_bbk('$idbr')";
$tampil=mysqli_query($cnmy, $query) or die("error");
?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    


                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>NO. SPD <span class='required'></span></label>
                        <div class='col-md-4'>
                            <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal</label>
                        <div class='col-md-3'>
                            <div class='input-group date' id=''>
                                <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl1; ?>'>
                                <span class='input-group-addon'>
                                    <span class='glyphicon glyphicon-calendar'></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Terakhir <span class='required'></span></label>
                        <div class='col-md-4'>
                            <input type='text' id='e_noakhir' name='e_noakhir' class='form-control col-md-7 col-xs-12' value='<?PHP echo $noterakhir; ?>' onblur="TampilkanDataSPDDIV('e_noakhir')">
                            <div hidden><br/>
                            <input type="checkbox" id="chksama" value="deselect" 
                                   onClick="ShowDataTampilkan('')"/> Samakan No. BBK</div>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12'>No. BR/Divisi &nbsp;<input type="checkbox" id="chkbtnnodiv" value="deselect" onClick="SelAllCheckBox('chkbtnnodiv', 'chkbox_nodiv[]')" checked/><span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <div id="kotak-multi9">
                                <table border="0px">
                                <?PHP
                                $query = "select * from dbtemp.t_sp0 order by idinput, divisi, nodivisi";
                                $tampil=mysqli_query($cnmy, $query) or die("error");
                                while( $row=mysqli_fetch_array($tampil) ) {
                                    $cdivisi=$row['divisi'];
                                    if (empty($cdivisi)) $cdivisi = "ETHICAL";
                                    $cnodivisi=$row['nodivisi'];
                                    $cidinput=$row['idinput'];
                                    
                                    $cnmkode=strtolower($row['nama']);
                                    $cnmsub=strtolower($row['subnama']);
                                    
                                    $cjumlah=$row['rpjumlah'];
                                    
                                    //echo "<input type=checkbox value='$cidinput' id='$cidinput' name=chkbox_nodiv[] onclick=\"\" checked> $cdivisi - $cnodivisi &nbsp; &nbsp; ($cnmkode) &nbsp; &nbsp; <b>Rp. $cjumlah</b><br/>";
                                    echo "<tr>";
                                    echo "<td><input type=checkbox value='$cidinput' id='$cidinput' name=chkbox_nodiv[] onclick=\"\" checked></td>";
                                    echo "<td>$cdivisi &nbsp;&nbsp;</td>";
                                    echo "<td>$cnodivisi &nbsp;&nbsp;</td>";
                                    echo "<td>$cnmkode &nbsp;&nbsp;</td>";
                                    echo "<td><b>Rp. $cjumlah</b></td>";
                                    echo "</tr>";
                                }
                                ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                        <div class='col-md-4'>
                            <button type='button' class='btn btn-info btn-xs' onclick="ShowDataTampilkan('')">Tampilkan Data</button> <span class='required'></span>
                        </div>
                    </div>
                    
                    <div id="div_isitabel">
                        
                        
                    </div>



                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                        <div class='col-xs-9'>
                            <div class="checkbox">
                                <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                            </div>
                        </div>
                    </div>
                                
                                

                    

                </div>
            </div>
            
           
        
            
        </form>
        
    </div>
    <!--end row-->
</div>


<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
    .ui-datepicker-calendar2 {
        display: none;
    }
</style>


<style>
    .divnone {
        display: none;
    }
    #datatablespdbbk th {
        font-size: 13px;
    }
    #datatablespdbbk td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

<script>
    
    $(function() {
        $('#e_tglberlaku').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                ShowNoUrutBBK();
                TampilkanDataSPDDIV('');
            } 
        });
    });
    
    
    $(document).ready(function() {
        TampilkanDataSPDDIV('');
        ShowNoUrutBBK();
        
        var dataTable = $('#datatablespdbbk').DataTable( {
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "searching": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [4] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 280,
            "scrollX": true/*,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true*/
        } );
    } );
    
    
    function ShowNoUrutBBK(){
        var itgl = document.getElementById('e_tglberlaku').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=viewnourutbbk",
            data:"utgl="+itgl,
            success:function(data){
                document.getElementById('e_noakhir').value=data;
            }
        });
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
        
        var mycek="";
        for (var i in checkboxes){
            if (checkboxes[i].checked) {
                mycek=mycek+"'"+checkboxes[i].value+"',";
            }
        }
        
        ShowDataTampilkan();
        

            
    }
    
    function ShowDataTampilkan(){
        TampilkanDataSPDDIV('');
        ShowNoUrutBBK();
    }
    function TampilkanDataSPDDIV(noterakhir){
        if (noterakhir=="") {
            var inoakhir = "";
        }else{
            var inoakhir = document.getElementById(noterakhir).value;
        }
        
        var isama = document.getElementById('chksama').checked;
        var ichksama = "Y";
        if (isama==false) ichksama = "N";
        
        var itgl = document.getElementById('e_tglberlaku').value;
        var nnoterakhir = document.getElementById('e_noakhir');
        var chk_arr =  document.getElementsByName("chkbox_nodiv[]");
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
            $("#div_isitabel").html("");
            return 0;
        }
        
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=showdatanospdnodiv&pilih=nobbk",
            data:"ucekbox="+allnobr+"&unoakhir="+nnoterakhir+"&utgl="+itgl+"&unoakhir="+inoakhir+"&uchksama="+ichksama,
            success:function(data){
                $("#div_isitabel").html(data);
            }
        });
    }
    
    
    function disp_confirm(pText_)  {
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                var act = urlku.searchParams.get("act");
                
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_suratpd/aksi_simpanbbk.php?module="+module+"&act="+act+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function SimpanHapusData(eact, eidinput, eurutan, enomor) {
        
        var pidinput =document.getElementById(eidinput).value;
        var purutan =document.getElementById(eurutan).value;
        
        
        var pText_="Simpan";
        var pmodule="simpandatanobbk";
        if (eact=="hapus") {
            var pText_="Hapus";
            var pmodule="hapusdatanobbk";
            
            var pnobbk = "";
        }else{
            var pnobbk =document.getElementById(enomor).value;
        }
        
        
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
                    url:"module/mod_br_suratpd/viewdata.php?module="+pmodule,
                    data:"uidinput="+pidinput+"&uurutan="+purutan+"&unobbk="+pnobbk,
                    success:function(data){
                        alert(data);
                        ShowDataTampilkan();
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>