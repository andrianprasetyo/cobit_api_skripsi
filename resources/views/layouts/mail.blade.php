<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <!--[if mso]>
    <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    <style>
      td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family: "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
    </style>
  <![endif]-->
    <title>{{ $subject }}</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <style>
        .hover-underline:hover {
            text-decoration: underline !important;
        }

        .font-text {
            font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes ping {

            75%,
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        @keyframes pulse {
            50% {
                opacity: .5;
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(-25%);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }

            50% {
                transform: none;
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }

        @media (max-width: 600px) {
            .sm-leading-32 {
                line-height: 32px !important;
            }

            .sm-px-24 {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }

            .sm-py-32 {
                padding-top: 32px !important;
                padding-bottom: 32px !important;
            }

            .sm-w-full {
                width: 100% !important;
            }
        }

    </style>
</head>

<body style="margin: 0; padding: 0; width: 100%; word-break: break-word; -webkit-font-smoothing: antialiased; background-color: #eceff1;">
    <div role="article" aria-roledescription="email" aria-label="Email Content">
        <table style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">

            <tr>
                <td align="center" style="background-color: #eceff1; font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI;">
                    <table class="sm-w-full" style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 600px;" width="600" cellpadding="0" cellspacing="0" role="presentation">
                        <tr class="sm-py-32">
                            <td align="center" class="sm-px-24" style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI;">
                                @yield('content')
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>

