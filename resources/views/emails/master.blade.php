<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head><!-- Yahoo App Android will strip this --></head>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=320, initial-scale=1" />
    <title>@yield('subject')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style type="text/css">
        /* ----- Client Fixes ----- */

        /* Force Outlook to provide a "view in browser" message */
        #outlook a {
            padding: 0;
        }

        /* Force Hotmail to display emails at full width */
        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        /* Force Hotmail to display normal line spacing */
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height: 100%;
        }


        /* Prevent WebKit and Windows mobile changing default text sizes */
        body,
        table,
        td,
        p,
        a,
        li,
        blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Remove spacing between tables in Outlook 2007 and up */
        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        /* Allow smoother rendering of resized image in Internet Explorer */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* ----- Reset ----- */

        /* html, */
        body,
        .body-wrap,
        .body-wrap-cell {
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font-size: 14px;
            color: #464646;
            text-align: left;
            font-family: 'Poppins', sans-serif;
        }

        img {
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        td,
        th {
            text-align: left;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #464646;
            line-height: 1.5em;
        }

        b a,
        .footer a {
            text-decoration: none;
            color: #464646;
        }

        a.blue-link {
            color: blue;
            text-decoration: underline;
        }

        /* ----- General ----- */

        td.center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .body-padding {
            /* padding: 24px 40px 40px; */
            margin: 0;
            padding: 0;
        }

        .border-bottom {
            /* border-bottom: 1px solid #D8D8D8; */
        }

        table.full-width-gmail-android {
            width: 100% !important;
        }


        /* ----- Header ----- */
        .header {
            font-weight: bold;
            font-size: 16px;
            line-height: 16px;
            height: 16px;
            padding-top: 19px;
            padding-bottom: 7px;
        }

        .header a {
            color: #464646;
            text-decoration: none;
        }

        /* ----- Body ----- */

        .body .body-padded {
            padding-top: 34px;
        }

        .body-thanks-cell {
            padding: 25px 0 10px;
        }

        .body-signature-cell {
            padding: 0 0 30px;
        }

        /* ----- Footer ----- */

        .footer a {
            font-size: 12px;
        }


        /* ----- Soapbox ----- */

        .soapbox .soapbox-title {
            text-align: center;
            font-size: 30px;
            padding-bottom: 20px;
            color: #464646;
        }

        /* ----- Status ----- */

        .status {
            border-collapse: collapse;
            margin-left: 15px;
            color: #656565;
        }

        .status .status-cell {
            border: 1px solid #b3b3b3;
            height: 50px;
            text-align: center;
            font-size: 15px;
            padding: 0 40px;
        }

        .status .status-cell.success,
        .status .status-cell.active {
            height: 65px;
        }

        .status .status-cell.success {
            background-color: #f2ffeb;
            color: #51da42;
        }

        .status .status-cell.active {
            background-color: #fffde0;
            width: 135px;
        }

        .status .status-title {
            font-size: 16px;
            font-weight: bold;
            line-height: 23px;
        }

        .status .status-image {
            vertical-align: text-bottom;
        }

        .main-content {
            padding-top: 40px;
            padding-bottom: 30px;
            padding-left: 60px;
            padding-right: 60px;
        }

        img.app-logo {
            width: 144px;
            height: auto;
        }
    </style>

    <style type="text/css" media="only screen">
        @media only screen and (max-width: 420px) {

            *[class*="w320"] {
                width: 300px !important;
            }

            table[class="status"] td[class*="status-cell"],
            table[class="status"] td[class*="status-cell"].active {
                display: block !important;
                width: auto !important;
            }

            table[class="status-container single"] table[class="status"] {
                width: 270px !important;
                margin-left: 0;
            }

            table[class="status"] td[class*="status-cell"],
            table[class="status"] td[class*="status-cell"].active,
            table[class="status"] td[class*="status-cell"] [class*="status-title"] {
                line-height: 65px !important;
                font-size: 18px !important;
                padding: 0 15px !important;
            }

            table[class="status-container single"] table[class="status"] td[class*="status-cell"],
            table[class="status-container single"] table[class="status"] td[class*="status-cell"].active,
            table[class="status-container single"] table[class="status"] td[class*="status-cell"] [class*="status-title"] {
                line-height: 51px !important;
            }

            table[class="status"] td[class*="status-cell"].active [class*="status-title"] {
                display: inline !important;
            }

            td[class="main-content"] {
                padding-top: 20px;
                padding-bottom: 15px;
                padding-left: 0px;
                padding-right: 0px;
            }


        }
    </style>

    <style type="text/css" media="only screen">
        @media only screen and (max-width: 505px) {

            *[class*="w320"] {
                width: 300px !important;
            }

            table[class="status"] td[class*="status-cell"],
            table[class="status"] td[class*="status-cell"].active {
                display: block !important;
                width: auto !important;
            }

            table[class="status-container single"] table[class="status"] {
                width: 270px !important;
                margin-left: 0;
            }

            table[class="status"] td[class*="status-cell"],
            table[class="status"] td[class*="status-cell"].active,
            table[class="status"] td[class*="status-cell"] [class*="status-title"] {
                line-height: 65px !important;
                font-size: 18px !important;
                padding: 0 15px !important;
            }

            table[class="status-container single"] table[class="status"] td[class*="status-cell"],
            table[class="status-container single"] table[class="status"] td[class*="status-cell"].active,
            table[class="status-container single"] table[class="status"] td[class*="status-cell"] [class*="status-title"] {
                line-height: 51px !important;
            }

            table[class="status"] td[class*="status-cell"].active [class*="status-title"] {
                display: inline !important;
            }

            td[class="main-content"] {
                padding-top: 20px;
                padding-bottom: 15px;
                padding-left: 10px;
                padding-right: 10px;
            }


        }
    </style>

    <style type="text/css" media="only screen and (max-width: 650px)">
        @media only screen and (max-width: 650px) {
            * {
                font-size: 16px !important;
            }

            .header h1 {
                font-size: 25px !important;
                margin-bottom: 0px !important;
            }

            .body-text-cell p {
                /* margin-bottom: 30px !important; */
            }

            table[class*="w320"] {
                width: 300px !important;
            }

            table[class*="main-table"] {
                margin: 0 auto !important;
            }

            td[class="mobile-center"],
            div[class="mobile-center"] {
                text-align: center !important;
            }

            td[class*="body-padding"] {
                padding: 10px !important;
            }

            td[class="mobile"] {
                text-align: right;
                vertical-align: top;
            }

            td[class="main-content"] {
                padding-top: 30px;
                padding-bottom: 15px;
                padding-left: 20px;
                padding-right: 20px;
            }


            img[class="app-logo"] {
                margin-left: 0 !important;
                margin-right: 0 !important;

                width: 120px !important;
                height: auto;
            }
        }
    </style>

</head>

<body style="padding:0; margin:0; display:block; background-color:#005766; -webkit-text-size-adjust:none;">
    <div style="background-color:#005766;">
        <table height="30px;">
            <tr>
                <td></td>
            </tr>
        </table>

        <table class="main-table w320" border="0" width="100%" cellspacing="0" cellpadding="0" style="background-color:#ffffff; width:70%;margin:0 auto;">
            <tbody>
                <tr>

                    <td class="main-content"
                        align="left" valign="top" width="100%" style="" bgcolor="#ffffff">
                        <div>
                            @yield('content')
                        </div>
                    </td>

                </tr>
            </tbody>
        </table>


        <table height="30px;">
            <tr>
                <td></td>
            </tr>
        </table>
    </div>
</body>

</html>