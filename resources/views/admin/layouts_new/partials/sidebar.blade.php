    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <img src="<?= gambar_desa($desa['logo']) ?>" class="img-circle" alt="User Image">
            <h4><?= ucwords(setting('sebutan_desa') . ' ' . $desa['nama_desa']) ?></h4>

            <?php
            $seb_kec = setting('sebutan_kecamatan');
            $nam_kec = $desa['nama_kecamatan'];
            $seb_kab = setting('sebutan_kabupaten');
            $nam_kab = $desa['nama_kabupaten'];
            ?>

            <?php if (strlen($nam_kec) <= 12 && strlen($nam_kab) <= 12): ?>
            <?= ucwords($seb_kec . ' ' . $nam_kec) ?>
            </br>
            <?= ucwords($seb_kab . ' ' . $nam_kab) ?>
            <?php else: ?>
            <?= ucwords(substr($seb_kec, 0, 3) . '. ' . $nam_kec) ?>
            </br>
            <?= ucwords(substr($seb_kab, 0, 3) . '. ' . $nam_kab) ?>
            <?php endif ?>
        </div>

<div class="menu-list">
    <?php $modul = admin_menu(); ?>
    <?php foreach ($modul as $index => $mod): ?>
        <?php if (is_array($mod['childrens']) && count($mod['childrens']) > 0): ?>
            <!-- Menu dengan child -->
            <a href="#submenu<?= $index ?>" class="menu-item has-submenu" data-bs-toggle="collapse">
                <i class="fa <?= $mod['ikon'] ?>"></i>
                <span><?= $mod['modul'] ?></span>
                <i class="fa fa-chevron-down submenu-arrow ms-auto"></i>
            </a>
            <div class="collapse submenu" id="submenu<?= $index ?>">
                <?php foreach ($mod['childrens'] as $submod): ?>
                    
                    <?php if ($submod['slug'] == 'pendaftaran-kerjasama'): ?>
                    <?php continue; ?>
                    <?php endif; ?>

                    <a href="<?= ci_route($submod['url']) ?>" class="submenu-item">
                        <i class="fa <?= $submod['ikon'] != null ? $submod['ikon'] : 'fa-circle-o' ?>"></i>
                        <span><?= $submod['modul'] ?></span>
                    </a>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <!-- Menu tanpa child -->
            <a href="<?= ci_route($mod['url']) ?>" class="menu-item">
                <i class="fa <?= $mod['ikon'] ?>"></i>
                <span><?= $mod['modul'] ?></span>
            </a>
        <?php endif ?>
    <?php endforeach ?>
</div>

        <!-- <div class="admin-account">
            <div class="admin-profile">
                <div class="admin-avatar">AP</div>
                <div class="admin-info">
                    <h6>Admin Desa</h6>
                    <p>Kepala Desa</p>
                </div>
            </div>
        </div> -->
    </div>