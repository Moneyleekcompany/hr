<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectMediaController extends Controller
{
    public function gallery($id)
    {
        // جلب المشروع مع جميع مهامه والمرفقات الخاصة بكل مهمة
        $project = Project::with('tasks.taskAttachments')->findOrFail($id);
        
        $mediaFiles = collect();
        
        foreach ($project->tasks as $task) {
            foreach ($task->taskAttachments as $attachment) {
                // فلترة الصور والفيديوهات فقط للمعرض
                if (in_array(strtolower($attachment->attachment_extension), ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'webp'])) {
                    $mediaFiles->push($attachment);
                }
            }
        }
        
        return view('admin.project.gallery', compact('project', 'mediaFiles'));
    }
}