<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $showSkeleton = request()->boolean('loading');

        $featuredCourse = [
            'code' => 'CP354101',
            'category' => 'AI',
            'credits' => '3 หน่วยกิต (2-2-5)',
            'title' => 'ปัญญาประดิษฐ์',
            'englishTitle' => 'ARTIFICIAL INTELLIGENCE',
            'description' => 'คอนเทนต์ตั้งแต่พื้นฐาน search algorithm จนถึง neural network เหมาะกับคนที่อยากต่อยอด AI แต่ไม่รู้จะเริ่มต้นตรงไหน',
            'image' => asset('images/course-ai.jpg'),
            'rating' => '4.7',
            'learners' => '1.4k',
            'lessons' => '128',
        ];

        $featuredCourses = [
            $featuredCourse,
            [
                'code' => 'CP422301',
                'category' => 'Cybersecurity',
                'credits' => '3 หน่วยกิต (2-2-5)',
                'title' => 'ความมั่นคงปลอดภัยไซเบอร์',
                'englishTitle' => 'CYBERSECURITY',
                'description' => 'เรียนรู้พื้นฐานการป้องกันข้อมูลและระบบเครือข่าย พร้อมแนวคิดที่นำไปใช้ได้จริง',
                'image' => asset('images/course-cybersecurity-reference.jpg'),
                'rating' => '4.8',
                'learners' => '2.1k',
                'lessons' => '96',
            ],
            [
                'code' => 'CP352005',
                'category' => 'Computer Network',
                'credits' => '3 หน่วยกิต (2-2-5)',
                'title' => 'เครือข่ายคอมพิวเตอร์',
                'englishTitle' => 'COMPUTER NETWORK',
                'description' => 'ทำความเข้าใจการสื่อสารระหว่างอุปกรณ์ ตั้งแต่พื้นฐานจนถึงการออกแบบเครือข่าย',
                'image' => asset('images/course-network.jpg'),
                'rating' => '4.6',
                'learners' => '1.8k',
                'lessons' => '128',
            ],
            [
                'code' => 'CP352210',
                'category' => 'Programming',
                'credits' => '3 หน่วยกิต (2-2-5)',
                'title' => 'การเขียนโปรแกรมเบื้องต้น',
                'englishTitle' => 'INTRODUCTION TO PROGRAMMING',
                'description' => 'เริ่มต้นคิดเป็นขั้นตอนและเขียนโปรแกรมด้วยโจทย์ที่ออกแบบมาสำหรับผู้เริ่มต้น',
                'image' => asset('images/course-computing.jpg'),
                'rating' => '4.7',
                'learners' => '1.6k',
                'lessons' => '110',
            ],
            [
                'code' => 'CP410201',
                'category' => 'Information Technology',
                'credits' => '3 หน่วยกิต (2-2-5)',
                'title' => 'เทคโนโลยีสารสนเทศ',
                'englishTitle' => 'INFORMATION TECHNOLOGY',
                'description' => 'สำรวจพื้นฐานเทคโนโลยีสารสนเทศและบทบาทของระบบดิจิทัลในองค์กรยุคใหม่',
                'image' => asset('images/course-security.jpg'),
                'rating' => '4.5',
                'learners' => '1.2k',
                'lessons' => '84',
            ],
        ];

        $courses = [
            [
                'code' => 'CP422301',
                'title' => 'ความมั่นคงปลอดภัยไซเบอร์ขั้นแนะนำ',
                'image' => asset('images/course-cybersecurity-reference.jpg'),
                'rating' => '4.7',
                'learners' => '1.4k',
                'lessons' => '128',
                'isAvailable' => true,
            ],
            [
                'code' => 'CP352005',
                'title' => 'เครือข่ายคอมพิวเตอร์',
                'image' => asset('images/course-network.jpg'),
                'rating' => '4.7',
                'learners' => '1.4k',
                'lessons' => '128',
                'isAvailable' => false,
            ],
            [
                'code' => 'CP354101',
                'title' => 'ปัญญาประดิษฐ์',
                'image' => asset('images/course-ai.jpg'),
                'rating' => '4.7',
                'learners' => '1.4k',
                'lessons' => '128',
                'isAvailable' => true,
            ],
            [
                'code' => 'CP352210',
                'title' => 'การเขียนโปรแกรมเบื้องต้น',
                'image' => asset('images/course-computing.jpg'),
                'rating' => '4.7',
                'learners' => '1.4k',
                'lessons' => '128',
                'isAvailable' => false,
            ],
        ];

        return view('home', compact('featuredCourses', 'courses', 'showSkeleton'));
    }
}
