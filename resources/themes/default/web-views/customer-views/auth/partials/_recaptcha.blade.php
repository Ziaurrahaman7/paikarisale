@php($recaptcha = getWebConfig(name: 'recaptcha'))

@if ($web_config['firebase_otp_verification'] && $web_config['firebase_otp_verification']['status'])
    
@elseif(isset($recaptcha) && $recaptcha['status'] == 1)
   
@else
   
@endif
