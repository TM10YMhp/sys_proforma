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
  import InputError from '@/components/InputError.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import Input from '@/components/ui/input/Input.svelte';
  import { Label } from '@/components/ui/label';
  import { buscarRUC } from '@/core/cliente';
  import type { Cliente } from '@/types/cliente';

  const form = useForm<Omit<Cliente, 'id' | 'created_at' | 'updated_at'>>({
    nombres: '',
    apellido_primario: '',
    apellido_secundario: '',
    ruc: '',
    dni: '',
    telefono: '',
  });
  let buscando = $state.raw(false);

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();
    form.post(ClienteController.store.url());
  };

  const fillRUC = async (
    e: KeyboardEvent & {
      currentTarget: EventTarget & HTMLInputElement;
    },
  ) => {
    if (e.key !== 'Enter') {
      return;
    }

    // TODO: verificar que el campo no este vacio
    e.preventDefault();

    if (buscando) {
      console.log('Esperando a que termine de buscar');

      return;
    }

    buscando = true;
    form.errors.ruc = '';

    try {
      // NOTE:Inertia pasa el tipo a number
      const razonSocial = await buscarRUC(form.ruc.toString());
      form.nombres = razonSocial;
    } catch (error) {
      if (error instanceof Error) {
        form.errors.ruc = error.message;
      } else {
        form.errors.ruc = 'Ocurrio un error inesperado';
      }
    } finally {
      buscando = false;
    }
  };
</script>

<AppHead title="Clientes | Nuevo" />

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
        <Input
          id="ruc"
          type="number"
          bind:value={form.ruc}
          onkeydown={fillRUC}
        />
        <InputError message={form.errors.ruc} />
        {#if buscando}
          <p class="">⏳ Buscando...</p>
        {/if}
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
    <Button disabled={form.processing} type="submit">Crear Cliente</Button>
  </form>
</div>
