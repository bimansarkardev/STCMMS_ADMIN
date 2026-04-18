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
            <div class="row">
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
                          <a href="{{ route('admin.addModuleMaster') }}" class="btn btn-info">Add New</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th style="width: 5%" class="text-center">#</th>
                            <th class="text-center">Image</th>
                            <th class="text-center">Name</th>                            
                            <th>Details</th>
                            @if(session('user')->user_type_id==1)
                            <th class="text-center">Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($params as $index => $list)
                          <tr class="align-middle">
                            <td class="text-center">{{ $index + 1 }}.</td>
                            <td class="text-center">
                              <img src="{{ url('') }}/{{$list->filepath}}" width="100">
                            </td>
                            <td class="text-center">{{ $list->name }}</td>                            
                            <td>{{ $list->details }}</td>
                            @if(session('user')->user_type_id==1)
                            <td class="text-center">
                              <a href="{{ route('admin.editModuleMaster.param', base64_encode($list->id)) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                              <a href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.deleteModuleMaster.param', base64_encode($list->id)) }}')" class="btn btn-danger btn-sm d-none">Delete</a>
                            </td>
                            @endif
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- /.card-body -->
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
