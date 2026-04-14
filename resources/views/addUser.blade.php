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
	                          <a href="{{ route('admin.users.param', $user_type->slug) }}" class="btn btn-info">List</a>
	                        </div>
	                      </div>
	                    </div>
	                </div>
                  <!--end::Header-->
                  <!--begin::Form-->
                  <form class="needs-validation" novalidate method="POST" action="{{ isset($user) ? route('admin.editUser.submit') : route('admin.addUser.submit') }}" autocomplete="off">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                      <!--begin::Row-->
                      <div class="row g-3">

                      	<!--begin::Col-->
                        <div class="col-md-6 col-12">
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

                        <!--begin::Col-->
                        <div class="col-md-6 col-12">
                          <label for="email" class="form-label">{{ $user_type->menu_title }} Code <span class="text-danger">*</span> (This will be the login id) </label>
                          <div class="input-group has-validation">
                            <span class="input-group-text">#</span>
                            <input
                              type="text"
                              class="form-control"
                              id="email"
                              name="email"
                              placeholder="Enter {{ $user_type->menu_title }} Code"
                              required
                              value="{{ old('email', $user->username ?? '') }}"
                            />
                            <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                            <div class="invalid-feedback">Please enter {{ $user_type->menu_title }} CODE.</div>
                          </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-4 col-12" id="levelDiv">
                          <label for="district" class="form-label">District <span class="text-danger">*</span></label>
                          <select class="form-select" id="district" name="district" required>
                            <option selected disabled value="">Select District</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district') == $district->id ? 'selected' : '' }} {{ isset($user) &&  $user->district_id == $district->id ? 'selected' : '' }}>
                                  {{ $district->district_name }}
                              </option>
                            @endforeach
                          </select>
                          <div class="invalid-feedback">Please select a District.</div>
                        </div>
                        <!--end::Col-->

                        <div class="col-md-4 col-12" id="full_addressDiv">
                          <label for="full_address" class="form-label">Address (Optional) </label>
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
                        <div class="col-md-4 col-12">
                          <label for="mobile" class="form-label">{{ $user_type->menu_title }} Contact Number (Optional)</label>
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

                        <div class="col-md-12 col-12" id="wantPass" style="display : none;">
                          <button class="btn btn-info" type="button" onclick="wantpass()">Click if Want to change password</button>
                        </div>

                        <div class="col-md-12 col-12" id="notWantPass" style="display : none;">
                          <button class="btn btn-info" type="button" onclick="notwantpass()">Click if Not want to change password</button>
                        </div>

                        <!--begin::Col-->
                        <div class="col-md-6 col-12" id="newPassDiv">
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
                        <div class="col-md-6 col-12" id="newPassConfirmDiv">
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

                        <!--begin::Col-->
                        <div class="col-md-12 col-12 d-none" id="addressDiv">
                          <label for="autocompleteAddress" class="form-label">Search Address <span class="text-danger">*</span></label>
                          <input
                            type="text"
                            class="form-control"
                            id="autocompleteAddress"
                            name="address"
                            placeholder="Type address here"
                            value="{{ old('address', $user->address ?? '') }}"                            
                          />
                          <div class="invalid-feedback">Please enter the address.</div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Auto-filled Fields-->
                        <div class="col-md-4 col-12 d-none" id="cityDiv">
                          <label for="city" class="form-label">City</label>
                          <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $user->city ?? '') }}" placeholder="Auto-filled from address">
                        </div>
                        <div class="col-md-4 col-12  d-none" id="stateDiv">
                          <label for="state" class="form-label">State</label>
                          <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $user->state ?? '') }}" placeholder="Auto-filled from address">
                        </div>
                        <div class="col-md-4 col-12  d-none" id="zipCodeDiv">
                          <label for="zip_code" class="form-label">Zip Code</label>
                          <input type="text" class="form-control" id="zip_code" name="zip_code" value="{{ old('zip_code', $user->zip_code ?? '') }}" placeholder="Auto-filled from address">
                        </div>
                        <!--end::Auto-filled Fields-->

                        <!--begin::Map & Geofence-->
                        <div class="col-md-12 col-12 mt-3 d-none" id="mapDiv">
                          <label class="form-label">Select Location</label>
                          <div id="map" style="height: 400px; border: 2px solid #ccc; border-radius: 8px;"></div>

                          <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $user->latitude ?? '') }}">
                          <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $user->longitude ?? '') }}">
                        </div>
                        <!--end::Map & Geofence-->

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

    <!-- Load Google Maps with Places & Drawing API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBH8H5qUkXP_oS_fRtoXtL8Km4RfCVOxC8&libraries=places,drawing&callback=initMap" async defer></script>

    <script>
      let map, marker;

      function initMap() {

          let lat = parseFloat(document.getElementById('latitude').value) || 22.9868;
          let lng = parseFloat(document.getElementById('longitude').value) || 87.8550;

          const location = { lat: lat, lng: lng };

          map = new google.maps.Map(document.getElementById("map"), {
              center: location,
              zoom: lat && lng ? 15 : 8,
          });

          // ✅ Marker
          marker = new google.maps.Marker({
              position: location,
              map: map,
              draggable: true
          });

          // ✅ Set lat/lng on load (EDIT CASE)
          document.getElementById("latitude").value = lat;
          document.getElementById("longitude").value = lng;

          // ✅ Drag marker → update lat/lng
          marker.addListener('dragend', function () {
              const pos = marker.getPosition();
              document.getElementById("latitude").value = pos.lat();
              document.getElementById("longitude").value = pos.lng();
          });

          // ✅ Click on map → move marker + update lat/lng
          map.addListener('click', function (event) {
              marker.setPosition(event.latLng);

              document.getElementById("latitude").value = event.latLng.lat();
              document.getElementById("longitude").value = event.latLng.lng();
          });

          // ✅ Address autocomplete
          const input = document.getElementById("autocompleteAddress");
          const autocomplete = new google.maps.places.Autocomplete(input);

          autocomplete.addListener("place_changed", () => {
              const place = autocomplete.getPlace();
              if (!place.geometry) return;

              const loc = place.geometry.location;

              map.setCenter(loc);
              map.setZoom(15);
              marker.setPosition(loc);

              // ✅ Auto fill lat/lng
              document.getElementById("latitude").value = loc.lat();
              document.getElementById("longitude").value = loc.lng();

              // ✅ Auto fill city/state/zip
              let city = "", state = "", zip = "";
              for (const component of place.address_components) {
                  if (component.types.includes("locality")) city = component.long_name;
                  if (component.types.includes("administrative_area_level_1")) state = component.long_name;
                  if (component.types.includes("postal_code")) zip = component.long_name;
              }

              document.getElementById("city").value = city;
              document.getElementById("state").value = state;
              document.getElementById("zip_code").value = zip;
          });
      }
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

    <script type="text/javascript">
    	$( document ).ready(function() {

        let user_type_id = "{{ isset($user_type) ? $user_type->id : '' }}";

        $('#newPass').val('');
        $('#newPassConfirm').val('');

        if(user_type_id==3)
        {
          $("#addressDiv").hide();
          $('#autocompleteAddress').prop('required', false);
          $("#cityDiv").hide();
          $('#city').prop('required', false);
          $("#stateDiv").hide();
          $('#state').prop('required', false);
          $("#zipCodeDiv").hide();
          $('#zip_code').prop('required', false);
          $("#mapDiv").hide();
          $('#geofence').prop('required', false);
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
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
