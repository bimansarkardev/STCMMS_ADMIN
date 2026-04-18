<li class="nav-item">
  <a href="{{route('admin.moduleMaster')}}" class="nav-link @if($lastSegment=='moduleMaster' || $lastSegment=='addModuleMaster') active @endif">
    <i class="nav-icon bi bi-flag"></i>
    <p>Services</p>
  </a>
</li>

<li class="nav-item">
  <a href="{{route('admin.ward')}}" class="nav-link @if($lastSegment == 'ward') active @endif">
    <i class="nav-icon bi bi-geo-alt-fill"></i>
    <p>Wards</p>
  </a>
</li>

<li class="nav-item">
  <a href="{{route('admin.road')}}" class="nav-link @if($lastSegment == 'road') active @endif">
    <i class="nav-icon bi bi-signpost-fill"></i>
    <p>Roads</p>
  </a>
</li>

<li class="nav-item d-none">
  <a href="{{route('admin.agencies')}}" class="nav-link @if($lastSegment=='agencies' || $lastSegment=='addAgency') active @endif">
    <i class="nav-icon bi bi-building"></i>
    <p>Agencies</p>
  </a>
</li>

<?php $stpFstpMenu = [
  'stpFstps',
  'addStpFstp',
  'taggedStpFstps',
  'tagStpFstp',
]; ?>

<li class="nav-item @if(in_array($lastSegment, $stpFstpMenu)) menu-open @endif">
  <a href="#" class="nav-link">
    <i class="nav-icon bi bi-droplet-fill"></i>
    <p>
      Plants
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
    <li class="nav-item d-none">
      <a href="{{ route('admin.addStpFstp') }}" class="nav-link @if($lastSegment == 'addStpFstp') active @endif">
        <i class="nav-icon bi bi-plus-circle"></i>
        <p>Add New</p>
      </a>
    </li>
    <li class="nav-item d-none">
      <a href="{{ route('admin.taggedStpFstps') }}" class="nav-link @if($lastSegment == 'taggedStpFstps' || $lastSegment == 'tagStpFstp') active @endif">
        <i class="nav-icon bi bi-tag"></i>
        <p style="white-space: normal;">Tagged Municipalities</p>
      </a>
    </li>
  </ul>
</li>

<li class="nav-item">
  <a href="{{route('admin.vehicles')}}" class="nav-link @if($lastSegment=='vehicles' || $lastSegment=='addVehicle') active @endif">
    <i class="nav-icon bi bi-truck-front-fill"></i>
    <p>Vehicles</p>
  </a>
</li>

<li class="nav-item">
  <a href="{{route('admin.fieldWorkers')}}" class="nav-link @if($lastSegment=='fieldWorkers' || $lastSegment=='addFieldWorkers') active @endif">
    <i class="nav-icon bi bi-person-workspace"></i>
    <p>Field Workers</p>
  </a>
</li>

<li class="nav-item">
  <a href="{{route('admin.attendanceList')}}" class="nav-link @if($lastSegment=='attendanceList') active @endif">
    <i class="nav-icon bi bi-check2-square"></i>
    <p>Attendance List</p>
  </a>
</li>

<li class="nav-item">
  <a href="{{route('admin.collectionsList')}}" class="nav-link @if($lastSegment=='collectionsList') active @endif">
    <i class="nav-icon bi bi-box-arrow-up"></i>
    <p>Collections List</p>
  </a>
</li>

<li class="nav-item">
  <a href="{{route('admin.disposalsList')}}" class="nav-link @if($lastSegment=='disposalsList') active @endif">
    <i class="nav-icon bi bi-box-arrow-down"></i>
    <p>Disposals List</p>
  </a>
</li>