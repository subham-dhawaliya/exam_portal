<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key from question_bank table only
        // Using raw SQL to avoid errors if key doesn't exist
        try {
            DB::statement('ALTER TABLE question_bank DROP FOREIGN KEY question_bank_created_by_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist, ignore
        }

        try {
            DB::statement('ALTER TABLE exams DROP FOREIGN KEY exams_created_by_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist, ignore
        }
    }

    public function down(): void
    {
        // No need to restore - admins table is separate from users
    }
};
