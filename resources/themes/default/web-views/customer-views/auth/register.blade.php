@extends('layouts.front-end.app')

@section('title', translate('register'))

@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset('public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush

@section('content')
<div class="container py-4 __inline-7 text-align-direction">
    <div class="login-card">
        <div class="mx-auto __max-w-760">
            <h2 class="text-center h4 mb-4 font-bold text-capitalize fs-18-mobile">{{ translate('sign_up') }}</h2>
            <form class="needs-validation_" id="customer-register-form" action="{{ route('customer.auth.sign-up') }}" method="post">
                @csrf

                <div class="d-flex justify-content-center my-3 gap-2">
                    @if(isset($web_config['customer_login_options']['social_login']) && $web_config['customer_login_options']['social_login'])
                        @foreach ($web_config['customer_social_login_options'] as $socialLoginServiceKey => $socialLoginService)
                            @if ($socialLoginService && $socialLoginServiceKey != 'apple')
                                <a class="d-block" href="{{ route('customer.auth.service-login', $socialLoginServiceKey) }}">
                                    <img src="{{ theme_asset('public/assets/front-end/img/icons/' . $socialLoginServiceKey . '.png') }}" alt="{{ translate($socialLoginServiceKey) }}">
                                </a>
                            @endif
                        @endforeach
                    @endif
                </div>

                @if ($web_config['social_login_text'])
                    <div class="text-center m-3 text-black-50">
                        <small>{{ translate('or_continue_with') }}</small>
                    </div>
                @endif

                <div class="row">
                    <!-- First Name -->
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label font-semibold">Your Name<span class="input-required-icon">*</span></label>
                            <input class="form-control text-align-direction" value="{{ old('f_name') }}" type="text"
                                name="f_name" placeholder="{{ translate('Ex') }}: {{ translate('Jhone') }}" required>
                            <div class="invalid-feedback">Please Enter Your Name!</div>
                        </div>
                    </div>

                    

                    <!-- Phone -->
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label font-semibold">{{ translate('phone_number') }} <span class="input-required-icon">*</span></label>
                            <input class="form-control text-align-direction phone-input-with-country-picker"
                                type="tel" value="{{ old('phone') }}" name="phone"
                                placeholder="{{ translate('enter_phone_number') }}" required>
                        </div>
                    </div>
  <!-- District Dropdown -->
<div class="col-sm-6">
    <div class="form-group">
        <label class="form-label font-semibold">{{ translate('zilla') }} <span class="input-required-icon">*</span></label>
        <select name="district" id="district" class="form-control" required>
            <option value="">{{ translate('select_zilla') }}</option>
            @foreach($districts as $district)
                <option value="{{ $district->id }}">{{ $district->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Upazila Dropdown -->
<div class="col-sm-6">
    <div class="form-group">
        <label class="form-label font-semibold">{{ translate('upazila') }} <span class="input-required-icon">*</span></label>
        <select name="upazila" id="upazila" class="form-control" required>
            <option value="">{{ translate('select_upazila') }}</option>
        </select>
    </div>
</div>
                    <!-- Password -->
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label font-semibold">{{ translate('password') }} <span class="input-required-icon">*</span></label>
                            <div class="password-toggle rtl">
                                <input class="form-control text-align-direction" name="password" type="password"
                                    id="si-password" placeholder="{{ translate('minimum_8_characters_long') }}" required>
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input" type="checkbox"><i
                                        class="tio-hidden password-toggle-indicator"></i>
                                    <span class="sr-only">{{ translate('show_password') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    

                    <!-- Confirm Password -->
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label font-semibold">{{ translate('confirm_password') }} <span class="input-required-icon">*</span></label>
                            <div class="password-toggle rtl">
                                <input class="form-control text-align-direction" name="con_password" type="password"
                                    placeholder="{{ translate('minimum_8_characters_long') }}" id="si-confirm-password" required>
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input text-align-direction" type="checkbox">
                                    <i class="tio-hidden password-toggle-indicator"></i>
                                    <span class="sr-only">{{ translate('show_password') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Referral Code -->
                    @if ($web_config['ref_earning_status'])
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-label font-semibold">{{ translate('refer_code') }} <small class="text-muted">({{ translate('optional') }})</small></label>
                            <input type="text" id="referral_code" class="form-control" name="referral_code"
                                placeholder="{{ translate('use_referral_code') }}">
                        </div>
                    </div>
                    @endif

                  

                </div> <!-- row -->

                <!-- Terms & Conditions -->
                <div class="col-12 mt-3">
                    <div class="rtl">
                        <label class="custom-control custom-checkbox m-0 d-flex">
                            <input type="checkbox" class="custom-control-input" name="remember" id="inputChecked">
                            <span class="custom-control-label">
                                <span>{{ translate('i_agree_to_Your') }}</span>
                                <a class="font-size-sm text-primary text-force-underline" target="_blank"
                                    href="{{ route('business-page.view', ['slug' => 'terms-and-conditions']) }}">{{ translate('terms_and_condition') }}</a>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="web-direction mt-4">
                    <div class="mx-auto __max-w-356">
                        <button class="w-100 btn btn--primary" id="sign-up" type="submit" disabled>
                            {{ translate('sign_up') }}
                        </button>
                    </div>

                    <div class="text-black-50 mt-3 text-center">
                        <small>
                            {{ translate('Already_have_account ') }}?
                            <a class="text-primary text-underline" href="{{ route('customer.auth.login') }}">
                                {{ translate('sign_in') }}
                            </a>
                        </small>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    var selectZillaText = "{{ translate('select_zilla') }}";
    var selectUpazilaText = "{{ translate('select_upazila') }}";

    // Enable submit button when terms checkbox checked
    $('#inputChecked').on('change', function() {
        $('#sign-up').prop('disabled', !this.checked);
    });

    // District → Upazila
    $('#district').on('change', function() {
        let district_id = $(this).val();
        $('#upazila').html('<option value="">' + selectUpazilaText + '</option>');

        if (district_id) {
            $.get("/get-upazilas/" + district_id, function(data) {
                $.each(data, function(key, value) {
                    $('#upazila').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
            });
        }
    });
});
</script>

@endsection
