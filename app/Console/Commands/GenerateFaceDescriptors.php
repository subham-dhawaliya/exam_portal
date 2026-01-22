<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateFaceDescriptors extends Command
{
    protected $signature = 'face:generate-descriptors';
    protected $description = 'Show users without face descriptors (descriptors must be generated client-side)';

    public function handle()
    {
        $usersWithoutDescriptor = User::whereNull('face_descriptor')
            ->whereNotNull('reference_face_image')
            ->count();

        $this->info("Users without face descriptors: {$usersWithoutDescriptor}");
        $this->info("Face descriptors are generated client-side during registration.");
        $this->info("Existing users will need to re-register or update their face via profile.");
        
        return 0;
    }
}
