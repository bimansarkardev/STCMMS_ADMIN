<?php 
$roadMenu = [
  'road',
  'addRoad',
];
$stpFstpMenu = [
  'stpFstps',
  'addStpFstp',
];
$vehicleMenu = [
  'vehicles',
  'addVehicle',
];
$driverHelperMenu = [
  'driverHelpers',
  'addDriverHelpers',
];
$agencyMenu = [
  'agencies',
  'addAgency',
];
?>

<li class="nav-item">
  <a href="{{route('admin.ward')}}" class="nav-link @if($lastSegment == 'ward') active @endif">
    <i class="nav-icon bi bi-geo-alt-fill"></i>
    <p>Ward</p>
  </a>
</li>

<li class="nav-item @if(in_array($lastSegment, $roadMenu)) menu-open @endif">
  <a href="#" class="nav-link">
    <i class="nav-icon bi bi-signpost-fill"></i>
    <p>
      Roads
      <i class="nav-arrow bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview submenu-indent"
    style="box-sizing: border-box; display: {{ in_array($lastSegment, $roadMenu) ? 'block' : 'none' }};">
    <li class="nav-item">
      <a href="{{ route('admin.road') }}" class="nav-link @if($lastSegment == 'road') active @endif">
        <i class="nav-icon bi bi-list"></i>
        <p>List</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.addRoad') }}" class="nav-link @if($lastSegment == 'addRoad') active @endif">
        <i class="nav-icon bi bi-plus-circle"></i>
        <p>Add New</p>
      </a>
    </li>
  </ul>
</li>

<li class="nav-item @if(in_array($lastSegment, $stpFstpMenu)) menu-open @endif">
  <a href="#" class="nav-link">
    <i class="nav-icon bi bi-droplet-fill"></i>
    <p>
      STP/FSTP Master
      <i class="nav-arrow bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview submenu-indent"
    style="box-sizing: border-box; display: {{ in_array($lastSegment, $stpFstpMenu) ? 'block' : 'none' }};">
    <li class="nav-item">
      <a href="{{ route('admin.stpFstps') }}" class="nav-link @if($lastSegment == 'stpFstps') active @endif">
        <i class="nav-icon bi bi-list"></i>
        <p>List</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.addStpFstp') }}" class="nav-link @if($lastSegment == 'addStpFstp') active @endif">
        <i class="nav-icon bi bi-plus-circle"></i>
        <p>Add New</p>
      </a>
    </li>
  </ul>
</li>

<li class="nav-item @if(in_array($lastSegment, $vehicleMenu)) menu-open @endif">
  <a href="#" class="nav-link">
    <i class="nav-icon bi bi-truck-front-fill"></i>
    <p>
      Vehicle Master
      <i class="nav-arrow bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview submenu-indent"
    style="box-sizing: border-box; display: {{ in_array($lastSegment, $vehicleMenu) ? 'block' : 'none' }};">
    <li class="nav-item">
      <a href="{{ route('admin.vehicles') }}" class="nav-link @if($lastSegment == 'vehicles') active @endif">
        <i class="nav-icon bi bi-list"></i>
        <p>List</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.addVehicle') }}" class="nav-link @if($lastSegment == 'addVehicle') active @endif">
        <i class="nav-icon bi bi-plus-circle"></i>
        <p>Add New</p>
      </a>
    </li>
  </ul>
</li>

<li class="nav-item @if(in_array($lastSegment, $driverHelperMenu)) menu-open @endif">
  <a href="#" class="nav-link">
    <i class="nav-icon bi bi-people-fill"></i>
    <p>
      Driver/Helper Master
      <i class="nav-arrow bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview submenu-indent"
    style="box-sizing: border-box; display: {{ in_array($lastSegment, $driverHelperMenu) ? 'block' : 'none' }};">
    <li class="nav-item">
      <a href="{{ route('admin.driverHelpers') }}" class="nav-link @if($lastSegment == 'driverHelpers') active @endif">
        <i class="nav-icon bi bi-list"></i>
        <p>List</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.addDriverHelpers') }}" class="nav-link @if($lastSegment == 'addDriverHelpers') active @endif">
        <i class="nav-icon bi bi-plus-circle"></i>
        <p>Add New</p>
      </a>
    </li>
  </ul>
</li>

<li class="nav-item @if(in_array($lastSegment, $agencyMenu)) menu-open @endif">
  <a href="#" class="nav-link">
    <i class="nav-icon bi bi-building"></i>
    <p>
      Agency Master
      <i class="nav-arrow bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview submenu-indent"
    style="box-sizing: border-box; display: {{ in_array($lastSegment, $agencyMenu) ? 'block' : 'none' }};">
    <li class="nav-item">
      <a href="{{ route('admin.agencies') }}" class="nav-link @if($lastSegment == 'agencies') active @endif">
        <i class="nav-icon bi bi-list"></i>
        <p>List</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.addAgency') }}" class="nav-link @if($lastSegment == 'addAgency') active @endif">
        <i class="nav-icon bi bi-plus-circle"></i>
        <p>Add New</p>
      </a>
    </li>
  </ul>
</li>