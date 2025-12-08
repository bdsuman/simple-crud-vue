<!DOCTYPE html>
<html lang="{{ $lang ?? app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Registration Email', [], $lang) }}</title>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff;max-width:480px;margin:40px auto;border-radius:24px;box-shadow:0 2px 16px rgba(0,0,0,0.06);font-family: 'Segoe UI', Arial, sans-serif;">
        <tr>
            <td style="padding:32px 24px 0 24px;text-align:center;">
                <div style="margin-bottom:24px;">
                    <!-- SVG unchanged -->
                    <svg width="48" height="49" viewBox="0 0 48 49" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 9.12665L17.826 16.9606C22.9232 19.8487 25.0768 19.8487 30.174 16.9606L44 9.12665" stroke="#3AB57C" stroke-width="3" stroke-linejoin="round" />
                        <path d="M30 35.1266C30 35.1266 31 35.1266 32 37.1266C32 37.1266 35.1764 32.1266 38 31.1266" stroke="#3AB57C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M44 34.1266C44 39.6494 39.5228 44.1266 34 44.1266C28.4772 44.1266 24 39.6494 24 34.1266C24 28.6038 28.4772 24.1266 34 24.1266C39.5228 24.1266 44 28.6038 44 34.1266Z" stroke="#3AB57C" stroke-width="2.5" stroke-linecap="round" />
                        <path d="M18.1672 39.6262C18.9954 39.647 19.6836 38.9926 19.7045 38.1644C19.7253 37.3362 19.0709 36.648 18.2427 36.627L18.1672 39.6262ZM42.4892 21.1426C42.4778 21.971 43.1402 22.6518 43.9684 22.663C44.7968 22.6744 45.4776 22.0122 45.4888 21.1838L42.4892 21.1426ZM18.2427 5.69945C22.1004 5.60237 25.92 5.60239 29.7778 5.69947L29.8534 2.70043C25.9452 2.60207 22.0754 2.60205 18.1672 2.70041L18.2427 5.69945ZM2.53187 18.1737C2.48937 20.1706 2.48937 22.1558 2.53187 24.1528L5.53119 24.089C5.48959 22.1346 5.48959 20.192 5.53119 18.2376L2.53187 18.1737ZM18.1672 2.70041C15.062 2.77855 12.5693 2.83747 10.5765 3.18507C8.5161 3.54449 6.84184 4.23383 5.42763 5.65573L7.55472 7.77129C8.40395 6.91741 9.43646 6.42925 11.092 6.14045C12.8153 5.83985 15.0477 5.77985 18.2427 5.69945L18.1672 2.70041ZM5.53119 18.2376C5.59757 15.12 5.64775 12.9484 5.93957 11.2651C6.21905 9.65315 6.69936 8.63131 7.55472 7.77129L5.42763 5.65573C4.01959 7.07145 3.33548 8.72357 2.98368 10.7527C2.64424 12.7105 2.59631 15.147 2.53187 18.1737L5.53119 18.2376ZM29.7778 5.69947C32.973 5.77987 35.2054 5.83987 36.9286 6.14047C38.5842 6.42927 39.6166 6.91745 40.4658 7.77131L42.593 5.65577C41.1788 4.23387 39.5044 3.54453 37.444 3.18511C35.4514 2.83749 32.9586 2.77857 29.8534 2.70043L29.7778 5.69947ZM45.4888 18.1738C45.4242 15.1471 45.3764 12.7105 45.0368 10.7527C44.6852 8.72361 44.001 7.07149 42.593 5.65577L40.4658 7.77131C41.3212 8.63135 41.8016 9.65317 42.081 11.2652C42.3728 12.9484 42.423 15.1201 42.4894 18.2376L45.4888 18.1738ZM18.2427 36.627C15.0477 36.5466 12.8153 36.4866 11.092 36.186C9.43646 35.8972 8.40395 35.4092 7.55474 34.5552L5.42765 36.6708C6.84184 38.0926 8.51612 38.782 10.5765 39.1414C12.5693 39.489 15.062 39.548 18.1672 39.6262L18.2427 36.627ZM2.53187 24.1528C2.59631 27.1794 2.64426 29.616 2.9837 31.5738C3.33548 33.603 4.0196 35.2551 5.42765 36.6708L7.55474 34.5552C6.69936 33.6952 6.21907 32.6734 5.93959 31.0614C5.64777 29.3782 5.59757 27.2064 5.53119 24.089L2.53187 24.1528ZM43.989 21.1632C45.4888 21.1838 45.4888 21.1838 45.4888 21.1838C45.4888 21.1834 45.4888 21.1842 45.4888 21.1838C45.4888 21.1828 45.489 21.1798 45.489 21.178C45.489 21.1744 45.489 21.1692 45.4892 21.1622C45.4894 21.1484 45.4896 21.128 45.49 21.102C45.4906 21.0496 45.4914 20.9738 45.4924 20.8794C45.4944 20.6908 45.4968 20.427 45.4982 20.1266C45.5014 19.5294 45.5014 18.7736 45.4888 18.1738L42.4894 18.2376C42.5012 18.7959 42.5014 19.5189 42.4984 20.111C42.4968 20.4052 42.4944 20.6636 42.4926 20.8486C42.4916 20.9408 42.4908 21.0148 42.4902 21.0654C42.4898 21.0908 42.4896 21.1102 42.4894 21.1232C42.4894 21.1298 42.4892 21.1348 42.4892 21.138C42.4892 21.1396 42.4892 21.1418 42.4892 21.1426C42.4892 21.1426 42.4892 21.1426 43.989 21.1632Z" fill="#3AB57C" />
                    </svg>
                </div>

                <h1 style="color:#3AB57C;font-size:2rem;margin:0 0 8px 0;font-weight:700;">
                    {{ __('Verify Your Email Address', [], $lang) }}
                </h1>

                <p style="font-size:1.1rem;color:#222;margin:0 0 24px 0;">
                    <strong>{{ __('Hello :name,', ['name' => $user->full_name], $lang) }}</strong>
                </p>

                <p style="font-size:1rem;color:#444;margin:0 0 16px 0;">
                    {{ __('Thanks for signing up! To get started, please verify your email address. Your One-Time Password (OTP) is:', [], $lang) }}
                </p>

                <div style="margin:24px 0;">
                    <div style="display:inline-block;background:#D1FADF;color:#005766;font-size:2rem;font-weight:700;padding:12px 36px;border-radius:8px;letter-spacing:2px;">
                        {{ $otp }}
                    </div>
                </div>

                <p style="font-size:1rem;color:#444;margin:0 0 16px 0;">
                    {{ __('Use this code to complete your email verification. This OTP is valid for 3 minutes.', [], $lang) }}
                </p>

                <p style="font-size:0.95rem;color:#00A5BF;margin:0 0 24px 0;">
                    {{ __('If you didn\'t sign up for a DeepGrow account, you can safely ignore this email.', [], $lang) }}
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:0 0 24px 0;text-align:center;">
                <hr style="border:none;border-top:1px solid #E6E6E6;margin:24px 0 16px 0;">
                <img src="{{ asset('images/email_footer.png') }}" alt="Email Footer" style="display:block;margin:0 auto;max-width:160px;">
            </td>
        </tr>
    </table>
</body>
</html>
