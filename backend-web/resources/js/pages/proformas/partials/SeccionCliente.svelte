<script lang="ts">
  import ClienteController from '@/actions/App/Http/Controllers/ClienteController';
  import Autocomplete from '@/components/_ui/autocomplete.svelte';
  import type { Cliente } from '@/types/cliente';
  import type { Proforma } from '@/types/proforma';

  type Props = {
    proforma: Pick<Proforma, 'cliente'>;
    onAcceptCliente?: (cliente_id: number) => void;
  };
  const { proforma, onAcceptCliente }: Props = $props();

  let buscando = $state.raw(false);

  // svelte-ignore state_referenced_locally
  let esEmpresa = $state.raw(Boolean(proforma.cliente.ruc));

  // svelte-ignore state_referenced_locally
  let clienteItem = $state({
    nombres: proforma.cliente.nombres,
    apellido_primario: proforma.cliente.apellido_primario ?? '',
    apellido_secundario: proforma.cliente.apellido_secundario ?? '',
    ruc: proforma.cliente.ruc ?? '',
    dni: proforma.cliente.dni ?? '',
    telefono: proforma.cliente.telefono ?? '',
  });

  const handleAcceptCliente = async (
    search: string,
    searchBy:
      | 'nombres'
      | 'apellido_primario'
      | 'apellido_secundario'
      | 'ruc'
      | 'dni'
      | 'telefono',
  ) => {
    buscando = true;

    try {
      const response = await fetch(
        ClienteController.get.url({ query: { q: search, by: searchBy } }),
      );
      const cliente = (await response.json()) as Cliente;

      clienteItem = {
        nombres: cliente.nombres,
        apellido_primario: cliente.apellido_primario ?? '',
        apellido_secundario: cliente.apellido_secundario ?? '',
        ruc: cliente.ruc ?? '',
        dni: cliente.dni ?? '',
        telefono: cliente.telefono ?? '',
      };

      esEmpresa = Boolean(cliente.ruc);

      onAcceptCliente?.(cliente.id);
    } catch (error) {
      console.error('Error al buscar cliente:', error);
    } finally {
      buscando = false;
    }
  };
</script>

<fieldset class="fieldset rounded-box border border-base-100 px-2 w-xs">
  <legend class="fieldset-legend px-2 leading-none"
    >Cliente{buscando ? ' - Buscando...' : ''}</legend
  >

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
      disabled={buscando}
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
    <Autocomplete
      bind:value={clienteItem.nombres}
      urlTemplate="/clientes/search?q=%s&by=nombres"
      onAccept={(search) => handleAcceptCliente(search, 'nombres')}
    />
  </label>
  {#if !esEmpresa}
    <div class="flex flex-row gap-4">
      <label>
        <span class="label">Primer Apellido</span>
        <Autocomplete
          bind:value={clienteItem.apellido_primario}
          urlTemplate="/clientes/search?q=%s&by=apellido_primario"
          onAccept={(search) =>
            handleAcceptCliente(search, 'apellido_primario')}
        />
      </label>
      <label>
        <span class="label">Segundo Apellido</span>
        <Autocomplete
          bind:value={clienteItem.apellido_secundario}
          urlTemplate="/clientes/search?q=%s&by=apellido_secundario"
          onAccept={(search) =>
            handleAcceptCliente(search, 'apellido_secundario')}
        />
      </label>
    </div>
  {/if}
  <div class="flex flex-row gap-4">
    <label class="min-w-30">
      {#if esEmpresa}
        <span class="label">RUC</span>
        <Autocomplete
          bind:value={clienteItem.ruc}
          urlTemplate="/clientes/search?q=%s&by=ruc"
          onAccept={(search) => handleAcceptCliente(search, 'ruc')}
        />
      {:else}
        <span class="label">DNI</span>
        <Autocomplete
          bind:value={clienteItem.dni}
          urlTemplate="/clientes/search?q=%s&by=dni"
          onAccept={(search) => handleAcceptCliente(search, 'dni')}
        />
      {/if}
    </label>
    <label class="min-w-30">
      <span class="label">Telefono</span>
      <Autocomplete
        bind:value={clienteItem.telefono}
        urlTemplate="/clientes/search?q=%s&by=telefono"
        onAccept={(search) => handleAcceptCliente(search, 'telefono')}
      />
    </label>
  </div>
</fieldset>
