<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyMailTemplateRequest;
use App\Http\Requests\StoreMailTemplateRequest;
use App\Http\Requests\UpdateMailTemplateRequest;
use App\Models\MailTemplate;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class MailTemplateController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('mail_template_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mailTemplates = MailTemplate::with(['team'])->get();

        return view('admin.mailTemplates.index', compact('mailTemplates'));
    }

    public function create()
    {
        abort_if(Gate::denies('mail_template_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.mailTemplates.create');
    }

    public function store(StoreMailTemplateRequest $request)
    {
        $mailTemplate = MailTemplate::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $mailTemplate->id]);
        }

        return redirect()->route('admin.mail-templates.index');
    }

    public function edit(MailTemplate $mailTemplate)
    {
        abort_if(Gate::denies('mail_template_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mailTemplate->load('team');

        return view('admin.mailTemplates.edit', compact('mailTemplate'));
    }

    public function update(UpdateMailTemplateRequest $request, MailTemplate $mailTemplate)
    {
        $mailTemplate->update($request->all());

        return redirect()->route('admin.mail-templates.index');
    }

    public function destroy(MailTemplate $mailTemplate)
    {   
        abort_if(Gate::denies('mail_template_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mailTemplate->delete();

        return back();
    }

    public function massDestroy(MassDestroyMailTemplateRequest $request)
    {
        $mailTemplates = MailTemplate::find(request('ids'));

        foreach ($mailTemplates as $mailTemplate) {
            $mailTemplate->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('mail_template_create') && Gate::denies('mail_template_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new MailTemplate();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}