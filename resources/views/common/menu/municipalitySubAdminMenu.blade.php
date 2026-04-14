<?php
$bookingsMenu = [
  'bookings'
];

?>
<li class="nav-item @if(in_array($lastSegment, $bookingsMenu)) menu-open @endif">
  <a href="#" class="nav-link">
    <i class="nav-icon bi bi-flag-fill"></i>
    <p>
      Bookings
      <i class="nav-arrow bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview" style="box-sizing: border-box; display: {{ in_array($lastSegment, $bookingsMenu) ? 'block' : 'none' }};">
    <li class="nav-item">
      <a href="{{ route('admin.bookings') }}?type=Pending" class="nav-link @if($type=='Pending') active @endif">
        <i class="nav-icon bi bi-list"></i>
        <p>Pending</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.bookings') }}?type=Completed" class="nav-link @if($type=='Completed') active @endif">
        <i class="nav-icon bi bi-list"></i>
        <p>Completed</p>
      </a>
    </li>
  </ul>
</li>