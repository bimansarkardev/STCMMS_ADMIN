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
	                          <a href="{{ route($listUrl) }}" class="btn btn-info"><i class="bi bi-list"></i> List</a>
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
                        <div class="col-md-4 col-12" id="levelDiv">
                          <label for="ward" class="form-label">Located Ward No.<span class="text-danger">*</span></label>
                          <select class="form-select" id="ward" name="ward" required>
                            <option selected disabled value="">Select</option>
                          </select>
                          <div class="invalid-feedback">Please select a valid ward.</div>
                        </div>
                        <!--end::Col-->

                      	<!--begin::Col-->
                        <div class="col-md-4 col-12">
                          <label for="location" class="form-label">Location/Address/Name <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="location"
                              name="location"
                              placeholder="Enter Location/Address/Name"
                              required
                              value="{{ old('location', $details->location ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter Location/Address/Name.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-4 col-12" id="levelDiv">
                          <label for="category" class="form-label">Category<span class="text-danger">*</span></label>
                          <select class="form-select" id="category" name="category" required>
                            <option selected disabled value="">Select</option>
                            @foreach($plant_categories as $pc)
                                <option value="{{ $pc->id }}" {{ old('category') == $pc->id ? 'selected' : '' }} {{ isset($details) &&  $details->category == $pc->id ? 'selected' : '' }}>
                                  {{ $pc->name }}
                              </option>
                            @endforeach
                          </select>
                          <div class="invalid-feedback">Please select a category.</div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-4 col-12">
                          <label for="capacity" class="form-label">Capacity (In ltr.)<span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="capacity"
                              name="capacity"
                              placeholder="Enter Capacity (In ltr.)"
                              required
                              value="{{ old('capacity', $details->capacity ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter Capacity (In ltr.).</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <div class="col-md-6 col-12">
                            <label class="form-label">
                                Who operates this plant? <span class="text-danger">*</span>
                            </label><br>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input operateByRadio" type="radio" name="operate_by" id="mo" value="1"
                                    {{ old('operate_by', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="mo">
                                    Municipality Operated
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input operateByRadio" type="radio" name="operate_by" id="ao" value="2"
                                    {{ old('operate_by') == '2' ? 'checked' : '' }}>
                                <label class="form-check-label" for="ao">
                                    Agency Operated
                                </label>
                            </div>
                        </div>

                        <!--begin::Col-->
                        <div class="col-md-6 col-12" id="agencyDiv">
                          <label for="agency" class="form-label">Choose Operate by Agency<span class="text-danger">*</span></label>
                          <select class="form-select" id="agency" name="agency">
                            <option selected disabled value="">Select Agency</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}" {{ old('agency') == $agency->id ? 'selected' : '' }} {{ isset($details) &&  $details->agency == $agency->id ? 'selected' : '' }}>
                                  {{ $agency->agency_name }}
                              </option>
                            @endforeach
                          </select>
                          <div class="invalid-feedback">Please select a category.</div>
                        </div>
                        <!--end::Col-->
                        
                        <div class="col-md-12">
                            <label class="form-label">Incharge Details <span class="text-danger">*</span></label>

                            <div id="inchargeWrapper">
                                <div class="row g-3 incharge-item mb-2">
                                    <div class="col-md-5">
                                        <input type="text" name="incharge_name[]" class="form-control" placeholder="Enter Incharge Name" required>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="incharge_contact_no[]" class="form-control" placeholder="Enter Contact Number" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-btn w-100"><i class="bi bi-trash-fill"></i> Remove</button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="addMore" class="btn btn-sm btn-primary mt-2">
                                + Add More Incharge
                            </button>
                        </div>

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
      <script>
        document.addEventListener('DOMContentLoaded', function () {

            const wrapper = document.getElementById('inchargeWrapper');
            const addBtn = document.getElementById('addMore');

            // Add More
            addBtn.addEventListener('click', function () {
                const html = `
                    <div class="row g-3 incharge-item mb-2">
                        <div class="col-md-5">
                            <input type="text" name="incharge_name[]" class="form-control" placeholder="Enter Incharge Name" required>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="incharge_contact_no[]" class="form-control" placeholder="Enter Contact Number" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-btn w-100"><i class="bi bi-trash-fill"></i> Remove</button>
                        </div>
                    </div>
                `;
                wrapper.insertAdjacentHTML('beforeend', html);
            });

            // Remove
            wrapper.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-btn')) {
                    const items = document.querySelectorAll('.incharge-item');
                    
                    if (items.length > 1) {
                        e.target.closest('.incharge-item').remove();
                    } else {
                        alertInfoMessage('At least one incharge is required');
                    }
                }
            });

        });
        </script>
        <script>
          document.getElementById('municipality').addEventListener('change', function () {

              let municipalityId = this.value;
              let wardDropdown = document.getElementById('ward');

              // Reset dropdown
              wardDropdown.innerHTML = '<option value="">Loading...</option>';

              fetch(`{{ route('admin.getMunicipalityWards') }}?municipality_id=${municipalityId}`)
                  .then(response => response.json())
                  .then(data => {

                      let options = '<option value="">Select Ward</option>';

                      data.forEach(ward => {
                          options += `<option value="${ward.id}">${ward.ward_no}</option>`;
                      });

                      wardDropdown.innerHTML = options;
                  })
                  .catch(error => {
                      wardDropdown.innerHTML = '<option value="">Error loading wards</option>';
                      console.error(error);
                  });
          });
        </script>
        <script>
          document.addEventListener('DOMContentLoaded', function () {

              let municipalityId = document.getElementById('municipality').value;
              let selectedWard = "{{ old('ward', $details->ward ?? '') }}";

              if (municipalityId) {
                  fetch(`{{ route('admin.getMunicipalityWards') }}?municipality_id=${municipalityId}`)
                      .then(res => res.json())
                      .then(data => {

                          let options = '<option value="">Select Ward</option>';

                          data.forEach(ward => {
                              let selected = ward.id == selectedWard ? 'selected' : '';
                              options += `<option value="${ward.id}" ${selected}>${ward.ward_no} No. Ward</option>`;
                          });

                          document.getElementById('ward').innerHTML = options;
                      });
              }

          });
        </script>
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    @include('common.script')
    <script>
      $(document).ready(function () {

        function toggleOperateByFields(operate_by) {

            if (operate_by == 1) {
                // ✅ Municipality
                $("#agencyDiv").hide();
                 $("#agency").prop('required', false).val('');
            } else {
                // ✅ Agency
                $("#agencyDiv").show();               
                $("#agency").prop('required', true);
            }
        }

        // 🔁 Radio change
        $('.operateByRadio').on('change', function () {
            let val = $(this).val();
            toggleOperateByFields(val);
        });

        // 🚀 Initial load
        let selected = $('.operateByRadio:checked').val();
        toggleOperateByFields(selected);
    });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
