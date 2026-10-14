@extends('layouts.app')

@section('title', 'แผนการเรียน')

@section('content')
    <x-navbar active="plans" />

    <main class="content-page source-page" data-plans-page>
        <div class="page-container">
            <section data-plan-selection aria-labelledby="plans-title">
                <p class="section-eyebrow">แผนการเรียน</p>
                <h1 id="plans-title">เลือก<span class="gradient-text">หลักสูตร</span>ของคุณ</h1>
                <p class="page-lead">เลือกสาขาที่คุณเรียนก่อน แล้วดูแผนการเรียนตลอดหลักสูตร</p>

                <div class="study-plan-major-grid" data-major-grid>
                    @foreach ($studyPlans as $majorCode => $studyPlan)
                        <button
                            type="button"
                            class="study-plan-major-card"
                            data-major-key="{{ $majorCode }}"
                        >
                            <span class="study-plan-major-card__image">
                                <img src="{{ asset($studyPlan['image']) }}" alt="">
                            </span>
                            <b>{{ $studyPlan['name'] }}</b>
                            <span class="study-plan-major-card__action">ดูแผนการเรียน</span>
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
