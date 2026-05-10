        <!-- Header -->
        <div class="header">
            <div class="header-content">

                @include('admin.layouts_new.components.breadcrumb')

                <div class="header-actions">
                    <!-- <div class="search-box">
                        <input type="text" placeholder="Cari...">
                        <i class="bi bi-search"></i>
                    </div> -->

                    @if (can('b', 'arsip-layanan') && (setting('verifikasi_kades') || setting('verifikasi_sekdes')))
                        <a class="icon-btn" href="{{ ci_route('keluar.masuk') }}">
                            <i class="bi bi-bell"></i>
                            
                            @if ($notif['permohonansurat'])
                                <span class="badge-notification"></span>
                            @endif
                        </a>
                    @endif

                    @if ($kategori_pengaturan && can('u', $akses_modul))
                        @if ($modul_ini === 'layanan-pelanggan' || $sub_modul_ini === 'layanan-pelanggan')
                            <a href="#" class="atur-token icon-btn">
                            @else
                                <a class="icon-btn" href="#" data-remote="false" data-toggle="modal" data-title="Pengaturan {{ ucwords($controller) }}" data-target="#pengaturan">
                        @endif
                        <span><i class="bi bi-gear"></i></span>
                        </a>
                    @endif
                </div>
            </div>
        </div>