<?php

return [
    'years' => [1, 2, 3, 4],
    'academic_year_start' => 2565,
    'major_images' => [
        'it' => 'images/photo-5.jpg',
        'cs' => 'images/photo-4.jpg',
        'geo' => 'images/photo-3.jpg',
        'ai' => 'images/photo-1.jpg',
        'cy' => 'images/photo-2.jpg',
    ],
    'copy' => [
        'term_heading' => 'ชั้นปีที่ :year ภาคการศึกษาที่ :term',
        'year_label' => 'ชั้นปีที่ :year',
    ],

    'majors' => [
        [
            'key' => 'it',
            'name' => 'เทคโนโลยีสารสนเทศและนวัตกรรมอัจฉริยะ',
            'short_name' => 'เทคโนโลยีสารสนเทศ',
            'image' => 'images/course-security.jpg',
            'courses' => [
                ['code' => 'CP410201', 'name' => 'เทคโนโลยีสารสนเทศ'],
                ['code' => 'CP410305', 'name' => 'การพัฒนาเว็บแอปพลิเคชัน'],
                ['code' => 'CP410310', 'name' => 'นวัตกรรมดิจิทัล'],
                ['code' => 'CP352210', 'name' => 'การเขียนโปรแกรมเบื้องต้น'],
                ['code' => 'CP410320', 'name' => 'การจัดการโครงการไอที'],
                ['code' => 'CP354201', 'name' => 'การเรียนรู้ของเครื่อง'],
            ],
        ],
        [
            'key' => 'cs',
            'name' => 'วิทยาการคอมพิวเตอร์',
            'short_name' => 'วิทยาการคอมพิวเตอร์',
            'image' => 'images/course-computing.jpg',
            'courses' => [
                ['code' => 'CP352220', 'name' => 'โครงสร้างข้อมูล'],
                ['code' => 'CP352101', 'name' => 'ระบบฐานข้อมูล'],
                ['code' => 'CP352230', 'name' => 'อัลกอริทึม'],
                ['code' => 'CP352005', 'name' => 'เครือข่ายคอมพิวเตอร์'],
                ['code' => 'CP352240', 'name' => 'ระบบปฏิบัติการ'],
                ['code' => 'CP354201', 'name' => 'การเรียนรู้ของเครื่อง'],
            ],
        ],
        [
            'key' => 'geo',
            'name' => 'ภูมิสารสนเทศศาสตร์',
            'short_name' => 'ภูมิสารสนเทศศาสตร์',
            'image' => 'images/course-network.jpg',
            'courses' => [
                ['code' => 'GI310101', 'name' => 'ระบบสารสนเทศภูมิศาสตร์'],
                ['code' => 'GI310201', 'name' => 'การรับรู้จากระยะไกล'],
                ['code' => 'GI310301', 'name' => 'การทำแผนที่ดิจิทัล'],
                ['code' => 'GI320101', 'name' => 'การวิเคราะห์ข้อมูลเชิงพื้นที่'],
                ['code' => 'GI320201', 'name' => 'ฐานข้อมูลเชิงพื้นที่'],
                ['code' => 'GI320301', 'name' => 'การประมวลผลภาพดาวเทียม'],
            ],
        ],
        [
            'key' => 'ai',
            'name' => 'ปัญญาประดิษฐ์',
            'short_name' => 'ปัญญาประดิษฐ์',
            'image' => 'images/course-ai.jpg',
            'courses' => [
                ['code' => 'CP354101', 'name' => 'ปัญญาประดิษฐ์'],
                ['code' => 'CP354201', 'name' => 'การเรียนรู้ของเครื่อง'],
                ['code' => 'CP354301', 'name' => 'การประมวลผลภาษาธรรมชาติ'],
                ['code' => 'CP354310', 'name' => 'คอมพิวเตอร์วิทัศน์'],
                ['code' => 'CP352220', 'name' => 'โครงสร้างข้อมูล'],
                ['code' => 'CP354320', 'name' => 'การเรียนรู้เชิงลึก'],
            ],
        ],
        [
            'key' => 'cy',
            'name' => 'ความมั่นคงปลอดภัยไซเบอร์',
            'short_name' => 'ความมั่นคงปลอดภัยไซเบอร์',
            'image' => 'images/course-cybersecurity-reference.jpg',
            'courses' => [
                ['code' => 'CP422301', 'name' => 'ความมั่นคงปลอดภัยไซเบอร์'],
                ['code' => 'CP422305', 'name' => 'การเข้ารหัสลับเบื้องต้น'],
                ['code' => 'CP352005', 'name' => 'เครือข่ายคอมพิวเตอร์'],
                ['code' => 'CP422310', 'name' => 'การทดสอบเจาะระบบ'],
                ['code' => 'CP422320', 'name' => 'นิติวิทยาศาสตร์ดิจิทัล'],
                ['code' => 'CP422330', 'name' => 'ความมั่นคงปลอดภัยเว็บ'],
            ],
        ],
    ],
    'first_year' => [
        [
            ['code' => 'LI 101 101', 'name' => 'ภาษาอังกฤษ 1', 'english_name' => 'English I', 'credits' => '3(3-0-6)', 'prerequisite' => 'ไม่มี'],
            ['code' => 'GE 341 511', 'name' => 'การคิดเชิงคำนวณและเชิงสถิติสำหรับเอบีซีดี', 'english_name' => 'Computational & Statistical Thinking for ABCD', 'credits' => '3(2-2-5)', 'prerequisite' => 'ไม่มี'],
            ['code' => 'GE 321 415', 'name' => 'ทักษะการเรียนรู้', 'english_name' => 'Learning Skills', 'credits' => '3(3-0-6)', 'prerequisite' => 'ไม่มี'],
            ['code' => 'SC 401 201', 'name' => 'แคลคูลัสสำหรับวิทยาศาสตร์กายภาพ 1', 'english_name' => 'Calculus for Physical Science I', 'credits' => '3(3-0-6)', 'prerequisite' => 'ไม่มี'],
            ['code' => 'CP 351 001', 'name' => 'วิทยาการคอมพิวเตอร์หลักมูล', 'english_name' => 'Fundamental Computer Science', 'credits' => '3(2-2-5)', 'prerequisite' => 'ไม่มี'],
            ['code' => 'CP 351 002', 'name' => 'การเขียนโปรแกรมเชิงโครงสร้างสำหรับวิทยาการคอมพิวเตอร์', 'english_name' => 'Structure Programming Languages for Computer Science', 'credits' => '3(2-2-5)', 'prerequisite' => 'ไม่มี'],
        ],
        [
            ['code' => 'LI 101 002', 'name' => 'ภาษาอังกฤษ 2', 'english_name' => 'English II', 'credits' => '3(3-0-6)', 'prerequisite' => 'LI 101 001 หรือเทียบเท่า'],
            ['code' => 'GE 341 512', 'name' => 'เอบีซีดีสำหรับทุกวิชาชีพ', 'english_name' => 'ABCD for All Professions', 'credits' => '3(2-2-5)', 'prerequisite' => 'ไม่มี'],
            ['code' => 'SC 002 104', 'name' => 'วิทยาศาสตร์กายภาพ', 'english_name' => 'Physical Science', 'credits' => '3(3-0-6)', 'prerequisite' => 'ไม่มี'],
            ['code' => 'SC 401 202', 'name' => 'แคลคูลัสสำหรับวิทยาศาสตร์กายภาพ 2', 'english_name' => 'Calculus for Physical Science II', 'credits' => '3(3-0-6)', 'prerequisite' => 'SC 401 201'],
            ['code' => 'CP 351 003', 'name' => 'การเขียนโปรแกรมเชิงวัตถุ', 'english_name' => 'Object Oriented Programming', 'credits' => '3(2-2-5)', 'prerequisite' => 'CP 351 001'],
            ['code' => 'CP 351 004', 'name' => 'สถาปัตยกรรมคอมพิวเตอร์', 'english_name' => 'Computer Architecture', 'credits' => '3(3-0-6)', 'prerequisite' => 'ไม่มี'],
            ['code' => 'CP 3XX XXX', 'name' => 'วิชาเลือกเฉพาะทาง', 'english_name' => 'Specialized Electives', 'credits' => '3', 'prerequisite' => '-'],
        ],
    ],
    'electives' => [
        ['name' => 'วิชาเลือกเฉพาะทาง', 'english_name' => 'Specialized Electives', 'credits' => '3', 'prerequisite' => '-'],
        ['name' => 'วิชาเลือกเสรี', 'english_name' => 'Free Electives', 'credits' => '3', 'prerequisite' => '-'],
        ['name' => 'วิชาศึกษาทั่วไป', 'english_name' => 'General Education', 'credits' => '3', 'prerequisite' => '-'],
    ],
];
