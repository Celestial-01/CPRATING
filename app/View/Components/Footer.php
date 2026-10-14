<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class Footer extends Component
{
    public string $columnHeadingClass = 'mb-2 block text-[13px] text-[#0b1220]';

    public string $columnLinkClass = 'block py-0.75 hover:text-[#0b1220] hover:underline';

    public array $exploreLinks;

    public string $disclaimer;

    public function __construct(public bool $flush = false)
    {
        $this->exploreLinks = [
            ['label' => 'ค้นหาวิชา', 'href' => route('courses.index')],
            ['label' => 'แผนการเรียน', 'href' => route('plans.index')],
            ['label' => 'รีวิวล่าสุด', 'href' => route('home').'#reviews'],
            ['label' => 'คำถามที่พบบ่อย', 'href' => route('home').'#faq'],
        ];

        if (request()->routeIs('home')) {
            array_splice($this->exploreLinks, 2, 0, [
                ['label' => 'ไฮไลต์', 'href' => route('home').'#features'],
                ['label' => 'วิธีใช้งาน', 'href' => route('home').'#steps'],
            ]);
        }

        $this->disclaimer = request()->routeIs('home')
            ? 'คะแนน รีวิว และตัวเลขสถิติในหน้านี้เป็นข้อมูลตัวอย่างเพื่อการออกแบบ รูปประกอบเป็นภาพตัวอย่างของรายวิชา'
            : 'คะแนน รีวิว ชื่อวิชา และแผนการเรียนในหน้านี้เป็นข้อมูลตัวอย่างเพื่อการออกแบบ รูปประกอบเป็นภาพตัวอย่าง';
    }

    public function render(): View
    {
        return view('components.footer');
    }
}
