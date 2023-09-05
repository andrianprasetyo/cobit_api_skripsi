@extends('layouts.mail')

@section('content')
<table style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
            <td class="sm-py-32 sm-px-24" style="background-color: #eceff1; font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding: 48px; text-align: center;" align="center">
              <a href="https://dev-cobit-fe.millennia-solusi.xyz/assets/logo/logo-msi-full-rounded.webp">
                <img src="https://dev-cobit-fe.millennia-solusi.xyz/assets/logo/logo-msi-full-rounded.webp" width="155" alt="Logo" style="border: 0; max-width: 100%; line-height: 100%; vertical-align: middle;">
              </a>
            </td>
    </tr>
    <tr>
        <td align="left" class="sm-px-24" style="background-color: #ffffff; border-radius: 4px; font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; font-size: 14px; line-height: 24px; padding: 48px; text-align: left; color: #626262;">
        <p style="font-weight: 600; font-size: 18px; margin-bottom: 0;">Halo</p>
        <p style="font-weight: 700; font-size: 20px; margin-top: 0; color: #203058;">{{$data->nama ?: $data->email}}</p>
        <p style="margin: 20px 0 10px 0;">
            Sehubungan dengan adanya Assesment Cobit 19 pada <b>{{$data->organisasi}}</b>,
            <br/>
            mohon sedianya Bapak/Ibu untuk mengisi Kuesioner  yang telah kami sediakan, silahkan klik tombol dibawah ini untuk melakukan pengisian Kuesioner
        </p>

        <table style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI;" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
            <td style="mso-padding-alt: 16px 24px; background-color: #0e45ce; border-radius: 4px; font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI;">
                <a href="{{$data->url}}" target="_blank" style="display: block; font-weight: 600; font-size: 14px; line-height: 100%; padding: 16px 24px; color: #ffffff; text-decoration: none;">Isi Kuesioner  &rarr;</a>
            </td>
            </tr>
        </table>

        <p style="margin: 20px 0 10px 0;">
            Atas perhatiannya kami ucapkan terimakasih,
        </p>
        <p style="margin: 20px 0 10px 0;">
            Salam
            <br/>
            <span>
                PT. Millenia Solusi Informatika
            </span>
        </p>

        <table style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
            <td style="font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI; padding-top: 32px; padding-bottom: 32px;">
                <div style="background-color: #eceff1; height: 1px; line-height: 1px;">&zwnj;</div>
            </td>
            </tr>
        </table>
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
