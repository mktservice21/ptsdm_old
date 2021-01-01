<script type="text/javascript">

function confSubmit(form) {
    if (confirm("Apakah akan melakukan approve data...?")) {
        form.submit();
    }else {
        return false;
    }
}
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
    
    
$(document).ready(function() {
    var table = $('#datatable').DataTable({
        <?PHP if ($_SESSION['MOBILE']=="Y") {?>
            fixedHeader: false,
        <?PHP } else {?>
            fixedHeader: true,
        <?PHP } ?>
        "ordering": false,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "displayLength": -1,
    } );


} );
</script>

<style>
    .divnone {
        display: none;
    }
    #per-kiri{float:left;width:30%; margin-right: 15px;}
    #per-kanan{float:left;width:30%; margin-right: 5px;}
</style>


<div class="">

    <div class="page-title"><div class="title_left"><h3>Approve Budget Request By SPV</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
                        <script>
                            function pilihData(modul, idmenu, act, tombol, periode1, periode2){
                                var per1=document.getElementById(periode1).value;
                                var per2=document.getElementById(periode2).value;
                                
                                var gmodule=document.getElementById(modul).value;
                                var gidmenu=document.getElementById(idmenu).value;
                                var gact=document.getElementById(act).value;
                                
                                $.ajax({
                                    type:"post",
                                    url:"module/mod_br_apvspv/viewdata.php?module=caridatabr",
                                    data:"umodule="+gmodule+"&uidmenu="+gidmenu+"&uact="+gact+"&buttonapv="+tombol+"&uperiode1="+per1+"&uperiode2="+per2,
                                    success:function(data){
                                        $("#c-data").html(data);
                                    }
                                });
                            }
                            function ProsesData(modul, idmenu, act, cekbr){
                                var cmt = confirm('Apakah akan melakukan unapprove...?');
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
                                var gmodule=document.getElementById(modul).value;
                                var gidmenu=document.getElementById(idmenu).value;
                                var gact=document.getElementById(act).value;
                                
                                $.ajax({
                                    type:"post",
                                    url:"module/mod_br_apvspv/aksi_brapvspv.php?module=caridatabr"+"&module="+gmodule+"&idmenu="+gidmenu+"&act="+gact,
                                    data:"umodule="+gmodule+"&uidmenu="+gidmenu+"&uact="+gact+"&chkbox_br="+allnobr+"&buttonapv=unapprove&e_stsapv=unapprove",
                                    success:function(data){
                                        TampilData();
                                    }
                                });
                            }
                            
                            function TampilData(){

                                var per1=document.getElementById('tgl01').value;
                                var per2=document.getElementById('tgl02').value;

                                var gmodule=document.getElementById('e_stsapv').value;
                                var gidmenu=document.getElementById('g_idmenu').value;
                                var gact=document.getElementById('g_act').value;
                                $.ajax({
                                    type:"post",
                                    url:"module/mod_br_apvspv/viewdata.php?module=caridatabr",
                                    data:"umodule="+gmodule+"&uidmenu="+gidmenu+"&uact="+gact+"&buttonapv="+gmodule+"&uperiode1="+per1+"&uperiode2="+per2,
                                    success:function(data){
                                        $("#c-data").html(data);
                                    }
                                });
                            }
                        </script>
        <?php
        
        $aksi="module/mod_br_apvspv/aksi_brapvspv.php";
        switch($_GET['act']){
            default:
                
                echo "<input type=\"hidden\" name=\"g_module\" id=\"g_module\" value=\"$_GET[module]\">";
                echo "<input type=\"hidden\" name=\"g_idmenu\" id=\"g_idmenu\" value=\"$_GET[idmenu]\">";
                echo "<input type=\"hidden\" name=\"g_act\" id=\"g_act\" value=\"$_GET[act]\">";

                echo "<form name='form1' id='form1' method='POST' action='$aksi?module=$_GET[module]&act=approve&idmenu=$_GET[idmenu]'"
                    . "enctype='multipart/form-data' onsubmit=\"return confirm('Apakah akan melalukan proses...?');\">";
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        /*
                        echo "<div class='x_title'><h2>";
                            echo "<input onclick=\"pilihData('g_module', 'g_idmenu', 'e_stsapv', 'approve','tgl01','tgl02')\" class='btn btn-default' type='button' name='buttonview1' value='Belum Approve'>";
                            echo "<input onclick=\"pilihData('g_module', 'g_idmenu', 'e_stsapv', 'unapprove','tgl01','tgl02')\" class='btn btn-default' type='button' name='buttonview2' value='Sudah Approve'>";
                            echo "<input onclick=\"pilihData('g_module', 'g_idmenu', 'e_stsapv', 'pending','tgl01','tgl02')\" class='btn btn-default' type='button' name='buttonview3' value='Pending'>";
                            echo "<input onclick=\"pilihData('g_module', 'g_idmenu', 'e_stsapv', 'reject','tgl01','tgl02')\" class='btn btn-default' type='button' name='buttonview4' value='Reject'>";
                        echo "</h2><div class='clearfix'></div></div>";
                        */
                        $hari_ini = date("Y-m-d");
                        $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                        $tgl_akhir = date('d F Y', strtotime($hari_ini));
                        ?>
                        <div class="well" style="overflow: auto">
                            <input onclick="pilihData('g_module', 'g_idmenu', 'e_stsapv', 'approve','tgl01','tgl02')" class='btn btn-default' type='button' name='buttonview1' value='Belum Approve'>
                            <input onclick="pilihData('g_module', 'g_idmenu', 'e_stsapv', 'unapprove','tgl01','tgl02')" class='btn btn-default' type='button' name='buttonview2' value='Sudah Approve'>
                            <input onclick="pilihData('g_module', 'g_idmenu', 'e_stsapv', 'pending','tgl01','tgl02')" class='btn btn-default' type='button' name='buttonview3' value='Pending'>
                            <input onclick="pilihData('g_module', 'g_idmenu', 'e_stsapv', 'reject','tgl01','tgl02')" class='btn btn-default' type='button' name='buttonview4' value='Reject'>
                        </div>
                        
                        <div class='col-sm-3'>
                            Periode
                            <div class="form-group">
                                <div class='input-group date' id='tgl01'>
                                    <input type='text' id='tgl01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                         <div class='col-sm-3'>
                            <small>s/d.</small>
                            <div class="form-group">
                                <div class='input-group date' id='tgl02'>
                                    <input type='text' id='tgl02' name='e_periode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                                            
                        <?PHP
                        /*
                        //panel
                        echo "<div class='x_panel'>";
                            $hari_ini = date("Y-m-d");
                            $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                            $gtgl_akhir = date('d F Y', strtotime($hari_ini));
                            
                            
                            echo "<div class='x_content form-horizontal form-label-left'>";
                                echo "<div class='form-group'>";
                                echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                    <div id='per-kiri'>
                                    Peiode : <span class='input-group'>
                                    <input type='text' id='tgl01' name='e_periode01' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='$tgl_pertama' placeholder='dd mmm yyyy' Readonly>
                                    </span>
                                    </div>
                                    <div id='per-kanan'>
                                    s/d. : <span class='input-group'>
                                    <input type='text' id='tgl02' name='e_periode02' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='$gtgl_akhir' placeholder='dd mmm yyyy' Readonly>
                                    </span>
                                    </div>
                                    </div>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";//end panel
                        */
                
                
                        //isi content
                        echo "<div id='c-data'>";
                        
                            echo "<div class='x_content'>";
                                
                                echo "<table id='datatable' class='table table-striped table-bordered'>";
                                echo "<thead><tr>";
                                echo "<th width='10px'>No</th>";
                                echo "<th width='10px'><input type=\"checkbox\" id=\"chkbtnbr\" value=\"select\" "
                                        . "onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" /></th>"
                                . "<th width='150px'>NOBR</th><th>Yang Mengajukan</th><th>Tgl. Perlu</th>"
                                . "<th>Rp.</th><th width='150px'>Keterangan</th>";
                                echo "</tr></thead>";
                                echo "<tbody>";
                                $no=1;
                                
                                $query = "SELECT * FROM dbbudget.v_br ";
                                if (strtoupper($_GET['act'])=="REJECT") {
                                    $query .=" ";
                                }elseif (strtoupper($_GET['act'])=="UNAPPROVE") {
                                    $query .=" where NOBR in (select distinct ifnull(NOBR,'') from dbbudget.t_br_ttd)";
                                }else{
                                    $query .=" where NOBR not in (select distinct ifnull(NOBR,'') from dbbudget.t_br_ttd)";
                                }
                                $query .=" order by NOBR";
                                
                                $tampil = mysqli_query($cnmy, $query);
                                while ($r=mysqli_fetch_array($tampil)){

                                    $rp=number_format($r['RP'],0,",",",");
                                    $tglperlu = date('d F Y', strtotime($r['TGL_PERLU']));
                                    echo "<tr scope='row'>";
                                    echo "<td>$no</td>";
                                    echo "<td><input type=checkbox value='$r[NOBR]' name=chkbox_br[]></td>";
                                    if (strtoupper($_GET['act'])=="REJECT" or strtoupper($_GET['act'])=="UNAPPROVE") {
                                        echo "<td>";
                                        ?><a href="#" class='btn btn-success btn-sm' data-toggle='modal' 
                                           onClick=window.open("<?PHP echo "eksekusi_ttd.php?module=$_GET[module]&idmenu=$_GET[idmenu]&act=unapprove&nobr=".$r['NOBR'];?>","Ratting","width=600,height=200,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes");>
                                            <?PHP echo $r['NOBR']; ?></a><?PHP
                                        echo "</td>";
                                    }else{
                                        echo "<td>";
                                        ?><a href="#" class='btn btn-success btn-sm' data-toggle='modal' 
                                           onClick=window.open("<?PHP echo "eksekusi_ttd.php?module=$_GET[module]&idmenu=$_GET[idmenu]&act=approve&nobr=".$r['NOBR'];?>","Ratting","width=600,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes");>
                                            <?PHP echo $r['NOBR']; ?></a><?PHP
                                        echo "</td>";
                                    }
                                    //echo "<td>$r[NOBR]</td>";
                                    echo "<td>$r[nama]</td>";
                                    echo "<td>$tglperlu</td>";
                                    echo "<td align='right'>$rp</td>";
                                    echo "<td>$r[KETERANGAN]</td>";
                                    echo "</tr>";

                                    $tampilakun = mysqli_query($cnmy, "SELECT * FROM dbbudget.v_br_d where NOBR='$r[NOBR]' order by NOBR");
                                    while ($a=mysqli_fetch_array($tampilakun)){
                                        $jml=number_format($a['JUMLAH'],0,",",",");
                                        echo "<tr scope='row'>";
                                        echo "<td colspan=2></td>";
                                        echo "<td class='divnone'></td>";
                                        echo "<td colspan=3>$a[NAMA_AKUN]</td>";
                                        echo "<td class='divnone'></td>";
                                        echo "<td class='divnone'></td>";
                                        echo "<td align='right'>$jml</td>";
                                        echo "<td>$a[KET]</td>";
                                        echo "</tr>";
                                    }

                                    $no++;
                                }
                                echo "</tbody>";
                                echo "</table>";
                            echo "</div>";

                            echo "<div class='x_title'><h2>";
                                if (strtoupper($_GET['act'])=="REJECT") {
                                    echo "<input type='hidden' name='e_stsapv' id='e_stsapv' value='reject'>";
                                }elseif (strtoupper($_GET['act'])=="UNAPPROVE") {
                                    echo "<input type='hidden' name='e_stsapv' id='e_stsapv' value='unapprove'>";
                                    echo "<input class='btn btn-default' type='button' name='buttonapv' value='UnApprove' onClick=\"ProsesData('g_module', 'g_idmenu', 'e_stsapv', 'chkbox_br[]')\">";
                                }else {
                                    echo "<input type='hidden' name='e_stsapv' id='e_stsapv' value='approve'>";
                                    //echo "<input class='btn btn-default' type='button' name='buttonapv' value='Approve' onClick=\"confSubmit(this.form);\">";
                                    //echo "<input class='btn btn-default' type='submit' name='buttonapv' value='Approve'>";
                                    echo "<input class='btn btn-default' type='submit' name='buttonapv' value='Reject'>";
                                }
                            echo "</h2><div class='clearfix'></div></div>";


                                    /*
                                    echo "<td>";//AKSI
                                        echo " <a class='btn btn-success btn-sm' href=?module=$_GET[module]&idmenu=$_GET[idmenu]&act=editdata&id=$r[NOBR]>Edit</a>
                                            <a class='btn btn-danger btn-sm' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[NOBR]&idmenu=$_GET[idmenu]\"
                                            onClick=\"return confirm('Apakah Anda benar-benar akan menghapusnya?')\">Hapus</a>";
                                    echo "</td>";
                                     */
                            if (strtoupper($_GET['act'])=="REJECT") {
                            }elseif (strtoupper($_GET['act'])=="UNAPPROVE") {
                            }else{
                                echo "<div class='col-sm-5'>";
                                include "tanda_tangan_base64/tanda_tangan_semua.php";
                                echo "</div>";
                            }
                        echo "</div>";//end panel
                    
                    echo "</div>";//c-data

                echo "</div>";
                echo "</form>";
                ?>
                        <!--
                        <script>
                            function saveData(){
                                var chk_arr =  document.getElementsByName("chkbox_br[]");
                                var chklength = chk_arr.length;             

                                for(k=0;k< chklength;k++)
                                {
                                    if (chk_arr[k].checked == true) {
                                        alert(chk_arr[k].value);
                                    }
                                } 
                            } 
                        </script>
                        <input type="button" onclick="saveData()" value="Save">
                        -->
                <?PHP
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
