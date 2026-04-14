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
                  <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data" action="{{ isset($details) ? route('admin.editTagStpFstp.submit') : route('admin.tagStpFstp.submit') }}">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                      <!--begin::Row-->
                      <div class="row g-3">

                        <!--begin::Col-->
                        <div class="col-md-4 col-12" id="municipalityDiv">
                          <label for="municipality" class="form-label">Select Municipality to search Plants<span class="text-danger">*</span></label>
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
                        <div class="col-md-8 col-12" id="plantDiv">
                          <label for="plant" class="form-label">Plant (STP/FSTP)<span class="text-danger">*</span></label>
                          <select class="form-select" id="plant" name="plant" required>
                            <option selected disabled value="">Select</option>
                          </select>
                          <div class="invalid-feedback">Please select a valid Plant (STP/FSTP).</div>
                        </div>
                        <!--end::Col-->

                        <!-- now here want another municipality dropdown multi select just like tag and can remove also so can tag municipalities with the plant -->

                        <!--begin::Col-->
                        <div class="col-md-12 col-12" id="taggedMunicipalityDiv">
                          <label for="taggedMunicipalities" class="form-label">Select Municipality to tagg with your selected Plant<span class="text-danger">*</span></label>
                            <select class="form-select" id="taggedMunicipalities" 
                                    name="taggedMunicipalities[]" 
                                    multiple="multiple" required>
                                
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality->user_id }}">
                                        {{ $municipality->name }}
                                    </option>
                                @endforeach
                            </select>
                          <div class="invalid-feedback">Please select at least one Municipality.</div>
                        </div>
                        <!--end::Col-->

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
    <script>
        document.getElementById('municipality').addEventListener('change', function () {

            let municipalityId = this.value;
            let plantDropdown = document.getElementById('plant');

            // Reset dropdown
            plantDropdown.innerHTML = '<option value="">Loading...</option>';

            fetch(`{{ route('admin.getMunicipalityPlants') }}?municipality_id=${municipalityId}`)
            .then(response => response.json())
            .then(data => {

                let options = '';

                // ✅ Check if empty
                if (!data || data.length === 0) {
                    options = '<option value="">No Plants found under this municipality</option>';
                } else {
                    options = '<option value="">Select Plant</option>';

                    data.forEach(plant => {
                        options += `
                            <option value="${plant.id}">
                                ${plant.municipality_name} - ${plant.ward_no} - ${plant.location} - ${plant.category_name} (${plant.capacity_mod})
                            </option>
                        `;
                    });
                }

                plantDropdown.innerHTML = options;
            })
            .catch(error => {
                plantDropdown.innerHTML = '<option value="">Error loading Plants</option>';
                console.error(error);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#taggedMunicipalities').select2({
                placeholder: "Select Municipalities",
                width: '100%',
                closeOnSelect: false,   // 🔥 keeps dropdown open
            });
        });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
