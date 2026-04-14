<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('constants.APP_NAME') }} | {{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('common.header_links')
    <style>
      #tags-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        min-height: 42px;
        cursor: text;
      }

      #tags-container input {
        flex: 1;
        min-width: 120px;
        border: none;
        background: transparent;
        outline: none;
        padding: 5px;
      }

      .tag {
        display: inline-flex;
        align-items: center;
        background-color: #e0e0e0;
        border-radius: 15px;
        padding: 5px 10px;
        margin: 3px;
        font-size: 14px;
      }

      .tag .remove-tag {
        cursor: pointer;
        margin-left: 5px;
        color: red;
      }
    </style>

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
	                          <a href="{{ route('admin.moduleMaster') }}" class="btn btn-info">List</a>
	                        </div>
	                      </div>
	                    </div>
	                </div>
                  <!--end::Header-->
                  <!--begin::Form-->
                  <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data" action="{{ isset($details) ? route('admin.editModuleMaster.submit') : route('admin.addModuleMaster.submit') }}">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                      <!--begin::Row-->
                      <div class="row g-3">

                      	<!--begin::Col-->
                        <div class="col-md-12 col-12">
                          <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="name"
                              name="name"
                              placeholder="Enter Name"
                              required
                              value="{{ old('name', $details->name ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter name.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        @if(isset($details) && $details->filepath!="")
                        <div class="col-md-12 col-12">
                        	<label>Uploaded Image : </label>
                        	<img src="{{ url('') }}/{{$details->filepath}}" width="200">
                        </div>
                        @endif

                        <!--begin::Col-->
                        <div class="col-md-12 col-12">
                          <label for="name" class="form-label"> {!! isset($details) ? 'Update Image' : 'Upload Image <span class="text-danger">*</span>' !!}</label>
                          <div class="input-group has-validation">
                            <input
                              type="file"
                              class="form-control"
                              id="file"
                              name="file"
                              placeholder="Upload Image"
                              required
                            />                            
                            <div class="input-group-text">Upload</div>
                            <div class="invalid-feedback">Please Upload Image.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-12 col-12">
                          <label for="details" class="form-label">Details<span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <textarea class="form-control" aria-label="With textarea" id="details" name="details" placeholder="Enter Details" required>{{ old('details', $details->details ?? '') }}</textarea>
                            <div class="invalid-feedback">Please enter details.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        @php
                            $existingTags = old('tags', $details->tags ?? '');
                            $tagArray = array_filter(array_map('trim', explode(',', $existingTags)));
                        @endphp

                        <!--begin::Col-->
                        <div class="col-md-12 col-12 d-none">
                          <label for="tags-input" class="form-label">Tags (press Enter or comma)</label>
                          <div class="form-control p-2" id="tags-container">
                            @foreach ($tagArray as $tag)
                              <span class="tag">{{ $tag }}<span class="remove-tag">&times;</span></span>
                            @endforeach
                            <input
                              type="text"
                              id="tags-input"
                              class="border-0"
                              placeholder="Type and press Enter or comma"
                              style="outline: none;"
                            />
                          </div>
                          <input type="hidden" name="tags" id="tags-hidden" value="{{ implode(',', $tagArray) }}">
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
            			    <a href="{{ route('admin.moduleMaster') }}" class="btn btn-danger">Cancel</a>
                      <button class="btn btn-warning" type="reset">Reset Form</button>
                      <button class="btn btn-success" type="submit">{{ isset($details) ? 'Update' : 'Add' }} {{ $title }}</button>
                    </div>
                    <!--end::Footer-->
                  </form>
                  <!--end::Form-->
                  <!--begin::JavaScript-->
                  <script>
                    // Example starter JavaScript for disabling form submissions if there are invalid fields
                    (() => {
                      'use strict';

                      // Fetch all the forms we want to apply custom Bootstrap validation styles to
                      const forms = document.querySelectorAll('.needs-validation');

                      // Loop over them and prevent submission
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
    <script type="text/javascript">
    	$( document ).ready(function() {
		    let id = "{{ isset($details) ? $details->id : '' }}";
		    if(id!="")
		    {
		    	$('#file').prop('required', false);
		    }
		    else
		    {
		    	$('#file').prop('required', true);
		    }
		});
    </script>

    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
