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
                      This is the list of <strong>{{ $title }}</strong>.
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
                  <div class="card-header">
                    <div class="row w-100 align-items-center">
                      <div class="col-md-6">
                        <h3 class="card-title mb-0">{{ $title }} List</h3>
                      </div>
                      <div class="col-md-6 text-end d-none">
                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                          <a href="{{ route($addUrl) }}" class="btn btn-info">Add New</a>
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
                            <th>Road Name</th>
                            <th class="text-center">Ward Number</th>
                            <th class="text-center">Municipality</th>
                            <th class="text-center d-none">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($params as $index => $list)
                          <tr class="align-middle">
                            <td class="text-center">{{ ($params->currentPage() - 1) * $params->perPage() + $index + 1 }}.</td>
                            <td>{{ $list->road_name }}</td>
                            <td class="text-center">{{ $list->ward_no }}</td>
                            <td class="text-center">{{ $list->municipality_name }}</td>
                            <td class="text-center d-none">
                              <a href="{{ route('admin.editRoad.param', base64_encode($list->id)) }}" class="btn btn-warning btn-sm">Edit</a>
                              <a href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.deleteRoad.param', base64_encode($list->id)) }}' , 'Are you sure you want to delete this {{$title}}? This action cannot be undone. {{$title}} linked to Complaint(s) cannot be deleted. Please confirm to proceed.')" class="btn btn-danger btn-sm">Delete</a>
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
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    @include('common.script')
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
