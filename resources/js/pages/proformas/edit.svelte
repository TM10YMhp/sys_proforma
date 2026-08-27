<script module lang="ts">
  import proformasRoutes from '@/routes/proformas';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Proformas',
        href: proformasRoutes.index.url(),
      },
      {
        title: 'Editar',
        href: proformasRoutes.edit.url(0),
      },
    ],
  };
</script>

<script lang="ts">
  import { useForm } from '@inertiajs/svelte';
  import ProformaController from '@/actions/App/Http/Controllers/ProformaController';
  import AppHead from '@/components/AppHead.svelte';
  import InputError from '@/components/InputError.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import Input from '@/components/ui/input/Input.svelte';
  import { Label } from '@/components/ui/label';
  import type { Proforma } from '@/types/proforma';

  type Props = {
    proforma: Proforma;
  };
  let { proforma }: Props = $props();

  // svelte-ignore state_referenced_locally
  const form = useForm<Omit<Proforma, 'id' | 'created_at' | 'updated_at'>>({
    codigo: proforma.codigo,
    fecha_emision: proforma.fecha_emision,
    fecha_vencimiento: proforma.fecha_vencimiento,
    subtotal: proforma.subtotal,
    igv_tasa: proforma.igv_tasa,
    igv_monto: proforma.igv_monto,
    total: proforma.total,
  });

  const onChangeSubtotal = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const value = target.valueAsNumber;
    // form.subtotal = value;
    form.igv_monto = value * form.igv_tasa;
    form.total = value + form.igv_monto;
  };

  const stringToDatetime = (fecha_utc: string) => {
    const dateLocal = new Date(fecha_utc);
    const offset = dateLocal.getTimezoneOffset() * 60000;
    const localISODate = new Date(dateLocal.getTime() - offset).toISOString();

    return localISODate.slice(0, 16);
  };

  // TODO: solo es necesario como valor inicial
  let fecha_emision = $state(stringToDatetime(form.fecha_emision));
  let fecha_vencimiento = $state(stringToDatetime(form.fecha_vencimiento));

  const datetimeToUTC = (datetime: string) => {
    return new Date(datetime).toISOString();
  };

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();
    form.fecha_emision = datetimeToUTC(fecha_emision);
    form.fecha_vencimiento = datetimeToUTC(fecha_vencimiento);
    form.put(ProformaController.update.url(proforma.id));
  };
</script>

<AppHead title="Proformas | Editar" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="w-8/12 space-y-4">
    <div class="grid gap-2">
      <Label for="codigo">Codigo</Label>
      <Input id="codigo" bind:value={form.codigo} />
      <InputError message={form.errors.codigo} />
    </div>
    <div class="grid gap-2">
      <Label for="emision">Fecha de Emision</Label>
      <Input id="emision" type="datetime-local" bind:value={fecha_emision} />
      <InputError message={form.errors.fecha_emision} />
    </div>
    <div class="grid gap-2">
      <Label for="vencimiento">Fecha de Vencimiento</Label>
      <Input
        id="vencimiento"
        type="datetime-local"
        bind:value={fecha_vencimiento}
      />
      <InputError message={form.errors.fecha_vencimiento} />
    </div>
    <div class="grid gap-2">
      <Label for="subtotal">Subtotal</Label>
      <Input
        id="subtotal"
        type="number"
        step="0.01"
        bind:value={form.subtotal}
        oninput={onChangeSubtotal}
      />
      <InputError message={form.errors.subtotal} />
    </div>
    <div class="grid gap-2">
      <Label for="igv_tasa">IGV Tasa</Label>
      <Input id="igv_tasa" bind:value={form.igv_tasa} />
      <InputError message={form.errors.igv_tasa} />
    </div>
    <div class="grid gap-2">
      <Label for="igv_monto">IGV Monto</Label>
      <Input id="igv_monto" bind:value={form.igv_monto} readonly />
      <InputError message={form.errors.igv_monto} />
    </div>
    <div class="grid gap-2">
      <Label for="total">Total</Label>
      <Input id="total" bind:value={form.total} readonly />
      <InputError message={form.errors.total} />
    </div>
    <Button disabled={form.processing} type="submit">Actualizar Proforma</Button
    >
  </form>
</div>
