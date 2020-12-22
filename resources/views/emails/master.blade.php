<html>
<head>
<style type="text/css">
html,
body {
  margin: 0 auto!important;
  padding: 0 !important;
  height: 100% !important;
  width: 100% !important;
  background-color: #ececec !important;
}

* {
  -ms-text-size-adjust: 100% !important;
  -webkit-text-size-adjust: 100% !important;
  outline: none;
}

table,
td {
  mso-table-lspace: 0pt !important;
  mso-table-rspace: 0pt !important;
}
table th {
  min-width: 130px;
}
table td, table th {
  font-size: 14px;
}
table {
  min-width: 75%;
  border-spacing: 0 !important;
  border-collapse: collapse !important;
  table-layout: fixed !important;
  background-color: #ececec;
}
table table {
  min-width: unset;
}
h1,
h2,
h3 {
  font-family: Arial, 'Times New Roman', sans-serif;
}
p,
li {
  font-family: Arial, 'Times New Roman', sans-serif;
  font-size: 14px;
  line-height: 20px;
  margin-bottom: 20px;
  /*color: #919191;*/
}
a.button {
  text-decoration: none;
  display: block;
  font-family: Arial, 'Times New Roman', sans-serif;
  font-size: 16px;
  line-height: 18px;
  padding: 10px 0px;
  min-width: 215px;
  text-align: center;
  color: #fff;
  background-color: #717171;
  /*-webkit-border-radius: 3px;
  border-radius: 3px;*/
  display: inline-block;
}
</style>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ececec">
  <tr><td>&nbsp;</td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td align="center">
    <table max-width="600" style="border: 1px solid #d7d7d7; box-sizing: border-box; background-color: #fff;     max-width: 600px;">
      <tr><td>
        <table width="100%" height="180" style="border-bottom: 1px solid #d7d7d7; background-color: #fff; text-align: center;">
          <tr>
            <td style="vertical-align: middle;text-align: center;width: 100%;padding: 40px 0;">
              <a href="https://www.collectionstock.com" target="_blank">
                <img src="https://www.collectionstock.com/images/email/logo.png" />
              </a>
            </td>
            {{-- <td style="vertical-align: middle;text-align: center;width: 100%;padding:0;">
              <a href="https://www.collectionstock.com" target="_blank">
                <img src="{{ url('api/v1/image/email/logo') }}" style="width: 100%;" />
              </a>
            </td> --}}
          </tr>
        </table>
          </td></tr>
      <tr><td style="padding: 45px 30px 115px 30px;">

      @yield('content')

      @if(strpos(config('app.url'), 'dev.collectionstock.com') !== false)
        (note: This is an email sent from test or dev)
      @endif

      </td></tr>
    </table>
  </td></tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ececec">
  <tr>
    <td align="center">
      <table style="max-width: 600px; margin: 0 auto;">
        <tr><td style="padding: 0 85px 0 85px;">
          <p style="font-family: Arial, 'Times New Roman', sans-serif; text-align: center; margin: 50px 0 20px; color: #717171; font-size: 12px; line-height: 20px;">
              @lang('emails.footer')
          </p>
        </td></tr>
        <tr>
          <td style="text-align: center;">
            <table width="240" style="margin: 0 auto;">
              <tr>
                <td width="60" style="text-align: center;"><a href="https://www.facebook.com/collectionstock/" target="_blank" style="margin: 0 7px;"><img src="https://www.collectionstock.com/images/email/fb.png" /></a></td>
                <td width="60" style="text-align: center;"><a href="https://www.linkedin.com/company/collectionstock" target="_blank" style="margin: 0 7px;"><img src="https://www.collectionstock.com/images/email/in.png" /></a></td>
                <td width="60" style="text-align: center;"><a href="https://twitter.com/COLLECTIONSTOCK" target="_blank" style="margin: 0 7px;"><img src="https://www.collectionstock.com/images/email/tt.png" /></a></td>
                <td width="60" style="text-align: center;"><a href="https://plus.google.com/u/0/b/111161146477376840583/111161146477376840583/about" target="_blank" style="margin: 0 7px;"><img src="https://www.collectionstock.com/images/email/gp.png" /></a></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td>&nbsp;</td></tr>
</table>
</body>
</html>
