<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateCategory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class CertificateCategoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('certificate_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = CertificateCategory::orderBy('name')->get();

        return view('admin.certificate_categories.index', compact('categories'));
    }

    public function create()
    {
        abort_if(Gate::denies('certificate_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.certificate_categories.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('certificate_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:0',
            'notify_days_before' => 'nullable|integer|min:1|max:365',
            'enable_notifications' => 'nullable|boolean',
            'notification_recipients' => 'required_if:enable_notifications,1|nullable|string',
            'reminder_frequency_days' => 'nullable|integer|min:1|max:30',
        ]);

        // Process notification_recipients from comma-separated string to array
        if (!empty($data['notification_recipients'])) {
            $emails = array_map('trim', explode(',', $data['notification_recipients']));
            // Validate each email
            foreach ($emails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return back()->withErrors(['notification_recipients' => "Ongeldig email adres: {$email}"])->withInput();
                }
            }
            $data['notification_recipients'] = $emails;
        } else {
            $data['notification_recipients'] = null;
        }

        // Set defaults
        $data['enable_notifications'] = $request->has('enable_notifications');
        $data['notify_days_before'] = $data['notify_days_before'] ?? 30;
        $data['reminder_frequency_days'] = $data['reminder_frequency_days'] ?? 7;

        $category = CertificateCategory::create($data);

        return redirect()->route('admin.certificate-categories.index')->with('success', 'Categorie aangemaakt');
    }

    /**
     * Quick store used by AJAX select2 tags: create or return existing category.
     */
    public function quickStore(Request $request)
    {
        abort_if(Gate::denies('certificate_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $name = trim($request->get('name', ''));
        $duration = $request->get('duration', 12);

        if ($name === '') {
            return response()->json(['message' => 'Name required'], 422);
        }

        $category = CertificateCategory::firstOrCreate(
            ['name' => $name],
            ['duration' => (int)$duration]
        );

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'duration' => $category->duration,
        ]);
    }

    public function edit(CertificateCategory $certificateCategory)
    {
        abort_if(Gate::denies('certificate_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.certificate_categories.edit', compact('certificateCategory'));
    }

    public function update(Request $request, CertificateCategory $certificateCategory)
    {
        abort_if(Gate::denies('certificate_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:0',
            'notify_days_before' => 'nullable|integer|min:1|max:365',
            'enable_notifications' => 'nullable|boolean',
            'notification_recipients' => 'required_if:enable_notifications,1|nullable|string',
            'reminder_frequency_days' => 'nullable|integer|min:1|max:30',
        ]);

        // Process notification_recipients from comma-separated string to array
        if (!empty($data['notification_recipients'])) {
            $emails = array_map('trim', explode(',', $data['notification_recipients']));
            // Validate each email
            foreach ($emails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return back()->withErrors(['notification_recipients' => "Ongeldig email adres: {$email}"])->withInput();
                }
            }
            $data['notification_recipients'] = $emails;
        } else {
            $data['notification_recipients'] = null;
        }

        // Set defaults
        $data['enable_notifications'] = $request->has('enable_notifications');
        $data['notify_days_before'] = $data['notify_days_before'] ?? 30;
        $data['reminder_frequency_days'] = $data['reminder_frequency_days'] ?? 7;

        $certificateCategory->update($data);

        return redirect()->route('admin.certificate-categories.index')->with('success', 'Categorie bijgewerkt');
    }

    public function show(CertificateCategory $certificateCategory)
    {
        abort_if(Gate::denies('certificate_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Eager-load certificates with driver for the detail view cards
        $certificateCategory->load(['certificates' => function($q) {
            $q->with('driver')->orderBy('expiry_date');
        }]);

        return view('admin.certificate_categories.show', compact('certificateCategory'));
    }

    public function destroy(CertificateCategory $certificateCategory)
    {
        abort_if(Gate::denies('certificate_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $certificateCategory->delete();

        return back();
    }

    /**
     * AJAX search for select2 autocomplete.
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');

        $query = CertificateCategory::query();

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        $results = $query->orderBy('name')->limit(20)->get()->map(function ($c) {
            return ['id' => $c->id, 'text' => $c->name, 'duration' => $c->duration];
        });

        return response()->json(['results' => $results]);
    }
}
