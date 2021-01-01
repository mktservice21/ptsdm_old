    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="../mysdm/img/users/foto_f2.jpg" class="img-circle" alt="User Image" width="160px" height="160px">
                </div>
                <div class="pull-left info">
                    <p><?PHP echo $_SESSION['NAMALENGKAP']; ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>

            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
                <!--<li class="header">&nbsp;</li>-->
                
                <!--
                <li class="active treeview">
                    <a href="#">
                        <i class="fa fa-dashboard"></i> <span>Approve</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="active"><a href="#"><i class="fa fa-circle-o"></i> Biaya Rutin</a></li>
                        <li><a href="#"><i class="fa fa-circle-o"></i> Biaya Luar Kota</a></li>
                    </ul>
                </li>

                <li><a href="#"><i class="fa fa-book"></i> <span>Documentation</span></a></li>
                <li class="header">LABELS</li>
                <li><a href="#"><i class="fa fa-circle-o text-red"></i> <span>Important</span></a></li>
                <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Warning</span></a></li>
                <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Information</span></a></li>
                -->
                
                <?PHP
                $myidmenu="";
                $myidsub="";
                if (isset($_GET['im'])) $myidmenu=$_GET['im'];
                if (isset($_GET['is'])) $myidsub=$_GET['is'];
                
                $query = "select distinct id, judul, micon from dbmaster.sdm_menu_khusus WHERE parent_id=0 order by urutan";
                $tampil=mysqli_query($cnmy, $query);
                $ketemu=mysqli_num_rows($tampil);
                if ($ketemu>0){
                    while ($sme= mysqli_fetch_array($tampil)) {
                        
                        $pid=$sme['id'];
                        $pjudul=$sme['judul'];
                        $picon=$sme['micon'];
                        if (empty($picon)) $picon="fa-print";
                        $spaktif = "";
                        if ($myidmenu==$pid) $spaktif="active";
                        if ($pid=="1") $spaktif="active";
                        
                        ?>
                        <li class="<?PHP echo $spaktif; ?> treeview">
                            <a href="#">
                                <i class="fa <?PHP echo $picon; ?>"></i> <span><?PHP echo $pjudul; ?></span>
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            
                            <?PHP
                            $query = "select * from dbmaster.sdm_menu_khusus WHERE parent_id<>0 AND parent_id='$pid' order by urutan";
                            $tampil1=mysqli_query($cnmy, $query);
                            $ketemu1=mysqli_num_rows($tampil1);
                            if ($ketemu1>0){
                                ?>
                                <ul class="treeview-menu">
                                    <?PHP
                                    while ($ssubm= mysqli_fetch_array($tampil1)) {
                                        $mpaktif="active";
                                        $psubid=$ssubm['id'];
                                        $psubjudul=$ssubm['judul'];
                                        $psuburl=$ssubm['url'];
                                        
                                        $mpaktif = "";
                                        if ($myidsub==$psubid) $mpaktif="active";
                                        
                                        ?>
                                        <li class="<?PHP echo $mpaktif; ?>"><a href="<?PHP echo "$psuburl&im=$pid&is=$psubid&act=$pid"; ?>"><i class="fa fa-circle-o"></i> <?PHP echo $psubjudul; ?></a></li>
                                        <?PHP
                                    }
                                    ?>
                                </ul>
                                <?PHP
                            }
                            ?>
                        </li>
                        <?PHP
                    }
                }
                ?>
                
            </ul>
        </section>
    <!-- /.sidebar -->
    </aside>