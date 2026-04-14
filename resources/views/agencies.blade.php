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
                            <th>Agency Name</th>
                            <th>Address</th>
                            <th>Contact Person</th>
                            <th class="text-center">Municipalities</th>
                            <th class="text-center">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($params as $index => $list)
                            <tr class="align-middle">
                              <td class="text-center">{{ ($params->currentPage() - 1) * $params->perPage() + $index + 1 }}.</td>
                              <td>{{ $list->agency_name }}</td>
                              <td>{!! $list->agency_address ?: '<span class="text-danger">Not Added!</span>' !!}</td>
                              <td>
                                  <i class="bi bi-person"></i> 
                                  {{ $list->contact_person ?? '' }}
                                  @if(empty($list->contact_person))
                                      <span class="text-danger">Not Added!</span>
                                  @endif
                                  <br>

                                  <i class="bi bi-phone"></i> 
                                  {{ $list->contact_person_contact_number ?? '' }}
                                  @if(empty($list->contact_person_contact_number))
                                      <span class="text-danger">Not Added!</span>
                                  @endif
                              </td>
                              <td class="text-center">
                                  <button 
                                      class="btn btn-sm btn-outline-success view-municipalities"
                                      data-municipalities='@json($list->agenciesMunicipalities)'
                                      data-name="{{ $list->agency_name }}"
                                      title="View Municipalities">
                                      <b>{{ $list->agencies_municipalities_count }}</b>
                                  </button>
                              </td>
                              <td class="text-center">
                                <a href="#" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                                <a href="javascript:void(0)" onclick="confirmDelete('#')" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></a>
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
      <div class="modal fade" id="municipalityModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header bg-info">
              <h5 class="modal-title">Municipality List</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>Name</th>
                    <th>Contract</th>
                    <th class="text-center">Contract File</th>
                  </tr>
                </thead>
                <tbody id="municipalityTableBody">
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
      document.addEventListener('DOMContentLoaded', function () {

          const modal = new bootstrap.Modal(document.getElementById('municipalityModal'));
          const tableBody = document.getElementById('municipalityTableBody');

          document.querySelectorAll('.view-municipalities').forEach(button => {

              button.addEventListener('click', function () {

                  let municipalities = JSON.parse(this.getAttribute('data-municipalities'));
                  
                  let agencyName = this.getAttribute('data-name');
                  
                  document.querySelector('#municipalityModal .modal-title').innerText = 
                      "Municipalities working with - " + (agencyName ?? '');

                  tableBody.innerHTML = '';

                  if (municipalities.length === 0) {
                      tableBody.innerHTML = `<tr><td colspan="4" class="text-center">No data found</td></tr>`;
                  } else {
                      municipalities.forEach((item, index) => {
                          tableBody.innerHTML += `
                              <tr>
                                  <td class="text-center">${index + 1}</td>
                                  <td>${item.municipality_name}</td>
                                  <td>${item.formatted_contract_from_date} - ${item.formatted_contract_to_date}</td>
                                  <td class="text-center">
                                    <a target="_blank" href="${item.contract_file_full_filepath}" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark"></i> Contract file</a>
                                  </td>
                              </tr>
                          `;
                      });
                  }

                  modal.show();
              });

          });

      });
      </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
