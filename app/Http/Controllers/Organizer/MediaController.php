<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\{Event, EventMedia};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Event $event)
    {
        $media = $event->media()->get();
        return view('organizer.media.index', compact('event','media'));
    }
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'files'    => 'required|array|max:10',
            'files.*'  => 'file|mimes:jpg,jpeg,png,gif,mp4,avi,pdf,doc,docx|max:51200',
            'category' => 'required|in:banner,gallery,promotional,document,video',
        ]);
        foreach ($request->file('files') as $file) {
            $path = $file->store('events/' . $event->id . '/media', 'public');
            EventMedia::create([
                'event_id'   => $event->id,
                'file_path'  => $path,
                'file_name'  => $file->getClientOriginalName(),
                'file_type'  => str_starts_with($file->getMimeType(), 'image') ? 'image' : (str_starts_with($file->getMimeType(), 'video') ? 'video' : 'document'),
                'mime_type'  => $file->getMimeType(),
                'file_size'  => $file->getSize(),
                'category'   => $request->category,
                'title'      => $request->input('title'),
                'sort_order' => EventMedia::where('event_id',$event->id)->max('sort_order') + 1,
            ]);
        }
        return back()->with('success', 'Media uploaded successfully.');
    }
    public function destroy(EventMedia $media)
    {
        Storage::disk('public')->delete($media->file_path);
        $media->delete();
        return back()->with('success', 'Media deleted.');
    }
}
