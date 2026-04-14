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
                      This is the list of <strong>{{ $title }}</strong>. You can <strong>edit</strong> existing ones, or <strong>delete</strong> those not linked to any records.
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
                      <div class="col-md-6 text-end">
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
                            <th>Plant Details</th>                            
                            <th class="text-center">Tagged Municipalities</th>
                            <th class="text-center d-none">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($params as $index => $list)
                            <tr class="align-middle">
                              <td class="text-center">{{ ($params->currentPage() - 1) * $params->perPage() + $index + 1 }}.</td>
                              <td>
                                    <i class="bi bi-building"></i> {{ $list->municipality_name }} <br>

                                    <i class="bi bi-geo-alt-fill"></i> {{ $list->ward_no }} <br>

                                    <i class="bi bi-diagram-3"></i> {{ $list->category_name }} <br>

                                    <i class="bi bi-droplet"></i> {{ $list->capacity_mod }} <br>

                                    <i class="bi bi-pin-map-fill"></i> {{ $list->location }} <br>
                                </td>
                              <td>
                                  <table class="table table-bordered table-sm mb-0">
                                      <thead>
                                          <tr>
                                              <th style="width: 40px;" class="text-center">#</th>
                                              <th>Municipality</th>
                                              <th style="width: 100px;" class="text-center">Un-tag</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          @forelse ($list->tagged_municipalities as $i => $tag)
                                              <tr>
                                                  <td class="text-center">{{ $i + 1 }}.</td>
                                                  <td>{{ $tag['municipality_name'] }}</td>
                                                  <td class="text-center">
                                                    <a href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.deleteStpFstp.param', base64_encode($tag['municipality_name'])) }}')" class="text-danger"><i class="bi bi-trash-fill"></i></a>
                                                  </td>
                                              </tr>
                                          @empty
                                              <tr>
                                                  <td colspan="2" class="text-center">
                                                    <span class="text-danger">No Municipality tagged with this Plant,<span> <a href="{{ route($addUrl) }}">Tag Municipalities</a>
                                                  </td>
                                              </tr>
                                          @endforelse
                                      </tbody>
                                  </table>
                              </td>
                              <td class="text-center d-none">
                                <a href="{{ route('admin.editStpFstp.param', [base64_encode($list->id)]) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                                <a href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.deleteStpFstp.param', base64_encode($list->id)) }}')" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></a>
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
  </body>
  <!--end::Body-->
</html>
