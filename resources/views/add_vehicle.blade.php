<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('constants.APP_NAME') }} | {{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
              <div class="col-sm-6"><h3 class="mb-0">{{ isset($details) ? 'Edit' : 'Add' }} {{$title}}</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ isset($details) ? 'Edit' : 'Add' }} {{$title}}</li>
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
              <!--begin::Col-->
              <div class="col-12">
                <div class="callout callout-info">
                  <span class="bi bi-info-circle-fill"></span> Please fill in all required <span class="text-danger">*</span> fields below. After completing the form, click <strong>"{{ isset($details) ? 'Update' : 'Add' }} {{ $title }}"</strong> to submit. Click <strong>"Reset Form"</strong> to clear the form.
                </div>
              </div>
              <!--end::Col-->

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

              <!--begin::Col-->
              <div class="col-md-12">                
                <!--begin::Form Validation-->
                <div class="card card-info card-outline mb-4">
                  <!--begin::Header-->
                  <div class="card-header">
	                  <div class="row w-100 align-items-center">
	                      <div class="col-md-6">
	                        <h3 class="card-title mb-0">{{ isset($details) ? 'Edit' : 'Add' }} {{$title}} Form</h3>
	                      </div>
	                      <div class="col-md-6 text-end">
	                        <div class="d-flex justify-content-end gap-2 flex-wrap">
	                          <a href="{{ route($listUrl) }}" class="btn btn-info">List</a>
	                        </div>
	                      </div>
	                    </div>
	                </div>
                  <!--end::Header-->
                  <!--begin::Form-->
                  <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data" action="#">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                      <!--begin::Row-->
                      <div class="row g-3">

                      <!--begin::Col-->
                        <div class="col-md-4 col-12" id="municipalityDiv">
                          <label for="municipality" class="form-label">Municipality<span class="text-danger">*</span></label>
                          <select class="form-select" id="municipality" name="municipality" required>
                            <option selected disabled value="">Select Municipality</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->user_id }}" {{ old('municipality') == $municipality->user_id ? 'selected' : '' }} {{ isset($details) &&  $details->municipality == $municipality->user_id ? 'selected' : '' }}>
                                  {{ $municipality->name }}
                              </option>
                            @endforeach
                          </select>
                          <div class="invalid-feedback">Please select a Municipality.</div>
                        </div>
                        <!--end::Col-->

                      	<!--begin::Col-->
                        <div class="col-md-4 col-12">
                          <label for="vehicle_type" class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="vehicle_type"
                              name="vehicle_type"
                              placeholder="Enter Vehicle Type"
                              required
                              value="{{ old('vehicle_type', $details->vehicle_type ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter Vehicle Type.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-4 col-12">
                          <label for="vehicle_reg_no" class="form-label">Vehicle Reg. Number <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="vehicle_reg_no"
                              name="vehicle_reg_no"
                              placeholder="Enter Vehicle Reg. Number"
                              required
                              oninput="this.value = this.value.toUpperCase()"
                              value="{{ old('vehicle_reg_no', $details->vehicle_reg_no ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter Vehicle Reg. Number.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-4 col-12">
                          <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="capacity"
                              name="capacity"
                              placeholder="Enter Capacity (in ltr.)"
                              required
                              value="{{ old('capacity', $details->capacity ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter Capacity (in ltr.)</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-4 col-12" id="levelDiv">
                          <label for="category" class="form-label">Category<span class="text-danger">*</span></label>
                          <select class="form-select" id="category" name="category" required>
                            <option selected disabled value="">Choose...</option>
                            @foreach($v_cats as $cat)
                                <option value="{{ $cat->id }}" {{ old('category') == $cat->id ? 'selected' : '' }} {{ isset($details) &&  $details->category == $cat->id ? 'selected' : '' }}>
                                  {{ $cat->name }}
                              </option>
                            @endforeach
                          </select>
                          <div class="invalid-feedback">Please select a category.</div>
                        </div>
                        <!--end::Col-->

                        <!--begin::hidden-->
                        <input type="hidden" name="id" id="id" value="{{ isset($details) ? $details->id : '' }}">
                        <!--end::hidden-->

                      </div>
                      <!--end::Row-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
            			     <a href="{{ route($listUrl) }}" class="btn btn-danger">Cancel</a>
                      <button class="btn btn-warning" type="reset">Reset Form</button>
                      <button class="btn btn-success" type="submit">{{ isset($details) ? 'Update' : 'Add' }} {{ $title }}</button>
                    </div>
                    <!--end::Footer-->
                  </form>
                  <!--end::Form-->
                  <!--begin::JavaScript-->
                  <script>
                    (() => {
                      'use strict';
                      const forms = document.querySelectorAll('.needs-validation');
                      Array.from(forms).forEach((form) => {
                        form.addEventListener(
                          'submit',
                          (event) => {
                            if (!form.checkValidity()) {
                              event.preventDefault();
                              event.stopPropagation();
                            }

                            form.classList.add('was-validated');
                          },
                          false,
                        );
                      });
                    })();
                  </script>
                  <!--end::JavaScript-->
                </div>
                <!--end::Form Validation-->
              </div>
              <!--end::Col-->
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
