<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class CurrentProjects extends Component
{
    public $status = 'active'; // Ensure status is a public property

    public function toggleStatus()
    {
        // Toggle the value of $status between 'active' and 'completed'
        $this->status = $this->status === 'active' ? 'completed' : 'active';
        // Log the status change for debugging
        Log::info("Status changed to: {$this->status}");
    }

    public function render()
    {
        // Fetch projects based on the current status (you can modify this to fetch from your database)
        $projects = Project::where('status', $this->status)->get(); // Use $this->status for dynamic fetching

        // Log the projects being fetched (for debugging)
        Log::info("Fetching projects with status: {$this->status}", ['projects_count' => $projects->count()]);

        // Return the view and pass the data
        
    }
}
