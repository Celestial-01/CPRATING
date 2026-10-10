<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        ยืนยันอีเมลเพื่อสมัครสมาชิก {{ config('app.name', 'CPRATING') }}
    </title>
</head>
<body
    style="
        margin: 0;
        padding: 0;
        background-color: #f1f5f9;
        color: #1e293b;
        font-family: 'IBM Plex Sans Thai', Arial, sans-serif;
    "
>
    <table
        role="presentation"
        width="100%"
        cellspacing="0"
        cellpadding="0"
        border="0"
        style="
            width: 100%;
            background-color: #f1f5f9;
        "
    >
        <tr>
            <td
                align="center"
                style="padding: 36px 16px;"
            >
                <table
                    role="presentation"
                    width="100%"
                    cellspacing="0"
                    cellpadding="0"
                    border="0"
                    style="
                        width: 100%;
                        max-width: 560px;
                    "
                >
                    <tr>
                        <td
                            align="center"
                            style="
                                padding: 0 0 20px;
                                color: #172033;
                                font-size: 22px;
                                font-weight: 700;
                            "
                        >
                            {{ config('app.name', 'CPRATING') }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="
                                padding: 36px 32px;
                                border: 1px solid #e2e8f0;
                                border-radius: 16px;
                                background-color: #ffffff;
                            "
                        >
                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                            >
                                <tr>
                                    <td
                                        align="center"
                                        style="padding: 0 0 20px;"
                                    >
                                        <table
                                            role="presentation"
                                            cellspacing="0"
                                            cellpadding="0"
                                            border="0"
                                        >
                                            <tr>
                                                <td
                                                    align="center"
                                                    valign="middle"
                                                    width="64"
                                                    height="64"
                                                    style="
                                                        width: 64px;
                                                        height: 64px;
                                                        border-radius: 50%;
                                                        background-color: #e8f7ff;
                                                        color: #1684df;
                                                        font-family: Arial, sans-serif;
                                                        font-size: 34px;
                                                        line-height: 64px;
                                                    "
                                                >
                                                    &#9993;
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        align="center"
                                        style="
                                            padding: 0 0 10px;
                                            color: #0f172a;
                                            font-size: 22px;
                                            font-weight: 600;
                                            line-height: 1.5;
                                        "
                                    >
                                        ยืนยันอีเมลของคุณ
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        align="center"
                                        style="
                                            padding: 0 0 8px;
                                            color: #64748b;
                                            font-size: 14px;
                                            line-height: 1.8;
                                        "
                                    >
                                        ขอบคุณที่สมัครสมาชิก {{ config('app.name', 'CPRATING') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        align="center"
                                        style="
                                            padding: 0 0 24px;
                                            color: #64748b;
                                            font-size: 14px;
                                            line-height: 1.8;
                                        "
                                    >
                                        กดปุ่มด้านล่างเพื่อยืนยันอีเมลและตั้งค่าบัญชีของคุณ
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        align="center"
                                        style="padding: 0 0 18px;"
                                    >
                                        <table
                                            role="presentation"
                                            cellspacing="0"
                                            cellpadding="0"
                                            border="0"
                                        >
                                            <tr>
                                                <td
                                                    align="center"
                                                    bgcolor="#1684df"
                                                    style="
                                                        border-radius: 12px;
                                                        background-color: #1684df;
                                                    "
                                                >
                                                    <a
                                                        href="{{ $verificationUrl }}"
                                                        style="
                                                            display: inline-block;
                                                            padding: 14px 32px;
                                                            border: 1px solid #1684df;
                                                            border-radius: 12px;
                                                            color: #ffffff;
                                                            font-size: 15px;
                                                            font-weight: 600;
                                                            line-height: 1.4;
                                                            text-decoration: none;
                                                        "
                                                    >
                                                        ยืนยันอีเมล
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        align="center"
                                        style="
                                            padding: 0 0 26px;
                                            color: #94a3b8;
                                            font-size: 13px;
                                            line-height: 1.7;
                                        "
                                    >
                                        ลิงก์นี้จะหมดอายุภายใน 15 นาที
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="
                                            padding: 22px 0 0;
                                            border-top: 1px solid #e2e8f0;
                                            color: #64748b;
                                            font-size: 12px;
                                            line-height: 1.8;
                                        "
                                    >
                                        หากปุ่มด้านบนใช้งานไม่ได้ ให้เปิดลิงก์นี้ในเบราว์เซอร์:
                                        <br>
                                        <a
                                            href="{{ $verificationUrl }}"
                                            style="
                                                color: #1684df;
                                                word-break: break-all;
                                            "
                                        >
                                            {{ $verificationUrl }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td
                            align="center"
                            style="
                                padding: 20px 16px 0;
                                color: #94a3b8;
                                font-size: 12px;
                                line-height: 1.8;
                            "
                        >
                            หากคุณไม่ได้ทำรายการนี้ สามารถละเว้นอีเมลฉบับนี้ได้
                            <br>
                            &copy; {{ now()->year }} {{ config('app.name', 'CPRATING') }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
