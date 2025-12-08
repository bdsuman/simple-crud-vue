<!DOCTYPE html>
<html lang="{{ $lang ?? app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Password Reset', [], $lang) }}</title>
</head>
<body>
    <div style="max-width: 420px; margin: 40px auto; background: #fff; border-radius: 28px; box-shadow: 0 2px 16px rgba(0,0,0,0.07); padding: 32px 24px; font-family: 'Inter', Arial, sans-serif; color: #232946;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="background: #eafcf3; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <!-- svg unchanged -->
                <svg width="40" height="41" viewBox="0 0 40 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.1131 32.0345C7.4879 34.8183 9.79363 36.9991 12.5995 37.1281C14.9605 37.2366 17.3589 37.2933 20.0001 37.2933C22.6412 37.2933 25.0396 37.2366 27.4006 37.1281C30.2066 36.9991 32.5122 34.8183 32.8871 32.0345C33.1317 30.2178 33.3334 28.356 33.3334 26.46C33.3334 24.564 33.1317 22.7021 32.8871 20.8855C32.5122 18.1016 30.2066 15.9208 27.4006 15.7918C25.0396 15.6833 22.6412 15.6266 20.0001 15.6266C17.3589 15.6266 14.9605 15.6833 12.5995 15.7918C9.79363 15.9208 7.4879 18.1016 7.1131 20.8855C6.8685 22.7021 6.66675 24.564 6.66675 26.46C6.66675 28.356 6.8685 30.2178 7.1131 32.0345Z" stroke="#3AB57C" stroke-width="2.5"/>
                    <path d="M12.5 15.6266V11.46C12.5 7.31784 15.8579 3.95998 20 3.95998C24.1422 3.95998 27.5 7.31784 27.5 11.46V15.6266" stroke="#3AB57C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M26.6667 26.4433V26.46" stroke="#3AB57C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20 26.4433V26.46" stroke="#3AB57C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.3333 26.4433V26.46" stroke="#3AB57C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 style="font-size: 2rem; font-weight: 700; color: #3AB57C; margin: 0 0 8px;">
                {{ __('OTP to Reset Password', [], $lang) }}
            </h1>
        </div>

        <div style="text-align: center; margin-bottom: 16px;">
            <span style="font-size: 1.25rem; font-weight: 600;">
                {{ __('Hi :name,', ['name' => $user->full_name], $lang) }}
            </span>
        </div>

        <p style="text-align: center; color: #232946; font-size: 1rem; margin-bottom: 24px;">
            {{ __('We received a request to reset the password for your ReadBack account. Your One-Time Password (OTP) is:', [], $lang) }}
        </p>

        <div style="display: flex; justify-content: center; margin-bottom: 24px;">
            <div style="background: #C6F7E2; color: #232946; font-size: 2rem; font-weight: 700; border-radius: 8px; padding: 16px 40px; letter-spacing: 2px; border: 1px solid #B2F5EA;">
                {{ $otp }}
            </div>
        </div>

        <p style="text-align: center; color: #232946; font-size: 1rem; margin-bottom: 16px;">
            {{ __('Please use this OTP to complete your password reset. This code is valid for 3 minutes. For security reasons, do not share this code with anyone.', [], $lang) }}
        </p>

        <p style="text-align: center; color: #00B8D9; font-size: 1rem; margin-bottom: 32px;">
            {{ __('If you did not request a password reset, please ignore this email.', [], $lang) }}
        </p>

        <div style="text-align: center; margin-top: 32px;">
            <img src="{{ asset('images/email_footer.png') }}" alt="Email Footer"
                 style="display:block;margin:0 auto;max-width:160px;">
        </div>
    </div>
</body>
</html>
