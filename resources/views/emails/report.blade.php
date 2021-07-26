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
                        لقد أرسل أحد مستخدمي الموقع تقييماً جديداً
                        <br><br>
                        <div>
                            <span>
                                إسم الزائر:
                            </span>
                            <span>
                                {{ $name }}
                            </span>
                        </div>
                        @if($country)
                        <div>
                            <span>
                                الدولة:
                            </span>
                            <span>
                                {{ $country }}
                            </span>
                        </div>
                        <br>
                        @endif

                        @if($text)
                        <br>
                        <div>
                            {!! $text !!}
                        </div>
                        @endif

                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>

@stop
