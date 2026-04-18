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
                    <form method="get" action="{{ route('admin.collectionsList') }}">
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

                                <!-- Municipality Dropdown -->
                                <select name="s" class="form-control w-auto">
                                    <option selected disabled value="">🛠 Select Service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ request('s') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Date Range -->
                                <div class="input-group w-auto">
                                    <span class="input-group-text">📅</span>
                                    <input type="date" name="fd" class="form-control"
                                        value="{{ request('fd', $from_date) }}">
                                    <span class="input-group-text">to</span>
                                    <input type="date" name="td" class="form-control"
                                        value="{{ request('td', $to_date) }}">
                                </div>

                                <!-- Search Button -->
                                <button type="submit" class="btn btn-success">
                                    🔍 Search
                                </button>

                                <!-- Reset Button (Redirect) -->
                                <a href="{{ route('admin.collectionsList') }}" class="btn btn-secondary">
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
                            <th>Municipality & Service</th>
                            <th>Beneficiary Details</th>
                            <th>Trip Details</th>
                            <th class="text-center">Trip Image</th>
                            <th class="text-center">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                             @foreach ($params as $index => $row)
                              <tr class="align-middle">
                                <td class="text-center">{{ ($params->currentPage() - 1) * $params->perPage() + $index + 1 }}.</td>
                                <td>
                                  {{ $row->municipality_name }} <br>
                                  {{ $row->service_name }}
                                </td>
                                <td>
                                    👤 {{ $row->beneficiary_name }}<br>
                                    📞 {{ $row->beneficiary_contact_number }}<br>
                                    📍 {{ $row->address }}                                    
                                </td>
                                <td>
                                    📅 {{ $row->formatted_created_at_date }} | 🔁 Trip {{ $row->trip_number }}<br>
                                    👷 {{ $row->created_by_name }} | 🚛 {{ $row->vehicle_type }} ({{ $row->vehicle_reg_no }})<br>
                                    🧪 {{ $row->volume_quantity_mod }}
                                </td>
                                <td class="text-center">
                                    @if($row->image_filepath)
                                        <button 
                                            class="btn btn-sm btn-outline-primary show-image-btn"
                                            data-img="{{ $row->image_filepath }}"
                                        >
                                            🖼️ Show Proof
                                        </button>
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge 
                                        {{ $row->status == 1 ? 'bg-warning' : ($row->status == 2 ? 'bg-success' : 'bg-secondary') }}">                                        
                                        {{ $row->status_name }}                                    
                                    </span>
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
      <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h5 class="modal-title">📸 Image Proof</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
              <img id="modalImage" src="" class="img-fluid rounded" style="max-height:500px;">
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
      $(document).on('click', '.show-image-btn', function () {
          let imgUrl = $(this).data('img');

          $('#modalImage').attr('src', imgUrl);
          $('#imageModal').modal('show');
      });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
