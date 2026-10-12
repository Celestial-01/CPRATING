<x-admin.page
    :title="$pageTitle"
    :description="$pageDescription"
>
    <x-admin.form-card
        :action="$formAction"
        :cancel="route('admin.teachers.index')"
        :submit="$submitLabel"
    >
        @if ($editing)
            @method('PUT')
        @endif

        <x-admin.field label="คำนำหน้า" name="prefix">
            <x-admin.select name="prefix">
                <option value="">-- เลือกคำนำหน้า --</option>

                @foreach ($prefixes as $prefix)
                    <option value="{{ $prefix }}" @selected($selectedPrefix === $prefix)>
                        {{ $prefix }}
                    </option>
                @endforeach
            </x-admin.select>
        </x-admin.field>

        <x-admin.field label="ชื่อและนามสกุลอาจารย์" name="teacher_name" required>
            <x-admin.input
                name="teacher_name"
                placeholder="กรอกชื่อและนามสกุลอาจารย์"
                :value="$teacherName"
                required
            />
        </x-admin.field>
    </x-admin.form-card>
</x-admin.page>
