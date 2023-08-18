@extends('layouts.mail')

@section('content')
<table style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="left" class="sm-px-24" style="background-color: #ffffff; border-radius: 4px; font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; font-size: 14px; line-height: 24px; padding: 48px; text-align: left; color: #626262;">
        <p style="font-weight: 600; font-size: 18px; margin-bottom: 0;">Halo</p>
        <p style="font-weight: 700; font-size: 20px; margin-top: 0; color: #ea7b0b;">{{$data->nama}}</p>
        <p class="sm-leading-32" style="font-weight: 600; font-size: 20px; margin: 0 0 16px; color: #263238;">
            Ada Undangan untuk verifikasi akun
        </p>
        <p style="margin: 0 0 24px;">
            Silahkan klik tombol dibawah ini untuk melakukan verifikasi akun
        </p>

        <table style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI;" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
            <td style="mso-padding-alt: 16px 24px; background-color: #ea7b0b; border-radius: 4px; font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI;">
                <a href="{{$data->url}}" target="_blank" style="display: block; font-weight: 600; font-size: 14px; line-height: 100%; padding: 16px 24px; color: #ffffff; text-decoration: none;">Konfirmasi Undangan &rarr;</a>
            </td>
            </tr>
        </table>

        <p style="margin: 20px 0 10px 0;">
            Jika Tombol diatas tidak berfungsi, silahkan gunakan link dibawah ini
        </p>
        <a href="{{$data->url}}" target="_blank" style="display: block; font-size: 14px; line-height: 120%; margin-bottom: 24px; color: #ea7b0b; text-decoration: none;">{{$data->token}}</a>
        <table style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
            <td style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding-top: 32px; padding-bottom: 32px;">
                <div style="background-color: #eceff1; height: 1px; line-height: 1px;">&zwnj;</div>
            </td>
            </tr>
        </table>
        </td>
    </tr>
    <tr>
        <td style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; height: 20px;" height="20"></td>
    </tr>
    <tr>
        <td style="font-family: Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI; height: 16px;" height="16"></td>
    </tr>
    </table>
@endsection
