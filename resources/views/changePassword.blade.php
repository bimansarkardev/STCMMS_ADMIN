<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('constants.APP_NAME') }} | Change Password</title>
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
              <div class="col-sm-6"><h3 class="mb-0">Change Password</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Change Password</li>
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
                  <span class="bi bi-info-circle-fill"></span> Please enter your registered email address, your current password, and your new password. Make sure the new password is secure and confirmed correctly.
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
                  <div class="card-header"><div class="card-title">Change Password Form</div></div>
                  <!--end::Header-->
                  <!--begin::Form-->
                  <form class="needs-validation" novalidate method="POST" action="{{ route('admin.changePassword.submit') }}">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                      <!--begin::Row-->
                      <div class="row g-3">

                        <!--begin::Col-->
                        <div class="col-md-6 col-12">
                          <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <span class="input-group-text">@</span>
                            <input
                              type="text"
                              class="form-control"
                              id="email"
                              name="email"
                              placeholder="Enter your registered email"
                              required
                            />
                            <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                            <div class="invalid-feedback">Please enter your email.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6 col-12">
                          <label for="currentPass" class="form-label">Current password <span class="text-danger">*</span></label>
                          <div class="input-group has-validation">
                            <span class="input-group-text"><span class="bi bi-lock-fill"></span></span>
                            <input
                              type="password"
                              class="form-control"
                              id="currentPass"
                              name="currentPass"
                              placeholder="Enter your current password"
                              required
                            />
                            <div class="input-group-text">
                              <span class="bi bi-eye-slash toggle-password" data-target="currentPass" style="cursor: pointer;"></span>
                            </div>
                            <div class="invalid-feedback">Please enter your current password.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6 col-12">
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
                            />
                            <div class="input-group-text">
                              <span class="bi bi-eye-slash toggle-password" data-target="newPass" style="cursor: pointer;"></span>
                            </div>
                            <div class="invalid-feedback">Please create your new password.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6 col-12">
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
                            />
                            <div class="input-group-text">
                              <span class="bi bi-eye-slash toggle-password" data-target="newPassConfirm" style="cursor: pointer;"></span>
                            </div>
                            <div class="invalid-feedback">Please confirm your new password.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                      </div>
                      <!--end::Row-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">                      
                      <button class="btn btn-warning" type="reset">Reset Form</button>
                      <button class="btn btn-info" type="submit">Change Password</button>
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
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
