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

  // svelte-ignore state_referenced_locally
  let esEmpresa = $state.raw(Boolean(cliente.ruc));
</script>

<AppHead title="Clientes | Editar" />

<form class="grid gap-2 grid-cols-1 max-w-80 m-4" onsubmit={handleSubmit}>
  <div class="flex gap-2 items-center">
    {const noLabelStyle = 'btn btn-link no-underline text-white btn-square'}

    <button
      type="button"
      class={noLabelStyle}
      onclick={() => (esEmpresa = false)}>DNI</button
    >
    <input
      type="checkbox"
      bind:checked={esEmpresa}
      class={[
        'toggle',
        'border-blue-600 bg-blue-500 text-blue-800',
        'checked:border-emerald-500 checked:bg-emerald-400 checked:text-emerald-800',
      ]}
    />
    <button
      type="button"
      class={noLabelStyle}
      onclick={() => (esEmpresa = true)}>RUC</button
    >
  </div>

  <label>
    <span class="label">Nombres</span>
    <input bind:value={form.nombres} class="input" />
  </label>
  {#if !esEmpresa}
    <div class="flex flex-row gap-4">
      <label>
        <span class="label">Primer Apellido</span>
        <input bind:value={form.apellido_primario} class="input" />
      </label>
      <label>
        <span class="label">Segundo Apellido</span>
        <input bind:value={form.apellido_secundario} class="input" />
      </label>
    </div>
  {/if}
  <div class="flex flex-row gap-4">
    <label class="min-w-30">
      {#if esEmpresa}
        <span class="label">RUC</span>
        <input bind:value={form.ruc} class="input" />
      {:else}
        <span class="label">DNI</span>
        <input bind:value={form.dni} class="input" />
      {/if}
    </label>
    <label class="min-w-30">
      <span class="label">Telefono</span>
      <input bind:value={form.telefono} class="input" />
    </label>
  </div>

  <button class="btn btn-primary" disabled={form.processing} type="submit"
    >Actualizar Cliente</button
  >
</form>
