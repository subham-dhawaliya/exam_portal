<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['name' => 'General Knowledge', 'icon' => '🌍', 'color' => '#3B82F6', 'description' => 'Current affairs, history, geography, and general awareness'],
            ['name' => 'Mathematics', 'icon' => '🔢', 'color' => '#10B981', 'description' => 'Arithmetic, algebra, geometry, and problem solving'],
            ['name' => 'English', 'icon' => '📝', 'color' => '#8B5CF6', 'description' => 'Grammar, vocabulary, comprehension, and writing'],
            ['name' => 'Reasoning', 'icon' => '🧠', 'color' => '#F59E0B', 'description' => 'Logical reasoning, puzzles, and analytical thinking'],
            ['name' => 'Science', 'icon' => '🔬', 'color' => '#EF4444', 'description' => 'Physics, chemistry, biology, and environmental science'],
            ['name' => 'Computer Science', 'icon' => '💻', 'color' => '#06B6D4', 'description' => 'Programming, databases, networking, and IT fundamentals'],
        ];

        foreach ($sections as $index => $section) {
            Section::create([
                'name' => $section['name'],
                'icon' => $section['icon'],
                'color' => $section['color'],
                'description' => $section['description'],
                'order' => $index,
                'is_active' => true,
            ]);
        }
    }
}
