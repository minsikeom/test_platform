@component('mail::message')
<p>{!! $translateConstants::MAIL_VERIFICATION_FORM[$lang]['hello'] !!}</p>
<p>{!! $translateConstants::MAIL_VERIFICATION_FORM[$lang]['thanks'] !!}</p>
<br>
<h2>{!! $translateConstants::MAIL_VERIFICATION_FORM[$lang]['code'] !!}</h2>
@component('mail::panel')
<h2>{{ $verificationCode }}</h2>
@endcomponent

{!! $translateConstants::MAIL_VERIFICATION_FORM[$lang]['ignore'] !!}

@endcomponent
