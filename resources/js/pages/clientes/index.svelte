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
  import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationInertiaLink,
    PaginationNext,
    PaginationPrevious,
  } from '@/components/_ui/pagination';
  import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
  } from '@/components/_ui/table';
  import AppHead from '@/components/AppHead.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import type { Cliente } from '@/types/cliente';
  import type { LaravelPaginator } from '@/types/paginate';

  type Props = {
    clientes: LaravelPaginator<Cliente>;
  };
  let { clientes }: Props = $props();

  const form = useForm();

  const handleDelete = (cliente: Cliente) => {
    const nombreCompleto = `${cliente.apellido_primario} ${cliente.apellido_secundario}, ${cliente.nombres}`;

    if (
      confirm(`Estas seguro que desea eliminar el cliente?:\n${nombreCompleto}`)
    ) {
      form.delete(ClienteController.destroy.url(cliente.id));
    }
  };
</script>

<AppHead title="Clientes" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <!-- TODO: el cursor no se establece -->
  <Link href={ClienteController.create.url()} class="w-fit">
    <Button>Nuevo Cliente</Button>
  </Link>

  <Table>
    <TableHeader>
      <TableRow>
        <TableHead>#</TableHead>
        <TableHead>Nombres</TableHead>
        <TableHead>Primer Apellido</TableHead>
        <TableHead>Segundo Apellido</TableHead>
        <TableHead>RUC</TableHead>
        <TableHead>DNI</TableHead>
        <TableHead>Telefono</TableHead>
        <TableHead>Acciones</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      {#each clientes.data as item, idx (item.id)}
        <TableRow>
          <TableCell>{idx + Number(clientes.from)}</TableCell>
          <TableCell>{item.nombres}</TableCell>
          <TableCell>{item.apellido_primario}</TableCell>
          <TableCell>{item.apellido_secundario}</TableCell>
          <TableCell class="font-mono">{item.ruc}</TableCell>
          <TableCell class="font-mono">{item.dni}</TableCell>
          <TableCell class="font-mono">{item.telefono}</TableCell>
          <TableCell>
            <Link href={ClienteController.edit(item.id)}>
              <Button class="bg-slate-500 hover:bg-slate-700">Editar</Button>
            </Link>
            <Button
              disabled={form.processing}
              class="bg-red-500 hover:bg-red-700"
              onclick={() => handleDelete(item)}
            >
              Borrar
            </Button>
          </TableCell>
        </TableRow>
      {/each}
    </TableBody>
  </Table>

  <div class="flex flex-row justify-between p-4">
    <span class="text-gray-400"
      >Mostrando <span class="font-semibold text-white"
        >{clientes.from}-{clientes.to}</span
      >
      de
      <span class="text-white">{clientes.total}</span></span
    >
    <Pagination class="mx-0 w-auto">
      <PaginationContent>
        <PaginationItem>
          <PaginationPrevious
            class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
            href={clientes.prev_page_url}
          />
        </PaginationItem>
        {#each clientes.links as item (item.label)}
          {#if !item.label.includes('Previous') && !item.label.includes('Next')}
            <PaginationItem>
              <PaginationInertiaLink
                href={item.url!}
                prefetch
                class="flex items-center justify-center px-3 py-2 text-sm leading-tight border"
                isActive={item.active}>{item.label}</PaginationInertiaLink
              >
            </PaginationItem>
          {/if}
        {/each}
        <PaginationItem>
          <PaginationNext
            class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
            href={clientes.next_page_url}
          />
        </PaginationItem>
      </PaginationContent>
    </Pagination>
  </div>
</div>
