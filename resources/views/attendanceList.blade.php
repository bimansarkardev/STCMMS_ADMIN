<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('constants.APP_NAME') }} | {{ $title }}</title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('common.header_links')
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      @include('common.header')
      <!--end::Header-->
      <!--begin::Sidebar-->
      @include('common.sidebar')
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">{{ $title }}</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ $title }} List</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row g-4">

              <div class="col-12">
                  <div class="callout callout-info">
                      <span class="bi bi-info-circle-fill"></span>
                      This is the list of <strong>{{ $title }}</strong>
                  </div>
              </div>

              <div class="col-md-12">

                @if(session('error'))
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                @endif

                @if(session('success'))
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                @endif

                @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                @endif

                <div class="card card-info card-outline mb-4">
                    <form method="get" action="{{ route('admin.attendanceList') }}">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">

                            <h3 class="card-title mb-0">{{ $title }} List</h3>

                            <div class="d-flex gap-2 flex-wrap ms-auto">
                                
                                <!-- Municipality Dropdown -->
                                @if(session('user')->user_type_id==1)
                                <select name="m" class="form-control w-auto">
                                    <option selected disabled value="">🏙 Select Municipality</option>
                                    @foreach($municipalities as $municipality)
                                        <option value="{{ $municipality->user_id }}"
                                            {{ request('m') == $municipality->user_id ? 'selected' : '' }}>
                                            {{ $municipality->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @endif

                                <!-- Date Field -->
                                <div class="input-group w-auto">
                                    <span class="input-group-text">📅</span>
                                    <input 
                                    type="date" 
                                    name="d" 
                                    class="form-control w-auto"
                                    value="{{ request('d') ? request('d') : $currentDate }}"
                                    required
                                >
                                </div>

                                <!-- Search Button -->
                                <button type="submit" class="btn btn-success">
                                    🔍 Search
                                </button>

                                <!-- Reset Button (Redirect) -->
                                <a href="{{ route('admin.attendanceList') }}" class="btn btn-secondary">
                                    ♻️ Reset
                                </a>

                            </div>
                        </div>
                    </form>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered mb-0 table-striped">
                        <thead>
                          <tr>
                            <th style="width: 5%" class="text-center">#</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th class="text-center">Is User</th>
                            <th>Contact No.</th>
                            <th>Municipality</th>                            
                            <th>Work With</th>
                            <th class="text-center">Attendance</th>
                          </tr>
                        </thead>
                        <tbody>
                             @foreach ($params as $index => $user)
                              <tr class="align-middle">
                                <td class="text-center">{{ ($params->currentPage() - 1) * $params->perPage() + $index + 1 }}.</td>
                                <td>{{ $user->field_worker_name }}</td>
                                <td>{{ $user->role_name }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $user->is_user == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $user->is_user == 1 ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>{{ $user->field_worker_mobile_no ? : 'Not Given'  }}</td>
                                <td>{{ $user->municipality_name }}</td>
                                <td>
                                    @if($user->operate_by == 1)
                                        <i class="bi bi-bank text-primary"></i>
                                        <small>{{ $user->municipality_name }}</small>
                                    @else
                                        <i class="bi bi-building text-success"></i>
                                        <small>{{ $user->agency_name }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <!-- Present / Absent -->
                                    <span class="badge {{ $user->total_sessions ? 'bg-success' : 'bg-danger' }}">
                                        {{ $user->total_sessions ? 'Present' : 'Absent' }}
                                    </span>

                                    <!-- Sessions Badge (Clickable) -->
                                    @if($user->total_sessions)
                                        <span 
                                            class="badge bg-info attendance-click"
                                            style="cursor:pointer"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->field_worker_name }}"
                                            data-date="{{ $user->attendance_date }}"
                                            data-fdate="{{ $user->formatted_attendance_date }}"
                                            data-role="{{ $user->role_name }}"
                                        >
                                            {{ $user->total_sessions }} {{ $user->total_sessions == 1 ? 'Session' : 'Sessions' }}
                                        </span>
                                    @endif
                                </td>
                              </tr>
                              @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer clearfix">
                    <div class="pagination">
                        {{ $params->onEachSide(2)->links('pagination::bootstrap-4') }}
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      @include('common.footer')
        <div class="modal fade" id="attendanceModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="attendanceModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>In Location</th>
                            <th>Time Out</th>
                            <th>Out Location</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody">
                        <tr>
                            <td colspan="6" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            </div>
        </div>
        </div>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    @include('common.script')
    <script>
        $(document).on('click', '.attendance-click', function () {

            let workerId = $(this).data('id');
            let name = $(this).data('name');
            let date = $(this).data('date');
            let fdate = $(this).data('fdate');
            let role = $(this).data('role');

            // Set modal header
            $('#attendanceModalTitle').html(
                name + ' | ' + role + ' | ' + fdate
            );

            $('#attendanceModal').modal('show');

            // Load data
            $.ajax({
                url: "{{ route('admin.getAttendanceSessions') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    field_worker_id: workerId,
                    date: date
                },
                success: function (res) {

                    let rows = '';

                    if (res.data.length > 0) {
                        res.data.forEach((item, index) => {
                            rows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.formatted_date}</td>
                                    <td>${item.formatted_login_time ?? '-'}</td>
                                    <td>${item.login_address ?? '-'}</td>
                                    <td>${item.formatted_logout_time ?? '-'}</td>
                                    <td>${item.logout_address ?? '-'}</td>
                                </tr>
                            `;
                        });
                    } else {
                        rows = `<tr><td colspan="6" class="text-center">No Data</td></tr>`;
                    }

                    $('#attendanceTableBody').html(rows);
                }
            });
        });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
