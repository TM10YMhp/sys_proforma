<script lang="ts">
  import type { InertiaForm } from '@inertiajs/svelte';
  import type { Proforma, ProformaForm } from '@/types/proforma';

  type Props = {
    form: InertiaForm<ProformaForm>;
    proforma: Proforma;
  };
  const { form = $bindable(), proforma }: Props = $props();

  // svelte-ignore state_referenced_locally
  let esEmpresa = $state(Boolean(proforma.cliente.ruc));

  // svelte-ignore state_referenced_locally
  let cliente = $state({
    nombres: proforma.cliente.nombres,
    apellido_primario: proforma.cliente.apellido_primario ?? '',
    apellido_secundario: proforma.cliente.apellido_secundario ?? '',
    ruc: proforma.cliente.ruc ?? '',
    dni: proforma.cliente.dni ?? '',
    telefono: proforma.cliente.telefono ?? '',
  });

  const noLabelStyle = 'btn btn-link no-underline text-white btn-square';
</script>

<fieldset class="fieldset rounded-box border border-base-100 px-2 w-xs">
  <legend class="fieldset-legend px-2 leading-none">Cliente - Solo Busqueda</legend>

  <label>
    <span class="label">Nombres</span>
    <input class="input" defaultValue={cliente.nombres} />
  </label>
  {#if !esEmpresa}
    <div class="flex flex-row gap-4">
      <label>
        <span class="label">Primer Apellido</span>
        <input class="input" defaultValue={cliente.apellido_primario} />
      </label>
      <label>
        <span class="label">Segundo Apellido</span>
        <input class="input" defaultValue={cliente.apellido_secundario} />
      </label>
    </div>
  {/if}
  <div class="flex flex-row gap-4">
    <label class="min-w-30">
      {#if esEmpresa}
        <span class="label">RUC</span>
        <input class="input" defaultValue={cliente.ruc} />
      {:else}
        <span class="label">DNI</span>
        <input class="input" defaultValue={cliente.dni} />
      {/if}
    </label>
    <label class="min-w-30">
      <span class="label">Telefono</span>
      <input class="input" defaultValue={cliente.telefono} />
    </label>
  </div>

  <div class="flex gap-2 items-center">
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
</fieldset>
