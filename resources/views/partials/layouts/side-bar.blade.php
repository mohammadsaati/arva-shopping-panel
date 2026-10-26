@php
    use App\Commons\Menu\MenuRegistry;
    $menuName = $menuName ?? (request()->is('owner*') ? 'owner' : 'admin');
@endphp

    <!-- Sidebar -->
<aside class="panel-sidebar flex-col fixed top-0 bottom-0 z-20 hidden lg:flex items-stretch shrink-0 w-[--tw-sidebar-width] dark" data-drawer="true" data-drawer-class="drawer drawer-start flex top-0 bottom-0" data-drawer-enable="true|lg:false" id="sidebar" aria-label="منوی اصلی">
    @include('partials.menu.header')

    <!-- Sidebar menu -->
    <div class="flex items-stretch grow shrink-0 justify-center my-5" id="sidebar_menu">
        <div class="scrollable-y-auto grow [--tw-scrollbar-thumb-color:var(--tw-gray-300)]" data-scrollable="true" data-scrollable-dependencies="#sidebar_header, #sidebar_footer" data-scrollable-height="auto" data-scrollable-offset="0px" data-scrollable-wrappers="#sidebar_menu">
            <!-- Primary Menu -->
            <div class="mb-5">
                <div class="menu flex flex-col w-full gap-1.5 px-3.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_primary_menu">
                    @foreach(MenuRegistry::visible($menuName) as $item)
                        @include('partials.menu.menu-item', ['item' => $item, 'depth' => 0])
                    @endforeach
                </div>
            </div>
            <!-- End of Primary Menu -->
        </div>
    </div>
    <!-- End of Sidebar menu-->

    @include('partials.menu.footer')
</aside>
<!-- End of Sidebar -->
