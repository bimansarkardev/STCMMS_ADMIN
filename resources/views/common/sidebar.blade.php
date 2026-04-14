<?php
$currentUrl = $_SERVER['REQUEST_URI'];
$path = parse_url($currentUrl, PHP_URL_PATH);
$urlSegments = explode('/', rtrim($path, '/'));
$lastSegment = end($urlSegments);
$secondLastSegment = count($urlSegments) >= 2 ? $urlSegments[count($urlSegments) - 2] : null;

$queryString = parse_url($currentUrl, PHP_URL_QUERY);
parse_str($queryString, $queryParams);
$type = isset($queryParams['type']) ? $queryParams['type'] : null;

?>
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <!--begin::Brand Link-->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
      <!--begin::Brand Image-->
      <!-- <img
        src="../../dist/assets/img/AdminLTELogo.png"
        alt="AdminLTE Logo"
        class="brand-image opacity-75 shadow"
      /> -->
      <!--end::Brand Image-->
      <!--begin::Brand Text-->
      <span class="brand-text fw-light">{{ config('constants.APP_NAME_SHORT') }}</span>
      <!--end::Brand Text-->
    </a>
    <!--end::Brand Link-->
  </div>
  <!--end::Sidebar Brand-->
  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <!--begin::Sidebar Menu-->
      <ul
        class="nav sidebar-menu flex-column"
        data-lte-toggle="treeview"
        role="menu"
        data-accordion="false"
      >

        <li class="nav-item">
          <a href="{{route('admin.dashboard')}}" class="nav-link @if($lastSegment=='dashboard') active @endif">
            <i class="nav-icon bi bi-palette"></i>
            <p>Dashboard</p>
          </a>
        </li>

        @if(Session::get('user')->user_type_id == 1)
            @include('common.menu.superAdminMenu')
        @endif

        @if(Session::get('user')->user_type_id == 2)
            @include('common.menu.municipalityAdminMenu')
        @endif

        @if(Session::get('user')->user_type_id == 3)
            @include('common.menu.municipalitySubAdminMenu')
        @endif

        <li class="nav-item">
          <a href="{{route('admin.changePassword')}}" class="nav-link @if($lastSegment=='changePassword') active @endif">
            <i class="nav-icon bi bi-lock-fill"></i>
            <p>Change Password</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="javascript:void(0)" title="Sign out" onclick="confirmLogout('{{ route("admin.logout") }}')" class="nav-link">
            <i class="nav-icon bi-box-arrow-left"></i>
            <p>Logout</p>
          </a>
        </li>

      </ul>
      <!--end::Sidebar Menu-->
    </nav>
  </div>
  <!--end::Sidebar Wrapper-->
</aside>