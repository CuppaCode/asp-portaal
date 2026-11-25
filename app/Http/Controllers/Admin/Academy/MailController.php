<?php

namespace App\Http\Controllers\Admin\Academy;

use App\Http\Controllers\Controller;
use App\Models\AcademyChecklistItem;
use App\Models\AcademyTraining;
use App\Models\Certificate;
use App\Models\Company;
use App\Services\Academy\AcademyMailService;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class MailController extends Controller
{
    public function __construct(protected AcademyMailService $mails)
    {
    }

    public function compose(Request $request)
    {
        abort_if(Gate::denies('academy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'template' => 'required|string|max:20',
            'context' => 'required|in:employer,training,certificate',
            'id' => 'required|integer',
            'item' => 'nullable|integer',
            'level' => 'nullable|integer|in:1,2',
        ]);

        $template = $this->mails->template($data['template']);

        if (!$template) {
            return redirect()->back()->with('message', 'Mailsjabloon ' . $data['template'] . ' is niet gevonden of niet actief. Beheer de sjablonen onder Mailsjablonen.');
        }

        $subject = $this->mails->resolveSubject($data['context'], (int) $data['id']);
        $item = $this->itemFor($subject, $data['item'] ?? null);

        $bulkRule = $item?->definition()['bulk'] ?? null;
        $targets = $bulkRule ? $this->mails->bulkTargets($bulkRule, $subject) : collect();

        $tags = $this->mails->commonTags($subject);
        $subjectLine = $this->mails->replaceTags((string) $template->subject, $tags);
        $body = $this->mails->replaceTags((string) $template->body, $tags);
        $recipients = $this->mails->defaultRecipients($template, $subject);

        $ignore = $bulkRule ? AcademyMailService::DRIVER_TAGS : [];

        return view('admin.academy.mail.compose', [
            'template' => $template,
            'context' => $data['context'],
            'subjectModel' => $subject,
            'item' => $item,
            'level' => $data['level'] ?? null,
            'bulk' => (bool) $bulkRule,
            'attachForm' => $item?->definition()['attach_form'] ?? null,
            'targets' => $targets,
            'to' => old('to', implode(', ', $recipients['to'])),
            'cc' => old('cc', implode(', ', $recipients['cc'])),
            'subjectLine' => old('subject', $subjectLine),
            'body' => old('body', $body),
            'placeholders' => $this->mails->placeholders($subjectLine . "\n" . $body, $ignore),
            'templateAttachments' => $template->getMedia('attachments'),
            'backUrl' => $this->backUrl($data['context'], $subject),
            'trainingChecks' => $subject instanceof AcademyTraining,
            'sender' => config('academy.mail.from_address'),
        ]);
    }

    public function send(Request $request)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $uploadConfig = config('file-uploads');
        $mailContext = $uploadConfig['contexts']['academy_mail'] ?? [];
        $maxFiles = (int) ($mailContext['max_files'] ?? 10);
        $maxFileSizeMb = (int) ($mailContext['max_file_size_mb'] ?? 25);
        $allowedExtensions = array_map('strtolower', $uploadConfig['allowed_extensions'] ?? ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx']);

        $data = $request->validate([
            'template' => 'required|string|max:20',
            'context' => 'required|in:employer,training,certificate',
            'id' => 'required|integer',
            'item' => 'nullable|integer',
            'level' => 'nullable|integer|in:1,2',
            'bulk' => 'nullable|boolean',
            'attach_form' => 'nullable|boolean',
            'to' => 'nullable|string|max:1000',
            'cc' => 'nullable|string|max:1000',
            'drivers' => 'nullable|array',
            'drivers.*' => 'integer',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'checks' => 'required|array|min:' . ($request->input('context') === 'training' ? 4 : 3),
            'attachments' => 'nullable|array|max:' . $maxFiles,
            'attachments.*' => 'file|max:' . ($maxFileSizeMb * 1024) . '|mimes:' . implode(',', $allowedExtensions),
        ], [
            'checks.required' => 'Bevestig de controle vóór verzending.',
            'checks.min' => 'Bevestig alle punten van de controle vóór verzending.',
        ]);

        $template = $this->mails->template($data['template']);
        abort_unless($template, Response::HTTP_NOT_FOUND);

        $subject = $this->mails->resolveSubject($data['context'], (int) $data['id']);
        $item = $this->itemFor($subject, $data['item'] ?? null);
        $cc = $this->parseEmails($data['cc'] ?? '', 'cc');
        $uploads = $request->file('attachments', []);
        $user = $request->user();
        // Only mails that are meant for it can carry the form.
        $attachForm = $request->boolean('attach_form') && !empty($item?->definition()['attach_form']);

        if ($request->boolean('bulk')) {
            $bulkRule = $item?->definition()['bulk'] ?? null;
            $targets = $this->mails->bulkTargets($bulkRule, $subject)
                ->filter(fn ($target) => in_array($target['driver']->id, $data['drivers'] ?? [], true))
                ->values();

            $mailings = $this->mails->sendBulk($template, $subject, $targets, $cc, $data['subject'], $data['body'], $user, $uploads, $item, $attachForm);
            $message = $mailings->count() . ' mail(s) verstuurd.';
        } else {
            $to = $this->parseEmails($data['to'] ?? '', 'to');

            if (empty($to)) {
                throw ValidationException::withMessages(['to' => 'Vul een ontvanger in.']);
            }

            $this->mails->sendSingle($template, $subject, $to, $cc, $data['subject'], $data['body'], $user, $uploads, $item, $data['level'] ?? null, $attachForm);
            $message = 'Mail ' . $template->code . ' is verstuurd.';
        }

        return redirect($this->backUrl($data['context'], $subject))->with('message', $message . ($item ? ' De regel is afgevinkt.' : ''));
    }

    /**
     * @return string[]
     */
    protected function parseEmails(string $value, string $field): array
    {
        $emails = array_values(array_filter(preg_split('/[\s,;]+/', trim($value))));

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([$field => $email . ' is geen geldig e-mailadres.']);
            }
        }

        return array_values(array_unique($emails));
    }

    protected function itemFor($subject, ?int $itemId): ?AcademyChecklistItem
    {
        if (!$itemId) {
            return null;
        }

        $item = AcademyChecklistItem::find($itemId);

        abort_unless(
            $item && $item->subject_type === get_class($subject) && (int) $item->subject_id === (int) $subject->getKey(),
            Response::HTTP_NOT_FOUND
        );

        return $item;
    }

    protected function backUrl(string $context, $subject): string
    {
        return match (true) {
            $subject instanceof Company => route('admin.academy.employers.show', $subject->id),
            $subject instanceof AcademyTraining => route('admin.academy.trainings.show', $subject->id),
            $subject instanceof Certificate => route('admin.academy.overview', ['tab' => 'bewaken']),
            default => route('admin.academy.overview'),
        };
    }
}
