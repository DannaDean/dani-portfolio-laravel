<?php

namespace App\Http\Controllers;

use App\Support\FactHtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private const SECTIONS = ['projects', 'skills', 'facts', 'contacts'];

    public function index(Request $request, ?string $section = null): Response
    {
        abort_if($section !== null && ! in_array($section, self::SECTIONS, true), 404);

        return Inertia::render('Dashboard', [
            'section' => $section ?? 'overview',
            'items' => $section ? DB::table($section)->orderByDesc('id')->get() : [],
            'counts' => collect(self::SECTIONS)->mapWithKeys(fn (string $table) => [
                $table => DB::table($table)->count(),
            ]),
            'user' => $request->user()->only('name', 'email', 'avatar_url'),
        ]);
    }

    public function store(Request $request, string $section): RedirectResponse
    {
        $this->editableSection($section);
        $data = $this->validatedData($request, $section);
        DB::table($section)->insert([...$data, 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Created successfully.');
    }

    public function update(Request $request, string $section, int $id): RedirectResponse
    {
        $this->editableSection($section);
        $existing = DB::table($section)->find($id);
        abort_unless($existing, 404);

        $data = $this->validatedData($request, $section, $existing);
        DB::table($section)->where('id', $id)->update([...$data, 'updated_at' => now()]);

        return back()->with('success', 'Updated successfully.');
    }

    public function destroy(string $section, int $id): RedirectResponse
    {
        abort_unless(in_array($section, self::SECTIONS, true), 404);
        $existing = DB::table($section)->find($id);
        abort_unless($existing, 404);

        DB::table($section)->where('id', $id)->delete();
        // Uploaded files can be shared by records; leave them intact until explicitly replaced.
        return back()->with('success', 'Deleted successfully.');
    }

    private function editableSection(string $section): void
    {
        abort_unless(in_array($section, ['projects', 'skills', 'facts'], true), 404);
    }

    private function validatedData(Request $request, string $section, ?object $existing = null): array
    {
        $rules = match ($section) {
            'projects' => [
                'title' => ['required', 'string', 'max:255'],
                'link' => ['nullable', 'url', 'max:2048'],
                'categories' => ['nullable', 'string', 'max:1000'],
                'desk_img' => ['nullable', 'string', 'max:2048'],
                'mobile_img' => ['nullable', 'string', 'max:2048'],
                'desk_upload' => ['nullable', 'image', 'max:5120'],
                'mobile_upload' => ['nullable', 'image', 'max:5120'],
            ],
            'skills' => [
                'title' => ['nullable', 'string', 'max:255'],
                'image' => ['nullable', 'string', 'max:2048'],
                'image_upload' => ['nullable', 'image', 'max:5120'],
            ],
            'facts' => [
                'title' => ['required', 'string', 'max:255'],
                'text' => ['nullable', 'string', 'max:10000'],
            ],
        };

        $data = $request->validate($rules);
        $imageFields = match ($section) {
            'projects' => ['desk', 'mobile'],
            'skills' => ['image'],
            default => [],
        };
        foreach ($imageFields as $field) {
            $uploadKey = $field.'_upload';
            $column = $field === 'image' ? 'image' : $field.'_img';
            if ($request->hasFile($uploadKey)) {
                $path = $request->file($uploadKey)->store($section, 'public');
                $data[$column] = '/storage/'.$path;
            } elseif ($existing && ! array_key_exists($column, $data)) {
                $data[$column] = $existing->$column;
            }
            unset($data[$uploadKey]);
        }

        if ($section === 'projects') {
            $categories = array_filter(array_map('trim', explode(',', $data['categories'] ?? '')));
            $data['categories'] = json_encode(array_values($categories));
        }

        if ($section === 'facts') {
            $data['text'] = FactHtmlSanitizer::clean($data['text'] ?? null);
        }

        return $data;
    }
}
