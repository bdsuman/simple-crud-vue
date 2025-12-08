<!DOCTYPE html>
<html lang="{{ $lang ?? app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Welcome to crud', [], $lang) }}</title>
</head>
<body>
    <div style="max-width: 480px; margin: 40px auto; background: #fff; border-radius: 32px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); padding: 32px 24px 0 24px; font-family: 'Inter', Arial, sans-serif;">
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="margin-bottom: 16px;">
                <svg width="48" height="49" viewBox="0 0 48 49" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M42 25.4746V22.6266C42 12.7026 33.926 4.62665 24 4.62665C14.074 4.62665 6 12.7026 6 22.6266V25.4746C2.474 27.0226 0 30.5366 0 34.6266C0 40.1406 4.486 44.6266 10 44.6266C11.94 44.6266 14 43.2246 14 40.6266V28.6266C14 26.0286 11.94 24.6266 10 24.6266V22.6266C10 14.9066 16.282 8.62665 24 8.62665C31.718 8.62665 38 14.9066 38 22.6266V24.6266C36.06 24.6266 34 26.0286 34 28.6266V40.6266C34 43.2246 36.06 44.6266 38 44.6266C43.514 44.6266 48 40.1406 48 34.6266C48 30.5366 45.526 27.0226 42 25.4746ZM10 40.6266C6.692 40.6266 4 37.9346 4 34.6266C4 31.3186 6.692 28.6266 10 28.6266V40.6266ZM38 40.6266V28.6266C41.308 28.6266 44 31.3186 44 34.6266C44 37.9346 41.308 40.6266 38 40.6266ZM32 34.6266C32 35.5806 31.332 36.3766 30.44 36.5786L27.762 41.5726C27.388 42.2786 26.618 42.6886 25.83 42.6206C25.034 42.5526 24.356 42.0166 24.104 41.2606L21.63 33.8406L20.79 35.5226C20.45 36.2006 19.758 36.6286 19 36.6286H18C16.894 36.6286 16 35.7326 16 34.6286C16 33.6066 16.768 32.7626 17.756 32.6426L20.212 27.7326C20.574 27.0106 21.316 26.5806 22.144 26.6326C22.95 26.6906 23.642 27.2286 23.898 27.9946L26.432 35.5926L27.458 33.6826C27.806 33.0326 28.484 32.6286 29.22 32.6286H30.004C31.11 32.6286 32.004 33.5246 32.004 34.6286L32 34.6266Z" fill="#3AB57C"/>
                </svg>
            </div>

            <h1 style="font-size: 2rem; font-weight: 700; color: #3AB57C; margin: 0;">
                {{ __('Welcome to crud!', [], $lang) }}
            </h1>
        </div>

        <div style="font-size: 1rem; color: #232B3A; line-height: 1.6;">
            <p style="margin: 0 0 8px 0;">
                <strong>
                    @if(!empty($user?->full_name))
                        {{ __('Hi :name,', ['name' => $user->full_name], $lang) }}
                    @else
                        {{ __('Hi there,', [], $lang) }}
                    @endif
                </strong>
            </p>

            <p style="margin: 0 0 8px 0;">
                {{ __('Welcome aboard! Your account at crud has been successfully created.', [], $lang) }}
            </p>

            <p style="margin: 0 0 24px 0;">
                {{ __('You can now log in and start using our services.', [], $lang) }}
            </p>

            <p style="margin: 0 0 4px 0;">{{ __('Thanks,', [], $lang) }}</p>
            <p style="font-weight: 700; color: #3F8B77; margin: 0;">
                {{ __('The crud Team', [], $lang) }}
            </p>
        </div>

        <div style="border-top: 1px solid #F0F0F0; margin: 32px -24px 0 -24px; padding: 24px 20px 20px 20px; text-align: center;">
            <img src="{{ asset('images/email_footer.png') }}" alt="Email Footer" style="display:block;margin:0 auto;max-width:160px;">
        </div>
    </div>

    <style>
    @media (max-width: 600px) {
        div[style*="max-width: 480px"] {
            padding: 16px 4vw 0 4vw !important;
            border-radius: 16px !important;
        }
        h1 { font-size: 1.5rem !important; }
        div[style*="border-top: 1px solid"] {
            margin: 24px -4vw 0 -4vw !important;
            padding-top: 16px !important;
        }
    }
    </style>
</body>
</html>
