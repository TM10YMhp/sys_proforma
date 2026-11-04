<script module lang="ts">
  import clienteRoutes from '@/routes/clientes';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Clientes',
        href: clienteRoutes.index.url(),
      },
      {
        title: 'Nuevo',
        href: clienteRoutes.create.url(),
      },
    ],
  };
</script>

<script lang="ts">
  import { useForm } from '@inertiajs/svelte';
  import ClienteController from '@/actions/App/Http/Controllers/ClienteController';
  import AppHead from '@/components/AppHead.svelte';
  import type { Cliente } from '@/types/cliente';

  const form = useForm<Omit<Cliente, 'id' | 'created_at' | 'updated_at'>>({
    nombres: '',
    apellido_primario: '',
    apellido_secundario: '',
    ruc: '',
    dni: '',
    telefono: '',
  });

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();
    form.post(ClienteController.store.url());
  };

  // let buscando = $state.raw(false);

  // const fillRUC = async (
  //   e: KeyboardEvent & {
  //     currentTarget: EventTarget & HTMLInputElement;
  //   },
  // ) => {
  //   if (e.key !== 'Enter') {
  //     return;
  //   }

  //   // TODO: verificar que el campo no este vacio
  //   e.preventDefault();

  //   if (buscando) {
  //     console.log('Esperando a que termine de buscar');

  //     return;
  //   }

  //   buscando = true;
  //   form.errors.ruc = '';

  //   try {
  //     // NOTE:Inertia pasa el tipo a number
  //     const razonSocial = await buscarRUC(form.ruc.toString());
  //     form.nombres = razonSocial;
  //   } catch (error) {
  //     if (error instanceof Error) {
  //       form.errors.ruc = error.message;
  //     } else {
  //       form.errors.ruc = 'Ocurrio un error inesperado';
  //     }
  //   } finally {
  //     buscando = false;
  //   }
  // };

  let esEmpresa = $state.raw(true);
</script>

<AppHead title="Clientes | Nuevo" />

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

  {#if form.hasErrors}
    <fieldset class="fieldset rounded-box border border-base-100 px-2 w-xs">
      <legend class="fieldset-legend px-2 leading-none">Errores</legend>
      {#each Object.values(form.errors) as error, i (i)}
        <p>{error}</p>
      {/each}
    </fieldset>
  {/if}

  <button class="btn btn-primary" disabled={form.processing} type="submit"
    >Crear Cliente</button
  >
</form>
