@extends("emails.layout")

@section("content")

<tr>
    <td class="wrapper"
        style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
        <table border="0" cellpadding="0" cellspacing="0"
            style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
            <tr>
                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                    <p
                        style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                        مرحبا مدير الموقع
                    </p>
                    <p
                        style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                        يوجد رسالة جديدة من زوار الموقع
                        <br><br>
                        <div>
                            <span>
                                المستخدم:
                            </span>
                            <span>
                                {{ $name }}
                            </span>
                        </div>
                        <div>
                            <span>
                                البريد الإلكتروني:
                            </span>
                            <span>
                                {{ $email }}
                            </span>
                        </div>
                        <div>
                            <span>
                                رقم التليفون:
                            </span>
                            <span>
                                {{ $phone }}
                            </span>
                        </div>
                        <div>
                            <span>
                                عنوان الرسالة:
                            </span>
                            <span>
                                {{ $subject }}
                            </span>
                        </div>
                        <br>
                        <div>
                            {{  $text  }}
                        </div>
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>

@stop
