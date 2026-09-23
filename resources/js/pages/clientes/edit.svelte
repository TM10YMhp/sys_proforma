<script module lang="ts">
  import clienteRoutes from '@/routes/clientes';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Clientes',
        href: clienteRoutes.index.url(),
      },
      {
        title: 'Editar',
        href: clienteRoutes.edit.url(0),
      },
    ],
  };
</script>

<script lang="ts">
  import { useForm } from '@inertiajs/svelte';
  import ClienteController from '@/actions/App/Http/Controllers/ClienteController';
  import AppHead from '@/components/AppHead.svelte';
  import InputError from '@/components/InputError.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import Input from '@/components/ui/input/Input.svelte';
  import { Label } from '@/components/ui/label';
  import type { Cliente } from '@/types/cliente';

  type Props = {
    cliente: Cliente;
  };
  let { cliente }: Props = $props();

  // svelte-ignore state_referenced_locally
  const form = useForm<Omit<Cliente, 'id' | 'created_at' | 'updated_at'>>({
    nombres: cliente.nombres,
    apellido_primario: cliente.apellido_primario,
    apellido_secundario: cliente.apellido_secundario,
    ruc: cliente.ruc,
    dni: cliente.dni,
    telefono: cliente.telefono,
  });

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();
    form.put(ClienteController.update.url(cliente.id));
  };
</script>

<AppHead title="Clientes | Editar" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="w-1/3 space-y-4">
    <div>
      <Label for="nombres">Nombres</Label>
      <Input id="nombres" bind:value={form.nombres} />
      <InputError message={form.errors.nombres} />
    </div>
    <div class="flex flex-row gap-4">
      <div>
        <Label for="apellido_primario">Primer Apellido</Label>
        <Input id="apellido_primario" bind:value={form.apellido_primario} />
        <InputError message={form.errors.apellido_primario} />
      </div>
      <div>
        <Label for="apellido_secundario">Segundo Apellido</Label>
        <Input id="apellido_secundario" bind:value={form.apellido_secundario} />
        <InputError message={form.errors.apellido_secundario} />
      </div>
    </div>
    <div class="flex flex-row gap-4">
      <div>
        <Label for="ruc">RUC</Label>
        <Input id="ruc" type="number" bind:value={form.ruc} />
        <InputError message={form.errors.ruc} />
      </div>
      <div>
        <Label for="dni">DNI</Label>
        <Input id="dni" type="number" bind:value={form.dni} />
        <InputError message={form.errors.dni} />
      </div>
      <div>
        <Label for="telefono">Telefono</Label>
        <Input id="telefono" bind:value={form.telefono} />
        <InputError message={form.errors.telefono} />
      </div>
    </div>
    <Button disabled={form.processing} type="submit">Actualizar Cliente</Button
    >
  </form>
</div>
