<article class="pt">
    <h3>ชั้นปีที่ {{ $year }} ภาคการศึกษาที่ {{ $term }}</h3>

    <div class="tw2">
        <table>
            <thead>
                <tr>
                    <th scope="col">รหัสวิชา</th>
                    <th scope="col">ชื่อวิชา</th>
                    <th scope="col">หน่วยกิต</th>
                    <th scope="col">เงื่อนไขของรายวิชา</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($termPlan['courses'] as $course)
                    <tr>
                        <td class="cd">
                            {{ $course['code'] }}
                        </td>
                        <td>
                            <span>{{ $course['name'] }}</span>

                            @if ($course['english_name'])
                                <small>{{ $course['english_name'] }}</small>
                            @endif
                        </td>
                        <td class="cr">
                            {{ $course['credits'] }}
                        </td>
                        <td>
                            {{ $course['prerequisite'] }}
                        </td>
                    </tr>
                @endforeach

                <tr class="sum f">
                    <td colspan="2">รวมจำนวนหน่วยกิตลงทะเบียนเรียน</td>
                    <td class="cr">
                        {{ $termPlan['registered_credits'] }}
                    </td>
                    <td>หน่วยกิต</td>
                </tr>
                <tr class="sum">
                    <td colspan="2">รวมจำนวนหน่วยกิตสะสม</td>
                    <td class="cr">
                        {{ $termPlan['accumulated_credits'] }}
                    </td>
                    <td>หน่วยกิต</td>
                </tr>
            </tbody>
        </table>
    </div>
</article>
