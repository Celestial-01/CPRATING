<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CourseController extends Controller
{
    public function show(string $code): View
    {
        $courses = $this->courses();
        $course = collect($courses)->firstWhere('code', strtoupper($code));

        if ($course === null) {
            throw new NotFoundHttpException;
        }

        $course['gradeAChance'] = $course['grades']['A'] ?? 0;
        $showSkeleton = request()->boolean('loading');

        $relatedCourses = collect($courses)
            ->where('code', '!=', $course['code'])
            ->take(4)
            ->values()
            ->all();

        $courseOverviewStats = [
            [
                'title' => 'ระดับความยาก',
                'percentage' => $course['difficulty'],
                'summary' => 'ยากมาก',
                'description' => 'รายวิชานี้ต้องมีความเข้าใจพื้นฐาน Network ค่อนข้างสูง',
                'accentClass' => 'bg-rose-400',
            ],
            [
                'title' => 'ปริมาณงาน',
                'percentage' => $course['workload'],
                'summary' => 'ปานกลาง',
                'description' => 'เน้นทำ Lab รายสัปดาห์ ไม่ค่อยมีรายงานกลุ่มขนาดใหญ่',
                'accentClass' => 'bg-[#477ff3]',
            ],
        ];

        $comments = [
            [
                'name' => 'Chicken',
                'time' => '3 เดือนที่แล้ว',
                'text' => 'ได้พื้นฐานเกี่ยวกับ Cybersecurity เยอะเลยครับ ถือว่าดีมากๆ งานไม่ค่อยเยอะ อึดลุยล่ะ',
                'likes' => 74,
            ],
            [
                'name' => 'Who',
                'time' => '11 ชั่วโมงที่แล้ว',
                'text' => 'ได้พื้นฐานเกี่ยวกับ Cybersecurity เยอะเลยค่ะ ถือว่าดีมากๆ งานไม่ค่อยเยอะ อึดลุยล่ะ',
                'likes' => 28,
            ],
            [
                'name' => 'Cat',
                'time' => '6 วันที่แล้ว',
                'text' => 'ได้พื้นฐานเกี่ยวกับ Cybersecurity เยอะเลยค่ะ ถือว่าดีมากๆ งานไม่ค่อยเยอะ อึดลุยล่ะ',
                'likes' => 12,
            ],
            [
                'name' => 'Conan',
                'time' => '11 ชั่วโมงที่แล้ว',
                'text' => 'ได้พื้นฐานเกี่ยวกับ Cybersecurity เยอะเลยค่ะ ถือว่าดีมากๆ งานไม่ค่อยเยอะ อึดลุยล่ะ',
                'likes' => 2,
            ],
        ];

        return view('course.show', compact('course', 'relatedCourses', 'courseOverviewStats', 'comments', 'showSkeleton'));
    }

    private function courses(): array
    {
        return [
            [
                'code' => 'CP422301',
                'category' => 'Cybersecurity',
                'categoryCode' => 'CY',
                'title' => 'ความมั่นคงปลอดภัยไซเบอร์ขั้นแนะนำ',
                'englishTitle' => 'INTRODUCTION TO CYBERSECURITY',
                'credits' => '3 หน่วยกิต (2-2-5)',
                'instructor' => 'อ. ดร. ณัฐวุฒิ ศรีสุข',
                'description' => 'เรียนรู้พื้นฐานด้านความมั่นคงปลอดภัยไซเบอร์ ตั้งแต่แนวคิดเกี่ยวกับภัยคุกคามทางดิจิทัล รูปแบบการโจมตี การป้องกันข้อมูลและระบบเครือข่าย ไปจนถึงหลักการรักษาความปลอดภัยเบื้องต้นที่จำเป็นสำหรับสายงานไอที',
                'image' => asset('images/course-cybersecurity-reference.jpg'),
                'rating' => '4.7',
                'learners' => '14.7k',
                'views' => '128',
                'lessons' => '128',
                'difficulty' => 85,
                'workload' => 40,
                'gradeSampleSize' => 128,
                'grades' => ['F' => 3, 'D' => 4, 'D+' => 6, 'C' => 12, 'C+' => 10, 'B' => 18, 'B+' => 11, 'A' => 36],
                'topics' => [
                    'Security Fundamentals',
                    'Threat & Attack Types',
                    'Cryptography',
                    'Network Security',
                    'Authentication',
                    'Firewall & IDS',
                    'Web Security',
                    'Security Policy',
                ],
                'prerequisites' => [
                    ['code' => 'CP422101', 'title' => 'Computer Networks'],
                    ['code' => 'CP422103', 'title' => 'Operating Systems'],
                ],
                'nextCourses' => [
                    ['code' => 'CP422302', 'title' => 'Network Security'],
                    ['code' => 'CP422302', 'title' => 'Network Security'],
                    ['code' => 'CP422302', 'title' => 'Network Security'],
                ],
            ],
            [
                'code' => 'CP354101',
                'category' => 'AI',
                'title' => 'ปัญญาประดิษฐ์',
                'englishTitle' => 'ARTIFICIAL INTELLIGENCE',
                'credits' => '3 หน่วยกิต (2-2-5)',
                'instructor' => 'อ. ดร. กิตติพงษ์ วัฒนะ',
                'description' => 'ทำความเข้าใจหลักการของ AI ตั้งแต่ search algorithm ไปจนถึง neural network พร้อมตัวอย่างการประยุกต์ใช้งาน',
                'image' => asset('images/course-ai.jpg'),
                'rating' => '4.7',
                'learners' => '1.4k',
                'views' => '7.5k',
                'lessons' => '128',
                'difficulty' => 74,
                'workload' => 68,
                'gradeSampleSize' => 100,
                'grades' => ['F' => 5, 'D' => 5, 'D+' => 8, 'C' => 15, 'C+' => 12, 'B' => 20, 'B+' => 7, 'A' => 28],
                'topics' => ['Search Algorithms', 'Machine Learning', 'Neural Networks'],
                'prerequisites' => [
                    ['code' => 'CP352210', 'title' => 'การเขียนโปรแกรมเบื้องต้น'],
                ],
                'nextCourses' => [
                    ['code' => 'CP454201', 'title' => 'Deep Learning'],
                ],
            ],
            [
                'code' => 'CP352005',
                'category' => 'Computer Network',
                'title' => 'เครือข่ายคอมพิวเตอร์',
                'englishTitle' => 'COMPUTER NETWORK',
                'credits' => '3 หน่วยกิต (2-2-5)',
                'instructor' => 'อ. ดร. ปริญญา อินทรศรี',
                'description' => 'พื้นฐานการสื่อสารระหว่างอุปกรณ์ ตั้งแต่โปรโตคอลเครือข่ายจนถึงการออกแบบระบบเครือข่าย',
                'image' => asset('images/course-network.jpg'),
                'rating' => '4.6',
                'learners' => '1.8k',
                'views' => '9.1k',
                'lessons' => '128',
                'difficulty' => 58,
                'workload' => 52,
                'gradeSampleSize' => 120,
                'grades' => ['F' => 4, 'D' => 4, 'D+' => 7, 'C' => 16, 'C+' => 13, 'B' => 22, 'B+' => 4, 'A' => 30],
                'topics' => ['TCP/IP', 'Routing', 'Network Design'],
                'prerequisites' => [
                    ['code' => 'CP352210', 'title' => 'การเขียนโปรแกรมเบื้องต้น'],
                ],
                'nextCourses' => [
                    ['code' => 'CP453301', 'title' => 'Network Security'],
                ],
            ],
            [
                'code' => 'CP352210',
                'category' => 'Programming',
                'title' => 'การเขียนโปรแกรมเบื้องต้น',
                'englishTitle' => 'INTRODUCTION TO PROGRAMMING',
                'credits' => '3 หน่วยกิต (2-2-5)',
                'instructor' => 'อ. ดร. ธนภัทร สุวรรณ',
                'description' => 'ฝึกคิดอย่างเป็นขั้นตอนและเขียนโปรแกรมผ่านโจทย์ที่ออกแบบมาสำหรับผู้เริ่มต้น',
                'image' => asset('images/course-computing.jpg'),
                'rating' => '4.7',
                'learners' => '1.6k',
                'views' => '8.7k',
                'lessons' => '110',
                'difficulty' => 45,
                'workload' => 42,
                'gradeSampleSize' => 110,
                'grades' => ['F' => 2, 'D' => 3, 'D+' => 5, 'C' => 12, 'C+' => 11, 'B' => 23, 'B+' => 6, 'A' => 38],
                'topics' => ['Problem Solving', 'Algorithms', 'Python'],
                'prerequisites' => [],
                'nextCourses' => [
                    ['code' => 'CP353101', 'title' => 'Data Structures'],
                ],
            ],
            [
                'code' => 'CP410201',
                'category' => 'Information Technology',
                'categoryCode' => 'IT',
                'title' => 'เทคโนโลยีสารสนเทศ',
                'englishTitle' => 'INFORMATION TECHNOLOGY',
                'credits' => '3 หน่วยกิต (2-2-5)',
                'instructor' => 'อ. ดร. พิมพ์ชนก รัตนกุล',
                'description' => 'สำรวจพื้นฐานเทคโนโลยีสารสนเทศและบทบาทของระบบดิจิทัลในองค์กรยุคใหม่',
                'image' => asset('images/course-security.jpg'),
                'rating' => '4.5',
                'learners' => '1.2k',
                'views' => '84',
                'lessons' => '84',
                'difficulty' => 45,
                'workload' => 40,
                'gradeSampleSize' => 96,
                'grades' => ['F' => 3, 'D' => 4, 'D+' => 6, 'C' => 12, 'C+' => 10, 'B' => 18, 'B+' => 11, 'A' => 36],
                'topics' => ['Information Systems', 'Digital Technology', 'IT Management'],
                'prerequisites' => [],
                'nextCourses' => [],
            ],
        ];
    }
}
