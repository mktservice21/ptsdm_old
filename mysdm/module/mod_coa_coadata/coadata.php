<script> 
function disp_confirm(pText_, ket)  {
    
    var elevel =document.getElementById('cb_level').value;
    var eid =document.getElementById('e_id').value;
    var enama =document.getElementById('e_nmcoa').value;
    
    if (elevel==""){
        alert("Level 4 / kelompok masih kosong....");
        return 0;
    }
    if (eid==""){
        alert("coa kode masih kosong....");
        document.getElementById('e_id').focus();
        return 0;
    }
    if (enama==""){
        alert("coa nama masih kosong....");
        document.getElementById('e_nmcoa').focus();
        return 0;
    }
  
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            var emodule =document.getElementById('u_module').value;
            var eact =document.getElementById('u_act').value;
            var eidmenu =document.getElementById('u_idmenu').value;
            
            if (ket=="update") {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_coa_coadata/aksi_coadata.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }else{
                $.ajax({
                    type:"post",
                    url:"module/mod_coa_coadata/aksi_coadata.php?module=carikodesama",
                    data:"ukode="+eid,
                    success:function(data){
                        var edata =data;
                        
                        if (edata=="") {
                            //document.write("You pressed OK!")
                            document.getElementById("demo-form2").action = "module/mod_coa_coadata/aksi_coadata.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                            alert("kode sudah ada...");
                        }
                    }
                });
            }
            
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}
</script>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Data COA <small> level 5</small></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_coa_coadata/aksi_coadata.php";
        switch($_GET['act']){
            default:
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                            onclick=\"window.location.href='?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru';\">";
                            if ($_SESSION['MOBILE']=="N")
                                echo "<a class='btn btn-default' href='eksekusi.php?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru' target='_blank'>Lihat Data COA</a>";
                        echo "<small></small>
                            </h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'>";

                            echo "<table id='datatable' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th width='260px'>Kelompok / Level 4</th><th width='60px'>Kode</th><th>Nama</th>"
                            . "<th width='20px'>Gol</th><th width='30px'>Aktif</th><th width='70px'>Aksi</th></tr></thead>";
                            echo "<tbody>";
                            $no=1;
                            $tampil = mysqli_query($cnmy, "SELECT c1.*, c2.*, c3.*, c4.*, c5.* FROM dbmaster.coa as c5 "
                                    . "left join dbmaster.coa_level4 as c4 on c5.COA4=c4.COA4 "
                                    . "left join dbmaster.coa_level3 as c3 on c4.COA3=c3.COA3 "
                                    . "left join dbmaster.coa_level2 as c2 on c3.COA2=c2.COA2 "
                                    . "left join dbmaster.coa_level1 as c1 on c2.COA1=c1.COA1 order by c1.COA1, c2.COA2, c3.COA3, c4.COA4, c5.COA_KODE");
                            while ($r=mysqli_fetch_array($tampil)){
                                echo "<tr scope='row'><td>$no</td>";
                                echo "<td>$r[COA4] - $r[NAMA4]</td>";
                                echo "<td>$r[COA_KODE]</td>";
                                echo "<td>$r[COA_NAMA]</td>";
                                echo "<td>$r[GOL]</td>";
                                echo "<td>$r[AKTIF]</td>";
                                echo "<td>";//AKSI
                                    echo " <a class='btn btn-success btn-xs' href=?module=$_GET[module]&idmenu=$_GET[idmenu]&act=editdata&id=$r[COA_KODE]>Edit</a>
                                        <a class='btn btn-danger btn-xs' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[COA_KODE]&idmenu=$_GET[idmenu]\"
                                        onClick=\"return confirm('Apakah Anda melakukan proses?')\">Aktif</a>";
                                echo "</td>";
                                echo "</tr>";
                                $no++;
                            }
                            echo "</tbody>";
                            echo "</table>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

            case "tambahbaru":
                ?> 
                    <script> window.onload = function() { document.getElementById("e_id").focus(); } </script>
                <?PHP
                $Tahun= date("Y");
                
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <small>Tambah Baru</small></h2>
                            <div class='clearfix'></div>
                            </div>";
                        
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>";
                        
                        //isi content
                        echo "<div class='x_content'><br/>";

                            //selalu ada
                            echo "<input type='hidden' id='u_module' name='u_module' value='$_GET[module]' Readonly>
                                <input type='hidden' id='u_idmenu' name='u_idmenu' value='$_GET[idmenu]' Readonly>
                                <input type='hidden' id='u_act' name='u_act' value='input' Readonly>";
                            //selalu ada


                            echo "<div class='form-group'>";
                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>LEVEL 4 <span class='required'>*</span></label>";
                            echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                                echo "<select class='form-control' id='cb_level' name='cb_level'>";
                                echo "<option value=''>-- Pilih --</option>";
                                $tampil=mysqli_query($cnmy, "SELECT COA4, NAMA4 FROM dbmaster.coa_level4 order by COA4");
                                while($a=mysqli_fetch_array($tampil)){
                                    echo "<option value='$a[COA4]'>$a[COA4] - $a[NAMA4]</option>";
                                }
                                echo "</select>";
                            echo "</div>";
                            echo "</div>";

                            echo "<div class='form-group'>";
                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>COA KODE <span class='required'>*</span></label>";
                            echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                <input type='text' id='e_id' name='id' required='required' autocomplete='off' class='form-control col-md-7 col-xs-12' data-inputmask=\"'mask' : '***-**-***'\">
                                </div>";
                            echo "</div>";

                            echo "<div class='form-group'>";
                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>NAMA <span class='required'>*</span></label>";
                            echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                <input type='text' id='e_nmcoa' name='e_nmcoa' required='required' class='form-control col-md-7 col-xs-12'>
                                </div>";
                            echo "</div>";
                        
                        /*
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Tipe <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                <div class='btn-group' data-toggle='buttons'>
                                    <input type='radio' name='rb_tipe' id='rb_tipe1' value='A' disabled='disabled'> Aktiva
                                    <input type='radio' name='rb_tipe' id='rb_tipe2' value='P' disabled='disabled'> Passiva
                                    <input type='radio' name='rb_tipe' id='rb_tipe3' value='R' disabled='disabled'> Rugi Laba
                                </div>
                            </div>";
                        echo "</div>";
                         * 
                         */
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Golongan <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                <div class='btn-group' data-toggle='buttons'>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol1' value='A'> A </label>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol2' value='B'> B </label>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol3' value='H'> H </label>
                                </div>
                            </div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_saldoawal'>Saldo Awal Tahun $Tahun</label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_saldoawal' name='e_saldoawal' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' >
                            </div>";
                        echo "</div>";
                        
                        
                            echo "<div class='ln_solid'></div>";
                            echo "<div class='form-group'>";
                            echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                                <button class='btn btn-primary' type='reset'>Reset</button>
                                <button type='button' class='btn btn-success' onclick=\"disp_confirm('Simpan ?', 'simpan')\">Save</button>
                                </div>";
                            echo "</div>";
                        
                        
                        echo "</div>";//end x_content
                            
                            
                            echo "<div class='x_content'><br/>";
                                echo "<div class='form-group'>";
                                echo "<div class='col-md-6 col-sm-6 col-xs-12'><b>Saldo Tahun $Tahun</b></div>";
                                echo "</div>";
                            echo "</div>";
                        
                        echo "<div class='col-md-6 col-xs-12'>";
                            echo "<div class='x_panel'>";
                              echo "<div class='x_content form-horizontal form-label-left'><br />";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_jan'>Januari </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_jan' name='e_jan' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_feb'>Februari </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_feb' name='e_feb' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_mar'>Maret </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_mar' name='e_mar' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_apr'>April </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_apr' name='e_apr' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_mei'>Mei </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_mei' name='e_mei' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_jun'>Juni </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_jun' name='e_jun' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                              echo "</div>";
                            echo "</div>";
                       echo "</div>";


                       echo "<div class='col-md-6 col-xs-12'>";
                            echo "<div class='x_panel'>";
                              echo "<div class='x_content form-horizontal form-label-left'><br />";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_jul'>Juli </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_jul' name='e_jul' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_agu'>Agustus </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_agu' name='e_agu' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_sep'>September </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_sep' name='e_sep' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_okt'>Oktober </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_okt' name='e_okt' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nov'>November </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_nov' name='e_nov' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_des'>Desember </label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_des' name='e_des' required='required' class='form-control col-md-7 col-xs-12 inputmaskrp2'>
                                        </div>";
                                    echo "</div>";

                              echo "</div>";
                            echo "</div>";
                       echo "</div>";//end konten
                       
                        echo "</form>";
                       
                       
                    echo "</div>";//end panel

                echo "</div>";
            break;

            case "editdata":
                ?> <script> window.onload = function() { document.getElementById("e_nmcoa").focus(); } </script> <?PHP
                $Tahun= date("Y");
                
                $edit=mysqli_query($cnmy, "SELECT * FROM dbmaster.coa WHERE COA_KODE='$_GET[id]'");
                $r=mysqli_fetch_array($edit);
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <small>Edit Data</small></h2>
                            <div class='clearfix'></div>
                            </div>";
                        
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=update&idmenu=$_GET[idmenu]' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>";
                        
                        
                        //isi content
                        echo "<div class='x_content'><br/>";
                        
                        //selalu ada
                        echo "<input type='hidden' id='u_module' name='u_module' value='$_GET[module]' Readonly>
                            <input type='hidden' id='u_idmenu' name='u_idmenu' value='$_GET[idmenu]' Readonly>
                            <input type='hidden' id='u_act' name='u_act' value='update' Readonly>";
                        //selalu ada
                        
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>LEVEL 4 <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='form-control' id='cb_level' name='cb_level'>";
                            echo "<option value=''>-- Pilih --</option>";
                            $tampil=mysqli_query($cnmy, "SELECT COA4, NAMA4 FROM dbmaster.coa_level4 order by COA4");
                            while($a=mysqli_fetch_array($tampil)){
                                if ($a['COA4']==$r['COA4'])
                                    echo "<option value='$a[COA4]' selected>$a[COA4] - $a[NAMA4]</option>";
                                else
                                    echo "<option value='$a[COA4]'>$a[COA4] - $a[NAMA4]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>COA KODE <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_id' name='id' required='required' class='form-control col-md-7 col-xs-12' value='$r[COA_KODE]' readonly>
                            </div>";
                        echo "</div>";
                        
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>NAMA <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmcoa' name='e_nmcoa' required='required' class='form-control col-md-7 col-xs-12' value='$r[COA_NAMA]'>
                            </div>";
                        echo "</div>";

                        
                        /*
                        $ltype1=""; $ltype2=""; $ltype3="";
                        if ($r['TIPE']=="A")
                            $ltype1="checked";
                        elseif ($r['TIPE']=="P")
                            $ltype2="checked";
                        elseif ($r['TIPE']=="R")
                            $ltype3="checked";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Tipe <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                <div class='btn-group' data-toggle='buttons'>
                                    <input type='radio' disabled='disabled' name='rb_tipe' id='rb_tipe1' value='A' $ltype1> Aktiva 
                                    <input type='radio' disabled='disabled' name='rb_tipe' id='rb_tipe2' value='P' $ltype2> Passiva 
                                    <input type='radio' disabled='disabled' name='rb_tipe' id='rb_tipe3' value='R' $ltype3> Rugi Laba
                                </div>
                            </div>";
                        echo "</div>";
                        */
                        $chk1="";
                        $chk2="";
                        $chk3="";
                        if ($r['GOL']=="A") $chk1="checked";
                        elseif ($r['GOL']=="B") $chk2="checked";
                        elseif ($r['GOL']=="H") $chk3="checked";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Golongan <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                <div class='btn-group' data-toggle='buttons'>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol1' value='A' $chk1> A </label>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol2' value='B' $chk2> B </label>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol3' value='H' $chk3> H </label>
                                </div>
                            </div>";
                        echo "</div>";
                        
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_saldoawal'>Saldo Awal Tahun $Tahun</label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_saldoawal' name='e_saldoawal' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='$r[SLDAWAL_00]'>
                            </div>";
                        echo "</div>";
                        
                        
                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='button' class='btn btn-success' onclick=\"disp_confirm('Update ?', 'update')\">Save</button>
                            </div>";
                        echo "</div>";

                        
                        echo "</div>";//end x_content
                        
                        
                        
                        echo "</form>";
                        
                        
                    echo "</div>";//end panel

                echo "</div>";

            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
