<div class="">

    <div class="page-title"><div class="title_left"><h3>Data Vendor</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_brg_supplier/aksi_supplier.php";
        switch($_GET['act']){
            default:

                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                            onclick=\"window.location.href='?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru';\">
                            <small></small>
                            </h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'>";

                            echo "<table id='datatable' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th width='40px'>Kode</th><th>Nama</th>"
                            . "<th width='200px'>Alamat</th><th width='60px'>Telp.</th><th width='80px'>Kontak Person</th><th width='20px'>Aktif</th><th width='70px'>Aksi</th></tr></thead>";
                            echo "<tbody>";
                            $no=1;
                            $tampil = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_supplier order by NAMA_SUP");
                            while ($r=mysqli_fetch_array($tampil)){
                                echo "<tr scope='row'><td>$no</td>";
                                echo "<td>$r[KDSUPP]</td>";
                                echo "<td>$r[NAMA_SUP]</td>";
                                echo "<td>$r[ALAMAT]</td>";
                                echo "<td>$r[TELP]</td>";
                                echo "<td>$r[KEYPERSON]</td>";
                                echo "<td>$r[AKTIF]</td>";
                                echo "<td>";//AKSI
                                    echo " <a class='btn btn-success btn-xs' href=?module=$_GET[module]&idmenu=$_GET[idmenu]&act=editdata&id=$r[KDSUPP]>Edit</a>
                                        <a class='btn btn-danger btn-xs' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[KDSUPP]&idmenu=$_GET[idmenu]\"
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
                <script> window.onload = function() { document.getElementById("e_nmsupplier").focus(); } </script> 
                
                <script>
                    function disp_confirm(pText_,ket)  {
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");
                                //document.write("You pressed OK!")
                                document.getElementById("demo-form2").action = "module/mod_brg_supplier/aksi_supplier.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }

                    }
                </script>
                
                <?PHP
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <small>Tambah Baru</small></h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'><br/>";
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmsupplier'>Nama <span class='required'>*</span></label>";
                        echo "<div class='col-md-4 col-sm-4 col-xs-3'>
                            <input type='text' id='e_nmsupplier' name='e_nmsupplier' required='required' class='form-control col-md-7 col-xs-12'>
                            </div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmsupplier'>Alamat <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_alamat' name='e_alamat' required='required' class='form-control col-md-7 col-xs-12'>
                            </div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmsupplier'>Telp. <span class='required'></span></label>";
                        echo "<div class='col-md-3 col-sm-3 col-xs-3'>
                            <input type='text' id='e_telp' name='e_telp' class='form-control col-md-7 col-xs-12'>
                            </div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmsupplier'>Kontak Person <span class='required'></span></label>";
                        echo "<div class='col-md-3 col-sm-3 col-xs-3'>
                            <input type='text' id='e_keyperson' name='e_keyperson' class='form-control col-md-7 col-xs-12'>
                            </div>";
                        echo "</div>";

                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='button' class='btn btn-success' onclick=\"disp_confirm('simpan', 'input')\">Save</button>
                            </div>";
                        echo "</div>";

                        echo "</form>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";
            break;

            case "editdata":
                ?> 
                <script> window.onload = function() { document.getElementById("e_nmsupplier").focus(); } </script> 
                
                <script>
                    function disp_confirm(pText_,ket)  {
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");
                                //document.write("You pressed OK!")
                                document.getElementById("demo-form2").action = "module/mod_brg_supplier/aksi_supplier.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }

                    }
                </script>
                
                <?PHP

                $edit=mysqli_query($cnmy, "SELECT * FROM dbmaster.t_supplier WHERE KDSUPP='$_GET[id]'");
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

                        //isi content
                        echo "<div class='x_content'><br/>";
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=update&idmenu=$_GET[idmenu]' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>
                            <input type='hidden' name='id' value='$r[KDSUPP]'>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmsupplier'>Nama <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmsupplier' name='e_nmsupplier' required='required' class='form-control col-md-7 col-xs-12' value='$r[NAMA_SUP]'>
                            </div>";
                        echo "</div>";

                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmsupplier'>Alamat <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_alamat' name='e_alamat' required='required' class='form-control col-md-7 col-xs-12' value='$r[ALAMAT]'>
                            </div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmsupplier'>Telp. <span class='required'></span></label>";
                        echo "<div class='col-md-3 col-sm-3 col-xs-3'>
                            <input type='text' id='e_telp' name='e_telp' class='form-control col-md-7 col-xs-12' value='$r[TELP]'>
                            </div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmsupplier'>Kontak Person <span class='required'></span></label>";
                        echo "<div class='col-md-3 col-sm-3 col-xs-3'>
                            <input type='text' id='e_keyperson' name='e_keyperson' class='form-control col-md-7 col-xs-12' value='$r[KEYPERSON]'>
                            </div>";
                        echo "</div>";
                        
                        
                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='button' class='btn btn-success' onclick=\"disp_confirm('simpan', 'update')\">Save</button>
                            </div>";
                        echo "</div>";

                        echo "</form>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
