<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class Navbar extends Component
{
    public array $links;

    public string $navbarClasses;

    public function __construct(
        public string $active = 'home',
        public bool $homeHero = false,
    ) {
        $this->links = collect([
            ['key' => 'home', 'label' => 'หน้าหลัก', 'route' => 'home'],
            ['key' => 'courses', 'label' => 'ค้นหาวิชา', 'route' => 'courses.index'],
            ['key' => 'plans', 'label' => 'แผนการเรียน', 'route' => 'plans.index'],
            ['key' => 'history', 'label' => 'ประวัติ', 'route' => 'history.index'],
        ])->map(function (array $link): array {
            $isActive = $this->active === $link['key'];

            return $link + [
                'isActive' => $isActive,
                'desktopClasses' => $isActive
                    ? 'site-navbar__link inline-flex rounded-full bg-[#dceeff] px-3.5 py-1.5 text-[15px] font-medium text-[#0a7ae6] transition-colors'
                    : 'site-navbar__link inline-flex rounded-full px-3.5 py-1.5 text-[15px] font-medium text-[#657083] transition-colors hover:bg-[#dceeff] hover:text-[#0a7ae6]',
                'mobileClasses' => $isActive
                    ? 'mobile-menu__link mobile-menu__link--active flex cursor-pointer items-center justify-between gap-4 bg-[#dceeff] px-4 py-4 text-[15px] font-semibold text-[#0a7ae6] transition-colors'
                    : 'mobile-menu__link flex cursor-pointer items-center justify-between gap-4 border-t border-slate-200 px-4 py-4 text-[15px] font-medium text-[#657083] transition-colors hover:bg-[#dceeff] hover:text-[#0a7ae6]',
            ];
        })->all();

        $this->navbarClasses = 'site-navbar mx-auto w-full max-w-[1120px] bg-transparent px-3 pb-2 pt-4 sm:px-6 sm:pb-3 sm:pt-4';

        if ($this->homeHero) {
            $this->navbarClasses .= ' site-navbar--hero';
        }
    }

    public function render(): View
    {
        return view('components.navbar');
    }
}
