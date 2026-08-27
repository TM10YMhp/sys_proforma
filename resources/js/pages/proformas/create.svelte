<script module lang="ts">
  import proformasRoutes from '@/routes/proformas';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Proformas',
        href: proformasRoutes.index.url(),
      },
      {
        title: 'Nuevo',
        href: proformasRoutes.create.url(),
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

  const form = useForm<Omit<Proforma, 'id' | 'created_at' | 'updated_at'>>({
    codigo: crypto.randomUUID().split('-')[0],
    fecha_emision: '',
    fecha_vencimiento: '',
    subtotal: 0,
    igv_tasa: 0.18,
    igv_monto: 0,
    total: 0,
  });

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();
    form.post(ProformaController.store.url());
  };

  const onChangeSubtotal = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const value = target.valueAsNumber;
    // form.subtotal = value;
    form.igv_monto = value * form.igv_tasa;
    form.total = value + form.igv_monto;
  };
</script>

<AppHead title="Proformas | Nuevo" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="w-8/12 space-y-4">
    <div class="grid gap-2">
      <Label for="codigo">Codigo</Label>
      <Input id="codigo" bind:value={form.codigo} />
      <InputError message={form.errors.codigo} />
    </div>
    <div class="grid gap-2">
      <Label for="emision">Fecha de Emision</Label>
      <Input id="emision" type="date" bind:value={form.fecha_emision} />
      <InputError message={form.errors.fecha_emision} />
    </div>
    <div class="grid gap-2">
      <Label for="vencimiento">Fecha de Vencimiento</Label>
      <Input id="vencimiento" type="date" bind:value={form.fecha_vencimiento} />
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
    <Button disabled={form.processing} type="submit">Crear Producto</Button>
  </form>
</div>
