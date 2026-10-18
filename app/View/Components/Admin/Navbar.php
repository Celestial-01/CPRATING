<?php

namespace App\View\Components\Admin;

use App\Models\User;
use Illuminate\View\Component;
use Illuminate\View\View;

final class Navbar extends Component
{
    public array $menus;

    public ?User $user;

    public function __construct()
    {
        $this->user = auth()->user();
        $this->user?->loadMissing('googleAccount');

        $this->menus = collect([
            ['label' => 'หน้าหลัก', 'route' => 'admin.dashboard'],
            ['label' => 'เพิ่มรายวิชา', 'route' => 'admin.courses.create'],
            ['label' => 'เพิ่มอาจารย์', 'route' => 'admin.teachers.index'],
            ['label' => 'หัวข้อทั้งหมด', 'route' => 'admin.topics.index'],
            ['label' => 'รายงาน', 'route' => 'admin.reports.index'],
            ['label' => 'รออนุมัติ', 'route' => 'admin.moderation.index'],
        ])->map(function (array $menu): array {
            $active = request()->routeIs($menu['route']);

            return $menu + [
                'active' => $active,
                'classes' => $active
                    ? 'border-b-2 border-[#1684e5] pb-1 text-[#1684e5]'
                    : 'text-slate-900 transition hover:text-[#1684e5]',
            ];
        })->all();
    }

    public function render(): View
    {
        return view('components.admin.navbar');
    }
}
