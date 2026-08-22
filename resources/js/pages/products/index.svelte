<script module lang="ts">
  import { index } from '@/routes/products';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Productos',
        href: index.url(),
      },
    ],
  };
</script>

<script lang="ts">
  import AppHead from '@/components/AppHead.svelte';
  import { Link, useForm } from '@inertiajs/svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import ProductController from '@/actions/App/Http/Controllers/ProductController';
  import type { LaravelPaginator } from '@/types/paginate';
  import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
  } from '@/components/_ui/table';
  import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
  } from '@/components/_ui/pagination';
  import type { Product } from '@/types/product';

  type Props = {
    products: LaravelPaginator<Product>;
  };
  let { products }: Props = $props();

  const form = useForm();

  const handleDelete = (id: number) => {
    if (confirm('Estas seguro que deseas eliminar este producto?')) {
      form.delete(ProductController.destroy.url(id));
    }
  };
</script>

<AppHead title="Productos" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <!-- TODO: el cursor no se establece -->
  <Link href={ProductController.create.url()} class="w-fit">
    <Button>Nuevo Producto</Button>
  </Link>

  <Table>
    <TableHeader>
      <TableRow>
        <TableHead>Nombre</TableHead>
        <TableHead>Descripcion</TableHead>
        <TableHead>Precio</TableHead>
        <TableHead>Unidad de Medida</TableHead>
        <TableHead>Stock</TableHead>
        <TableHead>Activo</TableHead>
        <TableHead>Acciones</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      {#each products.data as item (item.id)}
        <TableRow>
          <TableCell>{item.nombre}</TableCell>
          <TableCell>{item.descripcion}</TableCell>
          <TableCell>{item.precio}</TableCell>
          <TableCell>{item.unidad_medida}</TableCell>
          <TableCell>{item.stock}</TableCell>
          <TableCell>{item.activo}</TableCell>
          <TableCell>
            <Link href={ProductController.edit(item.id)}>
              <Button class="bg-slate-500 hover:bg-slate-700">Editar</Button>
            </Link>
            <Button
              disabled={form.processing}
              class="bg-red-500 hover:bg-red-700"
              onclick={() => handleDelete(item.id)}
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
        >{products.from}-{products.to}</span
      >
      de
      <span class="text-white">{products.total}</span></span
    >
    <Pagination class="mx-0 w-auto">
      <PaginationContent>
        <PaginationItem>
          <PaginationPrevious
            class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
            href={products.prev_page_url}
          />
        </PaginationItem>
        {#each products.links as item (item.label)}
          {#if !item.label.includes('Previous') && !item.label.includes('Next')}
            <PaginationItem>
              <PaginationLink
                href={item.url}
                class={`flex items-center justify-center px-3 py-2 text-sm leading-tight border ${item.active ? 'hover:bg-primary-100 hover:text-primary-700 border-gray-700 bg-gray-700 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700 hover:text-white'}`}
                >{item.label}</PaginationLink
              >
            </PaginationItem>
          {/if}
        {/each}
        <PaginationItem>
          <PaginationNext
            class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
            href={products.next_page_url}
          />
        </PaginationItem>
      </PaginationContent>
    </Pagination>
  </div>
</div>
