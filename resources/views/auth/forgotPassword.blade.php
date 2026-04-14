<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('constants.APP_NAME') }} | Login </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}" />
    <style type="text/css">
      a.disabled {
          pointer-events: none;
          cursor: not-allowed;
          color: #999; /* Optional: to give it a 'disabled' look */
          text-decoration: none;
      }
    </style>
    <!--end::Required Plugin(AdminLTE)-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/custom/alert.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-sanitize.js"></script>
    <script type="text/javascript">
      var app = angular.module('myApp', ['ngSanitize']);
      app.config(function($interpolateProvider) {
          $interpolateProvider.startSymbol('[[');
          $interpolateProvider.endSymbol(']]');
      });
      app.controller('Ctrl', function ($scope, $http) {

          $scope.resendDisabled = false;
          $scope.countdown = 0;
          let countdownInterval = null;

          $scope.forgetPassword = function () 
          {
              var forgotPasswordOtpUrl = "{{ route('forgotPasswordOtp') }}";
              var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
              let email = $("#email").val();

              if (!email) {
                  alertErrorMessage('Please enter your registered email');
              } else {
                  var Indata = { email: email };

                  var req = {
                      method: 'POST',
                      url: forgotPasswordOtpUrl,
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': token
                      },
                      data: Indata
                  };

                  $http(req).then(function(response){
                      console.log(response.data);
                      alertSuccessMessage(response.data.message);
                      $(".login-box-msg").html(response.data.message);
                      $("#passwordDiv").show();
                      $("#confirm_passwordDiv").show();
                      $("#otpDiv").show();
                      $("#forgetPasswordDiv").hide();
                      $("#resetPasswordDiv").show();
                      $('#email').attr('readonly', true);
                      $scope.startResendTimer();
                  }, function(error){
                      console.error(error.data);
                      alertErrorMessage(error.data?.error || 'Something went wrong');
                  });
              }
          }

          $scope.startResendTimer = function() {
              $scope.resendDisabled = true;
              $scope.countdown = 120;

              if (countdownInterval) {
                  clearInterval(countdownInterval);
              }

              countdownInterval = setInterval(function () {
                  $scope.countdown--;
                  if ($scope.countdown <= 0) {
                      clearInterval(countdownInterval);
                      $scope.resendDisabled = false;
                  }
                  $scope.$apply();
              }, 1000);
          }

          $scope.resendOtp = function () {
              if (!$scope.resendDisabled) {
                  $scope.forgetPassword();
              }
          };

          $scope.resetPassword = function()
          {
              var resetPasswordUrl = "{{ route('resetPassword') }}";
              var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
              let email = $("#email").val();
              let password = $("#password").val();
              let confirm_password = $("#confirm_password").val();
              let otp = $("#otp").val();

              var Indata = { 
                  email: email,
                  password: password,
                  confirm_password: confirm_password,
                  otp: otp,
              };

              var req = {
                  method: 'POST',
                  url: resetPasswordUrl,
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': token
                  },
                  data: Indata
              };

              $http(req).then(function(response){
                  console.log(response.data);
                  confirmAndGotToUrl('Sussess',response.data.message,'success','{{ route("logout") }}');
              }, function(error){
                console.error(error.data);
                if (error.data?.errors) {
                    let messages = [];
                    angular.forEach(error.data.errors, function(value, key) {
                        value.forEach(function(msg) {
                            messages.push(msg);
                        });
                    });
                    alertErrorMessage(messages.join('\n'));
                } else {
                    alertErrorMessage(error.data?.error || 'Something went wrong');
                }
              });
          }


      //end   
      });
    </script>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="login-page text-bg-info" id="angular_div" ng-app="myApp" ng-controller="Ctrl" ng-cloak>
    <div class="login-box">
      <div class="login-logo">
        <a href="../index2.html">{{ config('constants.APP_NAME_SHORT') }}</a>
      </div>
      <!-- /.login-logo -->
      <div class="card">
        
        <div class="card-body login-card-body">
          <p class="login-box-msg">Enter your registered email to reset password</p>

          @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Error!</strong> {{ session('error') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          @endif

          @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Success!</strong> {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          @endif
          
          <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="input-group mb-3">
              <input type="email" name="email" id="email" class="form-control" placeholder="Email" />
              <div class="input-group-text"><span class="bi bi-envelope"></span></div>
            </div>
            <div class="input-group mb-3" style="display: none;" id="passwordDiv">
              <input type="password" name="password" id="password" class="form-control" placeholder="New Password" />
              <div class="input-group-text">
                <span class="bi bi-eye-slash toggle-password" data-target="password" style="cursor: pointer;"></span>
              </div>
            </div>
            <div class="input-group mb-3" style="display: none;" id="confirm_passwordDiv">
              <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" />
              <div class="input-group-text">
                <span class="bi bi-eye-slash toggle-password" data-target="confirm_password" style="cursor: pointer;"></span>
              </div>
            </div>
            <div class="input-group mb-3" style="display: none;" id="otpDiv">
              <input type="number" name="otp" id="otp" class="form-control" placeholder="Enter OTP" />
              <div class="input-group-text">
                <span class="bi bi-lock-fill"></span>
              </div>
            </div>
            <!--begin::Row-->
            <div class="row">
              <!-- /.col -->
              <div class="col-12" id="forgetPasswordDiv">
                <div class="d-grid gap-2">
                  <button type="button" class="btn btn-primary" ng-click="forgetPassword()">Forgot Password</button>
                </div>
              </div>

              <div class="col-12" id="resetPasswordDiv" style="display : none;">
                <div class="d-grid gap-2">
                  <button type="button" class="btn btn-primary" ng-click="resetPassword()">Reset Password</button>
                </div>
                <!-- <p class="mb-1"><a href="javascript:void(0)" ng-click="forgetPassword()" title="Resend OTP">Resend OTP</a></p> -->
                <p class="mb-1" align="center">
                  <a href="javascript:void(0)"
                     ng-click="resendOtp()"
                     ng-disabled="resendDisabled"
                     ng-class="{ 'disabled': resendDisabled }"
                     title="Resend OTP">
                     <span ng-if="resendDisabled">Resend OTP will be available in [[ countdown ]]s</span>
                     <span ng-if="!resendDisabled">Resend OTP</span>
                  </a>
                </p>
              </div>

              <!-- /.col -->
            </div>
            <!--end::Row-->
          </form>
          <div class="social-auth-links text-center mb-3 d-grid gap-2">
            <p>- OR -</p>
            <a href="{{ route('login') }}" class="btn btn-success">
              Sign In
            </a>
          </div>

        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
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
    <!--end::OverlayScrollbars Configure-->
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
