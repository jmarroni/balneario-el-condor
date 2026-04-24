<x-admin.form-field name="subject" label="Asunto" :value="$campaign->subject" required />

<x-admin.form-field name="body_html" label="Cuerpo HTML" type="textarea" :value="$campaign->body_html"
    required class="min-h-[250px] font-mono text-xs" />

<x-admin.form-field name="body_text" label="Cuerpo texto plano (opcional)" type="textarea"
    :value="$campaign->body_text" class="min-h-[150px] font-mono text-xs" />

<x-admin.form-field name="scheduled_at" label="Programada para" type="datetime-local"
    :value="optional($campaign->scheduled_at)->format('Y-m-d\TH:i')" />

<x-admin.form-field name="status" label="Estado" type="select" :value="$campaign->status ?? 'draft'">
    @foreach(['draft' => 'Borrador', 'sending' => 'Enviando', 'sent' => 'Enviada'] as $k => $label)
        <option value="{{ $k }}" @selected(old('status', $campaign->status ?? 'draft') === $k)>{{ $label }}</option>
    @endforeach
</x-admin.form-field>
