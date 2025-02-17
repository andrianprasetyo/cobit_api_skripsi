@extends('layouts.mail')

@section('content')
    <table style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td class="sm-py-32 sm-px-24" style="background-color: #eceff1; font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding: 48px; text-align: center;" align="center">
              <a href="https://assesment-cobit-19.vercel.app/assets/logo/logo-msi-full-rounded.webp">
                <img src="https://assesment-cobit-19.vercel.app/assets/logo/logo-msi-full-rounded.webp" width="155" alt="Logo" style="border: 0; max-width: 100%; line-height: 100%; vertical-align: middle;">
              </a>
            </td>
        </tr>
        <tr>
            <td align="left" class="sm-px-24" style="background-color: #ffffff; border-radius: 4px; font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; font-size: 14px; line-height: 24px; padding: 48px; text-align: left; color: #626262;">

            <p style="font-weight: 600; font-size: 18px; margin-bottom: 0;">Halo</p>
            <p style="font-weight: 700; font-size: 20px; margin-top: 0; color: #203058;">{{$data->nama}}</p>

            <p class="sm-leading-32" style="font-weight: 600; font-size: 20px; margin: 0 0 16px; color: #263238;">
                Permintaan Atur Ulang Password 🔓
            </p>

            <p style="margin: 0 0 12px;">
                Berikut Kode untuk mengatur ulang password akun anda :
            </p>

            <table style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding: 24px 0 48px 0;" cellpadding="0" cellspacing="0" role="presentation">
                <tr >
                @foreach(str_split($data->kode) as $k)
                <td style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding-right: 20px;">
                    <div style="display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; border: solid 2px #203058; background-color: #ffffff; border-radius: 4px; display: grid;">
                    <span style="font-weight: 600; font-size: 14px; line-height: 100%; color: #203058; text-decoration: none; margin: auto;">{{$k}}</span>
                    </div>
                </td>
                @endforeach

                </tr>
            </table>

            <p style="margin: 0 0 24px;">
                Untuk mengatur ulang kata sandi anda, harap menekan tombol di bawah.
            </p>

            <table style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI;" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                <td style="background-color: #203058; border-radius: 4px; font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI;">
                    <a href="{{$data->url}}" target="_blank" style="display: block; font-weight: 600; font-size: 14px; line-height: 100%; padding: 16px 24px; color: #ffffff; text-decoration: none;">Reset Password &rarr;</a>
                </td>
                </tr>
            </table>

            <p style="margin: 20px 0 10px 0;">
                Jika Tombol diatas tidak berfungsi, silahkan gunakan link dibawah ini
            </p>
            <a href="{{$data->url}}" target="_blank" style="display: block; font-size: 14px; line-height: 120%; margin-bottom: 24px; color: #203058; text-decoration: none;">{{$data->url}}</a>
            <table style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                <td style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding-top: 32px; padding-bottom: 32px;">
                    <div style="background-color: #eceff1; height: 1px; line-height: 1px;">&zwnj;</div>
                </td>
                </tr>
            </table>
            <p style="margin: 0 0 16px;">
                Jika Anda tidak merasa mengatur ulang password, harap hubungi kami di
                <a href="mailto:andrianprasetyo29@gmail.com" class="hover-underline" style="color: #203058; text-decoration: none;">andrianprasetyo29@gmail.com</a>
            </p>
            <p style="margin: 0 0 16px;">Terima Kasih, <br>Cobit 19</p>
            </td>
        </tr>
        <tr>
            <td style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; height: 20px;" height="20"></td>
        </tr>
        <tr>
            <td style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; height: 16px;" height="16"></td>
        </tr>
        </table>
@endsection
