<script module lang="ts">
  import { index } from '@/routes/clientes';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Clientes',
        href: index.url(),
      },
    ],
  };
</script>

<script lang="ts">
  import { Link, useForm } from '@inertiajs/svelte';
  import ClienteController from '@/actions/App/Http/Controllers/ClienteController';
  import AppHead from '@/components/AppHead.svelte';
  import { cn } from '@/lib/utils';
  import { table } from '@/lib/variants';
  import type { Cliente } from '@/types/cliente';
  import type { LaravelPaginator } from '@/types/paginate';

  type Props = {
    clientes: LaravelPaginator<Cliente>;
  };
  let { clientes }: Props = $props();

  const form = useForm();

  const handleDelete = (cliente: Cliente) => {
    if (
      confirm(
        `Estas seguro que desea eliminar el cliente?:\n${formatClient(cliente)}`,
      )
    ) {
      form.delete(ClienteController.destroy.url(cliente.id));
    }
  };

  const formatClient = (cliente: Cliente) => {
    if (cliente.ruc) {
      return cliente.nombres;
    }

    return `${cliente.nombres}, ${cliente.apellido_primario} ${cliente.apellido_secundario}`;
  };
</script>

<AppHead title="Clientes" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <Link href={ClienteController.create.url()} class="btn btn-primary w-fit">
    Nuevo Cliente
  </Link>

  <div class="overflow-x-auto">
    <table class={table()}>
      <thead>
        <tr>
          <th>#</th>
          <th>Nombres</th>
          <th>RUC/DNI</th>
          <th>Telefono</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        {#each clientes.data as item, index (item.id)}
          <tr>
            <th>{index + Number(clientes.from)}</th>
            <td class="text-nowrap">{formatClient(item)}</td>
            <td class="font-mono">{item.ruc || item.dni}</td>
            <td class="font-mono">{item.telefono}</td>
            <td>
              <Link href={ClienteController.edit(item.id)} class="btn btn-info">
                Editar
              </Link>
              <button
                disabled={form.processing}
                class="btn btn-error"
                onclick={() => handleDelete(item)}
              >
                Borrar
              </button>
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </div>

  <div class="flex flex-row justify-between p-4">
    <span class="text-gray-400"
      >Mostrando <span class="font-semibold text-white"
        >{clientes.from}-{clientes.to}</span
      >
      de
      <span class="text-white">{clientes.total}</span></span
    >

    <div class="join">
      <Link
        prefetch="click"
        cacheFor="3m"
        href={clientes.prev_page_url!}
        class="join-item btn btn-soft">«</Link
      >
      {#each clientes.links as item (item.label)}
        {#if !item.label.includes('Previous') && !item.label.includes('Next')}
          <Link
            prefetch="click"
            cacheFor="3m"
            href={item.url!}
            class={cn('join-item btn btn-soft', item.active && 'btn-active')}
            >{item.label}</Link
          >
        {/if}
      {/each}
      <Link
        prefetch="click"
        cacheFor="3m"
        href={clientes.next_page_url!}
        class="join-item btn btn-soft">»</Link
      >
    </div>
  </div>
</div>
