<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('constants.APP_NAME') }} | {{ $user_type->add_menu_name }}</title>
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
              <div class="col-sm-6"><h3 class="mb-0">{{ isset($user) ? $user_type->edit_menu_name : $user_type->add_menu_name }}</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ isset($user) ? $user_type->edit_menu_name : $user_type->add_menu_name }}</li>
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
                  <span class="bi bi-info-circle-fill"></span> Please fill in all required <span class="text-danger">*</span> fields below. After completing the form, click <strong>"{{ isset($user) ? 'Update' : 'Add' }} {{ $user_type->menu_title }}"</strong> to submit. Click <strong>"Reset Form"</strong> to clear the form.
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

              @if(session('failed'))
              <div class="col-12">
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>Error!</strong> {{ session('failed') }}
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
	                        <h3 class="card-title mb-0">{{ isset($user) ? $user_type->edit_menu_name : $user_type->add_menu_name }} Form</h3>
	                      </div>
	                      <div class="col-md-6 text-end">
	                        <div class="d-flex justify-content-end gap-2 flex-wrap">
	                          {{-- list --}}
	                          <a href="{{ route('admin.fieldWorkers') }}" class="btn btn-info">List</a>
	                        </div>
	                      </div>
	                    </div>
	                </div>
                  <!--end::Header-->
                  <!--begin::Form-->
                  <form class="needs-validation" novalidate method="POST" action="{{ isset($user) ? route('admin.editUser.submit') : route('admin.addFieldWorkers.submit') }}" autocomplete="off">
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
                        <div class="col-md-4 col-12" id="roleDiv">
                          <label for="role" class="form-label">{{ $user_type->menu_title }} Role<span class="text-danger">*</span></label>
                          <select class="form-select" id="role" name="role" required>
                            <option selected disabled value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }} {{ isset($details) &&  $details->role == $role->id ? 'selected' : '' }}>
                                  {{ $role->name }}
                              </option>
                            @endforeach
                          </select>
                          <div class="invalid-feedback">Please select a Role.</div>
                        </div>
                        <!--end::Col-->

                      	<!--begin::Col-->
                        <div class="col-md-4 col-12">
                          <label for="name" class="form-label">{{ $user_type->menu_title }} Name <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="name"
                              name="name"
                              placeholder="Enter {{ $user_type->menu_title }} Name"
                              required
                              value="{{ old('name', $user->name ?? '') }}"
                            />
                            <div class="invalid-feedback">Please enter {{ $user_type->menu_title }} Name.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <div class="col-md-4 col-12">
                            <label class="form-label">Is this a User? <span class="text-danger">*</span></label><br>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input isUserRadio" type="radio" name="is_user" id="isUserNo" value="0"
                                    {{ old('is_user', '0') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="isUserNo">No</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input isUserRadio" type="radio" name="is_user" id="isUserYes" value="1"
                                    {{ old('is_user') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="isUserYes">Yes</label>
                            </div>

                        </div>

                        <!--begin::Col-->
                        <div class="col-md-4 col-12" id="mobileDiv">
                          <label for="mobile" class="form-label">{{ $user_type->menu_title }} Contact Number <span id="mobileLabel">(Optional)</span></label>
                          <div class="input-group has-validation">
                            <input
                              type="text"
                              class="form-control"
                              id="mobile"
                              name="mobile"
                              placeholder="Enter {{ $user_type->menu_title }} Contact Number"
                              value="{{ old('mobile', $user->mobile ?? '') }}"
                            />
                            <div class="input-group-text"><span class="bi bi-phone"></span></div>
                            <div class="invalid-feedback">Please enter {{ $user_type->menu_title }} Contact Number.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <div class="col-md-4 col-12" id="full_addressDiv">
                          <label for="full_address" class="form-label">{{ $user_type->menu_title }} Address (Optional) </label>
                          <input
                            type="text"
                            class="form-control"
                            id="full_address"
                            name="full_address"
                            placeholder="Type address here"
                            value="{{ old('full_address', $user->address ?? '') }}"
                          />
                          <div class="invalid-feedback">Please enter the address.</div>
                        </div>

                        <!--begin::Col-->
                        <div class="col-md-4 col-12" id="userIdDiv">
                          <label for="email" class="form-label">{{ $user_type->menu_title }} User Id <span class="text-danger">*</span> (This will be the login id) </label>
                          <div class="input-group has-validation">
                            <span class="input-group-text">#</span>
                            <input
                              type="text"
                              class="form-control"
                              id="email"
                              name="email"
                              placeholder="Automatically generated using mobile number"
                              readonly
                              value="{{ old('email', $user->username ?? '') }}"
                            />
                            <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                            <div class="invalid-feedback">Please enter {{ $user_type->menu_title }} CODE.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <div class="col-md-12 col-12" id="wantPass" style="display : none;">
                          <button class="btn btn-info" type="button" onclick="wantpass()">Click if Want to change password</button>
                        </div>

                        <div class="col-md-12 col-12" id="notWantPass" style="display : none;">
                          <button class="btn btn-info" type="button" onclick="notwantpass()">Click if Not want to change password</button>
                        </div>

                        <!--begin::Col-->
                        <div class="col-md-4 col-12" id="newPassDiv">
                          <label for="newPass" class="form-label">New password <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <span class="input-group-text"><span class="bi bi-lock-fill"></span></span>
                            <input
                              type="password"
                              class="form-control"
                              id="newPass"
                              name="newPass"
                              placeholder="Create a new password"
                              required
                              value="{{ old('newPass') }}"
                              autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            />
                            <div class="input-group-text">
                              <span class="bi bi-eye-slash toggle-password" data-target="newPass" style="cursor: pointer;"></span>
                            </div>
                            <div class="invalid-feedback">Please create new password.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-4 col-12" id="newPassConfirmDiv">
                          <label for="newPassConfirm" class="form-label">Confirm new password <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <span class="input-group-text"><span class="bi bi-lock-fill"></span></span>
                            <input
                              type="password"
                              class="form-control"
                              id="newPassConfirm"
                              name="newPassConfirm"
                              placeholder="Confirm your new password"
                              required
                              value="{{ old('newPassConfirm') }}"
                              autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            />
                            <div class="input-group-text">
                              <span class="bi bi-eye-slash toggle-password" data-target="newPassConfirm" style="cursor: pointer;"></span>
                            </div>
                            <div class="invalid-feedback">Please confirm new password.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <div class="col-md-6 col-12">
                            <label class="form-label">
                              Who Does the {{ $user_type->menu_title }} Work With? <span class="text-danger">*</span>
                            </label><br>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input operateByRadio" type="radio" name="operate_by" id="mo" value="1"
                                    {{ old('operate_by', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="mo">
                                    With Municipality
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input operateByRadio" type="radio" name="operate_by" id="ao" value="2"
                                    {{ old('operate_by') == '2' ? 'checked' : '' }}>
                                <label class="form-check-label" for="ao">
                                    With Agency
                                </label>
                            </div>
                        </div>

                        <!--begin::Col-->
                        <div class="col-md-6 col-12" id="agencyDiv">
                          <label for="agency" class="form-label">Select Agency<span class="text-danger">*</span></label>
                          <select class="form-select" id="agency" name="agency">
                            <option selected disabled value="">-- Select Agency --</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}" {{ old('agency') == $agency->id ? 'selected' : '' }} {{ isset($details) &&  $details->agency == $agency->id ? 'selected' : '' }}>
                                  {{ $agency->agency_name }}
                              </option>
                            @endforeach
                          </select>
                          <div class="invalid-feedback">Please select a category.</div>
                        </div>
                        <!--end::Col-->

                        <!--begin::hidden-->
                        <input type="hidden" name="user_type_id" id="user_type_id" value="{{ $user_type->id }}">
                        <input type="hidden" name="user_id" id="user_id" value="{{ isset($user) ? $user->user_id : '' }}">
                        <!--end::hidden-->

                      </div>
                      <!--end::Row-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
            			     <a href="{{ route('admin.users.param', $user_type->slug) }}" class="btn btn-danger">Cancel</a>
                      <button class="btn btn-warning" type="reset">Reset Form</button>
                      <button class="btn btn-success" type="submit">{{ isset($user) ? 'Update' : 'Add' }} {{ $user_type->menu_title }}</button>
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

    <script>
      document.querySelectorAll('.toggle-password').forEach((toggle) => {
        toggle.addEventListener('click', function () {
          const targetId = this.getAttribute('data-target');
          const input = document.getElementById(targetId);
          const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
          input.setAttribute('type', type);
          this.classList.toggle('bi-eye');
          this.classList.toggle('bi-eye-slash');
        });
      });
    </script>

    <script type="text/javascript">
    	$( document ).ready(function() {

        let user_type_id = "{{ isset($user_type) ? $user_type->id : '' }}";

        $('#newPass').val('');
        $('#newPassConfirm').val('');

        if(user_type_id==3)
        {
          
        }

		    let user_id = "{{ isset($user) ? $user->user_id : '' }}";
		    //alert(user_id);
		    if(user_id!="")
		    {
		    	$("#newPassDiv").hide();
		    	$('#newPass').prop('required', false);
		    	$("#newPassConfirmDiv").hide();
		    	$('#newPassConfirm').prop('required', false);
          $("#wantPass").show();
		    }
		    else
		    {
		    	$("#newPassDiv").show();
		    	$('#newPass').prop('required', true);
		    	$("#newPassConfirmDiv").show();
		    	$('#newPassConfirm').prop('required', true);
		    }
		});
    </script>
    <script type="text/javascript">
      function wantpass() 
      {
          $("#newPassDiv").show();
          $('#newPass').prop('required', true);
          $("#newPassConfirmDiv").show();
          $('#newPassConfirm').prop('required', true);
          $("#wantPass").hide();
          $("#notWantPass").show();
      }
      function notwantpass() 
      {
          $("#newPassDiv").hide();
          $('#newPass').prop('required', false);
          $("#newPassConfirmDiv").hide();
          $('#newPassConfirm').prop('required', false);
          $("#wantPass").show();
          $("#notWantPass").hide();
      }
    </script>
    <script>
      $(document).ready(function () {

        function toggleUserFields(isUser) {

            if (isUser == 1) {
                // ✅ YES USER
                $("#mobile").prop('required', true);
                $("#mobileLabel").html('<span class="text-danger">*</span>');

                $("#userIdDiv").show();
                $("#email").prop('required', true);

                $("#newPassDiv").show();
                $("#newPass").prop('required', true);                

                $("#newPassConfirmDiv").show();
                $("#newPassConfirm").prop('required', true);

            } else {
                // ❌ NOT USER
                $("#mobile").prop('required', false);
                $("#mobileLabel").html('(optional)');

                $("#userIdDiv").hide();
                $("#email").prop('required', false).val('');

                $("#newPassDiv").hide();
                $("#newPass").prop('required', false).val('');

                $("#newPassConfirmDiv").hide();
                $("#newPassConfirm").prop('required', false).val('');
            }
        }

        // 🔁 Radio change
        $('.isUserRadio').on('change', function () {
            let val = $(this).val();
            toggleUserFields(val);
        });

        // 📱 Auto-fill User ID from Mobile
        $('#mobile').on('keyup', function () {
            $('#email').val($(this).val());
        });

        // 🚀 Initial load
        let selected = $('.isUserRadio:checked').val();
        toggleUserFields(selected);
    });
    </script>
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
