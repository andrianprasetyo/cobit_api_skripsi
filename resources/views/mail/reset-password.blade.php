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
    <title>Reset Password | Cobit</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
      media="screen"
    />
    <style>
      .hover-underline:hover {
        text-decoration: underline !important;
      }

      .font-text {
        font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI;
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
    <div style="display: none;">Lupa Password Akun Anda</div>
    <div role="article" aria-roledescription="email" aria-label="Forgot Password">
      <table style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td class="sm-py-32 sm-px-24" style="background-color: #eceff1; font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding: 48px; text-align: center;" align="center">
              <a href="{{env('APP_URL_FE')}}">
                <img src="{{env('APP_URL_FE')}}/app-assets/images/logo/logo-jiep-square.png" width="155" alt="Logo JIEP" style="border: 0; max-width: 100%; line-height: 100%; vertical-align: middle;">
              </a>
            </td>
        </tr>
        <tr>
          <td align="center" style="background-color: #eceff1; font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI;">
            <table class="sm-w-full" style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 600px;" width="600" cellpadding="0" cellspacing="0" role="presentation">
              <tr class="sm-py-32">
                <td align="center" class="sm-px-24" style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI;">
                  <table style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                      <td align="left" class="sm-px-24" style="background-color: #ffffff; border-radius: 4px; font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; font-size: 14px; line-height: 24px; padding: 48px; text-align: left; color: #626262;">

                        <p style="font-weight: 600; font-size: 18px; margin-bottom: 0;">Halo</p>
                        <p style="font-weight: 700; font-size: 20px; margin-top: 0; color: #ea7b0b;">{{$data->nama}}</p>

                        <p class="sm-leading-32" style="font-weight: 600; font-size: 20px; margin: 0 0 16px; color: #263238;">
                          Ada Permintaan Atur Ulang Password 🔓
                        </p>


                        <p style="margin: 0 0 12px;">
                          Berikut Kode untuk mengatur ulang password akun anda :
                        </p>

                        <table style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding: 24px 0 48px 0;" cellpadding="0" cellspacing="0" role="presentation">
                          <tr >
                            @foreach(str_split($data->kode) as $k)
                            <td style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding-right: 20px;">
                              <div style="display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; border: solid 2px #ea7b0b; background-color: #ffffff; border-radius: 4px; display: grid;">
                                <span style="font-weight: 600; font-size: 14px; line-height: 100%; color: #ea7b0b; text-decoration: none; margin: auto;">{{$k}}</span>
                              </div>
                            </td>
                            @endforeach

                          </tr>
                        </table>

                        <p style="margin: 0 0 24px;">
                          Untuk mengatur ulang kata sandi anda, harap menekan tombol di bawah.
                        </p>

                        <table style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI;" cellpadding="0" cellspacing="0" role="presentation">
                          <tr>
                            <td style="background-color: #ea7b0b; border-radius: 4px; font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI;">
                              <a href="{{$data->url}}" target="_blank" style="display: block; font-weight: 600; font-size: 14px; line-height: 100%; padding: 16px 24px; color: #ffffff; text-decoration: none;">Reset Password &rarr;</a>
                            </td>
                          </tr>
                        </table>

                        <p style="margin: 20px 0 10px 0;">
                            Jika Tombol diatas tidak berfungsi, silahkan gunakan link dibawah ini
                        </p>
                        <a href="{{$data->url}}" target="_blank" style="display: block; font-size: 14px; line-height: 120%; margin-bottom: 24px; color: #ea7b0b; text-decoration: none;">{{$data->url}}</a>
                        <table style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                          <tr>
                            <td style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding-top: 32px; padding-bottom: 32px;">
                              <div style="background-color: #eceff1; height: 1px; line-height: 1px;">&zwnj;</div>
                            </td>
                          </tr>
                        </table>
                        <p style="margin: 0 0 16px;">
                            Jika Anda tidak merasa mengatur ulang password, harap hubungi kami di
                          <a href="mailto:admin@jiep.co.id" class="hover-underline" style="color: #ea7b0b; text-decoration: none;">admin@jiep.co.id</a>
                        </p>
                        <p style="margin: 0 0 16px;">Terima Kasih, <br>JIEP Record Center Team</p>
                      </td>
                    </tr>
                    <tr>
                      <td style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; height: 20px;" height="20"></td>
                    </tr>
                    <tr>
                      <td style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; font-size: 12px; padding-left: 48px; padding-right: 48px; color: #eceff1;">

                        <p style="color: #263238;">
                          COPYRIGHT © 2023
                          <a href="https://jiep.co.id" class="hover-underline" style="color: #ea7b0b; text-decoration: none;">Jakarta Industrial Estate Pulogadung (JIEP)</a>
                            , All rights Reserved
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; height: 16px;" height="16"></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </body>

</html>
