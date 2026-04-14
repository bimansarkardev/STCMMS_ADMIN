<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('constants.APP_NAME') }} | {{ $user_type->menu_name }}</title>
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
              <div class="col-sm-6"><h3 class="mb-0">{{ $user_type->menu_name }}</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ $user_type->menu_name }} List</li>
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

              @if(session('user')->user_type_id==2)
              <div class="col-12">
                  <div class="callout callout-info">
                      <span class="bi bi-info-circle-fill"></span>
                      This is the list of <strong>{{ $user_type->menu_name }}</strong>. You can <strong>edit</strong> existing entries, but <strong>deletion is restricted</strong> if they are linked to any applications records. 
                      {{ $user_type->menu_name }}(s) that are connected to application(s) <strong>cannot be deleted</strong> to maintain system integrity.
                      <br><strong>Note:</strong> Service Executors will handle the bookings/applications related to their respective services.<br>
                      <strong>Important:</strong> You can <strong>add multiple Executors per service or multiple services per executors</strong> while setting up service-wise Executors under a municipality.
                  </div>
              </div>
              @endif              

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
                  <div class="card-header">
                    <div class="row w-100 align-items-center">
                      <div class="col-md-6">
                        <h3 class="card-title mb-0">{{ $user_type->menu_name }} List</h3>
                      </div>
                      <div class="col-md-6 text-end">
                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                          {{-- Dropdown --}}
                          <select class="form-select w-auto d-none" name="filter_status" id="filter_status">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                          </select>
                          {{-- Search --}}
                          <input type="text" class="form-control w-auto d-none" placeholder="Search..." id="searchBox" name="search">
                          {{-- Add New --}}
                          <a href="{{ route('admin.addUser.param', $user_type->slug) }}" class="btn btn-info">Add New</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered mb-0 table-striped">
                        <thead>
                          <tr>
                            <th style="width: 5%" class="text-center">#</th>
                            <th>Name</th>
                            <th class="text-center">
                              @if($user_type->id==2)
                                Municipality Code / User ID
                              @else
                                Email / User ID
                              @endif
                            </th>
                            <!-- <th>Contact</th> -->
                            @if($user_type->id!=3)
                            <th>District</th>
                            <th>Address</th>
                            @endif
                            <th class="text-center">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($users as $index => $user)
                          <tr class="align-middle">
                            <td class="text-center">{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}.</td>
                            <td>{{ $user->name }}</td>
                            <td class="text-center">{{ $user->username  }}</td>
                            <!-- <td>{{ $user->mobile }}</td> -->
                            @if($user_type->id!=3)
                            <td>{{ $user->district_name }}</td>
                            <td>{{ $user->address }}</td>
                            @endif
                            <td class="text-center">
                              <a href="{{ route('admin.editUser.param.param2', [$user_type->slug, base64_encode($user->user_id)]) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                              <a href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.deleteUser.param', base64_encode($user->user_id)) }}')" class="btn btn-danger btn-sm d-none"><i class="bi bi-trash-fill"></i></a>
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
                        {{ $users->onEachSide(2)->links('pagination::bootstrap-4') }}
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
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    @include('common.script')
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
