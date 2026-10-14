@extends('layouts.app')

@section('title', 'แผนการเรียน')

@section('content')
    <x-navbar active="plans" />

    <main class="pg source-page" data-plans-page>
        <div class="wrap">
            <section data-plan-selection aria-labelledby="plans-title">
                <p class="eb">แผนการเรียน</p>
                <h1 id="plans-title">เลือก<span class="k">หลักสูตร</span>ของคุณ</h1>
                <p class="ld">เลือกสาขาที่คุณเรียนก่อน แล้วดูแผนการเรียนตลอดหลักสูตร</p>

                <div class="mg" data-major-grid>
                    @foreach ($studyPlans as $majorCode => $studyPlan)
                        <button
                            type="button"
                            class="mc2"
                            data-major-key="{{ $majorCode }}"
                        >
                            <span class="im">
                                <img src="{{ asset($studyPlan['image']) }}" alt="">
                            </span>
                            <b>{{ $studyPlan['name'] }}</b>
                            <span class="s">ดูแผนการเรียน</span>
                        </button>
                    @endforeach
                </div>
            </section>

            @foreach ($studyPlans as $majorCode => $studyPlan)
                @include('plans.detail')
            @endforeach
        </div>
    </main>

    <div id="toast" role="status" aria-live="polite"></div>
    <x-footer />
@endsection
