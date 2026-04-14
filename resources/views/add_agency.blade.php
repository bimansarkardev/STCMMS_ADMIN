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
                        <div class="col-md-6 col-12">
                          <label for="agency_name" class="form-label">Agency Name <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="agency_name"
                              name="agency_name"
                              placeholder="Enter Agency Name"
                              required
                              value="{{ old('agency_name', $details->agency_name ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter Agency Name.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6 col-12">
                          <label for="agency_address" class="form-label">Agency Address (Optional)</label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="agency_address"
                              name="agency_address"
                              placeholder="Enter Agency Address"
                              value="{{ old('agency_address', $details->agency_address ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter Agency Address.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6 col-12">
                          <label for="contact_person" class="form-label">Contact Person Name (Optional)</label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="contact_person"
                              name="contact_person"
                              placeholder="Enter Contact Person Name"
                              value="{{ old('contact_person', $details->contact_person ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter Contact Person Name.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6 col-12">
                          <label for="contact_person_contact_number" class="form-label">Contact Person Contact Number (Optional)</label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="contact_person_contact_number"
                              name="contact_person_contact_number"
                              placeholder="Enter Incharge Contact Number"
                              value="{{ old('contact_person_contact_number', $details->contact_person_contact_number ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter Contact Person Contact Number.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <div class="col-md-12">
                            <label class="form-label">Wokring with Municipalities Details <span class="text-danger">*</span></label>

                            <div id="municipalityWrapper">
                                <div class="row g-3 municipality-item mb-2">
                                    <div class="col-md-3">
                                        <label for="" class="form-label"><small>Municipality</small></label>
                                        <select class="form-select" name="municipality_id[]" required>
                                          <option selected disabled value="">Select Municipality</option>
                                          @foreach($municipalities as $municipality)
                                              <option value="{{ $municipality->user_id }}" {{ old('municipality[]') == $municipality->user_id ? 'selected' : '' }}>
                                                {{ $municipality->name }}
                                            </option>
                                          @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="" class="form-label"><small>Contract From Date</small></label>
                                        <input type="date" name="contract_from_date[]" class="form-control" placeholder="Enter Contract From Date" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="" class="form-label"><small>Contract Upto Date</small></label>
                                        <input type="date" name="contract_to_date[]" class="form-control" placeholder="Enter Contract Upto Date" required>
                                    </div>
                                    <div class="col-md-3">
                                      <label for="" class="form-label"><small>Upload Contract File</small></label>
                                      <input type="file" name="contract_file[]" class="form-control" placeholder="Enter Contract From Date" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="" class="form-label"></label>
                                        <button type="button" class="btn btn-danger remove-btn w-100"><i class="bi bi-trash-fill"></i> Remove</button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="addMore" class="btn btn-sm btn-primary mt-2">
                                + Add More Municipality
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

            const wrapper = document.getElementById('municipalityWrapper');
            const addBtn = document.getElementById('addMore');

            // Add More
            addBtn.addEventListener('click', function () {
                const html = `
                    <div class="row g-3 municipality-item mb-2">
                        <div class="col-md-3">
                            <select class="form-select" name="municipality_id[]" required>
                              <option selected disabled value=""><small>Select Municipality</small></option>
                              @foreach($municipalities as $municipality)
                                  <option value="{{ $municipality->user_id }}">
                                    {{ $municipality->name }}
                                </option>
                              @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="contract_from_date[]" class="form-control" placeholder="Enter Contract From Date" required>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="contract_to_date[]" class="form-control" placeholder="Enter Contract Upto Date" required>
                        </div>
                        <div class="col-md-3">
                          <input type="file" name="contract_file[]" class="form-control" placeholder="Enter Contract From Date" required>
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
                    const items = document.querySelectorAll('.municipality-item');
                    
                    if (items.length > 1) {
                        e.target.closest('.municipality-item').remove();
                    } else {
                        alertInfoMessage('At least one Municipality is required');
                    }
                }
            });

        });
        </script>
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    @include('common.script')
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
