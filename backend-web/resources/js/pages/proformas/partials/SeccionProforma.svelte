<script lang="ts">
  import type { InertiaForm } from '@inertiajs/svelte';
  import InputError from '@/components/InputError.svelte';
  import type { Proforma, ProformaForm } from '@/types/proforma';

  // HACK: tomar solo propiedades que se necesitan
  type Props = {
    form: InertiaForm<
      Pick<
        ProformaForm,
        'fecha_emision' | 'fecha_vencimiento' | 'igv_tasa' | 'subtotal'
      >
    >;
    proforma: Pick<Proforma, 'codigo'>;
  };
  const { form = $bindable(), proforma }: Props = $props();

  const stringToDatetime = (fecha_utc: string) => {
    const dateLocal = new Date(fecha_utc);
    const offset = dateLocal.getTimezoneOffset() * 60 * 1000; // minutos a milisegundos
    const localISODate = new Date(dateLocal.getTime() - offset).toISOString();

    return localISODate.slice(0, 16);
  };
  const datetimeToUTC = (datetime: string) => {
    return new Date(datetime).toISOString();
  };

  const montoIgv = $derived(form.subtotal * form.igv_tasa);
  const total = $derived(form.subtotal + montoIgv);
</script>

<fieldset class="fieldset rounded-box border border-base-100 px-2">
  <legend class="fieldset-legend px-2 leading-none mb-0">Proforma</legend>

  <div
    class="grid grid-cols-[auto_1fr] gap-x-2 [&>*:nth-child(odd)]:font-bold [&>*:nth-child(odd)]:text-right"
  >
    <span>Codigo:</span>
    <span>{proforma.codigo}</span>
    <span>Subtotal:</span>
    <span>{form.subtotal.toFixed(2)}</span>
    <span>IGV Monto:</span>
    <span>{montoIgv.toFixed(2)}</span>
    <span>Total:</span>
    <span>{total.toFixed(2)}</span>
  </div>

  <label for="emision" class="label">Fecha de Emision</label>
  <input
    id="emision"
    class="input font-mono"
    type="datetime-local"
    bind:value={
      () => stringToDatetime(form.fecha_emision),
      (v) => (form.fecha_emision = datetimeToUTC(v))
    }
  />
  <InputError message={form.errors.fecha_emision} />

  <label for="vencimiento" class="label">Fecha de Vencimiento</label>
  <input
    id="vencimiento"
    class="input font-mono"
    type="datetime-local"
    bind:value={
      () => stringToDatetime(form.fecha_vencimiento),
      (v) => (form.fecha_vencimiento = datetimeToUTC(v))
    }
  />
  <InputError message={form.errors.fecha_vencimiento} />

  <label for="igv_tasa" class="label">IGV Tasa (%)</label>
  <input
    class="input w-fit"
    id="igv_tasa"
    bind:value={
      () => Math.round(form.igv_tasa * 100), (v) => (form.igv_tasa = v / 100)
    }
    type="number"
    min="0"
    max="100"
    step="1"
  />
  <InputError message={form.errors.igv_tasa} />
</fieldset>
